<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
if(isset($_REQUEST['SelectAddr']))
	echo '<script>var SelectAddr="'.$_REQUEST['SelectAddr'].'";</script>';
else
	echo '<script>var SelectAddr="";</script>';
if(isset($_REQUEST['SelectDpt']))
	echo '<script>var SelectDpt="'.$_REQUEST['SelectDpt'].'";</script>';
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
</ul>
<div class="tab-content" style="height: 500px;overflow: auto;">
	<div role="tabpanel" class="tab-pane active" id="InconueTab">
		<span class="pull-right">
			<a class="btn btn-danger btn-xs pull-right removeAllGad" style="margin-bottom : 5px;">
				<i class="fa fa-trash-o"></i>
				{{ Supprimer}}
			</a>
		</span>
		<span class="pull-right">
			<a class="btn btn-warning btn-xs pull-right Include" data-validation=true style="margin-bottom : 5px;" ></a> 
		</span>
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
		<span class="pull-right">
			<a class="btn btn-warning btn-xs Ets4Parser" >
				<i class="fa fa-cloud-upload"></i>
				{{Importer projet KNX}}
			</a> 
		</span>
		<table id="table_Devices" class="table table-bordered table-condensed tablesorter GadInsert">
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
		<ul class="MyAdressGroup"></ul>
	</div>
</div>

<script>
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
							if($('body .EtsParserDiv .EtsParseParameter[data-l1key=createEqLogic]').checked){
								window.location.reload();
							}else{
								UpdateDeviceTable(data.result.Devices)
								CreateArboressance(data.result.GAD,$('.MyAdressGroup'),true);
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
			CreateArboressance(data.result.GAD,$('.MyAdressGroup'),true);
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
	SelectAddr=$(this).closest('tr').find('.DataPointType').text();
})
.on('dblclick','.AdresseGroupe',function(e){
	$('.AdresseGroupe').css('font-weight','unset');
	$('.GadInsert tr').css('font-weight','unset');
	$(this).css('font-weight','bold');
	SelectGad=$(this).attr('data-AdresseGroupe');
	SelectDpt=$(this).attr('data-DataPointType');
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
function CreateArboressance(data, Arboressance, first){
	if (first)
		Arboressance.html('');
	jQuery.each(data,function(Niveau, Parameter) {
		//if(typeof(Parameter) == 'object'){
		if(typeof Parameter.AdresseGroupe == "undefined") {
			Arboressance.append($('<li class="Level">').text(Niveau).append(CreateArboressance(Parameter, $('<ul>').hide(),false)));
		}else{
			var DataPointType = Parameter.DataPointType.replace(/\./g, '-');
			Arboressance.append($('<li class="AdresseGroupe" data-AdresseGroupe="'+Parameter.AdresseGroupe+'" data-DataPointType="'+DataPointType+'">').text(' (' + Parameter.AdresseGroupe + ') '+Niveau));
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
			SelectDpt=$(this).attr('data-DataPointType');
			e.stopPropagation();
		})
		.on('dblclick','.AdresseGroupe',function(e){
			$('.AdresseGroupe').css('font-weight','unset');
			$('.GadInsert tr').css('font-weight','unset');
			$(this).css('font-weight','bold');
			SelectGad=$(this).attr('data-AdresseGroupe');
			SelectDpt=$(this).attr('data-DataPointType');
			e.stopPropagation();
			$(this).closest('.modal-content').find('button[data-bb-handler=success]').trigger('click');
		});
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
