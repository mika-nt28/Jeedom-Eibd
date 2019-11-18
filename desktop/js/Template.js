$('.Template[data-action=add]').off().on('click', function () {
  	TemplateDialog('updateEqLogic');
});
$('.eqLogicAction[data-action=addByTemplate]').off().on('click', function () {
  	TemplateDialog('newEqLogic');
});
function TemplateDialog(type){
  	var dialog = bootbox.dialog({
		title: "{{Ajout d'un équipement avec template}}",
		message: $('<div>').load('index.php?v=d&modal=eibd.addByTemplate&plugin=eibd&type=eibd'),
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
					if($('.EqLogicTemplateAttr[data-l1key=template]').value() != ""){
						$.ajax({
							type: 'POST',   
							url: 'plugins/eibd/core/ajax/eibd.ajax.php',
							data:
							{
								action: 'getTemplate',
								template:$('.EqLogicTemplateAttr[data-l1key=template]').val()
							},
							dataType: 'json',
							global: true,
							error: function(request, status, error) {},
							success: function(data) {
								if (data.state != 'ok') {
									$('#div_alert').showAlert({message: data.result, level: 'danger'});
									return;
								}
								var eqLogic=data.result;
								var typeTemplate=$('.EqLogicTemplateAttr[data-l1key=template]').value();
								if (typeof(eqLogic.configuration) === 'undefined')
									eqLogic.configuration=new Object();
								$.each(eqLogic.options,function(id, option){
									if($('.TemplateOption[data-l1key='+id+']').is(':checked')){
										typeTemplate = typeTemplate + "_" + id;
										$.each(option.cmd,function(idCmd, cmd){
											eqLogic.cmd.push(cmd);
										});
									}
								});
								
								$.each(eqLogic.cmd,function(index, value){
									eqLogic.cmd[index].logicalId=searchSameCmd(eqLogic,index,'');
									if (typeof(eqLogic.cmd[index].value) !== 'undefined')
										eqLogic.cmd[index].value="#["+$('.EqLogicTemplateAttr[data-l1key=object_id] option:selected').text()+"]["+eqLogic.name+"]["+eqLogic.cmd[index].value+"]#";
									if(type == 'updateEqLogic')
										addCmdToTable(eqLogic.cmd[index]);
								});
								if(type == 'newEqLogic'){
									if($('.EqLogicTemplateAttr[data-l1key=name]').value() != ""){	
										eqLogic.name=$('.EqLogicTemplateAttr[data-l1key=name]').value();
										if (typeof(eqLogic.logicalId) === 'undefined')
											eqLogic.logicalId=new Object();
										eqLogic.logicalId=$('.EqLogicTemplateAttr[data-l1key=logicalId]').value();
										if (typeof(eqLogic.object_id) === 'undefined')
											eqLogic.object_id=new Object();
										eqLogic.object_id=$('.EqLogicTemplateAttr[data-l1key=object_id]').value();
										eqLogic.configuration.typeTemplate = typeTemplate;
										SaveTemplate(eqLogic);
									}
								}
							}
						});
					}
					
				}
			},
		}
	});
	if(type == 'updateEqLogic'){
		dialog.off('shown.bs.modal').on('shown.bs.modal', function(e){
			$(".EqLogicTemplateAttr[data-l1key=name]").val($(".eqLogicAttr[data-l1key=name]").val()).attr("disabled","true");
			$(".EqLogicTemplateAttr[data-l1key=logicalId]").val($(".eqLogicAttr[data-l1key=logicalId]").val()).attr("disabled","true");
			$(".EqLogicTemplateAttr[data-l1key=object_id]").val($(".eqLogicAttr[data-l1key=object_id]").val()).attr("disabled","true");
		});
	}
 	dialog.modal("show");
};
function SaveTemplate(eqLogic){
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
};
function searchSameCmd(eqLogic,index,option){
      	var GAD='';
	if (typeof(eqLogic.cmd[index].SameCmd) !== 'undefined'){
		$('.CmdEqLogicTemplateAttr[data-l2key=SameCmd]').each(function(){
			if($(this).val() == eqLogic.cmd[index].SameCmd){
				GAD =  $(option + ' .CmdEqLogicTemplateAttr[data-l1key='+$(this).attr('data-l1key')+'][data-l2key=logicalId]').val();
         			return GAD;
			}
         	});
         	return GAD;
	}
	$('.CmdEqLogicTemplateAttr[data-l2key=name]').each(function(){
		if($(this).val() == eqLogic.cmd[index].name){
			GAD =  $(option + ' .CmdEqLogicTemplateAttr[data-l1key='+$(this).attr('data-l1key')+'][data-l2key=logicalId]').val();
			return GAD;
		}
	});
	return GAD;								
}
var _optionsMultiple=null;
$('body').off('.EqLogicTemplateAttr[data-l1key=template]').on('change','.EqLogicTemplateAttr[data-l1key=template]', function () {
	var cmds=$('.CmdsTempates');
	cmds.html('');
	$.ajax({
		type: 'POST',   
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data:
		{
			action: 'getTemplate',
			template:$(this).val()
		},
		dataType: 'json',
		global: true,
		error: function(request, status, error) {},
		success: function(data) {
			var cmdindex = 0;
			$.each(data.result.cmd,function(index, value){
				var isExist = false;
				if(typeof value.SameCmd != "undefined") {
					$('.CmdEqLogicTemplateAttr[data-l2key=SameCmd]').each(function(){
						if($(this).val() == value.SameCmd){
							isExist = true;
							return;
						}
					});							
				}
				if(isExist == false){
					var cmd = $('<div class="form-group">');
					if(typeof  value.SameCmd == 'undefined'){
						cmd.append($('<label class="col-md-5 control-label" >')
							.text(value.name + " (DPT: " + value.configuration.KnxObjectType + ")"));
					}else{
						cmd.append($('<label class="col-md-5 control-label" >')
							.text(value.SameCmd + " (DPT: " + value.configuration.KnxObjectType + ")"));
					}
					cmd.append(addCmdEqLogicTemplateAttr(cmdindex,value));
					cmds.append(cmd);
					cmdindex++;
				}
			});
			$.each(data.result.options,function(id, options){
				if(typeof options.type == "undefined") {
					cmds.append($('<div class="form-group">')
						.append($('<label class="col-md-5 control-label" >')
							.text(options.name))
						.append($('<div class="col-md-5">')
							.append($('<input type="checkbox" class="TemplateOption" data-l1key="'+id+'">'))));
					$.each(options.cmd,function(index, value){
						var isExist = false;
						if(typeof value.SameCmd != "undefined") {
							$('.CmdEqLogicTemplateAttr[data-l2key=SameCmd]').each(function(){
								if($(this).val() == value.SameCmd){
									isExist = true;
									return;
								}
							});							
						}
						if(isExist == false){
							var cmd = $('<div class="form-group '+id+'">');
							if(typeof  value.SameCmd == 'undefined'){
								cmd.append($('<label class="col-md-5 control-label" >')
									.text(value.name + " (DPT: " + value.configuration.KnxObjectType + ")"));
							}else{
								cmd.append($('<label class="col-md-5 control-label" >')
									.text(value.SameCmd + " (DPT: " + value.configuration.KnxObjectType + ")"));
							}
							cmd.append(addCmdEqLogicTemplateAttr(cmdindex,value));
							cmds.append(cmd.hide());
							cmdindex++;
						}
					});
					$('.TemplateOption[data-l1key='+id+']').off().on('change',function(){
						if($(this).is(':checked'))
							$('.'+$(this).attr('data-l1key')).show();
						else
							$('.'+$(this).attr('data-l1key')).hide();
					});
				}else{
					_optionsMultiple=options;
					if(options.type == "eqLogic"){
						cmds.append(addBtOptionTemplate(id,options.name));
					}
					if(options.type == "cmd"){
						$.each(options.cmd,function(index, value){
							cmds.append(addBtOptionTemplate(index,value.name));
						});
					}
				}
			});
		}
	});
});
function addBtOptionTemplate(id,name){
	var html = $('<div class="input-group pull-right" style="display:inline-flex">')
		.append($('<span class="input-group-btn">')
			.append($('<a class="btn btn-warning btn-xs roundedRight bt_'+id+'" >')
				.append($('<i class="fas fa-plus-circle">'))
				.text(name)));
	
	$('body').off('.bt_'+id).on('click','.bt_'+id,function(){
		if(_optionsMultiple.type == "eqLogic"){
			//Lancer une nouvelle instance de template _optionsMultiple.template
		}
		if(_optionsMultiple.type == "cmd"){
			$.each(_optionsMultiple.cmd,function(index, value){
				$('.CmdsTempates').append(addCmdTemplate(index,value));
			});
		}
	});
	return html;
}
function addCmdTemplate(index,value){
	bootbox.dialog({
		title: "{{Nom de la nouvelle commande}}",
		message: $('<div>').append($('<input class="form-control input-sm" data-l1key="name">')),
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
					value.name= $(this).find('input[data-l1key=name]').val();
					return addCmdEqLogicTemplateAttr(index,value);
				}
			},
		}
	});
}
function addCmdEqLogicTemplateAttr(index,value){
	return $('<div class="col-md-5">')
		.append($('<div class="input-group">')
			.append($('<input type="hidden" class="CmdEqLogicTemplateAttr form-control input-sm" data-l1key="'+index+'" data-l2key="name">')
				.val(value.name))
			.append($('<input type="hidden" class="CmdEqLogicTemplateAttr form-control input-sm" data-l1key="'+index+'" data-l2key="SameCmd">')
				.val(value.SameCmd))
			.append($('<input type="hidden" class="CmdEqLogicTemplateAttr form-control input-sm" data-l1key="'+index+'" data-l2key="KnxObjectType">')
				.val(value.configuration.KnxObjectType)))
		.append($('<div class="input-group">')
			.append($('<input class="CmdEqLogicTemplateAttr form-control input-sm" data-l1key="'+index+'" data-l2key="logicalId">'))
			.append($('<span class="input-group-btn">')
				.append($('<a class="btn btn-success btn-sm bt_selectGadInconnue">')
					.append($('<i class="fa fa-list-alt">')))));
}
