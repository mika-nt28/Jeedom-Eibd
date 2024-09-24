<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
?>
<legend><a class="btn btn-danger btn-xs BusMonitorAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Nettoyer}}</a></legend>
<div style="height: 500px;overflow: auto;">
	<table id="table_BusMonitor" class="table table-bordered table-condensed tablesorter">
		<thead>
			<tr>
				<th>{{Date}}</th>
				<th>{{Mode}}</th>
				<th>{{Source}}</th>
				<th>{{Commande Jeedom}}</th>
				<th>{{Destination}}</th>
				<th>{{Data}}</th>
				<th>{{DPT}}</th>
				<th>{{Valeur}}</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
	<script>
	jeedomUtils.initTableSorter();
	$('.BusMonitorAction[data-action=remove]').off().on('click', function () {
		$('#table_BusMonitor tbody tr').remove();
	});	
	$('body').off('eibd::monitor').on('eibd::monitor', function (_event,_options) {
		$('#table_BusMonitor tbody').prepend($("<tr>")
			.append($("<td>").text(_options.datetime))
			.append($("<td>").text(_options.Mode))
			.append($("<td>").text(_options.AdressePhysique))
			.append($("<td>").text(_options.cmdJeedom))
			.append($("<td>").text(_options.AdresseGroupe))
			.append($("<td>").text(_options.data))
			.append($("<td>").text(_options.DataPointType))
			.append($("<td>").text(_options.valeur)));		
		if($('#table_BusMonitor tbody tr').length >= 255)
			$('#table_BusMonitor tbody tr:last').remove();
		$('#table_BusMonitor').trigger('update');
	});	   
	</script>
</div>
