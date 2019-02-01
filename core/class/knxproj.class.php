<?php
class knxproj {
	private $path;
	private $options;
	private $Devices=array();
	private $GroupAddresses=array();
	private $Templates=array();
	private $myProject=array();
	public static function ExtractTX100ProjectFile($File){
		$path = dirname(__FILE__) . '/../config/knxproj/';
		if (!is_dir($path)) 
			mkdir($path);
		exec('sudo chmod -R 777 '.$path);
		system('cd ' . $path . '; tar xfz "' . $File . '"');
		log::add('eibd','debug','[Import TX100] Extraction des fichiers de projets');
	}
	public static function ExtractETSProjectFile($File){
		$path = dirname(__FILE__) . '/../config/knxproj/';
		if (!is_dir($path)) 
			mkdir($path);
		exec('sudo chmod -R 777 '.$path);
		$zip = new ZipArchive(); 
		// On ouvre l’archive.
		if($zip->open($File) == TRUE){
			$zip->extractTo($path . 'knxproj/');
			$zip->close();
		}
		log::add('eibd','debug','[Import ETS] Extraction des fichiers de projets');
	}
 	public function __construct($_options){
		$this->path = dirname(__FILE__) . '/../config/knxproj/';
		$this->Templates=eibd::devicesParameters();
		$this->options=$_options[0];
		
		//log::add('eibd','debug','[Import ETS]'.json_encode($_options));
		$filename=$this->path.'EtsProj.json';
		if (file_exists($filename)) 
			exec('sudo rm '.$filename);
		switch($this->options['ProjetType']){
			case "ETS":
				$ProjetFile=$this->SearchETSFolder("P-");
				$this->myProject=simplexml_load_file($ProjetFile.'/0.xml');

				$this->ParserETSDevice();
				$this->ParserETSGroupAddresses();
				$this->CheckOptions();
			break;
			case "TX100":
				$ProjetFile=$this->SearchTX100Folder();
				$this->ParserTX100GroupAddresses();
			break;
		}
	}
 	public function __destruct(){
		$path = dirname(__FILE__) . '/../config/knxproj/';
		if (file_exists($path)) 
			exec('sudo rm -R '.$path );
	}
	public function WriteJsonProj(){
		$filename=dirname(__FILE__) . '/../config/KnxProj.json';
		if (file_exists($filename)) 
			exec('sudo rm '.$filename);
		$file=fopen($filename,"a+");
		fwrite($file,$this->getAll());
		fclose($file);	
	}
	public function getAll(){
		$myKNX['Devices']=$this->Devices;
		$myKNX['GAD']=$this->GroupAddresses;
		return json_encode($myKNX,JSON_PRETTY_PRINT);
	}
	private function SearchTX100Folder(){
		log::add('eibd','debug','[Import TX100] SearchTX100Folder ');
		if ($dh = opendir($this->path)){
			log::add('eibd','debug','[Import TX100] overture de  '.$this->path);
			while (($file = readdir($dh)) !== false){
				log::add('eibd','debug','[Import TX100] Rechecher '.$file);
				if($file != '.' && $file != '..'){
					if ($file == 'configuration'){
						$this->path .= $file.'/';
						log::add('eibd','debug','[Import TX100] Rechecher dossier '.$this->path);
						return $this->path;
					}else{
						$this->path .= $file.'/';
						log::add('eibd','debug','[Import TX100] Rechecher dossier '.$this->path);
						$this->SearchTX100Folder();
					}
				}
			}
			closedir($dh);
		}	
		return false;
	}
	private function SearchETSFolder($Folder){
		if ($dh = opendir($this->path)){
			while (($file = readdir($dh)) !== false){
				if (substr($file,0,2) == $Folder){
					if (opendir($this->path . $file)) 
						return $this->path . $file;
				}
			}
			closedir($dh);
		}	
		return false;
	}
	private function getETSCatalogue($DeviceProductRefId){	
		//log::add('eibd','debug','[Import ETS] Rechecher des nom de module dans le catalogue');
		$Catalogue = new DomDocument();
		if ($Catalogue->load($this->path . substr($DeviceProductRefId,0,6).'/Catalog.xml')) {//XMl décrivant les équipements
			foreach($Catalogue->getElementsByTagName('CatalogItem') as $CatalogItem){
				if ($DeviceProductRefId==$CatalogItem->getAttribute('ProductRefId'))
					return $CatalogItem->getAttribute('Name');
			}
		}
	}
	private function xml_attribute($object, $attribute){
		if(isset($object[$attribute]))
			return (string) $object[$attribute];
	}
	private function ParserTX100GroupAddresses(){
		log::add('eibd','debug','[Import TX100] Création de l\'arboressance de gad');
		$GroupLinks=simplexml_load_file($this->path . 'GroupLinks.xml');
		$this->GroupAddresses = $this->getTX100Level($GroupLinks);
	}
	private function getTX100Level($GroupRanges,$NbLevel=0){
		$Level = array();
		$NbLevel++;
		foreach ($GroupRanges->children() as $GroupRange) {
			$GroupName = $this->xml_attribute($GroupRange, 'name');
			//<property key="GroupAddress" value="50334" type="string"/>
			if($GroupRange->getName() == 'property' && $this->xml_attribute($GroupRange, 'key') == "GroupAddress"){
				config::save('level',$NbLevel,'eibd');
				$AdresseGroupe=$this->formatgaddr($this->xml_attribute($GroupRange, 'value'));
				$DataPointType=$this->xml_attribute($GroupRanges->config->property, 'value');
				$Level[$GroupName]=array('DataPointType' => $DataPointType,'AdresseGroupe' => $AdresseGroupe);
			}else{
				$Level[$GroupName]=$this->getTX100Level($GroupRange,$NbLevel);
			}
		}
		return $Level;
	}
	private function ParserETSGroupAddresses(){
		log::add('eibd','debug','[Import ETS] Création de l\'arboressance de gad');
		$GroupRanges = $this->myProject->Project->Installations->Installation->GroupAddresses->GroupRanges;
		$this->GroupAddresses = $this->getETSLevel($GroupRanges);
	}
	private function getETSLevel($GroupRanges,$NbLevel=0){
		$Level = array();
		$NbLevel++;
		foreach ($GroupRanges->children() as $GroupRange) {
			$GroupName = $this->xml_attribute($GroupRange, 'Name');
			if($GroupRange->getName() == 'GroupAddress'){
				config::save('level',$NbLevel,'eibd');
				$AdresseGroupe=$this->formatgaddr($this->xml_attribute($GroupRange, 'Address'));
				$GroupId=$this->xml_attribute($GroupRange, 'Id');
				$DataPointType=$this->updateDeviceGad($GroupId,$GroupName,$AdresseGroupe);
				$Level[$GroupName]=array('DataPointType' => $DataPointType,'AdresseGroupe' => $AdresseGroupe);
			}else{
				$Level[$GroupName]=$this->getETSLevel($GroupRange,$NbLevel);
			}
		}
		return $Level;
	}
	private function updateDeviceGad($id,$name,$addr){
		$DPT='';
		foreach($this->Devices as $DeviceProductRefId => $Device){
			foreach($Device['Cmd'] as $GroupAddressRefId=> $Cmd){
				if($GroupAddressRefId == $id){
					$this->Devices[$DeviceProductRefId]['Cmd'][$GroupAddressRefId]['cmdName']=$name;
					$this->Devices[$DeviceProductRefId]['Cmd'][$GroupAddressRefId]['AdresseGroupe']=$addr;
					if($DPT == '')
						$DPT = $this->Devices[$DeviceProductRefId]['Cmd'][$GroupAddressRefId]['DataPointType'];
				}
			}
		}
		return $DPT;
	}
	private function ParserETSDevice(){
		log::add('eibd','debug','[Import ETS] Recherche de device');
		$Topology = $this->myProject->Project->Installations->Installation->Topology;
		foreach($Topology->children() as $Area){
			$AreaAddress=$this->xml_attribute($Area, 'Address');
			foreach ($Area->children() as $Line)  {
				$LineAddress=$this->xml_attribute($Line, 'Address');
				foreach ($Line->children() as $Device)  {
					$DeviceId=$this->xml_attribute($Device, 'Id');
					$DeviceProductRefId=$this->xml_attribute($Device, 'ProductRefId');
					if ($DeviceProductRefId != ''){
						$this->Devices[$DeviceId]=array();
                      				$this->Devices[$DeviceId]['DeviceName']=$this->getETSCatalogue($DeviceProductRefId);
						$DeviceAddress=$this->xml_attribute($Device, 'Address');
						$this->Devices[$DeviceId]['AdressePhysique']=$AreaAddress.'.'.$LineAddress.'.'.$DeviceAddress;
						foreach($Device->children() as $ComObjectInstanceRefs){
							if($ComObjectInstanceRefs->getName() == 'ComObjectInstanceRefs'){
								foreach($ComObjectInstanceRefs->children() as $ComObjectInstanceRef){
									$DataPointType=explode('-',$this->xml_attribute($ComObjectInstanceRef, 'DatapointType'));
									foreach($ComObjectInstanceRef->children() as $Connector){
										foreach($Connector->children() as $Commande)
											$this->Devices[$DeviceId]['Cmd'][$this->xml_attribute($Commande, 'GroupAddressRefId')]['DataPointType']=$DataPointType[1].'.'.sprintf('%1$03d',$DataPointType[2]);
									}
								}
							}
						}
					}
				}
			}
		}
	}
	private function CheckOptions(){
		$ObjetLevel= $this->checkLevel('object');
		$TemplateLevel= $this->checkLevel('function');
		$CommandeLevel= $this->checkLevel('cmd');
		
		$Architecture=array();
		
		foreach($this->GroupAddresses as $Name1 => $Level1){
			$ObjectName = '';
			$TemplateName = '';
			$CmdName = '';
			if($ObjetLevel == 0)
				$ObjectName=$Name1;
			elseif($TemplateLevel == 0)
				$TemplateName=$Name1;
			elseif($CommandeLevel == 0)
				$CmdName=$Name1;
			if(is_array($Level1)){
				foreach($Level1 as $Name2 => $Level2){
					if($ObjetLevel == 1)
						$Object=$Name2;
					elseif($TemplateLevel == 1)
						$TemplateName=$Name2;
					elseif($CommandeLevel == 1)
						$CmdName=$Name2;
					if(is_array($Level2)){
						foreach($Level2 as $Name3 => $Gad){
							if($ObjetLevel == 2)
								$ObjectName=$Name3;
							elseif($TemplateLevel == 2)
								$TemplateName=$Name3;
							elseif($CommandeLevel == 2)
								$CmdName=$Name3;
							$Architecture[$ObjectName][$TemplateName][$CmdName]=$Gad;
						}
					}else{
						$Architecture[$ObjectName][$TemplateName][$CmdName]=$Gad;
					}
				}
			}else{
				$Architecture[$ObjectName][$TemplateName][$CmdName]=$Gad;
			}
		}
		foreach($Architecture as $ObjectName => $Template){
			$Object=$this->createObject($ObjectName);
			foreach($Template as $TemplateName => $Cmds){
				$this->createEqLogic($ObjectName,$TemplateName,$Cmds);
			}
		}
	}
	private function checkLevel($search){
		foreach($this->options as $level =>$options){
			if($options == $search)
				return $level;
		}
	}
	private function createObject($Name){
		$Object = object::byName($Name);
		if(!$this->options['createObjet'])
			return $Object;
		if (!is_object($Object)) {
			log::add('eibd','info','[Import ETS] Nous allons cree l\'objet : '.$Name);
			$Object = new object();
			$Object->setName($Name);
			$Object->setIsVisible(true);
			$Object->save();
		}
		return $Object;
	}
	private function createEqLogic($ObjectName,$TemplateName,$Cmds){
		if(!$this->options['createEqLogic'])
			return;
		$Object=$this->createObject($ObjectName);
		$EqLogic=eibd::AddEquipement($TemplateName,'',$Object->getId());
		$TemplateId=$this->getTemplateName($TemplateName);
		if($TemplateId != false){
			log::add('eibd','info','[Import ETS] Le template ' .$TemplateName.' existe, nous créons un equipement');
			$EqLogic->applyModuleConfiguration($TemplateId);
			foreach($EqLogic->getCmd() as $Cmd){
				$TemplateCmdName=$this->getTemplateCmdName($TemplateId,$Cmd->getName());
				if($TemplateCmdName === false)
					return;
				$Cmd->setLogicalId($Cmds[$TemplateCmdName]['AdresseGroupe']);
				$Cmd->save();
			}
		}else{
			if(!$this->options['createTemplate']){				
				log::add('eibd','info','[Import ETS] Il n\'exite aucun template ' .$TemplateName.', nous créons un equipement basique qu\'il faudra mettre a jours');
				foreach($Cmds as $Name => $Cmd)
					$EqLogic->AddCommande($Name,$Cmd['AdresseGroupe'],"info", $Cmd['DataPointType']);
			}
		}
	}
	private function getTemplateName($TemplateName){
		foreach($this->Templates as $TemplateId => $Template){
			if($Template['name'] == $TemplateName)
				return $TemplateId;
		}
		return false;
	}
	private function getTemplateCmdName($TemplateId,$CmdName){
		foreach($this->Templates[$TemplateId]['cmd'] as $TemplateCmdName){
			if($TemplateCmdName['name'] == $CmdName)
				return $TemplateCmdName['name'];
			foreach(explode('|',$TemplateCmdName['SameCmd']) as $SameCmd){
				if($SameCmd == $CmdName)
					return $TemplateCmdName['name'];
			}
		}
		return false;
	}
	private function formatgaddr($addr){
		switch(config::byKey('level', 'eibd')){
			case '3':
				return sprintf ("%d/%d/%d", ($addr >> 11) & 0x1f, ($addr >> 8) & 0x07,$addr & 0xff);
			break;
			case '2':
				return sprintf ("%d/%d", ($addr >> 11) & 0x1f,$addr & 0x7ff);
			break;
			case '1':
				return sprintf ("%d", $addr);
			break;
		}
	}
}
?>
