<?php
class autoCreate {
	private $options;
	private $Architecture=array();
	private $Arborescence=array();
	private $Templates=array();
	private $ObjetLevel=array();
	private $TemplateLevel=null;
	private $CommandeLevel=null;
 	public function __construct($_options){
		$this->Templates=eibd::devicesParameters();
		$this->options=$_options[0];
		
		$myKNX=json_decode(file_get_contents(dirname(__FILE__) . '/../config/KnxProj.json'),true);
		
		switch($this->options['arborescence']){
			case 'gad':
				$this->Arborescence=$myKNX['GAD'];
			break;
			case 'device':
				$this->Arborescence=$myKNX['Devices'];
			break;
			case 'locations':
				$this->Arborescence=$myKNX['Locations'];
			break;
		}
		foreach($this->options['levelType'] as $key => $type){
			switch($type){
				default:
				break;
				case "object":
					$this->ObjetLevel[] = $key;
				break;
				case "function":
					$this->TemplateLevel = $key;
				break;
				case "cmd":
					$this->CommandeLevel = $key;
				break;
			}
		}
	}
  	private function getOptionLevel($GroupLevel,$Groupe,$NbLevel=0){
        	$NextLevel = $NbLevel + 1;
		if($Groupe['Object'] != null)
			$parents =$Groupe['Object'];
		foreach ($GroupLevel as $Name => $Level) {
			if($NbLevel == $this->TemplateLevel && $this->TemplateLevel != null)
				$Groupe['Template']=$Name;
			if($NbLevel == $this->CommandeLevel && $this->CommandeLevel != null)
				$Groupe['Commande'] = $Name;
			else{
				foreach($this->ObjetLevel as $ObjetLevel){
					if($NbLevel == $ObjetLevel){
						$Groupe['Object']=$this->createObject($Name,$parents);
					}
				}
		    	}
			if(!isset($Level['AdresseGroupe']))
				$this->getOptionLevel($Level,$Groupe,$NextLevel);
			else
                		$this->Architecture[$Groupe['Object']][$Groupe['Template']][$Groupe['Commande']]=$Level;
		}
		return;
	}
	public function CheckOptions(){
		$Groupe['Object'] = null;
		$Groupe['Template'] = null;
		$Groupe['Commande'] = null;
		$Groupe['Commande'] = null;
		$this->getOptionLevel($this->Arborescence,$Groupe);
		foreach($this->Architecture as $Object => $Template){
			foreach($Template as $TemplateName => $Cmds){
				$this->createEqLogic($Object,$TemplateName,$Cmds);
			}
		}   
	}
	private function createObject($Name,$Father){
		$Object = jeeObject::byName($Name); 
		if (!is_object($Object)) {
			if(!$this->options['createObjet'])
				return null;
			log::add('eibd','info','[Création automatique] Nous allons créer l\'objet : '.$Name);
			$Object = new jeeObject(); 
			$Object->setName($Name);
			$Object->setFather_id($Father);
			$Object->setIsVisible(1);
			$Object->setDisplay('icon', "");
			$Object->setDisplay('tagColor', "#696969");
			$Object->setDisplay('tagTextColor', "#ebebeb");
			$Object->save();
		}
		return $Object->getId();
	}
	private function createEqLogic($Object,$Name,$Cmds){
		if(!$this->options['createEqLogic'])
			return;
		$TemplateId=$this->getTemplateName($Name);
		if($TemplateId != false){
			$TemplateOptions=$this->getTemplateOptions($TemplateId,$Cmds);
			log::add('eibd','info','[Création automatique] L\'équipement ' .$Name.' est reconnu sur le template '.$this->Templates[$TemplateId]['name']);
			$EqLogic=eibd::AddEquipement($Name,'',$Object);
			$EqLogic->applyModuleConfiguration($TemplateId,$TemplateOptions);
			foreach($EqLogic->getCmd() as $Commande){
				foreach($Cmds as $Name => $Cmd){
					$TemplateName = $this->getTemplateCmdByName($TemplateId,$Name);
					if($Commande->getName() != $TemplateName)
						continue;
					$Commande->setLogicalId($Cmd['AdresseGroupe']);
					$Commande->save();
					break;
				}
			}
		}else{
			if(!$this->options['createTemplate']){				
				log::add('eibd','info','[Création automatique] Il n\'existe aucun template ' .$Name.', nous créons un équipement basique qu\'il faudra mettre à jour');
				$EqLogic=eibd::AddEquipement($Name,'',$Object);
				foreach($Cmds as $Name => $Cmd){
					if($Cmd['DataPointType'] == ".000" ||$Cmd['DataPointType'] == ".000")
						$Cmd['DataPointType']= "1.xxx";
					$EqLogic->AddCommande($Name,$Cmd['AdresseGroupe'],"info", $Cmd['DataPointType']);
				}
			}
		}
	}
	private function getTemplateName($TemplateName){
		foreach($this->Templates as $TemplateId => $Template){
			if(strpos($TemplateName,$Template['name']) !== false || strpos($Template['name'],$TemplateName) !== false)
				return $TemplateId;
			foreach($Template['Synonyme'] as $SynonymeName){
				if(strpos($TemplateName,$SynonymeName) !== false || strpos($SynonymeName,$TemplateName) !== false)
					return $TemplateId;
			}
		}
		return false;
	}
	private function getTemplateOptions($TemplateId,$Cmds){
		$Options=array();
		foreach($Cmds as $Name => $Cmd){
			foreach($this->Templates[$TemplateId]['options'] as $TemplateOptionId =>$TemplateOption){	      
				foreach($TemplateOption['cmd'] as $OptionCmd){
					if(strpos($Name,$OptionCmd['name']) !== false || strpos($OptionCmd['name'],$Name) !== false){
						$Options[$TemplateOptionId]=true;
						break;
					}
					foreach($OptionCmd['Synonyme'] as $Synonyme){
						if(strpos($Name,$Synonyme) !== false || strpos($Synonyme,$Name) !== false){
							$Options[$TemplateOptionId]=true;
							break;
						}
					}
				}
			}
		}
		return $Options;
	}
	private function getTemplateCmdByName($TemplateId,$CmdName){
		foreach($this->Templates[$TemplateId]['cmd'] as $Commande){
			if(strpos($CmdName,$Commande['name']) !== false || strpos($Commande['name'],$CmdName) !== false){
				log::add('eibd','info','[Création automatique] La commande ('.$CmdName.') a été trouvée  ' .$Commande['name']);
				return $Commande['name'];
			}
			foreach($Commande['Synonyme'] as $Synonyme){
				if(strpos($CmdName,$Synonyme) !== false || strpos($Synonyme,$CmdName) !== false){
					log::add('eibd','info','[Création automatique] La commande ('.$CmdName.') a été trouvée en synonyme de ' .$Commande['name']);
					return $Commande['name'];
				}
			}
		}
		foreach ($this->Templates[$TemplateId]['options'] as $DeviceOptionsId => $DeviceOptions) {
			if(isset($TemplateOptions[$DeviceOptionsId])){
				foreach ($DeviceOptions['cmd'] as $Commande) {
					if(strpos($CmdName,$Commande['name']) !== false || strpos($Commande['name'],$CmdName) !== false){
						log::add('eibd','info','[Création automatique] La commande ('.$CmdName.') a été trouvée  ' .$Commande['name']);
						return $Commande['name'];
					}
					foreach($Commande['Synonyme'] as $Synonyme){
						if(strpos($CmdName,$Synonyme) !== false || strpos($Synonyme,$CmdName) !== false){
							log::add('eibd','info','[Création automatique] La commande ('.$CmdName.') a été trouvée en synonyme de ' .$Commande['name']);
							return $Commande['name'];
						}
					}
				}
			}
		}
		return false;
	}
}
?>
