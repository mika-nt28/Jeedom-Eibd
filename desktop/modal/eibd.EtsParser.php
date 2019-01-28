<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
?>
<div class="row EtsParserDiv">  	
	<div class="col-md-12"> 
		<p>{{Le parser ETS permet d'importer tous vos GAD et de simplifer la creation des commandes.}}</p>
		<p>{{Le parser ETS permet aussi de cree automatiquement des commandes}}</p>
		<p>{{Le parser ETS permet aussi de cree automatiquement des Template, pour cela les nom de votre projet ETS doivent etre identique au nom des Templates et de leur commandes}}</p>
		<p>{{Il est possible que tous les possibilitées de programation ne soit pas pris en compte, il est impératif de verifier et compléter la configuration a la fin de l'execution}}</p>
		<p><b>{{Attention :}}</b></p>
		<p>{{Cette opération peut etre longue.}}</p>
		<form class="form-horizontal" onsubmit="return false;"> 
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Créer les objets}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{Cette option autorise le parser a créer automatiquement les objets trouvés selon la definition des level defini precedement dans l'arboresance de groupe}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label> 
				<input type="checkbox" class="EtsParseParameter" data-l1key="createObjet"/>
			</div> 
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Créer les equipements}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{Cette option autorise le parser a créer automatiquement les equipements trouvés selon la definition des level defini precedement dans l'arboresance de groupe}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label> 
				<input type="checkbox" class="EtsParseParameter" data-l1key="createEqLogic"/>
			</div> 
			<div class="form-group withCreate"> 
				<label class="col-md-4 control-label">{{Arboressance des groupes}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{La définition de l'arboressance de groupe permet au parser de connaitre ou se situe le nom a prendre pour la creation automatique des objets ou des equipemnt}}" style="font-size : 1em;color:grey;"></i>
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
			<div class="form-group withCreateEqLogic"> 
				<label class="col-md-4 control-label">{{Uniquement correspondant a un Template}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{Cette option permet de filtrer la creation d'equipement a ceux qui corresponde a un Template (Nom du Template et des commandes}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label> 
				<input type="checkbox" class="EtsParseParameter" data-l1key="createTemplate"/>
			</div> 
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Importer votre projet}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{Uploader votre projet ETS (*.knxproj)}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label> 
				<input type="file" name="Knxproj" id="Knxproj" data-url="plugins/eibd/core/ajax/eibd.ajax.php?action=EtsParser" placeholder="{{Ficher export ETS}}" class="form-control input-md"/>
			</div> 
		</form> 
	</div>  
</div>
<script>
	$('.withCreate').hide();
	$('.withCreateEqLogic').hide();
	$('.EtsParseParameter[data-l1key=createEqLogic]').change(function() {
 		if(this.checked) {
			$('.withCreate').show();
			$('.withCreateEqLogic').show();
		}else{
			$('.withCreate').hide();
			$('.withCreateEqLogic').hide();
		}
	});
	$('.EtsParseParameter[data-l1key=createObjet]').change(function() {
 		if(this.checked) {
			$('.withCreate').show();
		}else{
			$('.withCreate').hide();
		}
	});
	$('#Knxproj').fileupload({
		dataType: 'json',
		replaceFileInput: false,
		//done: function (data) {
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#div_alert').showAlert({message: "Import ETS complet.</br>Vous pouvez commancer la configuration des equipements", level: 'success'});
			//$('.EtsImportData').append(data.result);
		}
	});
</script>
