<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

?>
<table id="table_Log" class="table table-bordered table-condensed tablesorter">
	<thead>
		<tr>
			<th>{{Date}}</th>
			<th>{{Message}}</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<script>
getLog();
function getLog(){
	$.ajax({
		type: "POST",
		timeout:8000,
		async: false, 
		url: "plugins/eibd/core/ajax/eibd.ajax.php",
		data: {
			action: "getLog",
		},
		dataType: 'json',
		error: function(request, status, error) {
			setTimeout(function() {
				getLog()
			}, 100);
		},
		success: function(data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#table_Log tbody').html('');
			$.each(data.result,function(key, value) {
				var tr=$("<tr>");
				tr.append($("<td>").text(timeConverter(parseInt(value.__REALTIME_TIMESTAMP)/1000)));
				tr.append($("<td>").text(value.MESSAGE));
				$('#table_Log tbody').append(tr);
			});	
			setTimeout(function() {
				getLog()
			}, 500);
		}
	});	
}
function timeConverter(UNIX_timestamp){
	var a = new Date(UNIX_timestamp);
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var year = a.getFullYear();
	var month = months[a.getMonth()];
	var date = a.getDate();
	var hour = a.getHours();
	var min = a.getMinutes();
	var sec = a.getSeconds();
	var milisec = a.getMilliseconds()
	var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec + ':' + milisec;
	return time;
}
</script>
