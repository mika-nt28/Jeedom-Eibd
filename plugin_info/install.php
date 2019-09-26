<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
function eibd_install() {
	log::add('eibd','debug','Instalation'); 
}
function eibd_update() {
	log::add('eibd','debug','Lancement du script de mise a jours'); 
	if(exec("command -v eibd") !='')
		config::save('KnxSoft', 'eibd','eibd');
	else{
		exec("sudo systemctl stop knxd.service");
		exec("sudo systemctl stop knxd.socket"); 
		exec("sudo systemctl disable knxd.service");
		exec("sudo systemctl disable knxd.socket"); 
	}
	if(config::byKey('KnxSoft', 'eibd') == 'eibd')
		exec('sudo usermod -a -G www-data eibd');
	if(config::byKey('KnxSoft', 'eibd') == 'knxd')
		exec('sudo usermod -a -G www-data knxd');
	while(is_object($listener=listener::byClassAndFunction('eibd', 'TransmitValue')))
		$listener->remove();
	foreach(eqLogic::byType('eibd') as $eqLogic){
		switch($eqLogic->getConfiguration('typeTemplate')){
				
			case 'bso':
				$eqLogic->setConfiguration('typeTemplate','shutter_BSO');
			break;
			case 'thermostat_ver':
				$eqLogic->setConfiguration('typeTemplate','thermostat_verrou');
			break;
			case 'dimmerRGB':
				$eqLogic->setConfiguration('typeTemplate','light_dimmer_rgb');
			break;
			case 'RGBW':
				$eqLogic->setConfiguration('typeTemplate','light_dimmer_rgb');
			break;
			case 'dimmer':
				$eqLogic->setConfiguration('typeTemplate','light_dimmer');
			break;
		}
		$eqLogic->save();
	}
	log::add('eibd','debug','Fin du script de mise a jours'); 
}
function eibd_remove() {
	while(is_object($listener=listener::byClassAndFunction('eibd', 'TransmitValue')))
		$listener->remove();
}
?>
