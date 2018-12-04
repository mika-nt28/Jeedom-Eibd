<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
//if(!isset($_REQUEST['SelectAddr']))
	echo '<script>var SelectAddr="'.$_REQUEST['SelectAddr'].'";</script>';
//if(!isset($_REQUEST['SelectDpt']))
	echo '<script>var SelectDpt="'.preg_replace('/.[0-9]+/', '.', $_REQUEST['SelectDpt']).'";</script>';
?>
<style>
	table #table_GadInconue {
	    width: 100%;
	    display:block;
	}
	thead #table_GadInconue {
	    display: inline-block;
	    width: 100%;
	}
	tbody #table_GadInconue {
	    height: 200px;
	    display: inline-block;
	    width: 100%;
	    overflow: auto;
	}
</style>
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active">
		<a href="#InconueTab" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">
			<i class="fa fa-tachometer"></i> {{Inconnue}}</a>
	</li>
	<li role="presentation" class="">
		<a href="#DeviceTab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">
			<i class="fa fa-list-alt"></i> {{Equipement}}</a>
	</li>
	<li role="presentation" class="">
		<a href="#AdressTab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">
			<i class="fa fa-list-alt"></i> {{Adresse de groupes}}</a>
	</li>
</ul>
<div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="InconueTab">
		
		<span class="pull-right">
			<a class="btn btn-danger btn-xs pull-right removeAllGad" style="margin-bottom : 5px;">
				<i class="fa fa-trash-o"></i>
				{{ Supprimer}}
			</a>
		</span>
		<span class="pull-right">
			<a class="btn btn-warning btn-xs pull-right Include" data-validation="true" style="margin-bottom : 5px;" >
				<i class="fa fa-spinner fa-pulse"></i>
				{{Désactiver l'inculsion}}
			</a> 
		</span>
		<table id="table_GadInconue" class="table table-bordered table-condensed tablesorter GadInsert" height="80%>
			<thead>
				<tr>
					<th>{{Source}}</th>
					<th>{{Destination}}</th>
					<th>{{Data Point Type}}</th>
					<th>{{Derniere valeur}}</th>
					<th>{{Action sur cette adresse de groupe}}</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<div role="tabpanel" class="tab-pane" id="DeviceTab">
		<span class="pull-right">
			<a class="btn btn-warning btn-xs Ets4Parser" >
				<i class="fa fa-cloud-upload"></i>
				{{Importer projet KNX}}
			</a> 
		</span>
		<table id="table_Devices" class="table table-bordered table-condensed tablesorter GadInsert" height="80%>
			<thead>
				<tr>
					<th>{{Equipement}}</th>
					<th>{{Source}}</th>
					<th>{{Commande}}</th>
					<th>{{Destination}}</th>
					<th>{{Data Point Type}}</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<div role="tabpanel" class="tab-pane" id="AdressTab">
		<span class="pull-right">
			<a class="btn btn-warning btn-xs Ets4Parser" >
				<i class="fa fa-cloud-upload"></i>
				{{Importer projet KNX}}
			</a> 
		</span>
		<ul class="GadSortable ui-sortable"></ul>
	</div>
</div>

<script>
jeedom.config.load({
	configuration: 'isInclude',
	plugin:'eibd',
	error: function (error) {
		$('#div_alert').showAlert({message: error.message, level: 'danger'});
	},
	success: function (data) {
		$('.Include').attr('data-validation',data);
		if(data == "true"){
			$('.Include').html($('<i class="fa fa-spinner fa-pulse">'))
				.append(' {{Désactiver l\'inculsion}}');
		}else{
			$('.Include').html($('<i class="fa fa-bullseye">'))
				.append(' {{Activer  l\'inculsion}}');
		}
	}
});
$('body').off().on('click','.Include', function () {
	if($(this).attr('data-validation') == "true"){
		$(this).attr('data-validation',false);
		$(this).html($('<i class="fa fa-bullseye">'))
			.append(' {{Activer  l\'inculsion}}');
	}else{
		$(this).attr('data-validation',true);
		$(this).html($('<i class="fa fa-spinner fa-pulse">'))
			.append(' {{Désactiver l\'inculsion}}');
	}
	jeedom.config.save({
		configuration: {'isInclude':$(this).attr('data-validation')},
		plugin:'eibd',
		error: function (error) {
			$('#div_alert').showAlert({message: error.message, level: 'danger'});
		},
		success: function () {
		}
	});
});
$('.Ets4Parser').on('click', function() {
	bootbox.dialog({
		title: "{{Importer votre projet KNX}}",
		height: "800px",
		width: "auto",
		message: $('<div>').load('index.php?v=d&modal=eibd.EtsParser&plugin=eibd&type=eibd'),
		buttons: {
			"Annuler": {
				className: "btn-default",
				callback: function () {
					//el.atCaret('insert', result.human);
				}
			},
			success: {
				label: "Valider",
				className: "btn-primary",
				callback: function () {
					$.ajax({
						type: 'POST',   
						url: 'plugins/eibd/core/ajax/eibd.ajax.php',
						data:
						{
							action: 'AnalyseEtsProj',
							option: $('body .EtsParserDiv').getValues('.EtsParseParameter')
						},
						dataType: 'json',
						global: true,
						error: function(request, status, error) {},
						success: function(data) {
							if($('body .EtsParserDiv .EtsParseParameter[data-l1key=createEqLogic]')){
								window.location.reload();
							}else{
								UpdateDeviceTable(data.result.Devices)
								UpdateGadArbo(data.result.GAD)
							}
						}
					});
				}
			},
		}
	});
});
var SelectGad='';
initTableSorter();
$("#table_GadInconue .tablesorter-filter[data-column=2]").val(SelectDpt);
$("#table_GadInconue .tablesorter-filter[data-column=0]").val(SelectAddr);
getKnxGadInconue();
function getKnxGadInconue () {
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data: {
			action: 'getCacheGadInconue',
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
			setTimeout(function() {
				getKnxGadInconue()
			}, 100);
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#table_GadInconue tbody').html('');
			jQuery.each(jQuery.parseJSON(data.result),function(key, value) {
				var tr=$("<tr>");
				tr.append($("<td class='AdressePhysique'>").text(value.AdressePhysique));
				tr.append($("<td class='AdresseGroupe'>").text(value.AdresseGroupe));
				tr.append($("<td class='DataPointType'>").text(value.DataPointType));
				tr.append($("<td class='valeur'>").text(value.valeur));
				tr.append($("<td>")
					.append($('<a class="btn btn-danger btn-xs Gad pull-right" data-action="remove">')
						.append($('<i class="fa fa-minus-circle">'))
						.text('{{Supprimer}}')));
			      	$('#table_GadInconue tbody').append(tr);
			});				
			$('#table_GadInconue').trigger('update');
			$("#table_GadInconue .tablesorter-filter[data-column=0]").trigger('keyup');
			$("#table_GadInconue .tablesorter-filter[data-column=2]").trigger('keyup');
			if ($('#md_modal').dialog('isOpen') === true) {
				setTimeout(function() {
					getKnxGadInconue()
				}, 10000);
			}
		}
	});
}
$("#table_Devices .tablesorter-filter[data-column=1]").val(SelectAddr);
$("#table_Devices .tablesorter-filter[data-column=4]").val(SelectDpt);
getEtsProj();
function getEtsProj () {
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data: {
			action: 'getEtsProj',
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
			getEtsProj()
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			if (data.result == false) 
				return;
			UpdateDeviceTable(data.result.Devices);
			UpdateGadArbo(data.result.GAD);
		}
	});
}
$('body').on('click', '.Gad[data-action=remove]', function(){
	var gad=$(this).closest('tr').find('.AdresseGroupe').text();
	removeInCache(gad);
	$(this).closest('tr').remove();
});
$('body').on('click', '.removeAllGad', function(){
	removeInCache('');
	$('#table_GadInconue tbody').html("");
});
$('body').on('click', '.GadInsert tbody tr', function(){
	$('.cmdSortable .gad').css('font-weight','unset');
	$('.GadInsert tr').css('font-weight','unset');
	$(this).closest('tr').css('font-weight','bold');
	SelectGad = $(this).closest('tr').find('.AdresseGroupe').text();
	SelectAddr=$(this).closest('tr').find('.DataPointType').text();
});
$('body').on('click', '.cmdSortable .gad', function(){
	$('.cmdSortable .gad').css('font-weight','unset');
	$('.GadInsert tr').css('font-weight','unset');
	$(this).css('font-weight','bold');
	SelectGad=$(this).attr('data-AdresseGroupe');
});
function removeInCache(gad){
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data: {
			action: 'setCacheGadInconue',
			gad:gad,
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
		}
	});
}

function UpdateDeviceTable(Devices){	
	$('#table_Devices tbody').html('');
	jQuery.each(Devices,function(EquipementId, Equipement) {
		jQuery.each(Equipement.Cmd,function(CmdId, Cmd) {
			var tr=$("<tr>");
			if (typeof(Equipement.DeviceName) !== 'undefined') 
				tr.append($("<td class='DeviceName'>").text(Equipement.DeviceName));
			else
				tr.append($("<td class='DeviceName'>"));
			tr.append($("<td class='AdressePhysique'>").text(Equipement.AdressePhysique));
			if (typeof(Cmd.cmdName) !== 'undefined') 
				tr.append($("<td class='cmdName'>").text(Cmd.cmdName));
			else
				tr.append($("<td class='cmdName'>"));
			tr.append($("<td class='AdresseGroupe'>").text(Cmd.AdresseGroupe));
			tr.append($("<td class='DataPointType'>").text(Cmd.DataPointType));
			$('#table_Devices tbody').append(tr);
		});				
	});				
	$('#table_Devices').trigger('update');
	$("#table_Devices .tablesorter-filter[data-column=1]").trigger('keyup');
	$("#table_Devices .tablesorter-filter[data-column=4]").trigger('keyup');
}
function UpdateGadArbo(GAD){	
	$('.GadSortable').html('');
	jQuery.each(GAD,function(Niveau1, Groups1) {
		var n1 =$('<ul class="cmdSortable ui-sortable">');
		if(typeof(Groups1) == 'object'){
			jQuery.each(Groups1,function(Niveau2, Groups2) {
				var n2 =$('<ul class="cmdSortable ui-sortable">');
				if(typeof(Groups2) == 'object'){
					jQuery.each(Groups2,function(Niveau3, Parameter) {
						n2.append($('<li class="cursor ui-sortable-handle gad" data-AdresseGroupe="'+Parameter.AdresseGroupe+'" data-DataPointType="'+Parameter.DataPointType+'">').text(' (' + Parameter.AdresseGroupe + ')'+Niveau3));
					});	
				}
				n1.append($('<li class="cursor ui-sortable-handle">').text(Niveau2).append(n2));
			});	
		}
		$('.GadSortable').append($('<li class="cursor ui-sortable-handle">').text(Niveau1).append(n1));
	});	
}	
</script>
