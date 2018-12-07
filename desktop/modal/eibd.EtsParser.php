<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
?>
<div class="row EtsParserDiv">  	
	<div class="col-md-12"> 
		<p>Cette option du plugin permet de configurer automatiquement votre installation sous Jeedom.</p>
		<p><b>Attention :</b></p>
		<p>Cette opération peut etre longue.</p>
		<p>Il est possible que tous les possibilitées de programation ne soit pas pris en compte, il est impératif de verifier et compléter la configuration a la fin de lexecution</p>
		<form class="form-horizontal" onsubmit="return false;"> 
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Arboressance des groupes}}</label> 
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
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Créer les equipements}}</label> 
				<input type="checkbox" class="EtsParseParameter" data-l1key="createEqLogic"/>
			</div> 
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Uniquement correspondant a un Template}}</label> 
				<input type="checkbox" class="EtsParseParameter" data-l1key="createTemplate"/>
			</div> 
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Créer les objet}}</label> 
				<input type="checkbox" class="EtsParseParameter" data-l1key="createObjet"/>
			</div> 
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Importer votre projet}}</label> 
				<input type="file" name="Knxproj" id="Knxproj" data-url="plugins/eibd/core/ajax/eibd.ajax.php?action=EtsParser" placeholder="{{Ficher export ETS}}" class="form-control input-md"/>
			</div> 
		</form> 
	</div>  
</div>
<script>
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
