<?php
class knxproj {
	private $path;
	private $Devices=array();
	private $GAD=array();
 	public function __construct(){
		$this->path = dirname(__FILE__) . '/../config/';
	}
	private function WriteJsonProj(){
		$filename=$this->path.'EtsProj.json';
		if (file_exists($filename)) 
			exec('sudo rm '.$filename);
		$file=fopen($filename,"a+");
		$myKNX['Devices']=$this->Devices;
		$myKNX['GAD']=$this->GAD;
		fwrite($file,$this->getAll());
		fclose($file);	
	}
	private function getAll(){
		$myKNX['Devices']=$this->Devices;
		$myKNX['GAD']=$this->GAD;
		return json_encode($myKNX,JSON_PRETTY_PRINT);
	}
	private function Clean(){
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
		if ($DataPointType[1] >0){
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
		}
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
	public function ParserEtsFile($File){
		$this->unzipKnxProj($File);
		$ProjetFile=$this->SearchFolder("P-");
		$Projet = new DomDocument();
		if ($Projet->load($ProjetFile.'/0.xml')){ // XML décrivant le projet
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
}
?>
