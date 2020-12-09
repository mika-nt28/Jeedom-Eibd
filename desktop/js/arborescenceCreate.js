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
function CreatebyTemplate(_el,_template){	
	var select = $('<select class="EqLogicTemplateAttr form-control" data-l1key="template">');
	var liste = templates;
	if(_template != '')
		liste = templates[_template].cmd;
	$.each(liste,function(id,template){
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
					var type = 'template';
					if(_template != '')
						type =  'cmd';
					_template = $('.EqLogicTemplateAttr[data-l1key=template]').val();
					_el.after($('<span class="template label label-success cursor">')
						   .attr('data-type',type)
						   .attr('data-id',_template)
						   .text(templates[_template].name));
				}
			},
		}
	});
}
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
			var _el = $(this).parents('.Level').find('label:first');
			/*$(this).parents('.Level').each(function(){
				var id = $(this).find('.template[data-type=template]').attr('data-id');
				if(id != '' && typeof id != 'undefined'){
					CreatebyTemplate($(this),id);
					return;
				}
			});*/
			var id = _el.find('.template[data-type=template]').attr('data-id');
			if(typeof id == 'undefined')
				CreatebyTemplate(_el,'');
			else
				CreatebyTemplate(_el,id);
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
