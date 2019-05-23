<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

?>
<div class='Log'></div>
<script>
getLog();
function getLog(){
	$.ajax({
		type: "POST",
		timeout:8000, 
		url: "plugins/eibd/core/ajax/eibd.ajax.php",
		data: {
			action: "getLog",
		},
		dataType: 'json',
		error: function(request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function(data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('.Log').html(data.result);
		}
	});	
}
</script>
