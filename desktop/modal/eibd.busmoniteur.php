<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
?>
<table id="table_BusMonitor" class="table table-bordered table-condensed tablesorter">
    <thead>
        <tr>
            <th>{{Date}}</th>
            <th>{{Mode}}</th>
            <th>{{Source}}</th>
            <th>{{Destination}}</th>
            <th>{{Data}}</th>
            <th>{{DPT}}</th>
            <th>{{Valeur}}</th>
        </tr>
    </thead>
    <tbody style="height: 500px;"></tbody>
</table>
<script>
initTableSorter();
$('body').off().on('eibd::monitor', function (_event,_options) {
	var monitors=jQuery.parseJSON(_options);
	$('#table_BusMonitor tbody').prepend($("<tr>")
		.append($("<td>").text(monitors.datetime))
		.append($("<td>").text(monitors.Mode))
		.append($("<td>").text(monitors.AdressePhysique))
		.append($("<td>").text(monitors.AdresseGroupe))
		.append($("<td>").text(monitors.data))
		.append($("<td>").text(monitors.DataPointType))
		.append($("<td>").text(monitors.valeur)));			
	$('#table_BusMonitor').trigger('update');
});	   
</script>
		
