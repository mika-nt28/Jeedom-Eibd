<?php
class autoCreate {
	private $options;
	private $Architecture=array();
	private $Arboresance=array();
	private $Templates=array();
	private $ObjetLevel;
	private $TemplateLevel;
	private $CommandeLevel;
 	public function __construct($_options){
		$this->Templates=eibd::devicesParameters();
		$this->options=$_options[0];
		
		$myKNX=json_decode(file_get_contents(dirname(__FILE__) . '/../config/KnxProj.json'),true);
		
		switch($this->options['arboresance']){
			case 'gad':
				$this->Arboresance=$myKNX['GAD'];
			break;
			case 'device':
				$this->Arboresance=$myKNX['Devices'];
			break;
			case 'locations':
				$this->Arboresance=$myKNX['Locations'];
			break;
		}
		foreach($this->options['levelType'] as $key => $type){
			switch($type){
				default:
				break;
				case "object":
					$this->ObjetLevel = $key;
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
        	$NbLevel++;
		foreach ($GroupLevel as $Name => $Level) {
			switch($NbLevel - 1){
		      		case $this->ObjetLevel:
					$Object=$this->createObject($Name,$Groupe['Object']);
					$Groupe['Object']=$Object->getId();
				break;
		      		case $this->TemplateLevel:
					$Groupe['Template']=$Name;
				break;
		      		case $this->CommandeLevel:
			 		$Groupe['Commande'] = $Name;
		      		break;
		    	}
			if(!isset($Level['AdresseGroupe']))
				$this->getOptionLevel($Level,$Groupe,$NbLevel);
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
		$this->getOptionLevel($this->Arboresance,$Groupe);
		foreach($this->Architecture as $Object => $Template){
			foreach($Template as $TemplateName => $Cmds){
				$this->createEqLogic($Object,$TemplateName,$Cmds);
			}
		}   
	}
	private function checkLevel($search){
		foreach($this->options as $level =>$options){
			if($options == $search)
				return $level;
		}
	}
	private function createObject($Name,$Father){
		if(!$this->options['createObjet'])
			return null;
		$Object = jeeObject::byName($Name); 
		if (!is_object($Object)) {
			log::add('eibd','info','[Création automatique] Nous allons cree l\'objet : '.$Name);
			$Object = new jeeObject(); 
			$Object->setName($Name);
			$Object->setFather_id($Father);
			$Object->setIsVisible(true);
			$Object->save();
		}
		return $Object;
	}
	private function createEqLogic($Object,$Name,$Cmds){
		if(!$this->options['createEqLogic'])
			return;
		$TemplateId=$this->getTemplateName($Name);
		if($TemplateId != false){
			log::add('eibd','info','[Création automatique] Le template ' .$Name.' existe, nous créons un equipement');
			$EqLogic=eibd::AddEquipement($Name,'',$Object);
			foreach($Cmds as $Name => $Cmd){
				$Commande = $this->createTemplateCmdByName($EqLogic,$TemplateId,$Name);
				$Commande->setLogicalId($Cmd['AdresseGroupe']);
				$Commande->save();
			}
		}else{
			if(!$this->options['createTemplate']){				
				log::add('eibd','info','[Création automatique] Il n\'exite aucun template ' .$Name.', nous créons un equipement basique qu\'il faudra mettre a jours');
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
			if($Template['name'] == $TemplateName)
				return $TemplateId;
			foreach($Template['Synonyme'] as $SynonymeName){
				if($SynonymeName == $TemplateName)
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
					if($OptionCmd['name'] == $Name){
						$Options[$TemplateOptionId]=true;
						break;
					}
				}
			}
		}
		return $Options;
	}
	private function createTemplateCmdByName($EqLogic,$TemplateId,$CmdName){
		foreach($this->Templates[$TemplateId]['cmd'] as $Commande){
			if($CmdName == $Commande['name']){
				$cmd = null;
				foreach ($EqLogic->getCmd() as $liste_cmd) {
					if (isset($Commande['name']) && $liste_cmd->getName() == $Commande['name']) {
						$cmd = $liste_cmd;	
						break;
					}
				}
				return $EqLogic->createTemplateCmd($cmd,$Commande);
			}
		}
		foreach ($this->Templates[$TemplateId]['options'] as $DeviceOptionsId => $DeviceOptions) {
			if(isset($TemplateOptions[$DeviceOptionsId])){
				$typeTemplate.='_'.$DeviceOptionsId;
				foreach ($DeviceOptions['cmd'] as $Commande) {
					if($CmdName == $Commande['name']){
						$cmd = null;
						foreach ($EqLogic->getCmd() as $liste_cmd) {
							if (isset($Commande['name']) && $liste_cmd->getName() == $Commande['name']) {
								$cmd = $liste_cmd;	
								break;
							}
						}
						$EqLogic->setConfiguration('typeTemplate',$typeTemplate);
						$EqLogic->save();
						return $EqLogic->createTemplateCmd($cmd,$Commande);
					}
				}
			}
		}
		return false;
	}
}
?>
