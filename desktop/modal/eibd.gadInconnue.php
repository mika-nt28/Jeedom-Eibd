<?php
if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
?>
<div>
	<center>
		<span style="font-size : 12px;" >Equipement : </span> 
		<strong class="equipement"></strong>
	</center>
	<center>
		<span style="font-size : 12px;" >Source : </span> 
		<strong class="source"></strong>
	</center>
	<center>
		<span style="font-size : 12px;" >Commande : </span> 
		<strong class="cmd"></strong>
	</center>
	<center>
		<span style="font-size : 12px;" >Data Point Type : </span> 
		<strong class="dpt"></strong>
	</center>
	<center>
		<span style="font-size : 12px;" >Destination : </span> 
		<strong class="destination"></strong>
	</center>
	<center>
		<span style="font-size : 12px;" >Valeur : </span> 
		<strong class="valeur"></strong>
	</center>
</div>
<div>
	<select class="actionIncludeGad">
		<option value="template">{{Ajouter a un template}}</option>
		<option value="equipement">{{Ajouter a un equipement}}</option>
		<option value="save">{{Enregister pour plus tard}}</option>
	</select>
</div>
<script>
	$('body').off().on('change','.actionIncludeGad', function(){
		switch($(this).val()){
			case 'template':
				//afficher un select avec les template deja configurer
			break;
			case 'equipement':
				//afficher la liste des equipement dans un select (proposer un ajout)
				//afficher la liste des commande de cette equipement dans un select (proposer un ajout)
			break;
		}
	})
</script>	
