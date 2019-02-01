<?php
try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    	include_file('core', 'authentification', 'php');
	include_file('core', 'dpt', 'class', 'eibd');
	include_file('core', 'knxproj', 'class', 'eibd');

    	if (!isConnect('admin')) {
        	throw new Exception(__('401 - Accès non autorisé', __FILE__));
    	}
	if (init('action') == 'setIsInclude') {
		ajax::success(cache::set('eibd::isInclude',init('value'), 0));
	}
	if (init('action') == 'getIsInclude') {
		ajax::success(cache::byKey('eibd::isInclude')->getValue(false));
	}
	if (init('action') == 'getLog') {
		ajax::success("<pre>".file_get_contents('/var/log/knx.log')."</pre>");
	}
	if (init('action') == 'SearchGatway') {
		switch(init('type')){
			case 'ip':
				$result=eibd::SearchBroadcastGateway();
				ajax::success($result['KnxIpGateway']);
			break;
			case 'ipt':
				$result=eibd::SearchBroadcastGateway();
				ajax::success($result['KnxIpGateway'].':'.$result['KnxPortGateway']);
			break;
			case 'iptn':
				$result=eibd::SearchBroadcastGateway();
				ajax::success($result['KnxIpGateway'].':'.$result['KnxPortGateway']);
			break;
			/*case 'ft12':
			break;
			case 'bcu1':
			break;
			case 'tpuarts':
			break;*/
			case 'usb':
				$result=eibd::SearchUsbGateway();
				ajax::success($result);
			break;
			default:
				ajax::success(false);
			break;
		}
	}
	if (init('action') == 'Read') {
		$Commande=cmd::byLogicalId(init('Gad'))[0];
		if (is_object($Commande)){
			$ga=$Commande->getLogicalId();
			$dpt=$Commande->getConfiguration('KnxObjectType');
			$inverse=$Commande->getConfiguration('inverse');
			log::add('eibd', 'debug', 'Lecture sur le bus de l\'adresse de groupe : '. $ga);
			$DataBus=eibd::EibdRead($ga);	
			$option=null;
			if ($dpt == '235.001')
				{
				$option=array(
					"Tarif"=>$Commande->getConfiguration('option1'),
					"validityTarif"=>$Commande->getConfiguration('option2'),
					"validityActiveElectricalEnergy"=>$Commande->getConfiguration('option3')
					);
				}
			$BusValue=Dpt::DptSelectDecode($dpt, $DataBus, $inverse,$option);
			$Commande->setCollectDate(date('Y-m-d H:i:s'));
			//$Commande->setConfiguration('doNotRepeatEvent', 1);
			$Commande->event($BusValue);
			$Commande->save();
			ajax::success($BusValue);
			//ajax::success(true);
		}
		ajax::success(false);
	}
	if (init('action') == 'getCacheGadInconue') {
		ajax::success(cache::byKey('eibd::CreateNewGad')->getValue('[]'));
	}
	if (init('action') == 'setCacheGadInconue') {
		if(init('gad') == ""){
			cache::set('eibd::CreateNewGad', '[]', 0);
		}else{
			$cache = cache::byKey('eibd::CreateNewGad');
			$value = json_decode($cache->getValue('[]'), true);
			foreach ($value as $key => $val) {
			       if ($val['AdresseGroupe'] == init('gad')){
				       unset($value[$key]);
				       array_shift($value);
			       }
			}
			cache::set('eibd::CreateNewGad', json_encode($value), 0);
		}
		ajax::success('');
	}
	if (init('action') == 'EtsParser') {
		if(isset($_FILES['Knxproj'])){ 
			$uploaddir = '/tmp/KnxProj/';
			if (!is_dir($uploaddir)) 
				mkdir($uploaddir);
			$uploadfile = $uploaddir.basename($_FILES['Knxproj']['name']);
			$ext = pathinfo($_FILES['Knxproj']['name'], PATHINFO_EXTENSION);
			if(move_uploaded_file($_FILES['Knxproj']['tmp_name'], $uploadfile)){
				if($ext == 'gz')
					knxproj::ExtractTX100ProjectFile($uploadfile);
				else
					knxproj::ExtractETSProjectFile($uploadfile);
				ajax::success(true);
			}else
				ajax::success(false);
		}
	}
	if (init('action') == 'AnalyseEtsProj') {
		$knxproj=new knxproj(init('option'));
		$knxproj->WriteJsonProj();
		ajax::success(json_decode($knxproj->getAll(),true));
	}
	if (init('action') == 'getEtsProj') {
		$filename=dirname(__FILE__) . '/../config/KnxProj.json';
		if (file_exists($filename))
			ajax::success(json_decode(file_get_contents($filename),true));
		ajax::success(false);
	}
  	if (init('action') == 'getTemplate') {
		ajax::success(eibd::devicesParameters()[init('template')]);
	}
  	if (init('action') == 'AppliTemplate') {
		$EqLogic=eqLogic::byId(init('id'));
		if (is_object($EqLogic)){
			$EqLogic->applyModuleConfiguration(init('template'));
		}
		ajax::success(true);
	}
   throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
