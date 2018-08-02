<?php
if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
sendVarToJS('template',eibd::devicesParameters());
?>
<div class="row">
	<div class="col-md-12">
		<center>
			<span style="font-size : 12px;" >Equipement : </span> 
			<strong class="equipement"></strong>
		</center>
		<center>
			<span style="font-size : 12px;" >Source : </span> 
			<strong class="source EqLogicTemplateAttr" data-l1key="logicalId"></strong>
		</center>
		<center>
			<span style="font-size : 12px;" >Commande : </span> 
			<strong class="cmd"></strong>
		</center>
		<center>
			<span style="font-size : 12px;" >Data Point Type : </span> 
			<strong class="dpt"></strong>
		</center>
		<center>
			<span style="font-size : 12px;" >Destination : </span> 
			<strong class="destination"></strong>
		</center>
		<center>
			<span style="font-size : 12px;" >Valeur : </span> 
			<strong class="valeur"></strong>
		</center>
	</div>
	<div class="col-md-12">
		<form class="form-horizontal" onsubmit="return false;">
			<div class="form-group">
				<label class="col-xs-5 control-label" >{{Nom de votre équipement}}</label>
			</div>
			<div class="col-xs-7">
				<select class="actionIncludeGad">
					<option value="template">{{Ajouter a un template}}</option>
					<option value="equipement">{{Ajouter a un equipement}}</option>
					<option value="save">{{Enregister pour plus tard}}</option>
				</select>
			</div>
		</form>
	</div>
</div>
<script>
	if (typeof(value.DeviceName) !== 'undefined') 
		$('.equipement').text(value.DeviceName);
	$('.source').text(value.AdressePhysique);
	if (typeof(value.cmdName) !== 'undefined') 
		$('.cmd').text(value.cmdName);
	$('.destination').text(value.AdresseGroupe);
	$('.dpt').text(value.DataPointType);
	$('.valeur').text(value.valeur);
	$('body').off().on('change','.actionIncludeGad', function(){
		switch($(this).val()){
			case 'template':
				var addTemplate=$('<div class="form-group">')
						.append($('<label class="col-xs-5 control-label" >')
							.text('{{Nom de votre équipement}}'))
						.append($('<div class="col-xs-7">')
							.append($('<input class="EqLogicTemplateAttr form-control" data-l1key="name"/>')));
				addTemplate.append($('<div class="form-group">')
						.append($('<label class="col-xs-5 control-label" >')
							.text('{{Objet parent}}'))
						.append($('<div class="col-xs-7">')
							.append($('<select class="EqLogicTemplateAttr form-control" data-l1key="object_id">')
								.append($('.eqLogicAttr[data-l1key=object_id] option').clone()))));
				addTemplate.append($('<div class="form-group">')
						.append($('<label class="col-xs-5 control-label" >')
							.text('{{Template de votre équipement}}'))
						.append($('<div class="col-xs-3">')
							.append($('<select class="EqLogicTemplateAttr form-control" data-l1key="template">')
								   .append($('<option>')
									.text('{{Séléctionner un template}}')))));
				addTemplate.append($('<div class="form-group">')
						.append($('<label class="col-xs-5 control-label" >')
							.text('{{Choisir la commande}}'))
						.append($('<div class="col-xs-3">')
							.append($('<select class="EqLogicTemplateAttr form-control" data-l1key="cmd">')
								   .append($('<option>')
									.text('{{Séléctionner une commande}}')))));	
				$.each(template,function(index, value){
					addTemplate.find('.EqLogicTemplateAttr[data-l1key=template]')
						.append($('<option value="'+index+'">')
							.text(value.name))
				});
				$(this).closest('.form-horizontal').append(addTemplate);
				$('body').on('change','.EqLogicTemplateAttr[data-l1key=template]', function () {
					$(this).closest('.form-horizontal').find('.EqLogicTemplateAttr[data-l1key=cmd]').html('');
					$.each(template[$(this).value()].cmd,function(index, value){
						$('.EqLogicTemplateAttr[data-l1key=cmd]').append($('<option value="'+index+'">').text(value.name));
					});
				});
				//afficher un select avec les template deja configurer
			break;
			case 'equipement':
				//afficher la liste des equipement dans un select (proposer un ajout)
				//afficher la liste des commande de cette equipement dans un select (proposer un ajout)
			break;
		}
	})
</script>	
