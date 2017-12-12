var AllDpt=null;
UpdateVar();
var template;	
var GadLevel=3;
$('body').on('change','.EqLogicTemplateAttr[data-l1key=template]', function () {
	//Creation du formulaire du template
	var form=$(this).closest('form');
	var cmds=$('<div class="form-horizontal CmdsTempates">');
	$.each(template[$(this).value()].cmd,function(index, value){
		cmds.append($('<div class="form-group">')
			.append($('<label class="col-xs-6 control-label" >')
				.text(value.name))
			.append($('<div class="col-xs-5">')
				.append($('<div class="input-group">')
					.append($('<input class="CmdEqLogicTemplateAttr form-control input-sm" data-l1key="'+index+'">'))
					.append($('<span class="input-group-btn">')
						.append($('<a class="btn btn-success btn-sm bt_selectGadInconnue">')
							.append($('<i class="fa fa-list-alt">')).attr('data-dpt',value.configuration.KnxObjectType).on('click',function () {
								var SelectAddr=$(this).closest('.modal-body').find('.EqLogicTemplateAttr[data-l1key=logicalId]').val();	
								var SelectDpt=$(this).attr('data-dpt');
								var input=$(this).closest('.input-group').find('.CmdEqLogicTemplateAttr');
								bootbox.dialog({
									title: "{{Choisir un Gad}}",
									height: "800px",
									width: "auto",
									message: $('<div>').load('index.php?v=d&modal=eibd.gadInconnue&plugin=eibd&type=eibd&SelectAddr='+SelectAddr+'&SelectDpt='+SelectDpt+'&param'),
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
												input.closest('.modal-body').find('.EqLogicTemplateAttr[data-l1key=logicalId]').val(SelectAddr);	
												input.val(SelectGad);
											}
										},
									}
								});
							}))))));
	});
	form.find('.CmdsTempates').remove();
	form.append(cmds);
});
$('.eqLogicAction[data-action=addByTemplate]').on('click', function () {
	$.ajax({
		type: 'POST',            
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data:
			{
			action: 'getTemplate',
			},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {},
		success: function(data) {
			if (!data.result){
				$('#div_alert').showAlert({message: 'Aucun message recu', level: 'error'});
				return;
			}
			template=data.result;
		}
	});
	var message = $('<div class="row">')
		.append($('<div class="col-md-12">')
			.append($('<form class="form-horizontal" onsubmit="return false;">')
				.append($('<div class="form-group">')
					.append($('<label class="col-xs-5 control-label" >')
						.text('{{Nom de votre équipement}}'))
					.append($('<div class="col-xs-7">')
						.append($('<input class="EqLogicTemplateAttr form-control" data-l1key="name"/>'))))
				.append($('<div class="form-group">')
					.append($('<label class="col-xs-5 control-label" >')
						.text('{{Adresse physique de l\'equipement}}'))
					.append($('<div class="col-xs-7">')
						.append($('<input class="EqLogicTemplateAttr form-control" data-l1key="logicalId"/>'))))
				.append($('<div class="form-group">')
					.append($('<label class="col-xs-5 control-label" >')
						.text('{{Objet parent}}'))
					.append($('<div class="col-xs-7">')
						.append($('<select class="EqLogicTemplateAttr form-control" data-l1key="object_id">')
						       .append($('.eqLogicAttr[data-l1key=object_id] option').clone()))))
				.append($('<div class="form-group">')
					.append($('<label class="col-xs-5 control-label" >')
						.text('{{Template de votre équipement}}'))
					.append($('<div class="col-xs-3">')
						.append($('<select class="EqLogicTemplateAttr form-control" data-l1key="template">')
							   .append($('<option>')
								.text('{{Séléctionner un template}}')))))
				   .append($('<label>').text('{{Configurer les adresse de groupe}}'))));				
	$.each(template,function(index, value){
		message.find('.EqLogicTemplateAttr[data-l1key=template]')
			.append($('<option value="'+index+'">')
				.text(value.name))
	});
	bootbox.dialog({
		title: "{{Ajout d'un équipement avec template}}",
		message: message,
		height: "800px",
		width: "auto",
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
					if($('.EqLogicTemplateAttr[data-l1key=template]').value() != "" && $('.EqLogicTemplateAttr[data-l1key=name]').value() != ""){
						var eqLogic=template[$('.EqLogicTemplateAttr[data-l1key=template]').value()];
						eqLogic.name=$('.EqLogicTemplateAttr[data-l1key=name]').value();
						if (typeof(eqLogic.logicalId) === 'undefined')
							eqLogic.logicalId=new Object();
						eqLogic.logicalId=$('.EqLogicTemplateAttr[data-l1key=logicalId]').value();
						if (typeof(eqLogic.object_id) === 'undefined')
							eqLogic.object_id=new Object();
						eqLogic.object_id=$('.EqLogicTemplateAttr[data-l1key=object_id]').value();
						if (typeof(eqLogic.configuration) === 'undefined')
							eqLogic.configuration=new Object();
						eqLogic.configuration.typeTemplate=$('.EqLogicTemplateAttr[data-l1key=template]').value();
						$.each(eqLogic.cmd,function(index, value){
							eqLogic.cmd[index].logicalId=$('.CmdEqLogicTemplateAttr[data-l1key='+index+']').value();
							if (typeof(eqLogic.cmd[index].value) !== 'undefined')
								eqLogic.cmd[index].value="#["+$('.EqLogicTemplateAttr[data-l1key=object_id] option:selected').text()+"]["+eqLogic.name+"]["+eqLogic.cmd[index].value+"]#";
						});
						jeedom.eqLogic.save({
							type: 'eibd',
							eqLogics: [eqLogic],
							error: function (error) {
								$('#div_alert').showAlert({message: error.message, level: 'danger'});
							},
							success: function (_data) {
								var vars = getUrlVars();
								var url = 'index.php?';
								for (var i in vars) {
									if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
										url += i + '=' + vars[i].replace('#', '') + '&';
									}
								}
								modifyWithoutSave = false;
								url += 'id=' + _data.id + '&saveSuccessFull=1';
								loadPage(url);
							}
						});
					}
				}
			},
		}
	});
});
jeedom.config.load({
	plugin: 'eibd',
	configuration: 'level',
	success: function (data) {
		GadLevel=data.result;
	}
});
$('.log').on('click', function() {
	$('#md_modal').dialog({
		title: "{{log}}",
		position: 'center',
		resizable: true,
		height: 600,
		width: 850});
	$('#md_modal').load('index.php?v=d&modal=eibd.log&plugin=eibd&type=eibd').dialog('open');
});
$('.GadInconue').on('click', function() {
	$('#md_modal').dialog({
		title: "{{Importer les Gad inconnue}}",
		resizable: true,
		height: 700,
		width: 850});
	$('#md_modal').load('index.php?v=d&modal=eibd.gadInconnue&plugin=eibd&type=eibd').dialog('open');
});
$('.BusMoniteur').on('click', function() {
	$('#md_modal').dialog({
		title: "{{Bus Moniteur}}",
		resizable: true,
		height: 700,
		width: 850});
	$('#md_modal').load('index.php?v=d&modal=eibd.busmoniteur&plugin=eibd&type=eibd').dialog('open');
});
$('.Ets4Parser').on('click', function() {
	$('#md_modal').dialog({
		title: "{{Ajout de vos équipement par ETS}}",
		resizable: true,
		height: 700,
		width: 850});
	$('#md_modal').load('index.php?v=d&modal=eibd.EtsParser&plugin=eibd&type=eibd').dialog('open');

});
$('body').on('click','.bt_selectCmdExpression',function() {
	var el=$(this).closest('.input-group').find('.cmdAttr');
	$(this).value()
	jeedom.cmd.getSelectModal({cmd: {type: 'info'},eqLogic: {eqType_name : ''}}, function (result) {
		var value=el.val();
		if(value != '')
			value= value+'|';
		value=value+result.human;
		el.val(value);
	});  
});  
$('body').on( 'click','.bt_read', function() {
	$.ajax({
		type: 'POST',            
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data:
			{
			action: 'Read',
			Gad:$(this).closest('.cmd').find('.cmdAttr[data-l1key=logicalId]').val(),
			},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {},
		success: function(data) {
			if (!data.result)
				$('#div_alert').showAlert({message: 'Aucun message recu', level: 'error'});
			else
				$('#div_alert').showAlert({message: 'Message recu', level: 'success'});
			}
	});
});
$('body').on('keyup','.cmdAttr[data-l1key=logicalId]', function() {
	var valeur= $(this).val();
	var Gad=valeur.split('/');
	if(Gad.length < parseInt(GadLevel)){
		if($.isNumeric(Gad[Gad.length - 1])){
			if(Gad[Gad.length - 1]==0 || Gad[Gad.length - 1]+100>254)
				valeur+='/';
		}
	}
	if(valeur.substr(-2) =='//')
		valeur.substring(0,-1);
	if(valeur.substr(-1) =='//' && Gad.length == parseInt(data.result))
		valeur.substring(0,-1);
	$(this).val(valeur);
}); 
$('body').on('change','.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectType]', function() {
	DptOption($(this).val(),$(this).closest('.cmd').find('.option'));
	if ($(this).closest('.cmd').find('.cmdAttr[data-l1key=unite]').val() == '')
		$(this).closest('.cmd').find('.cmdAttr[data-l1key=unite]').val(DptUnit($(this).val()));
	var valeur =$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectValue]').val();
	$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectValue]').empty();
	$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectValue]').append(DptValue($(this).val()));
	$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectValue] option[value="'+valeur+'"]').prop('selected', true);
	$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').trigger('change');
}); 
$('body').on('change','.cmdAttr[data-l1key=type]',function() {
	switch ($(this).val()){
		case "info":
			$(this).closest('.cmd').find('.RetourEtat').hide();
			$(this).closest('.cmd').find('.bt_read').show();
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectValue]').hide();
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=isHistorized]').closest('.input-group').parent().show();
		break;
		case "action":		
			$(this).closest('.cmd').find('.RetourEtat').show();
			$(this).closest('.cmd').find('.bt_read').hide();
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectValue]').show();
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=isHistorized]').closest('.input-group').parent().hide();
		break;
	}
	setTimeout(function() {
		$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').trigger('change');
	}.bind(this), 500);
});			
$('body').on('change','.cmdAttr[data-l1key=subType]', function() {
	var Dpt=$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectType]').val();
	var type=$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val();
	var value=$(this).val();
	if($(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=subTypeAuto]').is(':checked')){
		value=getDptSousType(Dpt,type);
		$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType] option[value="'+value+'"]').prop('selected', true);
		$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=subTypeAuto]').trigger('change');
	}
	switch (value){
		case "slider":
		case "numeric":
			$(this).closest('.cmd').find('.ValeurMinMax').show();
			$(this).closest('.cmd').find('.ValeurUnite').show();
			$(this).closest('.cmd').find('.ValeurDefaut').hide();
			$(this).closest('.cmd').find('.listValue').hide();
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=inverse]')
				.closest('.input-group').parent().show();
			if($(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').val() == "")
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=minValue]').val(DptMin(Dpt));
			if($(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').val() == "")
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=maxValue]').val(DptMax(Dpt));
		break;
		case "other":
			$(this).closest('.cmd').find('.ValeurDefaut').show();
			$(this).closest('.cmd').find('.ValeurMinMax').hide();
			$(this).closest('.cmd').find('.ValeurUnite').hide();
			$(this).closest('.cmd').find('.listValue').hide();
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=inverse]')
				.closest('.input-group').parent().hide();
		break;	
		case "binary":
			$(this).closest('.cmd').find('.ValeurMinMax').hide();
			$(this).closest('.cmd').find('.ValeurUnite').hide();
			$(this).closest('.cmd').find('.ValeurDefaut').hide();
			$(this).closest('.cmd').find('.listValue').hide();
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=inverse]')
				.closest('.input-group').parent().show();
		break;
		case "select":
			$(this).closest('.cmd').find('.ValeurMinMax').hide();
			$(this).closest('.cmd').find('.ValeurUnite').hide();
			$(this).closest('.cmd').find('.ValeurDefaut').hide();
			$(this).closest('.cmd').find('.listValue').show();
			if($(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=listValue]').val() == "")
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=listValue]').val(DptListSelect(Dpt));
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=inverse]')
				.closest('.input-group').parent().hide();
		break;
		default:
			$(this).closest('.cmd').find('.ValeurDefaut').hide();
			$(this).closest('.cmd').find('.ValeurMinMax').hide();
			$(this).closest('.cmd').find('.ValeurUnite').hide();
			$(this).closest('.cmd').find('.listValue').hide();
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=inverse]')
				.closest('.input-group').parent().hide();
		break;
	}
});			
$('body').on('change','.cmdAttr[data-l1key=configuration][data-l2key=subTypeAuto]', function() {
	if($(this).is(':checked')){
		$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').attr('disabled',true);
	}else
		$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').attr('disabled',false);
	
});
$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$(".eqLogicAttr[data-l1key=configuration][data-l2key=device]").html($(".eqLogicAttr[data-l1key=configuration][data-l2key=device] option").sort(function (a, b) {
	return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
}));
$('.Template[data-action=add]').on('click', function () {
	if($('.eqLogicAttr[data-l1key=configuration][data-l2key=typeTemplate]').val()!=""){
		$('.eqLogicAction[data-action=save]').trigger('click');
		$.ajax({
			type: 'POST',   
			url: 'plugins/eibd/core/ajax/eibd.ajax.php',
			data:
			{
				action: 'AppliTemplate',
				id:$('.eqLogicAttr[data-l1key=id]').val(),
				template:$('.eqLogicAttr[data-l1key=configuration][data-l2key=typeTemplate]').val()
			},
			dataType: 'json',
			global: true,
			error: function(request, status, error) {},
			success: function(data) {
				window.location.reload();
			}
		});
	}
});
function UpdateVar(){
	$.ajax({
		type: 'POST',            
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data:{
			action: 'getAllDpt'
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {},
		success: function(data) {
			AllDpt=jQuery.parseJSON(data.result);
		}
	});
	while(AllDpt.length==0);
}
function DptUnit(Dpt)	{
	var result;
	$.each(AllDpt, function(DptKeyGroup, DptValueGroup){
		$.each(DptValueGroup, function(DptKey, DptValue){
			if (DptKey==Dpt)
				result=DptValue.Unite;
		});
	});
	return result;
}
function DptMin(Dpt)	{
	var result;
	$.each(AllDpt, function(DptKeyGroup, DptValueGroup){
		$.each(DptValueGroup, function(DptKey, DptValue){
			if (DptKey==Dpt)
				result=DptValue.min;
		});
	});
	return result;
}
function DptMax(Dpt)	{
	var result;
	$.each(AllDpt, function(DptKeyGroup, DptValueGroup){
		$.each(DptValueGroup, function(DptKey, DptValue){
			if (DptKey==Dpt)
				result=DptValue.max;
		});
	});
	return result;
}
function getDptSousType(Dpt,type){
	var result;
	$.each(AllDpt, function(DptKeyGroup, DptValueGroup){
		$.each(DptValueGroup, function(DptKey, DptValue){
			if (DptKey==Dpt){
				if(type=='info')
					result=DptValue.InfoType;
				else
					result=DptValue.ActionType;
			}
		});
	});
	return result;
}
function DptOption(Dpt,div){
	$.each(AllDpt, function(DptKeyGroup, DptValueGroup){
		$.each(DptValueGroup, function(DptKey, DptValue){
			if (DptKey==Dpt){
				$.each(DptValue.Option, function(Optionkey, Optionvalue){
					if (DptKey==Dpt && div.find('.cmdAttr[data-l2key=option][data-l3key='+Optionvalue+']').length <= 0){
						div.append($('<label>')
								   .text(Optionvalue)
								   .append($('<sup>')
									   .append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
										   .attr('title',Optionvalue))));
						div.append($('<div class="input-group">')
								.append($('<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="option" data-l3key="'+Optionvalue+'">'))
								.append($('<span class="input-group-btn">')
									.append($('<a class="btn btn-success btn-sm bt_selectCmdExpression">')
										.append($('<i class="fa fa-list-alt">')))));
					}
				});
			}
		});
	});
}
function DptListSelect(Dpt){
  	var DptListSelect='';
	$.each(AllDpt, function(DptKeyGroup, DptValueGroup){
		$.each(DptValueGroup, function(DptKey, DptValue){
			if (DptKey==Dpt){
				$.each(DptValue.Valeurs, function(keyValeurs, Valeurs){
					if (DptListSelect != "")
						DptListSelect += ";";
					//DptListSelect += keyValeurs + "|" + Valeurs;
					DptListSelect += Valeurs + "|" + Valeurs;
				});
			}
		});
	});
	return DptListSelect;
}
function DptValue(Dpt){
  	var DptValues=$('<div>');
  	DptValues.append($('<option>').attr('value','').text('{{Imposer une valeur}}'));
	$.each(AllDpt, function(DptKeyGroup, DptValueGroup){
		$.each(DptValueGroup, function(DptKey, DptValue){
			if (DptKey==Dpt){
				$.each(DptValue.Valeurs, function(keyValeurs, Valeurs){
					DptValues.append($('<option>').attr('value',keyValeurs).text(Valeurs));
				});
			}
		});
	});
	return DptValues.children();
}
function OptionSelectDpt(){
  var DptSelectorOption=$('<div>');
    DptSelectorOption.append($('<option>').attr('value','').text('{{Sélèctionner un DPT}}'));
	$.each(AllDpt, function(DptKeyGroup, DptValueGroup){
      var DptOptionGroup=$('<optgroup>').attr('label',DptKeyGroup);
		$.each(DptValueGroup, function(DptKey, DptValue){
          DptOptionGroup.append($('<option>').attr('value',DptKey).text(DptKey+' - '+DptValue["Name"]));
		});
		DptSelectorOption.append(DptOptionGroup);
	});
	return DptSelectorOption.children();
}
function addCmdToTable(_cmd) {
	if (!isset(_cmd)) {
		var _cmd = {configuration: {}};
	}
	var tr =$('<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">');
  	tr.append($('<td>')
		.append($('<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove">'))
		.append($('<i class="fa fa-arrows-v pull-left cursor bt_sortable" style="margin-top: 9px;">')));
	tr.append($('<td>')
			.append($('<input type="hidden" class="cmdAttr form-control input-sm" data-l1key="id">'))
			.append($('<input class="cmdAttr form-control input-sm" data-l1key="name" value="' + init(_cmd.name) + '" placeholder="{{Name}}" title="Name">')));
	tr.append($('<td>')
		.append($('<label>')
			.text('{{Data Point Type}}')
			.append($('<sup>')
				.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Selectionner le type de data KNX'))))
		.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="KnxObjectType">')
			.append(OptionSelectDpt()))
		.append($('<label>')
			.text('{{Groupe d\'adresse}}')
			.append($('<sup>')
				.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Saisisez l\'adresse de groupe de votre commande KNX'))))
		.append($('<input class="cmdAttr form-control input-sm" data-l1key="logicalId" placeholder="{{GAD}}" title="GAD">')));
	tr.append($('<td>')
		  .append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Lecture}}" data-l1key="configuration" data-l2key="FlagRead"/>'))
					.append('{{Lecture}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
							.attr('title','Si un télégramme de type "READ" répondre en envoyant sur le bus la valeur actuelle de l\’objet.'))))))
		 .append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Ecriture}}" data-l1key="configuration" data-l2key="FlagWrite"/>'))
					.append('{{Ecriture}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
							.attr('title','La valeur de cet objet sera modifiée si un télégramme de type "WRITE" est vue sur le bus monitor'))))))
		  .append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Transmetre}}" data-l1key="configuration" data-l2key="FlagTransmit"/>'))
					.append('{{Transmetre}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
							.attr('title','Si la valeur de cet objet venait à être modifiée, envoyer un télégramme de type "WRITE" contenant la nouvelle valeur de l\’objet'))))))
		.append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Mise-à-jour}}" data-l1key="configuration" data-l2key="FlagUpdate"/>'))
					.append('{{Mise-à-jour}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
						.attr('title','Si un autre participant répond à un télégramme de type "READ" avec une valeur différente, mettre a jour la valeur par celle lue sur la réponse.'))))))
		
		.append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Initialiser}}" data-l1key="configuration" data-l2key="FlagInit"/>'))
					.append('{{Initialiser}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
						.attr('title','Au démarrage du participant, envoyer un télégramme de type "READ" pour initiliser une valeur initial correcte')))))));	
	tr.append($('<td>')
		.append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Inverser}}" data-l1key="configuration" data-l2key="inverse"/>'))
					.append('{{Inverser}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
							.attr('title','Souhaitez vous inverser l\'état de la valeur'))))))
		.append($('<div class="RetourEtat">')
			.append($('<label>')
				.text('{{Retour d\'état}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Choisissez un objet jeedom contenant la valeur de votre commande'))))
			.append($('<div class="input-group">')
				.append($('<input class="cmdAttr form-control input-sm" data-l1key="value">'))
				.append($('<span class="input-group-btn">')
					.append($('<a class="btn btn-success btn-sm bt_selectCmdExpression" id="value">')
						.append($('<i class="fa fa-list-alt">'))))))
		  .append($('<div class="option">'))
		.append($('<div class="ValeurMinMax">')
				.append($('<label>')
					.text('{{Valeur Min et Max}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
						.attr('title','Saisisez dans ses champs la valeur minimum et maximum de votre controle'))))
				.append($('<div class="input-group">')
			.append($('<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" >'))
			.append($('<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" >'))))		
		.append($('<div class="ValeurUnite">')
			.append($('<label>')
				.text('{{Unitée de cette commande}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Saisisez l\'unitée de cette commande'))))
			.append($('<div class="input-group">')
				.append($('<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="{{Unitée}}" title="Unitée">'))))
		.append($('<div class="listValue">')
			.append($('<label>')
				.text('{{Valeur de la liste}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Saisisez les differentes valeurs de cette liste'))))
			.append($('<div class="input-group">')
				.append($('<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="listValue" placeholder="{{Saisisez les differentes valeurs de cette liste séparer par |}}" title="Valeur de liste">'))))
		  .append($('<div class="ValeurDefaut">')
			.append($('<label>')
				.text('{{Valeur figer de cette commande}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
					.attr('title','Choisissez, si vous le souhaitez la valeur fixe de votre commande'))))
			.append($('<div class="input-group">')
				.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="KnxObjectValue">')
					.append(DptValue(init(_cmd.configuration.KnxObjectType)))))));
	tr.append($('<td>')	
		.append($('<div class="parametre">')
			.append($('<span class="type" type="' + init(_cmd.type) + '">')
				.append(jeedom.cmd.availableType()))
		.append($('<div>')
			.append($('<span class="expertModeVisible">')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Sous type automatique}}"  data-l1key="configuration"  data-l2key="subTypeAuto" checked/>'))
					.append('{{Sous type automatique}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
							.attr('title','Laissé Jeedom choisir le sous type'))))))
			.append($('<span class="subType" subType="'+init(_cmd.subType)+'">'))));
		var parmetre=$('<td>');
	if (is_numeric(_cmd.id)) {
		parmetre.append($('<a class="btn btn-default btn-xs cmdAction" data-action="test">')
			.append($('<i class="fa fa-rss">')
				.text('{{Tester}}')));
	}
	parmetre.append($('<a class="btn btn-default btn-xs cmdAction tooltips" data-action="configure">')
		.append($('<i class="fa fa-cogs">')));
	parmetre.append($('<a class="btn btn-default btn-xs cmdAction tooltips" data-action="copy" title="{{Dupliquer}}">')
		.append($('<i class="fa fa-files-o">')));
	parmetre.append($('<a class="btn btn-default btn-xs cmdAction tooltips bt_read">')
		.append($('<i class="fa fa-rss">')
			.text('{{Read}}')));
		parmetre.append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Historiser}}" data-l1key="isHistorized" checked/>'))
					.append('{{Historiser}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
						.attr('title','Souhaitez vous Historiser les changements de valeur'))))));
		parmetre.append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Afficher}}" data-l1key="isVisible" checked/>'))
					.append('{{Afficher}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
						.attr('title','Souhaitez vous afficher cette commande sur le dashboard'))))));
		parmetre.append($('<div>')
			.append($('<span>')
				.append($('<label class="checkbox-inline">')
					.append($('<input type="checkbox" class="cmdAttr checkbox-inline" data-size="mini" data-label-text="{{Niveau Batterie}}" data-l1key="configuration" data-l2key="noBatterieCheck"/>'))
					.append('{{Niveau Batterie}}')
					.append($('<sup>')
						.append($('<i class="fa fa-question-circle tooltips" style="font-size : 1em;color:grey;">')
							.attr('title','Activer cette option uniquement si votre équipement est sur batterie. Ce groupe d\'adresse correspond au niveau de batterie'))))));
	tr.append(parmetre);
	$('#table_cmd tbody').append(tr);
	DptOption(_cmd.configuration.KnxObjectType,$('#table_cmd tbody tr:last').find('.option'));
	$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
	$('#table_cmd tbody tr:last .cmdAttr[data-l1key=configuration][data-l2key=KnxObjectType]').trigger('change');
	$('#table_cmd tbody tr:last .cmdAttr[data-l1key=configuration][data-l2key=KnxObjectValue]').trigger('change');
	$('#table_cmd tbody tr:last').find('.cmdAttr[data-l1key=configuration][data-l2key=KnxObjectValue] option[value="'+init(_cmd.configuration.KnxObjectValue)+'"]').prop('selected', true);		
	jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
	$('#table_cmd tbody tr:last').find('.cmdAttr[data-l1key=configuration][data-l2key=subTypeAuto]').trigger('change');
}
