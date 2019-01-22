$('.templateAction').hide();
$('.templateAction').first().show();
$('body').on('change','.EqLogicTemplateAttr[data-l1key=template]', function () {
	//Creation du formulaire du template
	var form=$(this).closest('form');
	var cmds=$('<div class="form-horizontal CmdsTempates">');
	$.each(template[$(this).value()].cmd,function(index, value){
		cmds.append($('<div class="form-group">')
			    .append($('<input type="hidden" class="CmdEqLogicTemplateAttr form-control input-sm" data-l2key="KnxObjectType">')
				    .val(value.configuration.KnxObjectType)));
		cmds.append($('<div class="form-group">')
			.append($('<label class="col-xs-6 control-label" >')
				.text(value.name + " (DPT: " + value.configuration.KnxObjectType + ")"))
			.append($('<div class="col-xs-5">')
				.append($('<div class="input-group">')
					.append($('<input class="CmdEqLogicTemplateAttr form-control input-sm" data-l1key="'+index+'">'))
					.append($('<span class="input-group-btn">')
						.append($('<a class="btn btn-success btn-sm bt_selectGadInconnue">')
							.append($('<i class="fa fa-list-alt">')))))));
	});
	form.find('.CmdsTempates').remove();
	form.append(cmds);
});

$('.templateAction').on('click', function () {
	$('.eqLogicThumbnailContainer').hide();
	$('.templateAction').removeClass('btn btn-primary');
	$(this).addClass('btn btn-primary');
	$('.eqLogicDisplayCard').hide();
	if($(this).attr('data-template') == '')
		$('.eqLogicDisplayCard').show();
	else
		$('.eqLogicDisplayCard[data-template='+$(this).attr('data-template')+']').show();
	$('.eqLogicThumbnailContainer').show();
});
$('.eqLogicAction[data-action=addByTemplate]').on('click', function () {
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
