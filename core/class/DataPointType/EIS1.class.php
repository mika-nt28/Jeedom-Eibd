<?php
class EIS1{
	public function DptSelectEncode ($dpt, $value, $inverse=false, $option=null){
		$All_DPT=self::All_DPT();
		if(explode('.',$dpt)[0] == "1"){
			if ($value != 0 && $value != 1){
				$ValeurDpt=$All_DPT["Boolean"][$dpt]['Valeurs'];
				$value = array_search($value, $ValeurDpt); 
			}
			if ($inverse){
				if ($value == 0 )
					$value = 1;
				else
					$value = 0;
			}
			return $value;
		}
	}
	public function DptSelectDecode ($dpt, $data, $inverse=false, $option=null){
		$All_DPT=self::All_DPT();		
		if(explode('.',$dpt)[0] == "1"){
			$value = $data;		
			if ($inverse){
				if ($value == 0 )
					return 1;
				else
					return 0;
			}
     		}
	}
	public function OtherValue ($dpt, $oldValue){
		$All_DPT=self::All_DPT();
		if(explode('.',$dpt)[0] == "1"){
				if ($oldValue == 1)
					return 0;
				else
					return 1;
		}
	}
	public function All_DPT(){
		return array (
		"Boolean"=> array(
			"1.xxx"=> array(
				"Name"=>"Generic",
				"Valeurs"=>array(0, 1),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.001"=> array(
				"Name"=>"Switch",
				"Valeurs"=>array("Off", "On"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.002"=> array(
				"Name"=>"Boolean",
				"Valeurs"=>array("False", "True"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.003"=> array(
				"Name"=>"Enable",
				"Valeurs"=>array("Disable", "Enable"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.004"=> array(
				"Name"=>"Ramp",
				"Valeurs"=>array("No ramp", "Ramp"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.005"=> array(
				"Name"=>"Alarm",
				"Valeurs"=>array("No alarm", "Alarm"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.006"=> array(
				"Name"=>"Binary value",
				"Valeurs"=>array("Low", "High"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.007"=> array(
				"Name"=>"Step",
				"Valeurs"=>array("Decrease", "Increase"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.008"=> array(
				"Name"=>"Up/Down",
				"Valeurs"=>array("Up", "Down"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.009"=> array(
				"Name"=>"Open/Close",
				"Valeurs"=>array("Open", "Close"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.010"=> array(
				"Name"=>"Start",
				"Valeurs"=>array("Stop", "Start"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.011"=> array(
				"Name"=>"State",
				"Valeurs"=>array("Inactive", "Active"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.012"=> array(
				"Name"=>"Invert",
				"Valeurs"=>array("Not inverted", "Inverted"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.013"=> array(
				"Name"=>"Dimmer send-style",
				"Valeurs"=>array("Start/stop", "Cyclically"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.014"=> array(
				"Name"=>"Input source",
				"Valeurs"=>array("Fixed", "Calculated"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.015"=> array(
				"Name"=>"Reset",
				"Valeurs"=>array("No action", "Reset"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.016"=> array(
				"Name"=>"Acknowledge",
				"Valeurs"=>array("No action", "Acknowledge"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.017"=> array(
				"Name"=>"Trigger",
				"Valeurs"=>array("Trigger", "Trigger"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.018"=> array(
				"Name"=>"Occupancy",
				"Valeurs"=>array("Not occupied", "Occupied"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.019"=> array(
				"Name"=>"Window/Door",
				"Valeurs"=>array("Closed", "Open"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.021"=> array(
				"Name"=>"Logical function",
				"Valeurs"=>array("OR", "AND"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.022"=> array(
				"Name"=>"Scene A/B",
				"Valeurs"=>array("Scene A", "Scene B"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>""),
			"1.023"=> array(
				"Name"=>"Shutter/Blinds mode",
				"Valeurs"=>array("Only move Up/Down", "Move Up/Down + StepStop"),
				"min"=>'',
				"max"=>'',
				"InfoType"=>'binary',
				"ActionType"=>'other',
				"GenericType"=>"DONT",
				"Option" =>array(),
				"Unite" =>"")));
	}
}
?>
