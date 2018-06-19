<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'eibclient', 'class', 'eibd');
include_file('core', 'dpt', 'class', 'eibd');
class eibd extends eqLogic {
	public static function cron() {
		foreach(eqLogic::byType('eibd') as $Equipement){		
			if($Equipement->getIsEnable()){
				foreach($Equipement->getCmd('info') as $Commande)	{
					if (!$Commande->getConfiguration('FlagWrite') && $Commande->getConfiguration('FlagInit')){
						$ga=$Commande->getLogicalId();
						$dpt=$Commande->getConfiguration('KnxObjectType');
						$inverse=$Commande->getConfiguration('inverse');
						log::add('eibd', 'debug', $Commande->getHumanName().' Lecture de '. $Commande->getHumanName().' sur le GAD '.$ga);
						$DataBus=self::EibdRead($ga);
						if($DataBus === false){
							$Commande->setConfiguration('FlagInit',false);
							$Commande->save();
							continue;
						}
						$Option=$Commande->getConfiguration('option');
						$Option["id"]=$Commande->getId();
						$BusValue=Dpt::DptSelectDecode($dpt, $DataBus, $inverse,$Option);
						log::add('eibd', 'debug', $Commande->getHumanName().' => '.$BusValue);
						if ($Commande->execCmd() != $Commande->formatValue($BusValue)) {
							$Commande->event($BusValue);
						}
						$Commande->setCache('collectDate', date('Y-m-d H:i:s'));
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
	public function applyModuleConfiguration($template) {
		if ($template == '') {
			$this->save();
			return true;
		}
		$device = self::devicesParameters($template);
		if (!is_array($device) || !isset($device['cmd'])) {
			return true;
		}
		if (isset($device['configuration'])) {
			foreach ($device['configuration'] as $key => $value) {
				$this->setConfiguration($key, $value);
			}
		}
		$cmd_order = 0;
		$link_cmds = array();
		foreach ($device['cmd'] as $command) {
			if (isset($device['cmd']['logicalId'])) {
				continue;
			}
			$cmd = null;
			foreach ($this->getCmd() as $liste_cmd) {
				if (isset($command['name']) && $liste_cmd->getName() == $command['name']) {
					$cmd = $liste_cmd;	
					break;
				}
			}
			try {
				if ($cmd == null || !is_object($cmd)) {
					$cmd = new eibdCmd();
					$cmd->setOrder($cmd_order);
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
					foreach($command['configuration']['option'] as $option => $cmd){
						$CmdValue=cmd::byEqLogicIdCmdName($this->getId(),$cmd);
						if(is_object($CmdValue))
							$options[$option]='#'.$CmdValue->getId().'#';
					}
						$cmd->setConfiguration('option',$options);
				}
				$cmd->save();
				$cmd_order++;
			} catch (Exception $exc) {
				error_log($exc->getMessage());
			}
		$this->setConfiguration('typeTemplate',$template);
		$this->save();
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                                                                                               //
	//                                                      Recherche automatique passerelle                                                       // 
	//                                                                                                                                               //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public static function SearchUsbGateway(){
		/*$bus='0';
		$device='0';
		$config='1';
		$interface='0';
		$cmd="lsusb -v";
		$cmd .= ' >> ' . log::getPathToLog('eibd') . ' 2>&1 &';
		$result=exec($cmd,$result);
		//$UsbGateways = explode("\n", $result);
		$UsbGateways = explode("Bus", $result);
		foreach($UsbGateways as $UsbGateway){
			if(stripos($UsbGateway,"KNX")>0){
				log::add('eibd','debug', 'Passerelle USB trouvé');
				$UsbParametre = explode("\n", trim($UsbGateway));
				$UsbParametre = explode(" ", trim($UsbParametre[0]));
				$bus=$UsbParametre[0];
				$device=$UsbParametre[2];
				log::add('eibd','debug', $bus.':'.$device.':'.$config.':'.$interface);
				return $bus.':'.$device.':'.$config.':'.$interface;
			}
		}
		return false;*/
		$cmd="findknxusb | /bin/sed -e '1 d' -e 's/device //' | /bin/cut -d':' -f1-2";
		$cmd .= ' >> ' . log::getPathToLog('eibd') . ' 2>&1 &';
		return exec($cmd,$result);
	}
	public static function SearchBroadcastGateway(){	
		$result=array();
		$ServerPort=1024;
		$ServerAddr=config::byKey('internalAddr');
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
		$dataBrute='0x';
		foreach (unpack("C*", $hex_msg) as $Byte)
			$dataBrute.=sprintf('%02x',$Byte).' ';
		log::add('eibd', 'debug', 'Data emise: ' . $dataBrute);
		if (!$len = socket_sendto($BroadcastSocket, $hex_msg, strlen($hex_msg), 0, "224.0.23.12", 3671)) {
			$lastError = "socket_sendto() failed: reason: " . socket_strerror(socket_last_error($BroadcastSocket));
			return false;
		}
		$NbLoop=0;
		while(!isset($result['KnxIpGateway'])) { 
			$buf = '';
			socket_recvfrom($BroadcastSocket, $buf , 2048, 0, $name, $port);
			$ReadFrame= unpack("C*", $buf);
			$dataBrute='0x';
			foreach ($ReadFrame as $Byte)
				$dataBrute.=sprintf('%02x',$Byte).' ';
			log::add('eibd', 'debug', 'Data recus: ' . $dataBrute);		
			
			$HeaderSize=array_slice($ReadFrame,0,1)[0];
			$Header=array_slice($ReadFrame,0,$HeaderSize);
			$Body=array_slice($ReadFrame,$HeaderSize);
			switch (array_slice($Header,2,1)[0]){
				case 0x02:
					switch (array_slice($Header,3,1)[0]){
						case 0x02:
							$result['KnxIpGateway'] =	array_slice($Body,2,1)[0]
											.".".	array_slice($Body,3,1)[0]
											.".".	array_slice($Body,4,1)[0]
											.".".	array_slice($Body,5,1)[0];
							$KnxPortGateway =	array_slice($Body,6,2);
							$result['KnxPortGateway'] =$KnxPortGateway[0]<<8|$KnxPortGateway[1];
							$result['IndividualAddressGateWay']=array_slice($Body,12,1)[0]<<8|array_slice($Body,13,1);
							//$result['DeviceName']= self::Hex2String(array_slice($Body,32,4));
						break;
					}
				break;
			}
			if($NbLoop==100){
				$result['KnxIpGateway'] ="";
				$result['KnxPortGateway'] ="";
				$result['IndividualAddressGateWay']="";
				//$result['DeviceName']="";
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
			log::add('eibd', 'error', "Error: Unknown APDU: ".$buf[1]."X".$buf[2]);
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
			log::add('eibd','debug','Valeur du Header '.$buf[2] & 0xC0);
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
				throw new Exception(__("Error: Unknown APDU: ".$buf[1]."X".$buf[2], __FILE__));
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
		}	else {
			$header = 0x0040;
			$data = pack ("n", $header);
			for ($i = 0; $i < count ($val); $i++)
				$data .= pack ("C", $val[$i]);
		}
		$addr = self::gaddrparse ($addr);
		$r = $EibdConnexion->EIBOpenT_Group ($addr, 1);
		if ($r == -1)
			return -1;
		$r = $EibdConnexion->EIBSendAPDU($data);
		if ($r == -1)
			return -1;
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
		$r = $EibdConnexion->EIBOpenT_Group ($addr, 1);
		if ($r == -1)
			return -1;
		$r = $EibdConnexion->EIBSendAPDU($data);
		if ($r == -1)
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
		foreach(eqLogic::byType('eibd') as $Equipement)	{
			if ($Equipement->getIsEnable()){
				foreach($Equipement->getCmd('info') as $Commande)	{
					if ($Commande->getConfiguration('FlagInit')){
						$ga=$Commande->getLogicalId();
						$dpt=$Commande->getConfiguration('KnxObjectType');
						$inverse=$Commande->getConfiguration('inverse');
						log::add('eibd', 'debug',  $Commande->getHumanName().' Lecture de '. $Commande->getHumanName().' sur le GAD '.$ga);
						$DataBus=self::EibdRead($ga);
                      				if($DataBus === false){
							$Commande->setConfiguration('FlagInit',false);
							$Commande->save();
							continue;
						}
						$Option=$Commande->getConfiguration('option');
						$Option["id"]=$Commande->getId();
						$BusValue=Dpt::DptSelectDecode($dpt, $DataBus, $inverse,$Option);
						log::add('eibd', 'debug', $Commande->getHumanName().' => '.$BusValue);
						if ($Commande->execCmd() != $Commande->formatValue($BusValue)) {
							$Commande->event($BusValue);
						}
						$Commande->setCache('collectDate', date('Y-m-d H:i:s'));
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
		while(true) {    
			$src = new EIBAddr;
			$dest = new EIBAddr;
			$len = $conBusMonitor->EIBGetGroup_Src($buf, $src, $dest);
			if ($len != -1 && $len >= 2) {
				$mon = self::parseread($len,$buf);
				$KnxFrameInfo=array(
					"Mode"=>$mon[0],
					"Data"=>$mon[1],
					"AdrSource"=>$src->addr,
					"AdrGroup"=>$dest->addr
					);
				new _BusMonitorTraitement($KnxFrameInfo);
			}
			else
				break;
		}
		$conBusMonitor->EIBClose();		
		log::add('eibd', 'debug', 'Deconnexion a EIBD sur le serveur '.$host.':'.$port);	
	}
	
	public static function addCacheNoGad($_parameter) {
		$cache = cache::byKey('eibd::CreateNewGad');
		$value = json_decode($cache->getValue('[]'), true);
		if($key = array_search($_parameter['AdresseGroupe'], array_column($value, 'AdresseGroupe')) === false)
			$value[] = $_parameter;
		else
			$value[$key] = $_parameter;
		if(count($value) >=255){			
			unset($value[0]);
			array_shift($value);
		}
		cache::set('eibd::CreateNewGad', json_encode($value), 0);
	}
	public static function TransmitValue($_options) 	{
		$Commande = cmd::byId($_option['eibdCmd_id']);
		if (is_object($Commande) && $Commande->getIsEnable()) {
			$Event = cmd::byId($_option['event_id']);
			if(is_object($Event)){
				$ga=$Commande->getLogicalId();
				$dpt=$Commande->getConfiguration('KnxObjectType');
				$inverse=$Commande->getConfiguration('inverse');
				$Option=$Commande->getConfiguration('option');
				$Option["id"]=$Commande->getId();
				$data= Dpt::DptSelectEncode($dpt, $Event->execCmd(), $inverse,$Option);
				$WriteBusValue=eibd::EibdWrite($ga, $data);
			}
		}
	}
	public static function AddEquipement($Name,$_logicalId) 	{
			$Equipement = self::byLogicalId($_logicalId, 'eibd');
			if (is_object($Equipement)) {
				$Equipement->setIsEnable(1);
				$Equipement->save();
			} else {
				$Equipement = new eibd();
				$Equipement->setName($Name);
				$Equipement->setLogicalId($_logicalId);
				$Equipement->setObject_id(null);
				$Equipement->setEqType_name('eibd');
				$Equipement->setIsEnable(1);
				$Equipement->setIsVisible(1);
				$Equipement->save();
			}
			return $Equipement;
		}
	public static function AddCommande($Equipement,$Name,$_logicalId,$Type="info", $Dpt='') {
		$Commande = $Equipement->getCmd(null,$_logicalId);
		if (!is_object($Commande))
		{
			$VerifName=$Name;
			$Commande = new EibdCmd();
			$Commande->setId(null);
			$Commande->setLogicalId($_logicalId);
			$Commande->setEqLogic_id($Equipement->getId());
			$count=0;
			while (is_object(cmd::byEqLogicIdCmdName($Equipement->getId(),$VerifName)))
			{
				$count++;
				$VerifName=$Name.'('.$count.')';
			}
			$Commande->setName($VerifName);
			$Commande->setIsVisible(1);
			$Commande->setType($Type);
			$Commande->setUnite($unite);
			if ($Dpt!=''){
				if($Type=='info')
					$Commande->setSubType(Dpt::getDptInfoType($Dpt));
				else
					$Commande->setSubType(Dpt::getDptActionType($Dpt));
				$Commande->setUnite(Dpt::getDptUnite($Dpt));
				$Commande->setConfiguration('KnxObjectType',$Dpt);
			}
			else{
				if($Type=='info')
					$Commande->setSubType('string');
				else
					$Commande->setSubType('other');
				$Commande->setConfiguration('KnxObjectType','1.xxx');
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
           			if(exec("command -v knxd") !='')
					$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/update-knxd.sh';
				else
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
		return $return;
	}
	public static function deamon_start($_debug = false) {
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') 
			return;
		log::remove('eibd');
		self::deamon_stop();
		$cmd = '';
		switch(config::byKey('KnxSoft', 'eibd')){
			case 'knxd':
				$cmd = 'sudo knxd --daemon=/var/log/knx.log --pid-file=/var/run/knx.pid --eibaddr='.config::byKey('EibdGad', 'eibd').' --client-addrs='.config::byKey('EibdGad', 'eibd').':'.config::byKey('EibdNbAddr', 'eibd').' --Name=JeedomKnx -D -T -S --listen-tcp='.config::byKey('EibdPort', 'eibd').' -b';
			break;
			case 'eibd':
				$cmd = 'sudo eibd --daemon=/var/log/knx.log --pid-file=/var/run/knx.pid --eibaddr='.config::byKey('EibdGad', 'eibd').' -D -T -S --listen-tcp='.config::byKey('EibdPort', 'eibd');			
			break;
		}
		if($cmd != ''){
			switch(config::byKey('TypeKNXgateway', 'eibd')){
				case 'ip':
					$cmd .=' ip:';
				break;
				case 'ipt':
					$cmd .=' ipt:';
				break;
				case 'iptn':
					$cmd .=' iptn:';
				break;
				case 'ft12':
					$cmd .=' ft12:';
				break;
				case 'bcu1':
					$cmd .=' bcu1:';
				break;
				case 'tpuarts':
					$cmd .=' tpuarts:';
				break;
				case 'usb':
					$cmd .=' usb:';
				break;
			}
			$cmd .=config::byKey('KNXgateway', 'eibd');
			$cmd .= ' >> ' . log::getPathToLog('eibd') . ' 2>&1 &';
			exec($cmd);
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
		sleep(2);
		self::InitInformation();
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
			$cmd .= ' >> ' . log::getPathToLog('eibd') . ' 2>&1 &';
			exec($cmd);
		}
		$cron = cron::byClassAndFunction('eibd', 'BusMonitor');
		if (is_object($cron)) {
			$cron->stop();
			$cron->remove();
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                                                                                               //
	//                                                            Gestion du  parser ETS                                                             // 
	//                                                                                                                                               //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
class eibdCmd extends cmd {
	public function preSave() { 
		if($this->getId() != '' && $this->getValue() != '' && $this->getId() == str_replace('#','',$this->getValue()))
			throw new Exception(__('Le retour d\'etat ne peut pas etre lui meme', __FILE__));
		if ($this->getConfiguration('KnxObjectType') == '') 
			throw new Exception(__('Le type de commande ne peut etre vide', __FILE__));
		$this->setLogicalId(trim($this->getLogicalId()));    
	}
	public function postSave() {	
		if($this->getConfiguration('FlagTransmit')){
			$listener = listener::byClassAndFunction('eibd', 'TransmitValue', array('eibdCmd_id' => $this->getId()));
			if (!is_object($listener))
				$listener = new listener();
			$listener->setClass('eibd');
			$listener->setFunction('TransmitValue');
			$listener->setOption(array('eibdCmd_id' => $this->getId()));
			$listener->emptyEvent();
			$ActionValue=cmd::byId(str_replace('#','',$this->getValue()));
			if(is_object($ActionValue)){
				$listener->addEvent($ActionValue->getId());
			}
			$listener->save();	
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
	public function execute($_options = null){
		$ga=$this->getLogicalId();
		$dpt=$this->getConfiguration('KnxObjectType');
		$inverse=$this->getConfiguration('inverse');
		$Option=$this->getConfiguration('option');
		$Option["id"]=$this->getId();
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
						$ActionValue =$this->getConfiguration('KnxObjectValue');
						if (isset($Listener) && is_object($Listener) && $this->getConfiguration('KnxObjectValue') == "") 
							$ActionValue =Dpt::OtherValue($dpt,$Listener->execCmd());
					break;
				}
				log::add('eibd','debug',$this->getHumanName().' Valeur a envoyer '.$ActionValue);
				$data= Dpt::DptSelectEncode($dpt, $ActionValue, $inverse,$Option);
				if($ga != '' && $data !== false){
					$WriteBusValue=eibd::EibdWrite($ga, $data);
					if ($WriteBusValue != -1 && isset($Listener) && is_object($Listener) && $ga==$Listener->getLogicalId()){
						$Listener->event($BusValue);
						$Listener->setCache('collectDate', date('Y-m-d H:i:s'));
					}
				}
			break;
			case 'info':
				$inverse=$this->getConfiguration('inverse');
				log::add('eibd', 'debug',$this->getHumanName().' Lecture sur le bus de l\'adresse de groupe : '. $ga);
				$DataBus=eibd::EibdRead($ga);	
				$BusValue=Dpt::DptSelectDecode($dpt, $DataBus, $inverse,$Option);
				$this->setCollectDate(date('Y-m-d H:i:s'));
				$this->event($BusValue);
				$this->setCache('collectDate', date('Y-m-d H:i:s'));
			break;
		}
	}
	public function UpdateCommande($Mode,$data){	
		$valeur='';
		$unite='';		
		$dpt=$this->getConfiguration('KnxObjectType');
		$inverse=$this->getConfiguration('inverse');
		$Option=$this->getConfiguration('option');
		$Option["id"]=$this->getId();
		if ($dpt!= 'aucun' && $dpt!= ''){
			if($Mode=="Read" && $this->getConfiguration('FlagRead')){
				$ActionData="";
				$ActionValue=cmd::byId(str_replace('#','',$this->getValue()));
				if(is_object($ActionValue)){
					$valeur=$ActionValue->execCmd();
					$data= Dpt::DptSelectEncode($dpt, $valeur, $inverse,$Option);
					eibd::EibdReponse($this->getLogicalId(), $data);
					log::add('eibd', 'debug', $this->getHumanName().' Réponse a la demande de valeur');
				}
			}
			if($Mode=="Write"  || $Mode=="Reponse"){
				log::add('eibd', 'debug',$this->getHumanName().' : Décodage de la valeur avec le DPT :'.$dpt);
				$valeur=Dpt::DptSelectDecode($dpt, $data, $inverse, $Option);
				$unite=Dpt::getDptUnite($dpt);
				if($this->getConfiguration('noBatterieCheck')){
					switch(explode('.',$dpt)[0]){
						case 1 :
							$valeur=$valeur*100;
						break;
					}
					$this->getEqlogic()->batteryStatus($valeur,date('Y-m-d H:i:s'));
				}
				if($this->getType() == 'info'&& ($this->getConfiguration('FlagWrite') || $this->getConfiguration('FlagUpdate'))){
					log::add('eibd', 'info',$this->getHumanName().' : Mise a jours de la valeur : '.$valeur.$unite);
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
		log::add('eibd', 'Info', 'Mise a jours d\'une commande par ses options');
		$dpt=$this->getConfiguration('KnxObjectType');
		$inverse=$this->getConfiguration('inverse');
		$Option=$this->getConfiguration('option');
		$Option["id"]=$this->getId();
		$valeur=Dpt::DptSelectDecode($dpt, null, $inverse, $Option);
		if($this->getType() == 'info'&& ($this->getConfiguration('FlagWrite') || $this->getConfiguration('FlagUpdate'))){
			log::add('eibd', 'info',$this->getHumanName().' : Mise a jours de la valeur : '.$valeur.$unite);
			$this->event($valeur);
			$this->setCache('collectDate', date('Y-m-d H:i:s'));
		}
	}
}
class _BusMonitorTraitement{
	public function __construct($data){
		$monitor=array("Mode"=>$data["Mode"]);
		$monitor['AdresseGroupe']= self::formatgaddr($data["AdrGroup"]);
		$monitor['AdressePhysique']= self::formatiaddr($data["AdrSource"]);
		if(is_array($data["Data"])){
			$monitor['data']='0x ';
			foreach ($data["Data"] as $Byte)
				$monitor['data'].=sprintf(' %02x',$Byte);
			}
		else
			$monitor['data']='0x '.$data["Data"];
		$commandes=cmd::byLogicalId(trim($monitor['AdresseGroupe']));
		if(count($commandes)>0){
			foreach($commandes as $Commande){
				$monitor['valeur']=trim($Commande->UpdateCommande($data["Mode"],$data["Data"]));
				$monitor['cmdJeedom']= $Commande->getHumanName();
				$monitor['DataPointType']=$Commande->getConfiguration('KnxObjectType');
			}
		}else {
			$dpt=Dpt::getDptFromData($data["Data"]);
			if($dpt!=false){
				$monitor['valeur']=Dpt::DptSelectDecode($dpt, $data["Data"]);
				$monitor['DataPointType']=$dpt;
				eibd::addCacheNoGad($monitor);
			}else
				$monitor['valeur']="Impossible de convertire la valeur";
			$monitor['cmdJeedom']= "La commande n'exites pas";
			log::add('eibd', 'debug', 'Aucune commande avec l\'adresse de groupe  '.$monitor['AdresseGroupe'].' n\'a pas été trouvée');
		}
		$monitor['datetime'] = date('d-m-Y H:i:s');
		event::add('eibd::monitor', json_encode($monitor));
		//exit();
	}
	/*public static function UpdateCommande($Commande,$Mode,$data){	
		$valeur='';
		$unite='';
		if (is_object($Commande)) {		
			$dpt=$Commande->getConfiguration('KnxObjectType');
			$inverse=$Commande->getConfiguration('inverse');
			$Option=$Commande->getConfiguration('option');
			$Option["id"]=$Commande->getId();
			if ($dpt!= 'aucun' && $dpt!= ''){
				if($Mode=="Read" && $Commande->getConfiguration('FlagRead')){
					$ActionData="";
					$ActionValue=cmd::byId(str_replace('#','',$Commande->getValue()));
					if(is_object($ActionValue)){
						$valeur=$ActionValue->execCmd();
						$data= Dpt::DptSelectEncode($dpt, $valeur, $inverse,$Option);
						eibd::EibdReponse($Commande->getLogicalId(), $data);
						log::add('eibd', 'debug', $Commande->getHumanName().' Réponse a la demande de valeur');
					}
				}
				if($Mode=="Write"  || $Mode=="Reponse"){
					log::add('eibd', 'debug',$Commande->getHumanName().' : Décodage de la valeur avec le DPT :'.$dpt);
					$valeur=Dpt::DptSelectDecode($dpt, $data, $inverse, $Option);
					$unite=Dpt::getDptUnite($dpt);
					if($Commande->getConfiguration('noBatterieCheck')){
						switch(explode('.',$dpt)[0]){
							case 1 :
								$valeur=$valeur*100;
							break;
						}
						$Commande->getEqlogic()->batteryStatus($valeur,date('Y-m-d H:i:s'));
					}
					if($Commande->getType() == 'info'&& ($Commande->getConfiguration('FlagWrite') || $Commande->getConfiguration('FlagUpdate'))){
						log::add('eibd', 'info',$Commande->getHumanName().' : Mise a jours de la valeur : '.$valeur.$unite);
						$Commande->event($valeur);
						$Commande->setCache('collectDate', date('Y-m-d H:i:s'));
					}
				}
			}else{
				$valeur='Aucun DPT n\'est associé a cette adresse';
			}
		} 
		return $valeur.$unite ;
	}
	public static function UpdateCmdOption($_options) { 
		log::add('eibd', 'Info', 'Mise a jours d\'une commande par ses options');
		$Commande=cmd::byId($_options["id"]);
		if(!is_object($Commande))
			return;
		$dpt=$Commande->getConfiguration('KnxObjectType');
		$inverse=$Commande->getConfiguration('inverse');
		$Option=$Commande->getConfiguration('option');
		$Option["id"]=$Commande->getId();
		$valeur=Dpt::DptSelectDecode($dpt, null, $inverse, $Option);
		if($Commande->getType() == 'info'&& ($Commande->getConfiguration('FlagWrite') || $Commande->getConfiguration('FlagUpdate'))){
			log::add('eibd', 'info',$Commande->getHumanName().' : Mise a jours de la valeur : '.$valeur.$unite);
			$Commande->event($valeur);
			$Commande->setCache('collectDate', date('Y-m-d H:i:s'));
		}
	}*/
	private static function formatiaddr ($addr){
		return sprintf ("%d.%d.%d", ($addr >> 12) & 0x0f, ($addr >> 8) & 0x0f, $addr & 0xff);
	}
	private static function formatgaddr ($addr)	{
		switch(config::byKey('level', 'eibd')){
			case '3':
				return sprintf ("%d/%d/%d", ($addr >> 11) & 0x1f, ($addr >> 8) & 0x07,$addr & 0xff);
			break;
			case '2':
				return sprintf ("%d/%d", ($addr >> 11) & 0x1f,$addr & 0x7ff);
			break;
			case '1':
				return sprintf ("%d", $addr);
			break;
		}
	}
}
?>
