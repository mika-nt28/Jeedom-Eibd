<?php
class knxproj {
	private function unzipKnxProj($dir,$File){
		if (!is_dir($dir)) 
			mkdir($dir);
		$zip = new ZipArchive(); 
		// On ouvre l’archive.
		if($zip->open($File) == TRUE)
		{
			$zip->extractTo($dir);
			$zip->close();
		}
	}
	private function SearchFolder($dir,$Folder){
		if ($dh = opendir($dir)) 
		{
			while (($file = readdir($dh)) !== false)
			{
				if (substr($file,0,2) == $Folder)
				{
					if (opendir($dir.$file)) 
						return $dir . $file;
					}
			}
			closedir($dh);
		}	
	}
	private function AddCommandeETSParse($Projet,$ComObjectInstanceRef,$NewGad,$type){
		foreach($ComObjectInstanceRef->getElementsByTagName($type) as $Commande){
			$GroupAddressRefId=$Commande->getAttribute('GroupAddressRefId');
			foreach($Projet->getElementsByTagName('GroupRange') as $GroupRange){
				$NewGad['groupName']=$GroupRange->getAttribute('Name');
				foreach($GroupRange->getElementsByTagName('GroupAddress') as $GroupAddress){
					$NewGad['cmdName']=$GroupAddress->getAttribute('Name');
					$GroupAddressId=$GroupAddress->getAttribute('Id');
					if ($GroupAddressId!=""){
						if ($GroupAddressId == $GroupAddressRefId){
							$addr=$GroupAddress->getAttribute('Address');
							$NewGad['AdresseGroupe']=sprintf( "%d/%d/%d", ($addr >> 11) & 0xf, ($addr >> 8) & 0x7, $addr & 0xff);
							if($type == 'send')
								$NewGad['cmdType']='action';
							else
								$NewGad['cmdType']='info';
							if(count(cmd::byLogicalId($NewGad['AdresseGroupe']))<=0)
								self::addCacheNoGad($NewGad);
						}
					}
				}
			}
		}
	}
	public static function ParserEtsFile($File){
		$dir='/tmp/knxproj/';
		self::unzipKnxProj($dir,$File);
		$ProjetFile=self::SearchFolder($dir,"P-").'/0.xml';
		$Projet = new DomDocument();
		if ($Projet->load($ProjetFile)){ // XML décrivant le projet
			foreach($Projet->getElementsByTagName('Area') as $Area){
				$AreaAddress=$Area->getAttribute('Address');
				foreach($Area->getElementsByTagName('Line') as $Line){
					$LineAddress=$Line->getAttribute('Address');
					foreach($Line->getElementsByTagName('DeviceInstance') as $Device){
						$DeviceId=$Device->getAttribute('Id');
						$DeviceProductRefId=$Device->getAttribute('ProductRefId');
						if ($DeviceProductRefId != ''){
							$DeviceAddress=$Device->getAttribute('Address');
							$Equipement['AdressePhysique']=$AreaAddress.'.'.$LineAddress.'.'.$DeviceAddress;
							$DossierCataloge=$dir . substr($DeviceProductRefId,0,6).'/Catalog.xml';
							$Cataloge = new DomDocument();
							if ($Cataloge->load($DossierCataloge)) {//XMl décrivant les équipements
								foreach($Cataloge->getElementsByTagName('CatalogItem') as $CatalogItem){
									if ($DeviceProductRefId==$CatalogItem->getAttribute('ProductRefId'))
										$Equipement['DeviceName']=$CatalogItem->getAttribute('Name'). " - ".$PhysicalAdress;
								}
							}
							else{
								$Equipement['DeviceName']= "No name - ".$PhysicalAdress;
							}
							foreach($Device->getElementsByTagName('ComObjectInstanceRefs') as $ComObjectInstanceRefs){
								foreach($ComObjectInstanceRefs->getElementsByTagName('ComObjectInstanceRef') as $ComObjectInstanceRef){
									$DataPointType=explode('-',$ComObjectInstanceRef->getAttribute('DatapointType'));
									if ($DataPointType[1] >0)
										$Equipement['DataPointType']=$DataPointType[1].'.'.sprintf('%1$03d',$DataPointType[2]);
									self::AddCommandeETSParse($Projet,$ComObjectInstanceRef,$Equipement,'Receive');
									self::AddCommandeETSParse($Projet,$ComObjectInstanceRef,$Equipement,'Send');
								}
							}
						}
					}
				}
			}
		}
		else
		{
			throw new Exception(__( 'Impossible d\'analyser le document '.$ProjetFile, __FILE__));
		}
	}
}
?>
