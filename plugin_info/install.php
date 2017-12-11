<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
function eibd_install() {
	log::add('eibd','debug','Instalation'); 
}
function eibd_update() {
	log::add('eibd','debug','Lancement du scripte de mise a  jours des Flags'); 
	/*foreach(eqLogic::byType('eibd') as $eqLogic){ 
		foreach($eqLogic->getCmd('info') as $cmd){ 
			if(is_object($cmd)){
				if($cmd->getConfiguration('FlagWrite','NoExist') == 'NoExist' && $cmd->getConfiguration('FlagUpdate','NoExist') == 'NoExist'){ 
					log::add('eibd','debug','Remplacement du Flags eventOnly  par FlagWrite et FlagUpdate sur la commande '.$cmd->getHumanName()); 
					$cmd->setConfiguration('FlagWrite',$cmd->getConfiguration('eventOnly')); 
					$cmd->setConfiguration('FlagUpdate',$cmd->getConfiguration('eventOnly')); 
				} 
				if($cmd->getConfiguration('FlagInit','NoExist') == 'NoExist'){ 
					log::add('eibd','debug','Remplacement du Flags init  par FlagInit sur la commande '.$cmd->getHumanName()); 
					$cmd->setConfiguration('FlagInit',$cmd->getConfiguration('init')); 
				} 
				if($cmd->getConfiguration('FlagRead','NoExist') == 'NoExist'){ 
					log::add('eibd','debug','Remplacement du Flags transmitReponse par FlagRead sur la commande '.$cmd->getHumanName()); 
					$cmd->setConfiguration('FlagRead',$cmd->getConfiguration('transmitReponse')); 
					$cmd->setValue($cmd->getConfiguration('ObjetTransmit')); 
				} 
				$cmd->save(); 
			}
		}
	}*/
}
?>
