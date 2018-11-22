<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
?>
<div class="row">  	
	<div class="col-md-12"> 
		<p>Cette option du plugin permet de configurer automatiquement votre installation sous Jeedom.</p>
		<p><b>Attention :</b></p>
		<p>Cette opération peut etre longue.</p>
		<p>Il est possible que tous les possibilitées de programation ne soit pas pris en compte, il est impératif de verifier et compléter la configuration a la fin de lexecution</p>
		<form class="form-horizontal" onsubmit="return false;"> 
			<div class="form-group"> 
				<label class="col-md-4 control-label">{{Nom}}</label> 
				<input type="file" name="Knxproj" id="Knxproj" data-url="plugins/eibd/core/ajax/eibd.ajax.php?action=EtsParser" placeholder="{{Ficher export ETS}}" class="form-control input-md"/>
				<div class="col-md-4"></div> 
			</div> 
		</form> 
		<pre class="EtsImportData"></pre>
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
			$('.EtsImportData').append(data.result);
		}
	});
</script>
