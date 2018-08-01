<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'eibd');
$eqLogics = eqLogic::byType('eibd');
?>
<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default btn-sm tooltips BusMoniteur" title="Bus Moniteur" style="width : 30%">
			<i class="fa fa-archive"></i>
		</a>
                <a class="btn btn-default btn-sm tooltips Ets4Parser " title="Export ETS4" style="width : 30%">
			<i class="fa fa-spinner"></i>
		</a>
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add">
			<i class="fa fa-plus-circle"></i> {{Ajouter un équipement}}</a>
                <li class="filter" style="margin-bottom: 5px;">
			<input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/>
		</li>
                <?php
                foreach (eqLogic::byType('eibd') as $eqLogic) {
					echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
		<legend>{{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-plus-circle" style="font-size : 5em;color:#406E88;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#406E88"><center>{{Ajouter}}</center></span>
			</div>
			<div class="cursor eqLogicAction" data-action="addByTemplate" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-plus-circle" style="font-size : 5em;color:#406E88;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#406E88"><center>{{Par template}}</center></span>
			</div>
			<?php
				if(config::byKey('isInclude','eibd')){
			 ?>
			<div class="cursor Include" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-spinner fa-pulse" style="font-size : 5em;color:#406E88;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#406E88"><center>{{Désactiver l'inculsion}}</center></span>
			</div>
			<?php
				}else{
			 ?>
			<div class="cursor NotInculde" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-bullseye" style="font-size : 5em;color:#406E88;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#406E88"><center>{{Activer l'inculsion}}</center></span>
			</div>
			<?php
				}
			 ?>
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="height: 120px; margin-bottom: 10px; padding: 5px; border-radius: 2px; width: 160px; margin-left: 10px; position: absolute; left: 170px; top: 0px; background-color: rgb(255, 255, 255);">
				<center>
			      		<i class="fa fa-wrench" style="font-size : 5em;color:#767676;"></i>
			    	</center>
			    	<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>Configuration</center></span>
			</div>
			<div class="cursor BusMoniteur" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-archive" style="font-size : 5em;color:#767676;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Moniteur de bus}}</center></span>
			</div>
			<div class="cursor Ets4Parser" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-spinner" style="font-size : 5em;color:#767676;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Import ETS4}}</center></span>
			</div>
			<div class="cursor log" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-heartbeat" style="font-size : 5em;color:#767676;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Information sur le logiciel EIBD/KNXD}}</center></span>
			</div>
			<div class="cursor" id="bt_healthEibd" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center>
					<i class="fa fa-medkit" style="font-size : 6em;color:#767676;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Santé}}</center></span>
			</div>
		</div>
     	   	<legend>{{Mes Modules KNX}}</legend>
		<input class="form-control" placeholder="{{Rechercher}}" style="margin-bottom:4px;" id="in_searchEqlogic" />
		<div class="eqLogicThumbnailContainer">
			<?php
			if (count($eqLogics) == 0) {
				echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Vous n'avez pas encore de module KNX, cliquez sur Ajouter pour commencer}}</span></center>";
			} else {
				foreach ($eqLogics as $eqLogic) {
					$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
					echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
					echo "<center>";
					$file='plugins/eibd/core/config/devices/'.$eqLogic->getConfiguration('typeTemplate').'.png';
					if(file_exists($file))
						echo '<img src="'.$file.'" height="105" width="95" />';
					else
						echo '<img src="plugins/eibd/plugin_info/eibd_icon.png" height="105" width="95" />';
					echo "</center>";
					echo '<span class="name" style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
					echo '</div>';
				}
			} 
			?>
		</div>
    </div>
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
	<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> Sauvegarder</a>
	<a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> Supprimer</a>
	<a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> Configuration avancée</a>
	<a class="btn btn-default eqLogicAction pull-right expertModeVisible " data-action="copy"><i class="fa fa-copy"></i>{{Dupliquer}}</a>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation">
			<a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay">
				<i class="fa fa-arrow-circle-left"></i>
			</a>
		</li>
		<li role="presentation" class="active">
			<a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">
				<i class="fa fa-tachometer"></i> Equipement</a>
		</li>
		<li role="presentation" class="">
			<a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">
				<i class="fa fa-list-alt"></i> Commandes</a>
		</li>
	</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<br>
				<form class="form-horizontal">
					<fieldset>			
						<div class="form-group">
							<label class="col-md-2 control-label">
								{{Nom de l'équipement KNX}}
								<sup>
									<i class="fa fa-question-circle tooltips" title="Indiquez le nom de votre équipement" style="font-size : 1em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-md-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement KNX}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label ">{{Adresse Physique de l'équipement}}
								<sup>
									<i class="fa fa-question-circle tooltips" title="Indiquez l'adresse physique de votre équipement. Cette information n'est pas obigatoire mais peut etre utile dans certain cas. Pour la trouver, il faut la retrouver sur le logiciel ETS" style="font-size : 1em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-md-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="logicalId"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label" >
								{{Objet parent}}
								<sup>
									<i class="fa fa-question-circle tooltips" title="Indiquez l'objet dans lequel le widget de cette equipement apparaiterai sur le dashboard" style="font-size : 1em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-md-3">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<?php
									foreach (object::all() as $object) {
										echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">
								{{Catégorie}}
								<sup>
									<i class="fa fa-question-circle tooltips" title="Choisissez une catégorie
								Cette information n'est pas obigatoire mais peut etre utile pour filtrer les widget" style="font-size : 1em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-md-8">
								<?php
								foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
									echo '<label class="checkbox-inline">';
									echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
									echo '</label>';
								}
								?>

							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" >
								{{Etat du widget}}
								<sup>
									<i class="fa fa-question-circle tooltips" title="Choisissez les options de visibilité et d'activation
								Si l'equipement n'est pas activé il ne sera pas utilisable dans jeedom, mais visible sur le dashboard
								Si l'equipement n'est pas visible il ne sera caché sur le dashbord, mais utilisable dans jeedom" style="font-size : 1em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-sm-9">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
							</div>
						</div>
						<div class="form-group expertModeVisible">
							<label class="col-md-2 control-label">
								{{Délai max entre 2 messages}}
								<sup>
									<i class="fa fa-question-circle tooltips" title="Cette information est optionnel et permet de définir si un équipement est fonctionnel ou non. Il ne peut etre utilisé que si votre équipement envoi régulierement des information (sonde de température, horloge, ...)" style="font-size : 1em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-md-3">
								<input class="eqLogicAttr form-control" data-l1key="timeout" placeholder="Délai maximum autorisé entre 2 messages (en mn)"/>
							</div>
						</div>
					</fieldset> 
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<br>
				<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add"><i class="fa fa-plus-circle"></i> Ajouter une commande</a>
				<div class="pull-right" >
					<select class="eqLogicAttr input-sm" data-l1key="configuration" data-l2key="typeTemplate">
						<option value="">{{Aucun}}</option>
						<?php
						foreach (eibd::devicesParameters() as $id => $info) {
							echo '<option value="' . $id . '">' . $info['name'] . '</option>';
						}
						?>
					</select>
					<sup>
						<i class="fa fa-question-circle tooltips" title="Choisissez si vous le souhaité un model de configuration d'équipement" style="font-size : 1em;color:grey;"></i>
					</sup>
					<a class="btn btn-success btn-sm Template" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter le template}}</a>
				</div>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th ></th>
							<th>Nom</th>
							<th>Configuration KNX</th>
							<th>Flag</th>
							<th>Valeur</th>
							<th>Paramètre</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php 
include_file('desktop', 'event', 'js', 'eibd');
include_file('desktop', 'eibd', 'js', 'eibd');
include_file('core', 'plugin.template', 'js'); 
?>
