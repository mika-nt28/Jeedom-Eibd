<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
function eibd_install() {
	log::add('eibd','debug','Instalation'); 
}
function eibd_update() {
	log::add('eibd','debug','Lancement du scripte de mise a  jours'); 
	while(is_object($listener=listener::byClassAndFunction('eibd', 'TransmitValue')))
		$listener->remove();
}
function eibd_remove() {
	while(is_object($listener=listener::byClassAndFunction('eibd', 'TransmitValue')))
		$listener->remove();
}
?>
