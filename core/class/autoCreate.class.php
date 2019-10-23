<?php
class autoCreate {
	private $options;
	private $Devices=array();
	private $GroupAddresses=array();
	private $Locations=array();
	private $Templates=array();
 	public function __construct($_options){
		$this->Templates=eibd::devicesParameters();
		$this->options=$_options;
			$filename=dirname(__FILE__) . '/../config/KnxProj.json';
			$myKNX=json_decode(file_get_contents($filename),true);
			$this->Devices=$myKNX['DevicesAll'];
			$this->GroupAddresses=$myKNX['GAD'];
			$this->Locations=$myKNX['Locations'];
	}
  private function getOptionLevel($GroupLevel,$NbLevel=0){
		$Architecture = array();
		$NbLevel++;
		foreach ($GroupLevel as $Name => $Level) {
			$ObjectName = '';
			$TemplateName = '';
			$CmdName = '';
			if($ObjetLevel == $NbLevel)
				$ObjectName=$Name;
			elseif($TemplateLevel == $NbLevel)
				$TemplateName=$Name;
			elseif($CommandeLevel == $NbLevel)
				$CmdName=$Name;
			if(is_array($Level)){
				$Architecture[$GroupName]=$this->getOptionLevel($Level,$NbLevel);
			}else{
				$Architecture[$ObjectName][$TemplateName][$CmdName]=$Level;
			}
		}
		return $Architecture;
	}
		
	private function CheckOptions(){
		$ObjetLevel= $this->checkLevel('object');
		$TemplateLevel= $this->checkLevel('function');
		$CommandeLevel= $this->checkLevel('cmd');
		//$Architecture= $this->getOptionLevel($this->GroupAddresses);
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
		if(!$this->options['createObjet'])
			return null;
		$Object = jeeObject::byName($Name); 
		if (!is_object($Object)) {
			log::add('eibd','info','[Import ETS] Nous allons cree l\'objet : '.$Name);
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
			log::add('eibd','info','[Import ETS] Le template ' .$TemplateName.' existe, nous créons un equipement');
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
				log::add('eibd','info','[Import ETS] Il n\'exite aucun template ' .$TemplateName.', nous créons un equipement basique qu\'il faudra mettre a jours');
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
