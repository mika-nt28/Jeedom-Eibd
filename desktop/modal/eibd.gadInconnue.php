<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
include_file('desktop', 'ETSparse', 'js', 'eibd');
sendVarToJS('templates',eibd::devicesParameters());
if(isset($_REQUEST['SelectAddr']))
	echo '<script>var SelectAddr="'.$_REQUEST['SelectAddr'].'";</script>';
else
	echo '<script>var SelectAddr="";</script>';
if(isset($_REQUEST['SelectDpt']))
	echo '<script>var SelectDpt="'.str_replace("XXX","",$_REQUEST['SelectDpt']).'";</script>';
else
	echo '<script>var SelectDpt="";</script>';
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
	<li role="presentation" class="">
		<a href="#LocationsTab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">
			<i class="fa fa-list-alt"></i> {{Localisation}}</a>
	</li>
</ul>
<div class="tab-content" style="height: 500px;overflow: auto;">
	<div role="tabpanel" class="tab-pane active" id="InconueTab">
		<div class="col-xs-12 input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">roundedRight
				<a class="btn btn-danger btn-xs roundedRight removeAllGad" style="margin-bottom : 5px;">
					<i class="fa fa-trash-o"></i>
					{{ Supprimer}}
				</a>
				<a class="btn btn-warning btn-xs roundedRight Include" data-validation=true style="margin-bottom : 5px;" ></a> 
			</span>
		</div>	
		<table id="table_GadInconue" class="table table-bordered table-condensed tablesorter GadInsert">
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
		<div class="col-xs-12 input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-warning btn-xs roundedRight Ets4Parser" >
					<i class="fa fa-cloud-upload"></i>
					{{Importer projet KNX}}
				</a> 
			</span>
		</div>
		<ul class="MyDeviceGroup"></ul>
	</div>
	<div role="tabpanel" class="tab-pane" id="AdressTab">
		<div class="col-xs-12 input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-warning btn-xs roundedRight Ets4Parser" >
					<i class="fa fa-cloud-upload"></i>
					{{Importer projet KNX}}
				</a> 
			</span>
		</div>
		<ul class="MyAdressGroup"></ul>
	</div>
	<div role="tabpanel" class="tab-pane" id="LocationsTab">
		<div class="col-xs-12 input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-warning btn-xs roundedRight Ets4Parser" >
					<i class="fa fa-cloud-upload"></i>
					{{Importer projet KNX}}
				</a> 
			</span>
		</div>
		<ul class="MyLocationsGroup"></ul>
	</div>
</div>
<script>
var KnxGadInconueRefresh = null;
$.ajax({
	type: 'POST',
	async: false,
	url: 'plugins/eibd/core/ajax/eibd.ajax.php',
	data: {
		action: 'getIsInclude',
	},
	dataType: 'json',
	global: false,
	error: function(request, status, error) {
	},
	success: function(data) {
		if (data.state != 'ok') {
			$('#div_alert').showAlert({message: data.result, level: 'danger'});
			return;
		}
		if(data.result == "false"){
			$('.Include').attr('data-validation',"true");
			$('.Include').html($('<i class="fa fa-bullseye">'))
				.append(' {{Activer  l\'inculsion}}');
		}else{
			$('.Include').attr('data-validation',"false");
			$('.Include').html($('<i class="fa fa-spinner fa-pulse">'))
				.append(' {{Désactiver l\'inculsion}}');
		}
	}
});
$('.Include').off().on('click', function () {
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data: {
			action: 'setIsInclude',
			value: $('.Include').attr('data-validation')
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}			
			if($('.Include').attr('data-validation') == "false"){
				$('.Include').attr('data-validation',"true");
				$('.Include').html($('<i class="fa fa-bullseye">'))
					.append(' {{Activer  l\'inculsion}}');
			}else{
				$('.Include').attr('data-validation',"false");
				$('.Include').html($('<i class="fa fa-spinner fa-pulse">'))
					.append(' {{Désactiver l\'inculsion}}');
			}
		}
	});
});
$('.Ets4Parser').off().on('click', function() {
	ImportEts(false);
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
			KnxGadInconueRefresh=setTimeout(function() {
				getKnxGadInconue()
			}, 10000);
		}
	});
}
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
			CreateArboressance(data.result.Devices,$('.MyDeviceGroup'),true);
			CreateArboressance(data.result.GAD,$('.MyAdressGroup'),true);
			CreateArboressance(data.result.Locations,$('.MyLocationsGroup'),true);
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
	$('.AdresseGroupe').css('font-weight','unset');
	$('.GadInsert tr').css('font-weight','unset');
	$(this).closest('tr').css('font-weight','bold');
	SelectGad = $(this).closest('tr').find('.AdresseGroupe').text();
	SelectAddr = $(this).closest('tr').find('.AdressePhysique').text();
	SelectDpt=$(this).closest('tr').find('.DataPointType').text();
})
.on('dblclick','.AdresseGroupe',function(e){
	$('.AdresseGroupe').css('font-weight','unset');
	$('.GadInsert tr').css('font-weight','unset');
	$(this).css('font-weight','bold');
	SelectGad=$(this).attr('data-AdresseGroupe');
	SelectAddr=$(this).attr('data-AdressePhysique').replace(/\-/g, '.');
	SelectDpt=$(this).attr('data-DataPointType').replace(/\-/g, '.');
	$(this).closest('.modal-content').find('button[data-bb-handler=success]').trigger('click');
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

function CreatebyTemplate(){	
	var html = $('<span class="pull-right">');
	var select = $('<select class="EqLogicTemplateAttr form-control" data-l1key="template">');
	$.each(templates,function(id,template){
		select.append($('<option>')
			.attr('value',id)
			.append(template.name));
	});
	html.append(select);
}
function CreateArboressance(data, Arboressance, first){
	if (first)
		Arboressance.html('');
	jQuery.each(data,function(Niveau, Parameter) {
		if(Parameter == null) {
			Arboressance.append($('<li class="Level">').text(Niveau));
		}else if(typeof Parameter.AdresseGroupe == "undefined") {
			Arboressance.append($('<li class="Level">')
				.text(Niveau)
				.append($('<div class="col-xs-12 input-group pull-right" style="display:inline-flex">')
					.append($('<span class="input-group-btn">')
						.append($('<a class="btn btn-success btn-xs roundedRight createObject">')
							.append($('<i class="far fa-object-group">')))
						.append($('<a class="btn btn-warning btn-xs roundedRight createTemplate">')
							.append($('<i class="fas fa-address-card">'))))
					.append(CreateArboressance(Parameter, $('<ul>').hide(),false))));
		}else{
			var li =$('<li class="AdresseGroupe">');
			if(typeof Parameter.AdresseGroupe != "undefined"){
				var AdresseGroupe =Parameter.AdresseGroupe;
				li.attr('data-AdresseGroupe',AdresseGroupe);
			}
			if(typeof Parameter.AdressePhysique != "undefined"){
				var AdressePhysique =Parameter.AdressePhysique.replace(/\./g, '-');
				li.attr('data-AdressePhysique',AdressePhysique);
			}
			if(typeof Parameter.DataPointType != "undefined"){
				var DataPointType =Parameter.DataPointType.replace(/\./g, '-');
				li.attr('data-DataPointType',DataPointType);
			}
			li.text(' (' + Parameter.AdresseGroupe + ') '+Niveau);
			Arboressance.append(li);
		}
	});
	if (first){
		Arboressance.off().on('click','.Level',function(e){
			if(!$(this).find('ul:first').is(":visible"))
				$(this).find('ul:first').show();
			else
				$(this).find('ul:first').hide();
			e.stopPropagation();
		})
		.on('click','.AdresseGroupe',function(e){
			$('.AdresseGroupe').css('font-weight','unset');
			$('.GadInsert tr').css('font-weight','unset');
			$(this).css('font-weight','bold');
			SelectGad=$(this).attr('data-AdresseGroupe');
			SelectAddr=$(this).attr('data-AdressePhysique').replace(/\-/g, '.');
			SelectDpt=$(this).attr('data-DataPointType').replace(/\-/g, '.');
			e.stopPropagation();
		})
		.on('dblclick','.AdresseGroupe',function(e){
			$('.AdresseGroupe').css('font-weight','unset');
			$('.GadInsert tr').css('font-weight','unset');
			$(this).css('font-weight','bold');
			SelectGad=$(this).attr('data-AdresseGroupe');
			SelectAddr=$(this).attr('data-AdressePhysique').replace(/\-/g, '.');
			SelectDpt=$(this).attr('data-DataPointType').replace(/\-/g, '.');
			e.stopPropagation();
			$(this).closest('.modal-content').find('button[data-bb-handler=success]').trigger('click');
		});
		if(SelectAddr != ''){
			$.each(Arboressance.find(".AdresseGroupe"),function() {
				if($(this).attr("data-AdressePhysique") == SelectAddr.replace(/\./g, '-')){
					$(this).css('background-color','blue');
					$(this).css('color','white');
					$(this).parent().show();
					$(this).parent().parent().parent().show();
				}
			});
		}
		if(SelectDpt != ''){
			var SelectDptId = SelectDpt.replace(/\./g, '-');
			$.each(Arboressance.find(".AdresseGroupe"),function() {
				if($(this).attr("data-DataPointType") == SelectDptId){
					$(this).css('background-color','blue');
					$(this).css('color','white');
					$(this).parent().show();
					$(this).parent().parent().parent().show();
				}
				else if($(this).attr("data-DataPointType").replace($(this).attr("data-DataPointType").substr(-3), '') == SelectDptId.replace(SelectDptId.substr(-3), '')){
					$(this).css('background-color','yellow');
					$(this).parent().show();
					$(this).parent().parent().parent().show();
				}
			});
		}
	}
	return Arboressance;
}	
</script>
