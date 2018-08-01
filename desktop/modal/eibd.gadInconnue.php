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
