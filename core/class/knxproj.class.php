<?php
class knxproj {
	private $path;
	private $options;
	private $Devices=array();
	private $GroupAddresses=array();
	private $Templates=array();
	private $myProject=array();
 	public function __construct($_options){
		$this->path = dirname(__FILE__) . '/../config/';
		$this->Templates=eibd::devicesParameters();
		$this->options=$_options;
		
		log::add('eibd','debug','[Import ETS]'.json_encode($_options));
		$filename=$this->path.'EtsProj.json';
		if (file_exists($filename)) 
			exec('sudo rm '.$filename);
		$this->unzipKnxProj('/tmp/knxproj.knxproj');
		$ProjetFile=$this->SearchFolder("P-");
		$this->myProject=simplexml_load_file($ProjetFile.'/0.xml');
		
		$this->ParserDevice();
		$this->ParserGroupAddresses();
	}
 	public function __destroy(){
		if (file_exists('/tmp/knxproj.knxproj')) 
			exec('sudo rm  /tmp/knxproj.knxproj');
		if (file_exists($this->path)) 
			exec('sudo rm -R '.$this->path . 'knxproj/');
	}
	public function WriteJsonProj(){
		$filename=$this->path.'EtsProj.json';
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
	private function unzipKnxProj($File){
		if (!is_dir($this->path . 'knxproj/')) 
			mkdir($this->path . 'knxproj/');
		exec('sudo chmod -R 777 '.$this->path . 'knxproj/');
		$zip = new ZipArchive(); 
		// On ouvre l’archive.
		if($zip->open($File) == TRUE){
			$zip->extractTo($this->path . 'knxproj/');
			$zip->close();
		}
		log::add('eibd','debug','[Import ETS] Extraction des fichiers de projets');
	}
	private function SearchFolder($Folder){
		if ($dh = opendir($this->path . 'knxproj/')){
			while (($file = readdir($dh)) !== false){
				if (substr($file,0,2) == $Folder){
					if (opendir($this->path . 'knxproj/'.$file)) 
						return $this->path . 'knxproj/' . $file;
				}
			}
			closedir($dh);
		}	
		return false;
	}
	private function getCatalogue(){	
		log::add('eibd','debug','[Import ETS] Rechecher des nom de module dans le catalogue');
		foreach($this->Devices as $Device => $Parameter){
			$Catalogue = new DomDocument();
			if ($Catalogue->load($this->path . 'knxproj/'.substr($Device,0,6).'/Catalog.xml')) {//XMl décrivant les équipements
				foreach($Catalogue->getElementsByTagName('CatalogItem') as $CatalogItem){
					if ($Device==$CatalogItem->getAttribute('ProductRefId'))
						$this->Devices[$Device]['DeviceName']=$CatalogItem->getAttribute('Name');
				}
			}
			else{
				$this->Devices[$Device]['DeviceName']= "{{Inconnue}}";
			}
		}
	}
	private function xml_attribute($object, $attribute){
		if(isset($object[$attribute]))
			return (string) $object[$attribute];
	}
	private function ParserGroupAddresses(){
		log::add('eibd','debug','[Import ETS] Création de l\'arboressance de gad');
		$GroupRanges = $this->myProject->Project->Installations->Installation->GroupAddresses->GroupRanges->GroupRange;
		foreach ($GroupRanges as $GroupRange) {
			$this->GroupAddresses[$this->xml_attribute($GroupRange, 'Name')]='';
			foreach ($GroupRange->children() as $GroupRange2)  {
				$this->GroupAddresses[$this->xml_attribute($GroupRange, 'Name')][$this->xml_attribute($GroupRange2, 'Name')]='';
				foreach ($GroupRange2->children() as $GroupAddress)  {
					$GroupId=$this->xml_attribute($GroupAddress, 'Id');
					$addr=$this->xml_attribute($GroupAddress, 'Address');
					$AdresseGroupe=$this->formatgaddr($addr);
					$GroupName = $this->xml_attribute($GroupAddress, 'Name');
					$this->GroupAddresses[$this->xml_attribute($GroupRange, 'Name')][$this->xml_attribute($GroupRange2, 'Name')][$GroupName]=$AdresseGroupe;
					$this->updateDeviceGad($GroupId,$GroupName,$AdresseGroupe);
				}
			}
		}
	}
	private function updateDeviceGad($id,$name,$addr){
		$this->Devices[$DeviceProductRefId]['Cmd'][$this->xml_attribute($Commande, 'GroupAddressRefId')]['DataPointType']=$DataPointType[1].'.'.sprintf('%1$03d',$DataPointType[2]);							
		foreach($this->Devices as $DeviceProductRefId => $Device){
			foreach($Device['Cmd'] as $GroupAddressRefId=> $Cmd){
				if($GroupAddressRefId == $id){
					$this->Devices[$DeviceProductRefId]['Cmd'][$GroupAddressRefId]['cmdName']=$name;
					$this->Devices[$DeviceProductRefId]['Cmd'][$GroupAddressRefId]['AdresseGroupe']=$addr;
				}
			}
		}
	}
	private function ParserDevice(){
		log::add('eibd','debug','[Import ETS] Recherche de device');
		$Topology = $this->myProject->Project->Installations->Installation->Topology->Area;
		foreach($Topology as $Area){
			$AreaAddress=$this->xml_attribute($Area, 'Address');
			foreach ($Area->children() as $Line)  {
				$LineAddress=$this->xml_attribute($Line, 'Address');
				foreach ($Line->children() as $Device)  {
					$DeviceId=$this->xml_attribute($Device, 'Id');
					$DeviceProductRefId=$this->xml_attribute($Device, 'ProductRefId');
					if ($DeviceProductRefId != ''){
						$this->Devices[$DeviceProductRefId]=array();
						$DeviceAddress=$this->xml_attribute($Device, 'Address');
						$this->Devices[$DeviceProductRefId]['AdressePhysique']=$AreaAddress.'.'.$LineAddress.'.'.$DeviceAddress;
						$this->getCatalogue();
						foreach($Device->children() as $ComObjectInstanceRefs){
							if($ComObjectInstanceRefs->getName() == 'ComObjectInstanceRefs'){
								foreach($ComObjectInstanceRefs->children() as $ComObjectInstanceRef){
									$DataPointType=explode('-',$this->xml_attribute($ComObjectInstanceRef, 'DatapointType'));
									if ($DataPointType[1] >0)
									foreach($ComObjectInstanceRef->children() as $Connector){
										foreach($Connector->children() as $Commande)
											$this->Devices[$DeviceProductRefId]['Cmd'][$this->xml_attribute($Commande, 'GroupAddressRefId')]['DataPointType']=$DataPointType[1].'.'.sprintf('%1$03d',$DataPointType[2]);
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
		//[{"0":"object","1":"function","2":"cmd","createEqLogic":"1","createObjet":"1"}]
		foreach($this->GroupAddresses as $Name1 => $Level1){
			if($ObjetLevel == 0)
				$Object=$Name1;
			elseif($TemplateLevel == 0)
				$Template=$Name1;
			elseif($CommandeLevel == 0)
				$CmdName=$Name1;
				$Cmds[]=array('name'=>$Name1);
			foreach($Level1 as $Name2 => $Level2){
				if($ObjetLevel == 1)
					$Object=$Name2;
				elseif($TemplateLevel == 1)
					$Template=$Name2;
				elseif($CommandeLevel == 1)
					$CmdName=$Name2;
				foreach($Level2 as $Name3 => $Gad){
					if($ObjetLevel == 2)
						$Object=$Name3;
					elseif($TemplateLevel == 2)
						$Template=$Name3;
					elseif($CommandeLevel == 2)
						$CmdName=$Name3;
					$Cmds[]=array('name'=>$CmdName,'addr'=>$Gad);
				}
			}
			$this->createObject($Object);
			$this->createEqLogic($Object,$Template,$Cmds);
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
		if($this->options['createObjet']){
				//Script pour cree un objet
			if (!is_object($Object)) {
				$Object = new object();
				$Object->setName($Name);
				$Object->setIsVisible(true);
				$Object->save();
			}
			return $Object;
		}
	}
	private function createEqLogic($ObjectName,$TemplateName,$Cmds){
		if($this->options['createEqLogic']){
			if(isset($this->Templates[$TemplateName])){
				$Template=$this->Templates[$TemplateName];
				foreach($Cmds as $Cmd){
 					if(isset($Template['cmd'][$Cmd['name']]))
						$Template['cmd'][$Cmd['name']]['logicalId']=$Cmd['addr'];
					else
					   return false;
				}
				$Object=$this->createObject($ObjectName);
				$EqLogic=eibd::AddEquipement($Template,'',$Object->getId());
				$EqLogic->applyModuleConfiguration($Template);
			}
		}
		
	}
	private function formatgaddr($addr)	{
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
