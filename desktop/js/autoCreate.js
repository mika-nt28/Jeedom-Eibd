function autoCreate(merge){
	var html = $('<form class="form-horizontal" onsubmit="return false;">');
  
	html.append($('<div class="form-group">') 
				.append($('<label class="col-md-4 control-label">') 
        .append($('{{Créer les objets}}
					.append($('<sup>
						.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option autorise le parser a créer automatiquement les objets trouvés selon la definition des level defini precedement dans l'arboresance de groupe}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label> 
				.append($('<input type="checkbox" class="EtsParseParameter" data-l1key="createObjet"/>
			</div> 
	html.append($('<div class="form-group">') 
				.append($('<label class="col-md-4 control-label">
        .append($('{{Créer les equipements}}
					.append($('<sup>
						.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option autorise le parser a créer automatiquement les equipements trouvés selon la definition des level defini precedement dans l'arboresance de groupe}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label> 
				.append($('<input type="checkbox" class="EtsParseParameter" data-l1key="createEqLogic"/>
			</div> 
	html.append($('<div class="form-group withCreate">') 
				.append($('<label class="col-md-4 control-label">
        .append($('{{Arborescence des groupes}}
					.append($('<sup>
						.append($('<i class="fa fa-question-circle tooltips" title="{{La définition de l'arboressance de groupe permet au parser de connaitre ou se situe le nom a prendre pour la creation automatique des objets ou des equipemnt}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label> 
				<?php
				for($loop = 0; $loop < config::byKey('level','eibd'); $loop++){
					echo '<select class=" EtsParseParameter" data-l1key="'.$loop.'">';
					echo '	<option value="object">{{Objet}}</option>';
					echo '	<option value="function">{{Fonction}}</option>';
					echo '	<option value="cmd">{{Commande}}</option>';
					echo '</select>';
				}
				?>
			</div> 
	html.append($('<div class="form-group withCreateEqLogic">') 
				.append($('<label class="col-md-4 control-label">
        .append($('{{Uniquement correspondant a un Template}}
					.append($('<sup>
						.append($('<i class="fa fa-question-circle tooltips" title="{{Cette option permet de filtrer la creation d'equipement a ceux qui corresponde a un Template (Nom du Template et des commandes}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label> 
				<input type="checkbox" class="EtsParseParameter" data-l1key="createTemplate"/>
			</div> 
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
							action: 'AnalyseEtsProj',
							merge: merge,
							ProjetType: $('.EtsParseParameter[data-l1key=ProjetType]').val()
						},
						dataType: 'json',
						global: true,
						error: function(request, status, error) {},
						success: function(data) {
							bootbox.confirm({
								message: "{{Voulez vous importer un autre fichier projet?}}",
								buttons: {
									confirm: {
										label: '{{Oui}}',
										className: 'btn-success'
									},
									cancel: {
										label: '{{Non}}',
										className: 'btn-danger'
									}
								},
								callback: function (result) {
									if(result){
										ImportEts(true);
									}else{
										CreateArboressance(data.result.Devices,$('.MyDeviceGroup'),true);
										CreateArboressance(data.result.GAD,$('.MyAdressGroup'),true);
										CreateArboressance(data.result.Locations,$('.MyLocationsGroup'),true);
									}
								}
							});
						}
					});
				}
			},
		}
	});
}
