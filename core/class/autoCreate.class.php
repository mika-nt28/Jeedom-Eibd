<?php
class autoCreate {
	private $options;
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
	}
  	private function getOptionLevel($GroupLevel,$NbLevel=0){
		$Architecture = array();
		foreach ($GroupLevel as $Name => $Level) {
			$ObjectName = '';
			$TemplateName = '';
			$CmdName = '';
			if($this->ObjetLevel == $NbLevel)
				$ObjectName=$Name;
			elseif($this->TemplateLevel == $NbLevel)
				$TemplateName=$Name;
			elseif($this->CommandeLevel == $NbLevel)
				$CmdName=$Name;
			if(is_array($Level)){
				$Architecture[$GroupName]=$this->getOptionLevel($Level,$NbLevel++);
			}else{
				$Architecture[$ObjectName][$TemplateName][$CmdName]=$Level;
			}
		}
		return $Architecture;
	}
		
	public function CheckOptions(){
		$this->ObjetLevel= $this->checkLevel('object');
		$this->TemplateLevel= $this->checkLevel('function');
		$this->CommandeLevel= $this->checkLevel('cmd');
		$Architecture= $this->getOptionLevel($this->Arboresance);
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
		if(!$this->options['createObjet'])
			return null;
		$Object = jeeObject::byName($Name); 
		if (!is_object($Object)) {
			log::add('eibd','info','[Création automatique] Nous allons cree l\'objet : '.$Name);
			$Object = new jeeObject(); 
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
		if (is_object($Object))
			$ObjectId = $Object->getId();
		else
			$ObjectId = null;
		$TemplateId=$this->getTemplateName($TemplateName);
		if($TemplateId != false){
			$TemplateOptions=$this->getTemplateOptions($TemplateId,$Cmds);
			log::add('eibd','info','[Création automatique] Le template ' .$TemplateName.' existe, nous créons un equipement');
			$EqLogic=eibd::AddEquipement($TemplateName,'',$ObjectId);
			$EqLogic->applyModuleConfiguration($TemplateId,$TemplateOptions);
			foreach($EqLogic->getCmd() as $Cmd){
				$TemplateCmdName=$this->getTemplateCmdName($TemplateId,$Cmd->getName());
				if($TemplateCmdName === false)
					return;
				$Cmd->setLogicalId($Cmds[$TemplateCmdName]['AdresseGroupe']);
				$Cmd->save();
			}
		}else{
			if(!$this->options['createTemplate']){				
				log::add('eibd','info','[Création automatique] Il n\'exite aucun template ' .$TemplateName.', nous créons un equipement basique qu\'il faudra mettre a jours');
				$EqLogic=eibd::AddEquipement($TemplateName,'',$ObjectId);
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
	private function getTemplateCmdName($TemplateId,$CmdName){
		foreach($this->Templates[$TemplateId]['cmd'] as $TemplateCmdName){
			if($TemplateCmdName['name'] == $CmdName)
				return $TemplateCmdName['name'];
			foreach(explode('|',$TemplateCmdName['SameCmd']) as $SameCmd){
				if($SameCmd == $CmdName)
					return $TemplateCmdName['name'];
			}
			foreach($TemplateCmdName['Synonyme'] as $SynonymeName){
				if($SynonymeName == $CmdName)
					return $SynonymeName;
			}
		}
		return false;
	}
}
?>
