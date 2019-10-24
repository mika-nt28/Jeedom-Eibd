function getLevelSelect(Level){
	var html = $('<div class="col-sm-7 control-label">');
	for(var loop = 0; loop < Level; loop++){
		html.append($('<select class="autoCreateParameter" data-l1key="levelType" data-l2key="'+loop+'">')
			.append($('<option value="">')
				  .append('{{Aucun}}'))
			.append($('<option value="object">')
				  .append('{{Objet}}'))
			.append($('<option value="function">')
				.append('{{Fonction}}'))
			.append($('<option value="cmd">')
				.append('{{Commande}}')));
	}
	return html;
}
function getNbLevel(arbo,nbLevel){
  	nbLevel++;
  	//var Level = 0;
	$.each(arbo, function(Niveau, Parameter){
      	if(Parameter == null) 
          return nbLevel;
		else if(typeof Parameter.AdresseGroupe == "undefined")
			getNbLevel(Parameter,nbLevel);
		else
			return Level = nbLevel;
	});
	return Level;
}
function autoCreate(){
	var html = $('<form class="autoCreate form-horizontal" onsubmit="return false;">');
  	html.append($('<div class="form-group">') 
		.append($('<label class="col-sm-4 control-label">') 
        		.append('{{Sur quel arboressance choisir}}') 
			.append($('<sup>') 
				.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option permet de choisir l\'arboresance sur lequel on vas cree nos objet / equipement / commande}}" >'))))
		.append($('<div class="col-sm-7 control-label">') 
			.append($('<select class="autoCreateParameter" data-l1key="arboresance">')
			.append($('<option value="gad">')
				  .append('{{Adresse de groupe}}'))
			.append($('<option value="device">')
				  .append('{{Equipement}}'))
			.append($('<option value="locations">')
				  .append('{{Localisation}}')))));
  	html.append($('<div class="form-group">') 
		.append($('<label class="col-sm-4 control-label">') 
        		.append('{{Créer les objets}}') 
			.append($('<sup>') 
				.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option autorise le parser a créer automatiquement les objets trouvés selon la definition des level defini precedement dans l\'arboresance de groupe}}" >'))))
		.append($('<div class="col-sm-7 control-label">') 
			.append($('<input type="checkbox" class="autoCreateParameter" data-l1key="createObjet"/>'))));
	html.append($('<div class="form-group">') 
		.append($('<label class="col-sm-4 control-label">') 
      			.append('{{Créer les equipements}}') 
			.append($('<sup>') 
				.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option autorise le parser a créer automatiquement les equipements trouvés selon la definition des level defini precedement dans l\'arboresance de groupe}}">'))))
		.append($('<div class="col-sm-7 control-label">') 
			.append($('<input type="checkbox" class="autoCreateParameter" data-l1key="createEqLogic"/>'))));
	html.append($('<div class="form-group withCreate">') 
		.append($('<label class="col-sm-4 control-label">') 
		        .append('{{Arborescence des groupes}}') 
			.append($('<sup>') 
				.append($('<i class="fa fa-question-circle tooltips" title="{{La définition de l\'arboressance de groupe permet au parser de connaitre ou se situe le nom a prendre pour la creation automatique des objets ou des equipemnt}}">'))))
			.append($('<div class="level">')).hide());
	html.append($('<div class="form-group withCreateEqLogic">') 
		.append($('<label class="col-sm-4 control-label">')
       			.append('{{Uniquement correspondant a un Template}}')
			.append($('<sup>')
				.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option permet de filtrer la creation d\'equipement a ceux qui corresponde a un Template (Nom du Template et des commandes}}">'))))
		.append($('<div class="col-sm-7 control-label">') 
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
	$('.autoCreateParameter[data-l1key=arboresance]').off().on('change',function() {
		var arbo = null;		
		switch($(this).val()){
			case 'gad':
				arbo = KnxProject.GAD;
			break;
			case 'device':
				arbo = KnxProject.Devices;
			break;
			case 'locations':
				arbo = KnxProject.Locations;
			break;
		}
		if(arbo != null){
			$('.autoCreate .level').html(getLevelSelect(getNbLevel(arbo,0)));
			$('.autoCreateParameter[data-l1key=levelType]').off().on('change',function() {
				if($(this).val() != 'object' && $(this).val() != ''){
					if($('.autoCreateParameter[data-l1key=levelType] option[value='+$(this).val()+']:selected').length > 1){
						$(this).val('');
						alert('{{Impossible d\'avoir plusieur champs Equipement ou commmandes}}');
					}
				}
			});
		}		
	});
	$('.autoCreateParameter[data-l1key=createEqLogic]').off().on('change',function() {
 		if(this.checked) {
			$('.autoCreate .withCreate').show();
			$('.autoCreate .withCreateEqLogic').show();
		}else{
			$('.autoCreate .withCreate').hide();
			$('.autoCreate .withCreateEqLogic').hide();
		}
	});
	$('.autoCreateParameter[data-l1key=createObjet]').off().on('change',function() {
 		if(this.checked) {
			$('.autoCreate .withCreate').show();
		}else{
			$('.autoCreate .withCreate').hide();
		}
	});
	$('.autoCreateParameter[data-l1key=arboresance]').trigger('change');
}
