<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'eibclient', 'class', 'eibd');
include_file('core', 'BusMonitor', 'class', 'eibd');
include_file('core', 'dpt', 'class', 'eibd');
class eibd extends eqLogic {
	public static function cron() {
		foreach(eqLogic::byType('eibd') as $Equipement){		
			if($Equipement->getIsEnable()){
				foreach($Equipement->getCmd() as $Commande){
					if ($Commande->getConfiguration('CycliqueSend') == "cron"){
						$ga=$Commande->getLogicalId();
						$BusValue = $Commande->execute();
						if($Commande->getType() == 'info')			
							log::add('eibd', 'debug', $Commande->getHumanName().'[Lecture Cyclique] GAD: '.$ga.' = '.$BusValue);
						else
							log::add('eibd', 'debug', $Commande->getHumanName().'[Envoi Cyclique] GAD: '.$ga);
					}
				}
			}
		}
    	}
	public static function cron5() {
		foreach(eqLogic::byType('eibd') as $Equipement){		
			if($Equipement->getIsEnable()){
				foreach($Equipement->getCmd() as $Commande){
					if ($Commande->getConfiguration('CycliqueSend') == "cron5"){
						$ga=$Commande->getLogicalId();
						$BusValue = $Commande->execute();
						if($Commande->getType() == 'info')			
							log::add('eibd', 'debug', $Commande->getHumanName().'[Lecture Cyclique] GAD: '.$ga.' = '.$BusValue);
						else
							log::add('eibd', 'debug', $Commande->getHumanName().'[Envoi Cyclique] GAD: '.$ga);
					}
				}
			}
		}
	}
	public static function cron15() {
		foreach(eqLogic::byType('eibd') as $Equipement){		
			if($Equipement->getIsEnable()){
				foreach($Equipement->getCmd() as $Commande){
					if ($Commande->getConfiguration('CycliqueSend') == "cron15"){
						$ga=$Commande->getLogicalId();
						$BusValue = $Commande->execute();
						if($Commande->getType() == 'info')			
							log::add('eibd', 'debug', $Commande->getHumanName().'[Lecture Cyclique] GAD: '.$ga.' = '.$BusValue);
						else
							log::add('eibd', 'debug', $Commande->getHumanName().'[Envoi Cyclique] GAD: '.$ga);
					}
				}
			}
		}
	}
	public static function cron30() {
		foreach(eqLogic::byType('eibd') as $Equipement){		
			if($Equipement->getIsEnable()){
				foreach($Equipement->getCmd() as $Commande){
					if ($Commande->getConfiguration('CycliqueSend') == "cron30"){
						$ga=$Commande->getLogicalId();
						$BusValue = $Commande->execute();
						if($Commande->getType() == 'info')			
							log::add('eibd', 'debug', $Commande->getHumanName().'[Lecture Cyclique] GAD: '.$ga.' = '.$BusValue);
						else
							log::add('eibd', 'debug', $Commande->getHumanName().'[Envoi Cyclique] GAD: '.$ga);
					}
				}
			}
		}
	}	
	public static function cronHourly() {
		foreach(eqLogic::byType('eibd') as $Equipement){		
			if($Equipement->getIsEnable()){
				foreach($Equipement->getCmd() as $Commande){
					if ($Commande->getConfiguration('CycliqueSend') == "cronHourly"){
						$ga=$Commande->getLogicalId();
						$BusValue = $Commande->execute();
						if($Commande->getType() == 'info')			
							log::add('eibd', 'debug', $Commande->getHumanName().'[Lecture Cyclique] GAD: '.$ga.' = '.$BusValue);
						else
							log::add('eibd', 'debug', $Commande->getHumanName().'[Envoi Cyclique] GAD: '.$ga);
					}
				}
			}
		}
	}
	public static function cronDaily() {
		foreach(eqLogic::byType('eibd') as $Equipement){		
			if($Equipement->getIsEnable()){
				foreach($Equipement->getCmd() as $Commande){
					if ($Commande->getConfiguration('CycliqueSend') == "cronDaily"){
						$ga=$Commande->getLogicalId();
						$BusValue = $Commande->execute();
						if($Commande->getType() == 'info')			
							log::add('eibd', 'debug', $Commande->getHumanName().'[Lecture Cyclique] GAD: '.$ga.' = '.$BusValue);
						else
							log::add('eibd', 'debug', $Commande->getHumanName().'[Envoi Cyclique] GAD: '.$ga);
					}
				}
			}
		}
	}
	public function preInsert() {
		if (is_object(eqLogic::byLogicalId($this->getLogicalId(),'eibd')))     
			$this->setLogicalId('');
	}
	public function preSave() {
		$this->setLogicalId(trim($this->getLogicalId()));    
	}	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                                                                                               //
	//                                                      Gestion des Template d'equipement                                                       // 
	//                                                                                                                                               //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public static function devicesParameters($_device = '') {
		$path = dirname(__FILE__) . '/../config/devices';
		if (isset($_device) && $_device != '') {
			$files = ls($path, $_device . '.json', false, array('files', 'quiet'));
			if (count($files) == 1) {
				try {
					$content = file_get_contents($path . '/' . $files[0]);
					if (is_json($content)) {
						$deviceConfiguration = json_decode($content, true);
						return $deviceConfiguration[$_device];
					}
				} catch (Exception $e) {
					return array();
				}
			}
		}
		$files = ls($path, '*.json', false, array('files', 'quiet'));
		$return = array();
		foreach ($files as $file) {
			try {
				$content = file_get_contents($path . '/' . $file);
				if (is_json($content)) {
					$return = array_merge($return, json_decode($content, true));
				}
			} catch (Exception $e) {
			}
		}
		if (isset($_device) && $_device != '') {
			if (isset($return[$_device])) {
				return $return[$_device];
			}
			return array();
		}
		return $return;
	}
	public function applyModuleConfiguration($template, $TemplateOptions=null) {
		if ($template == '') {
			$this->save();
			return true;
		}
		$typeTemplate=$template;
		$device = self::devicesParameters($template);
		if (!is_array($device) || !isset($device['cmd'])) {
			return true;
		}
		if (isset($device['category'])) {
			foreach ($device['category'] as $key => $value) {
				$this->setCategory($key, $value);
			}
		}
		if (isset($device['configuration'])) {
			foreach ($device['configuration'] as $key => $value) {
				$this->setConfiguration($key, $value);
			}
		}
		foreach ($device['cmd'] as $command) {
			$cmd = null;
			foreach ($this->getCmd() as $liste_cmd) {
				if (isset($command['name']) && $liste_cmd->getName() == $command['name']) {
					$cmd = $liste_cmd;	
					break;
				}
			}
			$this->createTemplateCmd($cmd,$command);
		}
		if(is_array($TemplateOptions)){
			foreach ($device['options'] as $DeviceOptionsId => $DeviceOptions) {
				if(isset($TemplateOptions[$DeviceOptionsId])){
					$typeTemplate.='_'.$DeviceOptionsId;
					foreach ($DeviceOptions['cmd'] as $command) {
						$cmd = null;
						foreach ($this->getCmd() as $liste_cmd) {
							if (isset($command['name']) && $liste_cmd->getName() == $command['name']) {
								$cmd = $liste_cmd;	
								break;
							}
						}
						$this->createTemplateCmd($cmd,$command);
					}
				}
			}
		}
		$this->setConfiguration('typeTemplate',$typeTemplate);
		$this->save();
	}
	public function createTemplateCmd($cmd,$command) {		
		try {
			if ($cmd == null || !is_object($cmd)) {
				$cmd = new eibdCmd();
				$cmd->setEqLogic_id($this->getId());
			} else {
				$command['name'] = $cmd->getName();
			}
			utils::a2o($cmd, $command);
			if (isset($command['value']) && $command['value']!="") {
				$CmdValue=cmd::byEqLogicIdCmdName($this->getId(),$command['value']);
				if(is_object($CmdValue))
					$cmd->setValue('#'.$CmdValue->getId().'#');
				else
					$cmd->setValue(null);
			}
			if (isset($command['configuration']['option']) && $command['configuration']['option']!="") {
				$options=array();
				foreach($command['configuration']['option'] as $option => $cmdOption){
					$options[$option]=$cmdOption;
					$CmdValue=cmd::byEqLogicIdCmdName($this->getId(),$cmdOption);
					if(is_object($CmdValue))
						$options[$option]='#'.$CmdValue->getId().'#';
				}
				$cmd->setConfiguration('option',$options);
			}
			$cmd->save();
		} catch (Exception $exc) {
			error_log($exc->getMessage());
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                                                                                               //
	//                                                      Recherche automatique passerelle                                                       // 
	//                                                                                                                                               //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public static function SearchUsbGateway(){		
		if(config::byKey('KnxSoft', 'eibd') == 'knxd')
			$cmd="sudo /usr/local/src/knxd/knxd/src/usb/findknxusb";
		else
			$cmd="sudo findknxusb";
		$result=explode(" ",exec($cmd));	
		$USBaddr = explode(":",$result[1]);
		$cmd = sprintf("/dev/bus/usb/%'.03d/%'.03d",$USBaddr[0],$USBaddr[1]);
		log::add('eibd', 'debug', "Droit d'acces sur la passerelle USB " . $cmd);
		exec("sudo chmod 777 ".$cmd. ' >> ' . log::getPathToLog('eibd') . ' 2>&1');
		if(config::byKey('KnxSoft', 'eibd') == 'knxd'){
			return $USBaddr[0].":".$USBaddr[1];
		}else{
			$cmd="sudo findknxusb";
			$result=explode(" ",exec($cmd));	
			return $result[1];
		}
	}
	public static function SearchBroadcastGateway(){	
		$result[0]['KnxIpGateway'] ="";
		$result[0]['KnxPortGateway'] ="";
		$result[0]['IndividualAddressGateWay']="";
		$result[0]['DeviceName']="";
		$ServerPort=1024;
		$ServerAddr = network::getNetworkAccess('internal','ip');
		set_time_limit(0); 
		$BroadcastSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if (!$BroadcastSocket) {
			log::add('eibd', 'debug', "socket_create() failed: reason: " . socket_strerror(socket_last_error($BroadcastSocket)));
			return false;
		}
		while(!socket_bind($BroadcastSocket, '0.0.0.0', $ServerPort)) 
			$ServerPort++;
		if (!socket_set_option($BroadcastSocket, IPPROTO_IP, MCAST_JOIN_GROUP, array("group"=>"224.0.23.12","interface"=>0))) {
			log::add('eibd', 'debug', "socket_set_option() failed: reason: " . socket_strerror(socket_last_error($BroadcastSocket)));
			return false;
		}
     		if (!socket_set_option($BroadcastSocket,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>1,"usec"=>0))) {
			log::add('eibd', 'debug', "socket_set_option() failed: reason: " . socket_strerror(socket_last_error($BroadcastSocket)));
			return false;
		}
		log::add('eibd', 'debug', 'Envoi de la trame search request');
		$msg = "06".						// 06 HEADER_SIZE
		"10".					// 10 KNX/IP v1.0
		"0201" .			// servicetypeidentifier
		"000E".						// totallength,14octets
		
		//Host Protocol Address Information (HPAI)		
		"08".						// structure length
		"01".						//host protocol code, e.g. 01h, for UDP over IPv4
		bin2hex(inet_pton($ServerAddr)).					//192.168.0.49
		sprintf('%04x', $ServerPort);						//portnumberofcontrolendpoint
		
		$hex_msg = hex2bin($msg);
		$dataBrute='';
		foreach (unpack("C*", $hex_msg) as $Byte)
			$dataBrute.=sprintf('0x%02x ',$Byte);
		log::add('eibd', 'debug', 'Data emise: ' . $dataBrute);
		if (!$len = socket_sendto($BroadcastSocket, $hex_msg, strlen($hex_msg), 0, "224.0.23.12", 3671)) {
			$lastError = "socket_sendto() failed: reason: " . socket_strerror(socket_last_error($BroadcastSocket));
			return false;
		}
		$NbLoop=0;
		while(true) { 
			$buf = '';
			socket_recvfrom($BroadcastSocket, $buf , 2048, 0, $name, $port);
			if($buf == '') 
				break;
			$ReadFrame= unpack("C*", $buf);
			
			$dataBrute='';
			foreach ($ReadFrame as $Byte)
				$dataBrute.=sprintf('0x%02x ',$Byte);
			log::add('eibd', 'debug', 'Data recus: ' . $dataBrute);		
			
			$HeaderSize=array_slice($ReadFrame,0,1)[0];
			$Header=array_slice($ReadFrame,0,$HeaderSize);
			$Body=array_slice($ReadFrame,$HeaderSize);
			switch (array_slice($Header,2,1)[0]){
				case 0x02:
					switch (array_slice($Header,3,1)[0]){
						case 0x02:
							$result[$NbLoop]['KnxIpGateway'] =	array_slice($Body,2,1)[0]
											.".".	array_slice($Body,3,1)[0]
											.".".	array_slice($Body,4,1)[0]
											.".".	array_slice($Body,5,1)[0];
							$KnxPortGateway =	array_slice($Body,6,2);
							$result[$NbLoop]['KnxPortGateway'] =$KnxPortGateway[0]<<8|$KnxPortGateway[1];
							$addr = array_slice($Body,12,1)[0]<<8|array_slice($Body,13,1);
							$result[$NbLoop]['IndividualAddressGateWay']=sprintf ("%d.%d.%d", ($addr >> 12) & 0x0f, ($addr >> 8) & 0x0f, $addr & 0xff);$string='';
							foreach (array_slice($Body,32) as $hexcar){
								if($hexcar == 0 || $hexcar > 170)
									break;
								$string .= chr($hexcar);
							}
							$result[$NbLoop]['DeviceName'] = $string;
						break;
					}
				break;
			}	
			$NbLoop++;
		}
		socket_close($BroadcastSocket);
		return $result;
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                                                                                               //
	//                                                      Gestion du de la communication KNX                                                       // 
	//                                                                                                                                               //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private static function parseread ($len,$buf){
		$buf = unpack("C*", $buf->buffer);
		if ($buf[1] & 0x3 || ($buf[2] & 0xC0) == 0xC0)
			return false;
		else if (($buf[2] & 0xC0) == 0x00)
			return array ("Read", null);
		else if (($buf[2] & 0xC0) == 0x40){
			if ($len == 2)
				return array ("Reponse", $buf[2] & 0x3F);
			else
				return array ("Reponse", array_slice($buf, 2));
		}else if (($buf[2] & 0xC0) == 0x80){
			if ($len == 2)
				return array ("Write", $buf[2] & 0x3F);
			else
				return array ("Write", array_slice($buf, 2));
		}else{
			return array ("Read", null);
		}
	}
   	private static function gaddrparse ($addr)	{
		$addr = explode("/", $addr);
		if (count ($addr) >= 3)
			$r =(($addr[0] & 0x1f) << 11) | (($addr[1] & 0x7) << 8) | (($addr[2] & 0xff));
		if (count ($addr) == 2)
			$r = (($addr[0] & 0x1f) << 11) | (($addr[1] & 0x7ff));
		if (count ($addr) == 1)
			$r = (($addr[1] & 0xffff));
		return $r;
	}
	public static function EibdRead($addr){
		$host=config::byKey('EibdHost', 'eibd');
		$port=config::byKey('EibdPort', 'eibd');
		$EibdConnexion = new EIBConnection($host,$port);
		$EibdConnexion->setTimeout(5);
		$addr = self::gaddrparse($addr);
		if ($EibdConnexion->EIBOpenT_Group ($addr, 0) == -1)
			throw new Exception(__('Erreur de connexion au Bus KNX', __FILE__));
		$val =  0 & 0x3f;
		$val |= 0x0000;
		$data = pack ("n", $val);
		$len = $EibdConnexion->EIBSendAPDU($data);
		if ($len == -1)
          		return false;
		$loop=0;
		$return=null;
		while (1){ 
			$data = new EIBBuffer();
			$src = new EIBAddr();
			$len = $EibdConnexion->EIBGetAPDU_Src($data, $src);
			if ($len == -1)	
				return false;
			if ($len < 2)
				return false;
			$buf = unpack("C*", $data->buffer);
			if ($buf[1] & 0x3 || ($buf[2] & 0xC0) == 0xC0){
				throw new Exception(__("Type de data non prise en charge par la plugin (".BusMonitorTraitement::formatiaddr($src->addr).")", __FILE__));
			}
			else if (($buf[2] & 0xC0) == 0x40){
				if ($len == 2)                     
					$return=$buf[2] & 0x3F;
				else
					$return=array_slice($buf, 2);
				break;
			}	
		}
		$EibdConnexion->EIBClose();
		return $return;
	}		
   	public static function EibdReponse($addr, $val){
		$host=config::byKey('EibdHost', 'eibd');
		$port=config::byKey('EibdPort', 'eibd');
		$EibdConnexion = new EIBConnection($host,$port);
		if(!is_array($val)){
			$val = ($val + 0) & 0x3f;
			$val |= 0x0040;
			$data = pack ("n", $val);
		}else {
			$header = 0x0040;
			$data = pack ("n", $header);
			for ($i = 0; $i < count ($val); $i++)
				$data .= pack ("C", $val[$i]);
		}
		$addr = self::gaddrparse ($addr);
		$len = $EibdConnexion->EIBOpenT_Group ($addr, 1);
		if ($len == -1)
          		return false;
		$len = $EibdConnexion->EIBSendAPDU($data);
		if ($len == -1)
          		return false;
		$EibdConnexion->EIBClose();
		return true;
	}
   	public static function EibdWrite($addr, $val){
		$host=config::byKey('EibdHost', 'eibd');
		$port=config::byKey('EibdPort', 'eibd');
		$EibdConnexion = new EIBConnection($host,$port);
		if(!is_array($val))
		{
			$val = ($val + 0) & 0x3f;
			$val |= 0x0080;
			$data = pack ("n", $val);
		} else	{
			$header = 0x0080;
			$data = pack ("n", $header);
			for ($i = 0; $i < count ($val); $i++)
				$data .= pack ("C", $val[$i]);
		}
		$addr = self::gaddrparse ($addr);
		$len = $EibdConnexion->EIBOpenT_Group ($addr, 1);
		if ($len == -1)
			return -1;
		$len = $EibdConnexion->EIBSendAPDU($data);
		if ($len == -1)
			return -1;
		$EibdConnexion->EIBClose();
		return true;
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                                                                                               //
	//                                                            Gestion du BusMonitor                                                              // 
	//                                                                                                                                               //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public static function InitInformation() { 
		log::add('eibd', 'debug', 'Initialisation de valeur des objets KNX');
		foreach(eqLogic::byType('eibd') as $Equipement){		
			if($Equipement->getIsEnable()){
				foreach($Equipement->getCmd() as $Commande){
					if($Commande->getLogicalId() == ''){
						message::add('info',$Commande->getHumanName().'[Initialisation]: Pas d\'adresse de groupe','','');
						continue;
					}
					if ($Commande->getConfiguration('FlagInit')){
						$BusValue = $Commande->execute(array('init'=>true));
						if($Commande->getType() == 'info')			
							log::add('eibd', 'debug', $Commande->getHumanName().'[Initialisation] Lecture du GAD: '.$Commande->getLogicalId().' = '.$BusValue);
						else
							log::add('eibd', 'debug', $Commande->getHumanName().'[Initialisation] Envoi sur le GAD: '.$Commande->getLogicalId());
					}
				}
			}
		}
	}
	public static function BusMonitor() { 
		log::add('eibd', 'debug', 'Lancement du Bus Monitor');
		$host=config::byKey('EibdHost', 'eibd');
		$port=config::byKey('EibdPort', 'eibd');
		log::add('eibd', 'debug', 'Connexion a EIBD sur le serveur '.$host.':'.$port);
		$conBusMonitor = new EIBConnection($host,$port);
		$buf = new EIBBuffer();		
		if ($conBusMonitor->EIBOpen_GroupSocket(0) == -1)
			log::add('eibd', 'error',$conBusMonitor->getLastError);	
		while(true){	
			$src = new EIBAddr;
			$dest = new EIBAddr;
			$len = $conBusMonitor->EIBGetGroup_Src($buf, $src, $dest);  
			if($len != -1)
				break;
			sleep(1);
		}
		self::InitInformation();
		$nbError = 0;
		while(true) {    
			$src = new EIBAddr;
			$dest = new EIBAddr;
			$len = $conBusMonitor->EIBGetGroup_Src($buf, $src, $dest);      
			if ($len == -1){
				if($nbError >3){
					message::add('info','[BusMonitor] 3 erreur consécutive, nous redémarront le demon', '', '') ;
              				break;
				} 
				$nbError++;
			}
			if ($len >= 2) {
				$nbError = 0;
				$mon = self::parseread($len,$buf);
				if($mon !== false){
					$Traitement=new BusMonitorTraitement($mon[0],$mon[1],$src->addr,$dest->addr);
					$Traitement->run(); 
				}else
					log::add('eibd', 'debug', "Type de data non prise en charge par la plugin (".BusMonitorTraitement::formatiaddr($src->addr).' - '.BusMonitorTraitement::formatgaddr($dest->addr).")");
			}
		}
		$conBusMonitor->EIBClose();		
		log::add('eibd', 'debug', 'Deconnexion a EIBD sur le serveur '.$host.':'.$port);	
	}
	public static function TransmitValue($_options) 	{
		$Event = cmd::byId($_options['event_id']);
		if(!is_object($Event)){
			log::add('eibd','error','Impossible de touvée l\'objet '.$_options['event_id']);
			return;
		}
		log::add('eibd','info',$Event->getHumanName().' est mise a jour: '.$_options['value']);
		$Commande = cmd::byId($_options['eibdCmd_id']);
		if (!is_object($Commande)){
			log::add('eibd','error','Impossible de touvée la commande '.$_options['eibdCmd_id']);
			return;
		}
		$ga=$Commande->getLogicalId();
		$dpt=$Commande->getConfiguration('KnxObjectType');
		$inverse=$Commande->getConfiguration('inverse');
		$Option=$Commande->getConfiguration('option');
		if(!is_array($Option))
			$Option = json_decode($Option,true);
		$Option['id']=$Commande->getId();
		$data= Dpt::DptSelectEncode($dpt, $_options['value'], $inverse,$Option);
		$WriteBusValue=eibd::EibdWrite($ga, $data);
		log::add('eibd','info',$Commande->getHumanName().'[Transmission]: Envoie de la valeur '.$_options['value'].' sur le GAD '.$ga);
	}
	public static function AddEquipement($Name,$_logicalId,$_objectId=null) {
		foreach(eqLogic::byType('eibd') as $Equipement){
			if($Equipement->getName() == $Name && $Equipement->getObject_id() == $_objectId)
				return $Equipement;
		}
		$Equipement = new eibd();
		$Equipement->setName($Name);
		$Equipement->setLogicalId($_logicalId);
		$Equipement->setObject_id($_objectId);
		$Equipement->setEqType_name('eibd');
		$Equipement->setIsEnable(1);
		$Equipement->setIsVisible(1);
		$Equipement->save();
		return $Equipement;
	}
	public function AddCommande($Name,$_logicalId,$Type="info", $Dpt='', $Configuration = null) {
		$Commande = $this->getCmd(null,$_logicalId);
		if (!is_object($Commande)){
			$VerifName=$Name;
			$Commande = new eibdCmd();
			$Commande->setId(null);
			$Commande->setLogicalId($_logicalId);
			$Commande->setEqLogic_id($this->getId());
			$count=0;
			while (is_object(cmd::byEqLogicIdCmdName($this->getId(),$VerifName))){
				$count++;
				$VerifName=$Name.'('.$count.')';
			}
			$Commande->setName($VerifName);
			$Commande->setIsVisible(1);
			$Commande->setType($Type);
			if ($Dpt!=''){
				if($Type=='info')
					$Commande->setSubType(Dpt::getDptInfoType($Dpt));
				else
					$Commande->setSubType(Dpt::getDptActionType($Dpt));
				$Commande->setUnite(Dpt::getDptUnite($Dpt));
				$Commande->setConfiguration('KnxObjectType',$Dpt);
			}else{
				if($Type=='info')
					$Commande->setSubType('string');
				else
					$Commande->setSubType('other');
				$Commande->setConfiguration('KnxObjectType','1.xxx');
				$Commande->setUnite('');
			}
			if(is_array($Configuration)){
				foreach($Configuration as $type => $value)
					$Commande->setConfiguration($type,$value);
			}
			$Commande->save();
		}
		return $Commande;
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                                                                                               //
	//                                                            Gestion du logiciel EIBD                                                           // 
	//                                                                                                                                               //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public static function dependancy_info() {
		$return = array();
		$return['log'] = 'eibd_update';
		$return['progress_file'] = '/tmp/compilation_eibd_in_progress';
		$return['state'] = 'nok';
		switch(config::byKey('KnxSoft', 'eibd')){
			case 'eibd':
            			if(exec("command -v eibd") !='')
					$return['state'] = 'ok';
			break;
			case 'knxd':
           			if(exec("command -v knxd") !='')
					$return['state'] = 'ok';
			break;
			case 'manual':
				$return['state'] = 'ok';
			break;
		}
		return $return;
	}
	public static function dependancy_install() {
		if (file_exists('/tmp/compilation_eibd_in_progress')) {
			return;
		}
		log::remove('eibd_update');
		config::save('lastDependancyInstallTime', date('Y-m-d H:i:s'),'eibd');
		switch(config::byKey('KnxSoft', 'eibd')){
			case 'knxd':
            	$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install-knxd.sh';
			break;
			case 'eibd':
				$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install-eibd.sh';
			break;
		}
		if(isset($cmd)){
			$cmd .= ' >> ' . log::getPathToLog('eibd_update') . ' 2>&1 &';
			exec($cmd);
		}
	}
	public static function deamon_info() {
		$return = array();
		$return['log'] = 'eibd';	
		$return['launchable'] = 'nok';
		$return['state'] = 'nok';
		switch(config::byKey('KnxSoft', 'eibd')){
			case 'knxd':
				$result=exec("ps aux | grep knxd | grep -v grep | awk '{print $2}'",$result);	
				if($result!="")
					$return['state'] = 'ok';
				if(config::byKey('EibdPort', 'eibd')!=''&&config::byKey('EibdGad', 'eibd')!=''&&config::byKey('KNXgateway', 'eibd')!='')
					$return['launchable'] = 'ok';
			break;
			case 'eibd':
				$result=exec("ps aux | grep eibd | grep -v grep | awk '{print $2}'",$result);	
				if($result!="")
					$return['state'] = 'ok';
				if(config::byKey('EibdPort', 'eibd')!=''&&config::byKey('EibdGad', 'eibd')!=''&&config::byKey('KNXgateway', 'eibd')!='')
					$return['launchable'] = 'ok';
			break;
			case 'manual':
				$return['state'] = 'ok';
				$return['launchable'] = 'ok';
			break;
		}
		
		if($return['state'] == 'ok'){
			$cron = cron::byClassAndFunction('eibd', 'BusMonitor');
			if(is_object($cron) && $cron->running())
				$return['state'] = 'ok';
			else
				$return['state'] = 'nok';
		}
		foreach(eqLogic::byType('eibd') as $Equipement)	{
			if ($Equipement->getIsEnable()){
				foreach($Equipement->getCmd('action') as $Commande){
					if($Commande->getConfiguration('FlagTransmit') && $Commande->getValue() != ''){
						$listener = listener::byClassAndFunction('eibd', 'TransmitValue', array('eibdCmd_id' => $Commande->getId()));
						if (!is_object($listener)){
							log::add('eibd','debug',$Commande->getHumanName().' Un probleme sur cette commande provoque l\'arret du demon');
							$return['state'] = 'nok';
							return $return;
						}
					}	
				}
			}
		}
		return $return;
	}
	public static function deamon_start($_debug = false) {
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') 
			return;
		log::remove('eibd');
		self::deamon_stop();
		foreach(eqLogic::byType('eibd') as $Equipement)	{
			if ($Equipement->getIsEnable()){
				foreach($Equipement->getCmd('action') as $Commande){
					if($Commande->getConfiguration('FlagTransmit') && $Commande->getValue() != ''){
						$ActionValue=cmd::byId(str_replace('#','',$Commande->getValue()));
						if(is_object($ActionValue)){
							$listener = new listener();
							$listener->setClass('eibd');
							$listener->setFunction('TransmitValue');
							$listener->setOption(array('eibdCmd_id' => $Commande->getId()));
							$listener->emptyEvent();
							$listener->addEvent($ActionValue->getId());
							$listener->save();
						}	
					}	
				}
			}
		}
		exec("sudo touch /var/log/knx.log");
		exec("sudo chmod 777 /var/log/knx.log");
		$cmd = '';
		switch(config::byKey('KnxSoft', 'eibd')){
			case 'knxd':
            			$clientAddrs = explode('.',config::byKey('EibdGad', 'eibd'));
            			$clientAddrs[count($clientAddrs)-1] +=1;
				$cmd .= 'knxd --daemon=/var/log/knx.log --pid-file=/var/run/knx.pid';
				if(config::byKey("log::level::eibd")[1000] != 1)
					$cmd .= ' -t1023';
				$cmd .= ' --eibaddr='.config::byKey('EibdGad', 'eibd').' --client-addrs='.implode('.',$clientAddrs).':'.config::byKey('EibdNbAddr', 'eibd');
           		break;
			case 'eibd':
				$cmd .= '/usr/local/bin/eibd --daemon=/var/log/knx.log --pid-file=/var/run/knx.pid';
				if(config::byKey("log::level::eibd")[1000] != 1)
					$cmd .= ' -t1023';
				$cmd .= ' --eibaddr='.config::byKey('EibdGad', 'eibd');			
			break;
		}
		if(config::byKey('KnxSoft', 'eibd') == 'knxd' && config::byKey('ServeurName', 'eibd') !='')
			$cmd .= ' --Name='.config::byKey('ServeurName', 'eibd');
		if(config::byKey('Discovery', 'eibd'))
				$cmd .= ' -D';
		if(config::byKey('Routing', 'eibd'))
				$cmd .= ' -R';
		if(config::byKey('Tunnelling', 'eibd'))
				$cmd .= ' -T';
		if(config::byKey('Discovery', 'eibd') || config::byKey('Routing', 'eibd') || config::byKey('Tunnelling', 'eibd'))
				$cmd .= ' -S';
		$cmd .= ' --listen-tcp='.config::byKey('EibdPort', 'eibd');
		if(config::byKey('KnxSoft', 'eibd') == 'knxd')
			$cmd .= ' -b ';	
		if($cmd != ''){
			if(config::byKey('TypeKNXgateway', 'eibd') == 'usb'){
				$USBaddr = explode(':',config::byKey('KNXgateway', 'eibd'));
				$cmdUSB = sprintf("/dev/bus/usb/%'.03d/%'.03d",$USBaddr[0],$USBaddr[1]);
				log::add('eibd', 'debug', "Droit d'acces sur la passerelle USB " . $cmdUSB);
				exec("sudo chmod 777 ".$cmdUSB. ' >> ' . log::getPathToLog('eibd') . ' 2>&1');
			}
			$cmd .= ' '. config::byKey('TypeKNXgateway', 'eibd') . ':' . config::byKey('KNXgateway', 'eibd');
			if(isset($cmd)){
				$cmd .= ' >> /var/log/knx.log 2>&1';
				log::add('eibd','info', '[Start] '.$cmd);
				exec($cmd);
			}
		}
		$cron = cron::byClassAndFunction('eibd', 'BusMonitor');
		if (!is_object($cron)) {
			$cron = new cron();
			$cron->setClass('eibd');
			$cron->setFunction('BusMonitor');
			$cron->setEnable(1);
			$cron->setDeamon(1);
			$cron->setSchedule('* * * * *');
			$cron->setTimeout('999999');
			$cron->save();
		}
		$cron->start();
		$cron->run();
	}
	public static function deamon_stop() {
		switch(config::byKey('KnxSoft', 'eibd')){
			case 'knxd':
				$cmd='sudo pkill knxd';
			break;
			case 'eibd':
				$cmd='sudo pkill eibd';
			break;
		}
		if(isset($cmd)){
			$cmd .= ' >> ' . log::getPathToLog('eibd') . ' 2>&1';
			exec($cmd);
		}
		$cron = cron::byClassAndFunction('eibd', 'BusMonitor');
		if (is_object($cron)) {
			$cron->stop();
			$cron->remove();
		}
		while(is_object($listener=listener::byClassAndFunction('eibd', 'TransmitValue')))
			$listener->remove();
		if(file_exists("/var/log/knx.log"))			
			exec("sudo rm /var/log/knx.log");
			
	}
  }
class eibdCmd extends cmd {
	public function preSave() { 
		if($this->getConfiguration('FlagTransmit') && $this->getValue() != ''){
			$CmdState=cmd::byId(str_replace('#','',$this->getValue()));
			if(is_object($CmdState) && $CmdState->getEqType_name() == 'eibd'){
				if($CmdState->getLogicalId() == $this->getLogicalId())
					throw new Exception(__('{{Il est impossible de transmettre / retransmettre un état sur le même GAD}}', __FILE__));
			}
		}
		if ($this->getConfiguration('KnxObjectType') == '') 
			throw new Exception(__('Le type de commande ne peut être vide', __FILE__));
		$this->setLogicalId(trim($this->getLogicalId()));    
	}
	public function postSave() {			
		if ($this->getConfiguration('FlagInit')){
			$BusValue = $this->execute(array('init'=>true));
			if($this->getType() == 'info')			
				log::add('eibd', 'info', $this->getHumanName().'[Initialisation] Lecture du GAD: '.$this->getLogicalId().' = '.$BusValue);
			else
				log::add('eibd', 'info', $this->getHumanName().'[Initialisation] Envoi sur le GAD: '.$this->getLogicalId());
		}
		$listener = listener::byClassAndFunction('eibd', 'TransmitValue', array('eibdCmd_id' => $this->getId()));
		if($this->getConfiguration('FlagTransmit') && $this->getValue() != ''){
			if (!is_object($listener)){
				$CmdState=cmd::byId(str_replace('#','',$this->getValue()));
				if(is_object($CmdState) && $CmdState->getEqType_name() == 'eibd'){
					if($CmdState->getLogicalId() != $this->getLogicalId()){
						$listener = new listener();
						$listener->setClass('eibd');
						$listener->setFunction('TransmitValue');
						$listener->setOption(array('eibdCmd_id' => $this->getId()));
						$listener->emptyEvent();
						$listener->addEvent($CmdState->getId());
						$listener->save();
					}
				}
			}
		}else{
			if (is_object($listener))
				$listener->remove();
		}
		$cache = cache::byKey('eibd::CreateNewGad');
		$value = json_decode($cache->getValue('[]'), true);
		$key = array_search($this->getLogicalId(), array_column($value, 'AdresseGroupe'));
		if($key != false){
			unset($value[$key]);
			array_shift($value);
			cache::set('eibd::CreateNewGad', json_encode($value), 0);
		}
	}
	public function getOtherActionValue(){
		$ActionValue = jeedom::evaluateExpression($this->getConfiguration('KnxObjectValue'));
		if ($this->getConfiguration('KnxObjectValue') == "") 
			$ActionValue = Dpt::OtherValue($this->getConfiguration('KnxObjectType'),jeedom::evaluateExpression($this->getValue()));
		return $ActionValue;
	}
	public function execute($_options = null){
		$deamon_info = eibd::deamon_info();
		if ($deamon_info['state'] != 'ok') 
			return false;		
		$ga=$this->getLogicalId();
		$dpt=$this->getConfiguration('KnxObjectType');
		$inverse=$this->getConfiguration('inverse');
		$Option=$this->getConfiguration('option');
		if(!is_array($Option))
			$Option = json_decode($Option,true);
		$Option['id']=$this->getId();
		switch ($this->getType()) {
			case 'action' :
				$Listener=cmd::byId(str_replace('#','',$this->getValue()));
				if (isset($Listener) && is_object($Listener)) 
					$inverse=$Listener->getConfiguration('inverse');
				switch ($this->getSubType()) {
					case 'slider':    
						$ActionValue = $_options['slider'];
					break;
					case 'color':
						$ActionValue = $_options['color'];
					break;
					case 'message':
						$ActionValue = $_options['message'];
					break;
					case 'select':
						$ActionValue = $_options['select'];
					break;
					case 'other':
						$ActionValue = $this->getOtherActionValue();
					break;
				}
				log::add('eibd','debug',$this->getHumanName().'[Write] Valeur a envoyer '.$ActionValue);
				$data= Dpt::DptSelectEncode($dpt, $ActionValue, $inverse,$Option);
				if($ga != '' && $data !== false){
					if(is_array($data[0])){
						foreach($data as $frame){
							$WriteBusValue=eibd::EibdWrite($ga, $frame);
							usleep(config::byKey('SendSleep','eibd')*1000);
						}
						break;
					}else{
						$WriteBusValue=eibd::EibdWrite($ga, $data);
						/*if ($WriteBusValue != -1 && isset($Listener) && is_object($Listener) && $ga==$Listener->getLogicalId()){
							$Listener->event($ActionValue);
							$Listener->setCache('collectDate', date('Y-m-d H:i:s'));
						}*/
					}
				}
				sleep(config::byKey('SendSleep','eibd')*1000);
			break;
			case 'info':
				if($this->getConfiguration('FlagWrite') && !isset($_options['init'])){
					return $this->execCmd();
				}else{
					try {
						log::add('eibd','debug',$this->getHumanName().'[Read] Interrogation du bus');
						$DataBus = eibd::EibdRead($ga);
					} catch (Exception $exception) {
						$DataBus = false;
					}
					if($DataBus === false){
						message::add('info',$this->getHumanName().'[Read]: Aucune réponse','',$ga);
						return;
					}
					$BusValue=Dpt::DptSelectDecode($dpt, $DataBus, $inverse,$Option);
					if($BusValue !== false)
						$this->getEqLogic()->checkAndUpdateCmd($ga,$BusValue);
					usleep(config::byKey('SendSleep','eibd')*1000);
					return $BusValue;
				}
			break;
		}
	}
	public function SendReply(){
		log::add('eibd', 'info',$this->getHumanName().'[Réponse]: Demande de valeur sur l\adresse de groupe : '.$this->getLogicalId());			
		$valeur='';
		$unite='';
		$dpt=$this->getConfiguration('KnxObjectType');
		$inverse=$this->getConfiguration('inverse');
		$Option=$this->getConfiguration('option');
		if(!is_array($Option))
			$Option = json_decode($Option,true);
		$Option['id']=$this->getId();
		if ($dpt != 'aucun' && $dpt!= ''){
			$unite=Dpt::getDptUnite($dpt);
			$Listener=cmd::byId(str_replace('#','',$this->getValue()));
			if(is_object($Listener)) {
				$inverse=$Listener->getConfiguration('inverse');
				if($Listener->getLogicalId() == $this->getLogicalId()){
					log::add('eibd', 'debug', $this->getHumanName().'[Réponse]: Impossible de répondre avec le même GAD');
					return false;
				}
			}
			$valeur = $this->getOtherActionValue();
			if($valeur != false && $valeur != ''){
				$data= Dpt::DptSelectEncode($dpt, $valeur, $inverse,$Option);
				log::add('eibd', 'info',$this->getHumanName().'[Réponse]: Réponse avec la valeur : '.$valeur.$unite);
				eibd::EibdReponse($this->getLogicalId(), $data);
			}
		}else{
			$valeur='Aucun DPT n\'est associé a cette adresse';
		}
		return $valeur.$unite ;
	}
	public function UpdateCommande($data){	
		$valeur='';		
		$dpt=$this->getConfiguration('KnxObjectType');
		$inverse=$this->getConfiguration('inverse');
		$Option=$this->getConfiguration('option');
		if(!is_array($Option))
			$Option = json_decode($Option,true);
		$Option['id']=$this->getId();
		if ($dpt != 'aucun' && $dpt!= ''){
			$unite=Dpt::getDptUnite($dpt);
			log::add('eibd', 'debug',$this->getHumanName().' : Décodage de la valeur avec le DPT :'.$dpt);
			$valeur=Dpt::DptSelectDecode($dpt, $data, $inverse, $Option);
			if($valeur !== false){
				if($this->getConfiguration('noBatterieCheck')){
					switch(explode('.',$dpt)[0]){
						case 1 :
							$valeur=$valeur*100;
						break;
					}
					$this->getEqlogic()->batteryStatus($valeur,date('Y-m-d H:i:s'));
				}
				if($this->getType() == 'info'){
					log::add('eibd', 'info',$this->getHumanName().' : Mise à jour de la valeur : '.$valeur.$unite);
					$this->event($valeur);
					$this->setCache('collectDate', date('Y-m-d H:i:s'));
				}
			}
		}else{
			$valeur='Aucun DPT n\'est associé a cette adresse';
		}
		return $valeur.$unite ;
	}
	public function UpdateCmdOption($_options) { 
		log::add('eibd', 'Info', 'Mise à jour d\'une commande par ses options');
		$dpt=$this->getConfiguration('KnxObjectType');
		$inverse=$this->getConfiguration('inverse');
		$Option=$this->getConfiguration('option');
		if(!is_array($Option))
			$Option = json_decode($Option,true);
		$Option['id']=$this->getId();
		$unite=Dpt::getDptUnite($dpt);
		$valeur=Dpt::DptSelectDecode($dpt, null, $inverse, $Option);
		if($this->getType() == 'info' && $valeur !== false){
			log::add('eibd', 'info',$this->getHumanName().' : Mise à jour de la valeur : '.$valeur.$unite);
			$this->event($valeur);
			$this->setCache('collectDate', date('Y-m-d H:i:s'));
		}
	}
}
?>
