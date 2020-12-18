function CreateObject(object){
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data: {
			action: 'CreateObject',
			name:object,
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
function selectTemplate(){
	var select = $('<select class="EqLogicTemplateAttr form-control" data-l1key="template">');
	$.each(templates,function(id,template){
		select.append($('<option>')
			.attr('value',id)
			.append(template.name));
	});
	bootbox.dialog({
		title: "{{Selectionner le template}}",
		message: select,
		buttons: {
			"Annuler": {
				className: "btn-default",
				callback: function () {
				}
			},
			success: {
				label: "Valider",
				className: "btn-primary",
				callback: function () {
					return $('.EqLogicTemplateAttr[data-l1key=template]').val();
				}
			},
		}
	});
      
}
function htmlMergeTemplate(template,cmds){
	var selectCmd = $('<select class="EqLogicTemplateAttr form-control" data-l1key="cmd">');
	var optgroup = $('<optgroup>').attr('label','Base');
	$.each(template.cmd,function(id,cmd){
		var optionName = cmd.name;
		if(isset(cmd.SameCmd) && cmd.SameCmd != '') 
			optionName = cmd.SameCmd;
		var optionExist = false;
		optgroup.find('option').each(function() {
			if($(this).text() == optionName)
				optionExist = true;
		});
		if(!optionExist){
			optgroup.append($('<option>')
				.attr('value',id)
				.text(optionName));
		}
	});
	selectCmd.append(optgroup);
	$.each(template.options,function(id,option){
		var optgroup = $('<optgroup>').attr('label',option.name);
		$.each(option.cmd,function(idCmd, cmd){
			var optionName = cmd.name;
			if(isset(cmd.SameCmd) && cmd.SameCmd != '') 
				optionName = cmd.SameCmd;
			var optionExist = false;
			optgroup.find('option').each(function() {
				if($(this).text() == optionName)
					optionExist = true;
			});
			if(!optionExist){
				optgroup.append($('<option>')
					.attr('value',id)
					.text(optionName));
			}
		});
		selectCmd.append(optgroup);
	});
	var html = $('<div>');
	$.each(cmds,function(id, cmd){
		html.append($('<div class="col-sm-6">').text(cmd.name));
		html.append($('<div class="col-sm-6">').append(selectCmd.clone().attr('data-l2key',cmd.AdresseGroupe)));
	});
	return html;
}
function getTemplate(_template){
	if(_template != ''){
		if(templates[_template] == 'Undefinded')
			return templates[selectTemplate()];
		var isTemplate;
		$.each(templates,function(id, template){
			if(template.name.includes(_template))
				isTemplate = template;
				return;
		});
		if(isTemplate != null)
			return isTemplate;
	}
	return templates[selectTemplate()];
}
function CreatebyTemplate(_equipement){	
	var template = getTemplate(_equipement.find('label:first').text());
	var eqLogic = new Object();
	eqLogic.name = template.name;
	eqLogic.isEnable = true;
	eqLogic.isVisible = true;
	var dataArbo = new Array();
	_equipement.find(' ul:first li').each(function(){
		var cmd = new Object();
		if($(this).attr('data-AdresseGroupe') != 'Undefinded'){
			cmd.AdresseGroupe = $(this).attr('data-AdresseGroupe');
			cmd.AdressePhysique = $(this).attr('data-AdressePhysique').replace( '-',/\./g);
			cmd.DataPointType = $(this).attr('data-DataPointType').replace( '-',/\./g);
			cmd.name = $(this).text();
			dataArbo.push(cmd);
		}
	});	
	bootbox.dialog({
		title: "{{Merge des commandes sur le template}}",
		message: htmlMergeTemplate(template,dataArbo),
		buttons: {
			"Annuler": {
				className: "btn-default",
				callback: function () {
				}
			},
			success: {
				label: "Valider",
				className: "btn-primary",
				callback: function () {
					eqLogic.cmd = new Array();
					$('.EqLogicTemplateAttr[data-l1key=cmd]').each(function(){
						var index = $(this).val();
						if (typeof(template.cmd[index]) !== 'undefined'){
							var logicalId=$(this).attr('data-l2key');
							template.cmd[index].logicalId = logicalId;
							if (typeof(template.cmd[index].value) !== 'undefined')
								template.cmd[index].value="#[Aucun]["+template.name+"]["+template.cmd[index].value+"]#";
							eqLogic.push(template.cmd[index]);
							$.each(template.cmd[index].SameCmd.split('|'),function(id, name){
								$.each(template.cmd,function(idCmd, cmd){
									if(cmd.name == name && idCmd != index){
										template.cmd[idCmd].logicalId=logicalId;
										if (typeof(template.cmd[index].value) !== 'undefined')
											template.cmd[index].value="#[Aucun]["+template.name+"]["+template.cmd[index].value+"]#";
										eqLogic.push(template.cmd[idCmd]);
									}
								});
							});
						}
						$.each(template.options,function(id, option){	
							if (typeof(template.options[id].cmd[index]) !== 'undefined'){
								template.options[id].cmd[index].logicalId=$(this).attr('data-l2key');
								if (typeof(template.options[id].cmd[index].value) !== 'undefined')
									template.options[id].cmd[index].value="#[Aucun]["+template.name+"]["+template.options[id].cmd[index].value+"]#";
								eqLogic.push(template.options[id].cmd[index]);
								$.each(template.options[id].cmd[index].SameCmd.split('|'),function(id, name){
									$.each(template.options[id].cmd,function(idCmd, cmd){
										if(cmd.name == name && idCmd != index){
											template.options[id].cmd[idCmd].logicalId=logicalId;
											eqLogic.push(template.options[id].cmd[idCmd]);
										}
									});
								});
							}
						});
					});
                  			alert(JSON.stringify(eqLogic));
					//SaveMergeTemplate(eqLogic);
				}
			},
		}
	});
		
}
function SaveMergeTemplate(eqLogic){
	jeedom.eqLogic.save({
		type: 'eibd',
		eqLogics: [eqLogic],
		error: function (error) {
			$('#div_alert').showAlert({message: error.message, level: 'danger'});
		},
		success: function (_data) {
			
		}
	});
};
function CreateMenu(data){
	var menu = $('<span class="input-group-btn">');
	menu.append($('<a class="btn btn-success btn-xs roundedRight createObject">').append($('<i class="far fa-object-group">')));
	menu.append($('<a class="btn btn-warning btn-xs roundedRight createTemplate">').append($('<i class="fas fa-address-card">')));
	return $('<div class="input-group pull-right" style="display:inline-flex">').append(menu);
}
function CreateArboressance(data, Arboressance, first){
	if (first)
		Arboressance.html('');
	jQuery.each(data,function(Niveau, Parameter) {
		if(Parameter == null) {
			Arboressance.append($('<li class="col-sm-11 Level">').text(Niveau));
		}else if(typeof Parameter.AdresseGroupe == "undefined") {
			Arboressance.append($('<li class="col-sm-11 Level">')
				.append(CreateMenu(Parameter))
				.append($('<label>')
					.append(Niveau))
				.append(CreateArboressance(Parameter, $('<ul>').hide(),false)));
		}else{
			var li =$('<li class="col-sm-11 AdresseGroupe">');
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
			li.text(Niveau);
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
		})
		.on('click','.createObject',function(e){
			e.stopPropagation();
			CreateObject($(this).parents('.Level:first').find('label:first').text());
		})
		.on('click','.createTemplate',function(e){
			e.stopPropagation();
			CreatebyTemplate($(this).parents('.Level:first'));
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
