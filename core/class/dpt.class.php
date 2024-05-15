<?php
require_once dirname(__FILE__) . '/DataPointType/EIS14_ABB_ControlAcces.class.php';
class Dpt{
	public static function DptSelectEncode ($dpt, $value, $inverse=false, $option=null){
		$All_DPT=self::All_DPT();
		switch (explode('.',$dpt)[0]){
			case "1":
				if ($value != 0 && $value != 1)
					{
					$ValeurDpt=$All_DPT["Boolean"][$dpt]['Valeurs'];
					$value = array_search($value, $ValeurDpt); 
					}
				if ($inverse){
					if ($value == 0 )
						$value = 1;
					else
						$value = 0;
				}
				$data= $value;
				break;
			case "2":
				$data= $value;
				break;
			case "3":
				if ($value > 0)
					$stepCode = abs($value) & 0x07;
				$data = $option["ctrl"] << 3 | $stepCode;
				break;
			case "5":
				switch ($dpt){
					case "5.001":
						if ($inverse)
							$value=100-$value;
						$value = round(intval($value) * 255 / 100);
						break;
					case "5.003":
						if ($inverse)
							$value=360-$value;
						$value = round(intval($value) * 255 / 360);
						break;
					case "5.004":
						if ($inverse)
							$value=255-$value;
						$value = round(intval($value) * 255);
						break;
				}
				$data= array($value);
				break;
			case "6":
				if ($value < 0)
					$value = (abs($value) ^ 0xff) + 1 ; # twos complement
				$data= array($value);
				break;
			case "7":
				$data= array(($value >> 8)&0xff, ($value& 0xff));
				break;
			case "8":
				if($value >= 0x8000)
					$value = -(($value - 1) ^ 0xffff);  # invert twos complement
				$data= array(($value >> 8)&0xff, ($value& 0xff));
				break;
			case "9": 
				if($value<0){
					$sign = 1;
					$value = - $value;
				}
				else
					$sign = 0;
				$value = $value * 100.0;
				$exp = 0;
				while ($value > 2047){
					$exp ++;
					$value = $value / 2;
				}
				if ($sign)
					$value = - $value;
				$value = $value & 0x7ff;
				$data= array(($sign << 7) | (($exp & 0x0f)<<3)| (($value >> 8)&0x07), ($value& 0xff));
				break;
			case "10": 
				$date   = new DateTime($value); 
				$wDay = $date->format('N');
				$hour = $date->format('H');
				$min = $date->format('i');
				$sec = $date->format('s');
				$data = array(($wDay << 5 )| $hour  , $min , $sec);
				break;
			case "11":
				$date = new DateTime(); 
				if($value != ''){
					$value = strtotime(str_replace('/', '-', $value)); 
					$date->setTimestamp($value);
				}
				$day = $date->format('d');
				$month = $date->format('m');
				$year = $date->format('y');
				$data = array($day,$month ,$year);
				break;
			case "12":
				$data= unpack("C*", pack("L", $value));
				break;
			case "13":
				if ($value < 0)
					$value = (abs($value) ^ 0xffffffff) + 1 ; # twos complement
				$data= array(($value>>24) & 0xFF, ($value>>16) & 0xFF,($value>>8) & 0xFF,$value & 0xFF);
				break;
			case "14":
				$value = unpack("L",pack("f", $value)); 
				$data = array(($value[1]>>24)& 0xff, ($value[1]>>16)& 0xff, ($value[1]>>8)& 0xff,$value[1]& 0xff);
				break;
			case "16":
				$data=array();
				$chr=str_split($value);
				for ($i = 0; $i < 14; $i++)
					$data[$i]=ord($chr[$i]);
				break;
			case "17":
				$data= array(($value -1) & 0x3f);
				break;
			case "18":
				$control = jeedom::evaluateExpression($option["ctrl"]);
				$data= array(($control << 8) & 0x80 | $value & 0x3f);
				break;
			case "19": 
				$date = new DateTime(); 
				if($value != ''){
					$value = strtotime(str_replace('/', '-', $value)); 
					$date->setTimestamp($value);
				}
				$wDay = $date->format('N');
				$hour = $date->format('H');
				$min = $date->format('i');
				$sec = $date->format('s');
				$day = $date->format('d');
				$month = $date->format('m');
				$year = $date->format('Y')-1900;
				$data = array($year,$month & 0x0f ,$day & 0x1f,($wDay << 5 ) & 0xe0| $hour  & 0x1f , $min  & 0x3f , $sec & 0x3f,0x00,0x00);
			break;
			case "20":
				if ($dpt != "20.xxx"){
					if(!is_numeric($value)){
						$ValeurDpt=$All_DPT["8BitEncAbsValue"][$dpt]["Valeurs"];
						$value = array_search($value, $ValeurDpt); 
					}
				}
				$data= array($value);
			break;
			case "23":
				if ($dpt != "23.xxx"){
					if(!is_numeric($value)){
						$ValeurDpt=$All_DPT["2bit"][$dpt]["Valeurs"];
						$value = array_search($value, $ValeurDpt); 
					}
				}
				$data= array($value);
			break;
			case "27":
				foreach(explode('|',$option["Info"]) as $bit => $Info){
					$value=cmd::byId(str_replace('#','',$Info))->execCmd();
					$data= array();
					if($bit < 8)
						$data[0].=$value>>$bit;
					elseif($bit < 16)
						$data[1].=$value>>$bit;
					if($value){
						if($bit < 8)
							$data[2].=0x01>>$bit;
						elseif($bit < 16)
							$data[3].=0x01>>$bit;
					}
						
				}
			break;
			case "225":
				if ($dpt != "225.002"){
					$TimePeriode=cmd::byId(str_replace('#','',$option["TimePeriode"]));
					$data= array(($TimePeriode->execCmd() >> 8) & 0xFF, $TimePeriode->execCmd() & 0xFF, $value);
				}
			break;
			case "229":
				if ($dpt != "229.001"){
					if ($value < 0)
					   $value = (abs($value) ^ 0xffffffff) + 1 ; 
					$ValInfField=cmd::byId(str_replace('#','',$option["ValInfField"]));
					$StatusCommande=cmd::byId(str_replace('#','',$option["StatusCommande"]));
					$data= array(($value>>24) & 0xFF, ($value>>16) & 0xFF,($value>>8) & 0xFF,$value & 0xFF,$ValInfField->execCmd(),$StatusCommande->execCmd());
				}
			break;
			case "232":	
				$data= self::html2rgb($value);
			break;
			case "235":
				if ($dpt != "235.001"){
					/*if ($value < 0)
					   $value = (abs($value) ^ 0xffffffff) + 1 ; */
					foreach(explode('|',$option["ActiveElectricalEnergy"]) as $tarif => $ActiveElectricalEnergy){
						$value=cmd::byId(str_replace('#','',$ActiveElectricalEnergy))->execCmd();
						$data= array(($value>>24) & 0xFF, ($value>>16) & 0xFF,($value>>8) & 0xFF,$value & 0xFF,$tarif,(0<< 1) & 0x02 | 0);
					}
				}
			break;
			case "251":
				list($r, $g, $b)=self::html2rgb($value);
				$w=jeedom::evaluateExpression($option["TempÃ©rature"]);
				$data= array($r, $g, $b, $w, 0x00, 0x0F);
			break;
			case "Color":	
				$data= false;
				list($r, $g, $b)=self::html2rgb($value);
				$cmdR=cmd::byId(str_replace('#','',$option["R"]));
				if(is_object($cmdR))
					$cmdR->execCmd(array('slider'=>$r));
				$cmdG=cmd::byId(str_replace('#','',$option["G"]));
				if(is_object($cmdG))
					$cmdG->execCmd(array('slider'=>$g));
				$cmdB=cmd::byId(str_replace('#','',$option["B"]));
				if(is_object($cmdB))
					$cmdB->execCmd(array('slider'=>$b));
			break;	
			case "ABB_ControlAcces_Read_Write":
				$Group=jeedom::evaluateExpression($option["Group"]);
				$PlantCode=jeedom::evaluateExpression($option["PlantCode"]);
				$Expire=jeedom::evaluateExpression($option["Expire"]);
				$data = EIS14_ABB_ControlAcces::WriteTag($value,$Group,$PlantCode,$Expire);
			break;
			default:
				switch($dpt){
					case "x.001":
						if ($option["Mode"] !=''){
							$data= array();
							$Mode=cmd::byId(str_replace('#','',$option["Mode"]));
							if (is_object($Mode)){
								$Mode->event(($data[0]>>1) & 0xEF);
								$Mode->setCache('collectDate', date('Y-m-d H:i:s'));
							}
						}
						$data= array(($Mode->execCmd()<< 1) & 0xEF | $value& 0x01);
					break;
				}
			break;
		};
		return $data;
	}
	public static function DptSelectDecode ($dpt, $data, $inverse=false, $option=null){
		if ($inverse)
			log::add('eibd', 'debug','La commande sera inversÃ©e');
		$All_DPT=self::All_DPT();
		switch (explode('.',$dpt)[0]){
			case "1":
				$value = $data;		
				if ($inverse)
					{
					if ($value == 0 )
						$value = 1;
					else
						$value = 0;
					}
				break;
			case "2":
				$value = $data;	
				break;
			case "3": 
				$ctrl = ($data & 0x08) >> 3;
				$stepCode = $data & 0x07;
				if ($ctrl)
					$value = $stepCode;
				else 
					$value = -$stepCode;
				break;
			case "5":  
				switch ($dpt)
				{
					case "5.001":
						$value = round((intval($data[0]) * 100) / 255);
						if ($inverse)
							$value=100-$value;
						break;
					case "5.003":
						$value = round((intval($data[0]) * 360) / 255);
						if ($inverse)
							$value=360-$value;
						break;
					case "5.004":
						$value = round(intval($data[0]) / 255);
						break;
					default:
						$value = intval($data[0]);
						break;
				}     
				break;
			case "6":
				if ($data[0] >= 0x80)
					$value = -(($data[0] - 1) ^ 0xff);  # invert twos complement
				else
					$value = $data[0];
				break;
			case "7":
				$value = $data[0] << 8 | $data[1];
				break;
			case "8":  
				$value = $data[0] << 8 | $data[1];
				if ($value >= 0x8000)
					$value = -(($value - 1) ^ 0xffff);  # invert twos complement
				break;
			case "9": 
				$exp = ($data[0] & 0x78) >> 3;
				$sign = ($data[0] & 0x80) >> 7;
				$mant = ($data[0] & 0x07) << 8 | $data[1];
				if ($sign)
					$sign = -1 << 11;
				else
					$sign = 0;
				$value = ($mant | $sign) * pow (2, $exp) * 0.01;   
				break;
			case "10": 
				$wDay =($data[0] >> 5) & 0x07;
				$hour =$data[0]  & 0x1f;
				$min = $data[1] & 0x3f;
				$sec = $data[2] & 0x3f;
				$value = /*new DateTime(*/$hour.':'.$min.':'.$sec;//);
				break;
			case "11":
				$day = $data[0] & 0x1f;
				$month = $data[1] & 0x0f;
				$year = $data[2] & 0x7f;
				if ($year<90)
					$year+=2000;
				else
					$year+=1900;
				$value =/* new DateTime(*/$day.'/'.$month.'/'.$year;//);
				break;
			case "12":
				$value = unpack("L",pack("C*",$data[3],$data[2],$data[1],$data[0]));
				break;
			case "13":
				$value = $data[0] << 24 | $data[1] << 16 | $data[2] << 8 | $data[3] ;
				if ($value >= 0x80000000)
					$value = -(($value - 1) ^ 0xffffffff);  # invert twos complement           
				break;
			case "14":
				$value= $data[0]<<24 |  $data[1]<<16 |  $data[2]<<8 |  $data[3]; 
				$value = unpack("f", pack("L", $value))[1];
				break;
			case "16":
				$value='';
				foreach($data as $chr)
					$value.=chr(($chr));
				break;

			case "17":
				$value = $data[0] & 0x3f;
				$value += 1;
				break;
			case "18":
				if ($option != null)	{
					if ($option["ctrl"] !=''){	
						$control=cmd::byId(str_replace('#','',$option["ctrl"]));
						if (is_object($control)){
							$ctrl = ($data[0] >> 7) & 0x01;
							log::add('eibd', 'debug', 'L\'objet '.$control->getName().' Ã  Ã©tÃ© trouvÃ© et va Ãªtre mis Ã  jour avec la valeur '. $ctrl);
							$control->event($ctrl);
							$control->setCache('collectDate', date('Y-m-d H:i:s'));
						}
					}
				}
				$value = $data[0] & 0x3f;
				break;
			case "19":
				$year=$data[0]+1900;
				$month=$data[1]& 0x0f;
				$day=$data[2]& 0x1f;
				$wDay =($data[3] >> 5) & 0x07;
				$hour =$data[3]  & 0x1f;
				$min = $data[4] & 0x3f;
				$sec = $data[5] & 0x3f;
				$Fault=($data[6] >> 7) & 0x01;
				$WorkingDay=($data[6] >> 6) & 0x01;
				$noWorkingDay=($data[6] >> 5) & 0x01;
				$noYear=($data[6] >> 4) & 0x01;
				$noDate=($data[6] >> 3) & 0x01;
				$noDayOfWeek=($data[6] >> 2) & 0x01;
				$NoTime=($data[6] >> 1) & 0x01;
				$SummerTime=$data[6] & 0x01;
				$QualityOfClock=($data[7] >> 7) & 0x01;
				//$date = new DateTime();
				//$date->setDate($year ,$month ,$day );
				//$date->setTime($hour ,$min ,$sec );
				//$value = $date->format('Y-m-d h:i:s')	
				$value = $day.'/'.$month.'/'.$year.' '.$hour.':'.$min.':'.$sec;
				break;
			case "20":
				$value = $data[0];
				if ($dpt != "20.xxx"){
					if (dechex($value)>0x80)
						$value = dechex($value)-0x80;
					if (dechex($value)>0x20)
						$value = dechex($value)-0x20;
					$value = $All_DPT["8BitEncAbsValue"][$dpt]["Valeurs"][$value];
				}
			break;
			case "23":
				$value = $data[0];
				if ($dpt != "23.xxx")
					$value = $All_DPT["2bit"][$dpt]["Valeurs"][$value];
			break;
			case "27":
				if ($option != null){
					for($byte=0;$byte<count($data);$byte++){
						if ($option["Info"] !='')
							$Info=explode('|',$option["Info"]);	
						for($bit=0;$bit <= 0xFF;$bit++){
							$bits=str_split($data[$byte],1);
							$InfoCmd=cmd::byId(str_replace('#','',$Info[$bit]));
							if (is_object($InfoCmd)){
								log::add('eibd', 'debug', 'Nous allons mettre Ã  jour l\'objet: '. $InfoCmd->getHumanName);
								$InfoCmd->event($bits[$bit]);
								$InfoCmd->setCache('collectDate', date('Y-m-d H:i:s'));
							}
						}
					}
				}
			break;
			case "225":
				if ($dpt != "225.002"){
					$value = $data[0];    
					if ($option != null){
						if ($option["ValInfField"] !='' /*&& is_numeric($data[4])&& $data[4]!=''*/){	
							//log::add('eibd', 'debug', 'Mise Ã  jour de l\'objet Jeedom ValInfField: '.$option["ValInfField"]);
							$TimePeriode=cmd::byId(str_replace('#','',$option["TimePeriode"]));
							if (is_object($TimePeriode)){
								$valeur = $data[0] << 8 | $data[1];
								log::add('eibd', 'debug', 'L\'objet '.$TimePeriode->getName().' Ã  Ã©tÃ© trouvÃ© et va Ãªtre mis Ã  jour avec la valeur '. $valeur);
								$TimePeriode->event($valeur);
								$TimePeriode->setCache('collectDate', date('Y-m-d H:i:s'));
							}
						}
						
					}
				}
			break;
			case "229":
				if ($dpt != "229.001"){
					/*if ($value < 0)
					   $value = (abs($value) ^ 0xffffffff) + 1 ; 
					$ValInfField=cmd::byId(str_replace('#','',$option["ValInfField"]));
					$StatusCommande=cmd::byId(str_replace('#','',$option["StatusCommande"]));
					$data= array(($value>>24) & 0xFF, ($value>>16) & 0xFF,($value>>8) & 0xFF,$value & 0xFF,$ValInfField->execCmd(),$StatusCommande->execCmd());*/
					$value = $data[0] << 24 | $data[1] << 16 | $data[2] << 8 | $data[3] ;
					if ($value >= 0x80000000)
						$value = -(($value - 1) ^ 0xffffffff);  # invert twos complement       
					if ($option != null){
						//Mise Ã  jour de l'objet Jeedom ValInfField
						if ($option["ValInfField"] !='' /*&& is_numeric($data[4])&& $data[4]!=''*/){	
							//log::add('eibd', 'debug', 'Mise Ã  jour de l\'objet Jeedom ValInfField: '.$option["ValInfField"]);
							$ValInfField=cmd::byId(str_replace('#','',$option["ValInfField"]));
							if (is_object($ValInfField)){
								$valeur=$data[4];
								log::add('eibd', 'debug', 'L\'objet '.$ValInfField->getName().' Ã  Ã©tÃ© trouvÃ© et va Ãªtre mis Ã  jour avec la valeur '. $valeur);
								$ValInfField->event($valeur);
								$ValInfField->setCache('collectDate', date('Y-m-d H:i:s'));
							}
						}
						//Mise Ã  jour de l'objet Jeedom StatusCommande
						if ($option["StatusCommande"] !='' /*&& is_numeric(($data[5]>>1) & 0x01)&& $data[5]!=''*/){
							//log::add('eibd', 'debug', 'Mise Ã  jour de l\'objet Jeedom StatusCommande: '.$option["StatusCommande"]);
							$StatusCommande=cmd::byId(str_replace('#','',$option["StatusCommande"]));
							if (is_object($StatusCommande)){
								$valeur=($data[5]>>1) & 0x01;
								log::add('eibd', 'debug', 'L\'objet '.$StatusCommande->getName().' Ã  Ã©tÃ© trouvÃ© et va Ãªtre mis Ã  jour avec la valeur '. $valeur);
								$StatusCommande->event($valeur);
								$StatusCommande->setCache('collectDate', date('Y-m-d H:i:s'));
							}
						}
					}
				}
			break;
			case "232":
				$value= self::rgb2html($data[0],$data[1], $data[2]);
			break;
			case "235":
				if ($dpt == "235.001"){
					$value = $data[5] & 0x01;  
					if($value == 1)
					   break; 
					log::add('eibd', 'debug', 'La valeur de la Ã©nergie electrique est valide');		
					$value=($data[5]>>1) & 0x01;
					if($value == 1)
					   break;
					log::add('eibd', 'debug', 'La valeur du tarif est valide');	
					if ($option != null){
						if ($option["ActiveElectricalEnergy"] !=''){	
							$ActiveElectricalEnergy=explode('|',$option["ActiveElectricalEnergy"]);
							$Tarif=$data[4];
							log::add('eibd', 'debug', 'Nous allons mettre Ã  jour le tarif '. $Tarif);	
							$ActiveElectricalEnergyCommande=cmd::byId(str_replace('#','',$ActiveElectricalEnergy[$Tarif]));
							if (is_object($ActiveElectricalEnergyCommande)){
								$valeur =$data[0] << 24 | $data[1] << 16 | $data[2] << 8 | $data[3] ;
								if ($valeur >= 0x80000000)
									$valeur = -(($valeur - 1) ^ 0xffffffff);  # invert twos complement    
								log::add('eibd', 'debug', 'L\'objet '.$ActiveElectricalEnergyCommande->getName().' Ã  Ã©tÃ© trouvÃ© et va Ãªtre mis Ã  jour avec la valeur '. $valeur);
								$ActiveElectricalEnergyCommande->event($valeur);
								$ActiveElectricalEnergyCommande->setCache('collectDate', date('Y-m-d H:i:s'));
							}
						}
					}
				}
			break;
			case "251":
				$Temperature=cmd::byId(str_replace('#','',$option["TempÃ©rature"]));
				if (is_object($Temperature)/* && $data[5]&0x01*/){
					$valeur=$data[3];
					log::add('eibd', 'debug', 'L\'objet '.$Temperature->getName().' Ã  Ã©tÃ© trouvÃ© et va Ãªtre mis Ã  jour avec la valeur '. $valeur);
					$Temperature->event($valeur);
					$Temperature->setCache('collectDate', date('Y-m-d H:i:s'));
				}
				$value= self::rgb2html($data[0],$data[1], $data[2]);
			break;			
			case "Color":	
				$R=cmd::byId(str_replace('#','',$option["R"]));
				if(!is_object($R) && $R->getType() == 'info')
					return;
				$G=cmd::byId(str_replace('#','',$option["G"]));
				if(!is_object($G) && $G->getType() == 'info')
					return;
				$B=cmd::byId(str_replace('#','',$option["B"]));
				if(!is_object($B) && $B->getType() == 'info')
					return;
				$listener = listener::byClassAndFunction('eibd', 'UpdateCmdOption', $option);
				if (!is_object($listener)){
					$listener = new listener();
					$listener->setClass('eibd');
					$listener->setFunction('UpdateCmdOption');
					$listener->setOption($option);
					$listener->emptyEvent();
					$listener->addEvent($R->getId());
					$listener->addEvent($G->getId());
					$listener->addEvent($B->getId());
					$listener->save();
				}
				$value= self::rgb2html($R->execCmd(),$G->execCmd(),$B->execCmd());
			break;
			case "ABB_ControlAcces_Read_Write":
				$Read= EIS14_ABB_ControlAcces::ReadTag($data);
				if(!$Read)
					return false;
				list($value,$PlantCode,$Expire)=$Read;
				$isValidCode = false;
				/*
				foreach(explode("&&",$option["Group"]) as $Groupe){
					if(jeedom::evaluateExpression($Groupe) == $Groupe){
						$isValidCode= true;
						break;
					}
				}
				if(!$isValidCode){
					log::add('eibd','debug','{{Le badge ('.$value.')  n\'appartient a aucun groupe  ('.$Group.') }}');
					return false;
				}*/				
				foreach(explode("&&",$option["PlantCode"]) as $Plant){
					if(jeedom::evaluateExpression($Plant) == $PlantCode){
						$isValidCode= true;
						break;
					}
				}
				if(!$isValidCode){
					log::add('eibd','debug','{{Le badge ('.$value.') n\'appartient a aucun PlantCode ('.$PlantCode.')}}');
					return false;
				}
// 				if(jeedom::evaluateExpression($option["Expire"]) > $Expire){
// 					log::add('eibd','debug','{{Le badge ('.$value.') est expirer ('.date("d/m/Y H:i:s",$Expire).')}}');
// 					return false;
// 				}
			break;	
			default:
				switch($dpt){
					case "x.001":
						$value = $data[0]& 0x01;      
						if ($option != null){
							//Mise Ã  jour de l'objet Jeedom Mode
							if ($option["Mode"] !=''){		
								$Mode=cmd::byId(str_replace('#','',$option["Mode"]));
								if (is_object($Mode)){
									$Mode->event(($data[0]>>1) & 0xEF);
									$Mode->setCache('collectDate', date('Y-m-d H:i:s'));
								}
							}
						}
					break;		
				}
			break;
		};
		return $value;
	}
	public static function OtherValue ($dpt, $oldValue){
		$All_DPT=self::All_DPT();
		$type= substr($dpt,0,strpos( $dpt, '.' ));
		switch ($type){
			default:
				$value=$oldValue;
			break;
			case "1":
				if ($oldValue == 1)
					$value=0;
				else
					$value=1;
			break;
		}
		return $value;
	}
	private function html2rgb($color){
		if ($color[0] == '#')
			$color = substr($color, 1);
		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],
		$color[2].$color[3],
		$color[4].$color[5]);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			return false;
		$r = hexdec($r); 
		$g = hexdec($g);
		$b = hexdec($b);
		return array($r, $g, $b);
	}
	private function rgb2html($r, $
