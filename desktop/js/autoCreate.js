function getLevelSelect(){
	var html=$('<div class="col-sm-9 control-label">');
	for(var loop = 0; loop < GadLevel; loop++){
		html.append($('<select class=" autoCreateParameter" data-l1key="'+loop+'">')
			.append($('<option value="object">')
				  .append('{{Objet}}'))
			.append($('<option value="function">')
				.append('{{Fonction}}'))
			.append($('<option value="cmd">')
				.append('{{Commande}}')));
	}
}
function autoCreate(){
	var html = $('<form class="autoCreate form-horizontal" onsubmit="return false;">');
  	html.append($('<div class="form-group">') 
		.append($('<label class="col-sm-4 control-label">') 
        		.append('{{Créer les objets}}') 
			.append($('<sup>') 
				.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option autorise le parser a créer automatiquement les objets trouvés selon la definition des level defini precedement dans l\'arboresance de groupe}}" >'))))
		.append($('<div class="col-sm-9 control-label">') 
			.append($('<input type="checkbox" class="autoCreateParameter" data-l1key="createObjet"/>'))));
	html.append($('<div class="form-group">') 
		.append($('<label class="col-sm-4 control-label">') 
      			.append('{{Créer les equipements}}') 
			.append($('<sup>') 
				.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option autorise le parser a créer automatiquement les equipements trouvés selon la definition des level defini precedement dans l\'arboresance de groupe}}">'))))
		.append($('<div class="col-sm-9 control-label">') 
			.append($('<input type="checkbox" class="autoCreateParameter" data-l1key="createEqLogic"/>'))));
	html.append($('<div class="form-group withCreate">') 
		.append($('<label class="col-sm-4 control-label">') 
		        .append('{{Arborescence des groupes}}') 
			.append($('<sup>') 
				.append($('<i class="fa fa-question-circle tooltips" title="{{La définition de l\'arboressance de groupe permet au parser de connaitre ou se situe le nom a prendre pour la creation automatique des objets ou des equipemnt}}">'))))
			.append(getLevelSelect).hide());
	html.append($('<div class="form-group withCreateEqLogic">') 
		.append($('<label class="col-sm-4 control-label">')
       			.append('{{Uniquement correspondant a un Template}}')
			.append($('<sup>')
				.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option permet de filtrer la creation d\'equipement a ceux qui corresponde a un Template (Nom du Template et des commandes}}">'))))
		.append($('<div class="col-sm-9 control-label">') 
			.append($('<input type="checkbox" class="autoCreateParameter" data-l1key="createTemplate"/>'))).hide());
bootbox.dialog({
		title: "{{Creation automatique des equipements KNX}}",
		message: html,
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
					$.ajax({
						type: 'POST',   
						url: 'plugins/eibd/core/ajax/eibd.ajax.php',
						data:
						{
							action: 'autoCreate',
							option: $('.autoCreate').getValues('.autoCreateParameter')
						},
						dataType: 'json',
						global: true,
						error: function(request, status, error) {},
						success: function(data) {
							window.location.reload();
						}
					});
				}
			},
		}
	});
	$('.autoCreateParameter[data-l1key=createEqLogic]').change(function() {
 		if(this.checked) {
			$('.withCreate').show();
			$('.withCreateEqLogic').show();
		}else{
			$('.withCreate').hide();
			$('.withCreateEqLogic').hide();
		}
	});
	$('.autoCreateParameter[data-l1key=createObjet]').change(function() {
 		if(this.checked) {
			$('.withCreate').show();
		}else{
			$('.withCreate').hide();
		}
	});
}
