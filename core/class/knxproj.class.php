<?php
class knxproj {
	private $path;
	private $options;
	private $Devices=array();
	private $GroupAddresses=array();
	private $Templates=array();
 	public function __construct(){
		$this->path = dirname(__FILE__) . '/../config/';
		$this->Templates=eibd::devicesParameters();
	}
	private function WriteJsonProj(){
		$filename=$this->path.'EtsProj.json';
		if (file_exists($filename)) 
			exec('sudo rm '.$filename);
		$file=fopen($filename,"a+");
		fwrite($file,$this->getAll());
		fclose($file);	
	}
	private function getAll(){
		$myKNX['Devices']=$this->Devices;
		$myKNX['GAD']=$this->GroupAddresses;
		return json_encode($myKNX,JSON_PRETTY_PRINT);
	}
	private function Clean(){
		if (file_exists('/tmp/knxproj.knxproj')) 
			exec('sudo rm  /tmp/knxproj.knxproj');
		if (file_exists($this->path)) 
			exec('sudo rm -R '.$this->path . 'knxproj/');
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
	}
	private function AddCommandeETSParse($Projet,$ComObjectInstanceRef,$type,$DeviceProductRefId){
		$DataPointType=explode('-',$ComObjectInstanceRef->getAttribute('DatapointType'));
		//if ($DataPointType[1] >0){
			$DPT=$DataPointType[1].'.'.sprintf('%1$03d',$DataPointType[2]);
			foreach($ComObjectInstanceRef->getElementsByTagName($type) as $Commande){
				$GroupAddressRefId=$Commande->getAttribute('GroupAddressRefId');
				foreach($Projet->getElementsByTagName('GroupRange') as $GroupRange){
					foreach($GroupRange->getElementsByTagName('GroupAddress') as $GroupAddress){
						$GroupAddressId=$GroupAddress->getAttribute('Id');
						if ($GroupAddressId!=""){
							if ($GroupAddressId == $GroupAddressRefId){
								$addr=$GroupAddress->getAttribute('Address');
								$AdresseGroupe=sprintf( "%d/%d/%d", ($addr >> 11) & 0xf, ($addr >> 8) & 0x7, $addr & 0xff);
								$this->Devices[$DeviceProductRefId]['Cmd'][$AdresseGroupe]['AdresseGroupe']=$AdresseGroupe;
								$this->Devices[$DeviceProductRefId]['Cmd'][$AdresseGroupe]['cmdName']=$GroupAddress->getAttribute('Name');
								$this->Devices[$DeviceProductRefId]['Cmd'][$AdresseGroupe]['groupName']=$GroupRange->getAttribute('Name');
								$this->Devices[$DeviceProductRefId]['Cmd'][$AdresseGroupe]['DataPointType']=$DPT;	
							}
						}
					}
				}
			}
		//}
	}
	public function getCatalogue(){	
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
	public function ParserGroupAddresses($Projet){
		$ProjetFile=$this->SearchFolder("P-");
		$Projet=simplexml_load_file($ProjetFile.'/0.xml');
		$GroupRanges =$Projet->Project->Installations->Installation->GroupAddresses->GroupRanges->GroupRange;
		foreach ($GroupRanges as $GroupRange) {
			$this->GroupAddresses[$this->xml_attribute($GroupRange, 'Name')]='';
			foreach ($GroupRange->children() as $GroupRange2)  {
				$this->GroupAddresses[$this->xml_attribute($GroupRange, 'Name')][$this->xml_attribute($GroupRange2, 'Name')]='';
				foreach ($GroupRange2->children() as $GroupAddress)  {
					$addr=$this->xml_attribute($GroupAddress, 'Address');
					$AdresseGroupe=sprintf( "%d/%d/%d", ($addr >> 11) & 0xf, ($addr >> 8) & 0x7, $addr & 0xff);
					$this->GroupAddresses[$this->xml_attribute($GroupRange, 'Name')][$this->xml_attribute($GroupRange2, 'Name')][$this->xml_attribute($GroupAddress, 'Name')]=$AdresseGroupe;	
				}
			}
		}
	}
	public function ParserEtsFile($_options){
		$this->options=$_options;
		log::add('eibd','debug','[Import ETS]'.json_encode($_options));
		$filename=$this->path.'EtsProj.json';
		if (file_exists($filename)) 
			exec('sudo rm '.$filename);
		$this->unzipKnxProj('/tmp/knxproj.knxproj');
		$ProjetFile=$this->SearchFolder("P-");
		$Projet = new DomDocument();
		if ($Projet->load($ProjetFile.'/0.xml')){ // XML décrivant le projet
			$this->ParserGroupAddresses($Projet);
			foreach($Projet->getElementsByTagName('Area') as $Area){
				$AreaAddress=$Area->getAttribute('Address');
				foreach($Area->getElementsByTagName('Line') as $Line){
					$LineAddress=$Line->getAttribute('Address');
					foreach($Line->getElementsByTagName('DeviceInstance') as $Device){
						$DeviceId=$Device->getAttribute('Id');
						$DeviceProductRefId=$Device->getAttribute('ProductRefId');
						if ($DeviceProductRefId != ''){
							$this->Devices[$DeviceProductRefId]=array();
							$DeviceAddress=$Device->getAttribute('Address');
							$this->Devices[$DeviceProductRefId]['AdressePhysique']=$AreaAddress.'.'.$LineAddress.'.'.$DeviceAddress;
							$this->getCatalogue();
							foreach($Device->getElementsByTagName('ComObjectInstanceRefs') as $ComObjectInstanceRefs){
								foreach($ComObjectInstanceRefs->getElementsByTagName('ComObjectInstanceRef') as $ComObjectInstanceRef){
									$this->AddCommandeETSParse($Projet,$ComObjectInstanceRef,'Receive',$DeviceProductRefId);	
									$this->AddCommandeETSParse($Projet,$ComObjectInstanceRef,'Send',$DeviceProductRefId);
								}
							}
						}
					}
				}
			}
		}
		else
		{
			throw new Exception(__( 'Impossible d\'analyser le document '.$ProjetFile.'/0.xml', __FILE__));
		}
		$this->WriteJsonProj();
		$this->Clean();
		return $this->getAll();
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
}
?>
