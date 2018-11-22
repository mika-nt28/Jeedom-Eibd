<?php
class knxproj {
	private $proj=array();
	private function WriteJsonProj(){
		$filename=dirname(__FILE__) . '/../config/EtsProj.json';
		if (file_exists($filename)) 
			exec('sudo rm '.$filename);
		$file=fopen($filename,"a+");
		fwrite($file,json_encode($this->proj));
		fclose($file);	
	}
	private function Clean($dir){
		if (file_exists($filename)) 
			exec('sudo rm -R '.$dir);
	}
	private function unzipKnxProj($dir,$File){
		if (!is_dir($dir)) 
			mkdir($dir);
		exec('sudo chmod -R 777 '.$dir);
		$zip = new ZipArchive(); 
		// On ouvre l’archive.
		if($zip->open($File) == TRUE){
			$zip->extractTo($dir);
			$zip->close();
		}
	}
	private function SearchFolder($dir,$Folder){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if (substr($file,0,2) == $Folder){
					if (opendir($dir.$file)) 
						return $dir . $file;
				}
			}
			closedir($dh);
		}	
	}
	private function AddCommandeETSParse($Projet,$ComObjectInstanceRef,$type,$DPT){
		foreach($ComObjectInstanceRef->getElementsByTagName($type) as $Commande){
			$GroupAddressRefId=$Commande->getAttribute('GroupAddressRefId');
			foreach($Projet->getElementsByTagName('GroupRange') as $GroupRange){
				foreach($GroupRange->getElementsByTagName('GroupAddress') as $GroupAddress){
					$GroupAddressId=$GroupAddress->getAttribute('Id');
					if ($GroupAddressId!=""){
						if ($GroupAddressId == $GroupAddressRefId){
							$addr=$GroupAddress->getAttribute('Address');
							$AdresseGroupe=sprintf( "%d/%d/%d", ($addr >> 11) & 0xf, ($addr >> 8) & 0x7, $addr & 0xff);
							$NewGad[$AdresseGroupe]['cmdName']=$GroupAddress->getAttribute('Name');
							$NewGad[$AdresseGroupe]['groupName']=$GroupRange->getAttribute('Name');
							$NewGad[$AdresseGroupe]['DataPointType']=$DPT;	
							if($type == 'send')
								$NewGad[$AdresseGroupe]['cmdType']='action';
							else
								$NewGad[$AdresseGroupe]['cmdType']='info';
						}
					}
				}
			}
		}
		return $NewGad;
	}
	public function ParserEtsFile($File){
		$dir=dirname(__FILE__) . '/../config/knxproj/';
		$this->unzipKnxProj($dir,$File);
		$ProjetFile=$this->SearchFolder($dir,"P-");
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
							$DeviceAddress=$Device->getAttribute('Address');
							$AdressePhysique=$AreaAddress.'.'.$LineAddress.'.'.$DeviceAddress;
							$DossierCataloge=$dir . substr($DeviceProductRefId,0,6).'/Catalog.xml';
							$Cataloge = new DomDocument();
							if ($Cataloge->load($DossierCataloge)) {//XMl décrivant les équipements
								foreach($Cataloge->getElementsByTagName('CatalogItem') as $CatalogItem){
									if ($DeviceProductRefId==$CatalogItem->getAttribute('ProductRefId'))
										$this->proj[$AdressePhysique]['DeviceName']=$CatalogItem->getAttribute('Name'). " - ".$AdressePhysique;
								}
							}
							else{
								$this->proj[$AdressePhysique]['DeviceName']= "No name - ".$AdressePhysique;
							}
							foreach($Device->getElementsByTagName('ComObjectInstanceRefs') as $ComObjectInstanceRefs){
								foreach($ComObjectInstanceRefs->getElementsByTagName('ComObjectInstanceRef') as $ComObjectInstanceRef){
									$DataPointType=explode('-',$ComObjectInstanceRef->getAttribute('DatapointType'));
									if ($DataPointType[1] >0){
										$DPT=$DataPointType[1].'.'.sprintf('%1$03d',$DataPointType[2]);
										$this->proj[$AdressePhysique]['Cmd'][] = $this->AddCommandeETSParse($Projet,$ComObjectInstanceRef,'Receive',$DPT);
										$this->proj[$AdressePhysique]['Cmd'][] = $this->AddCommandeETSParse($Projet,$ComObjectInstanceRef,'Send',$DPT);
									}
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
		$this->Clean($dir);
		return json_encode($this->proj);
	}
}
?>
