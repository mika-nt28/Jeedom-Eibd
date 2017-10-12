<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
//if(!isset($_REQUEST['SelectAddr']))
	echo '<script>var SelectAddr="'.$_REQUEST['SelectAddr'].'";</script>';
//if(!isset($_REQUEST['SelectDpt']))
	echo '<script>var SelectDpt="'.$_REQUEST['SelectDpt'].'";</script>';
?>
<style>
	table #table_GadInconue {
	    width: 100%;
	    display:block;
	}
	thead #table_GadInconue {
	    display: inline-block;
	    width: 100%;
	}
	tbody #table_GadInconue {
	    height: 200px;
	    display: inline-block;
	    width: 100%;
	    overflow: auto;
	}
</style>
<table id="table_GadInconue" class="table table-bordered table-condensed tablesorter">
    <thead>
        <tr>
            <th>{{Equipement}}</th>
            <th>{{Source}}</th>
            <th>{{Commande}}</th>
            <th>{{Destination}}</th>
            <th>{{Data Point Type}}</th>
            <th>{{Derniere valeur}}</th>
		<?php
  			if(!isset($_REQUEST['param']))
           			echo '<th>{{Action sur cette adresse de groupe}}</th>';
		?>
        </tr>
    </thead>
    <tbody style="height: 500px;"></tbody>
</table>
<script>
var SelectGad='';
initTableSorter();
getKnxGadInconue();
function getKnxGadInconue () {
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data: {
			action: 'getCacheGadInconue',
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
			setTimeout(function() {
				getKnxGadInconue()
			}, 100);
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#table_GadInconue tbody').html('');
			jQuery.each(jQuery.parseJSON(data.result),function(key, value) {
				var tr=$("<tr>");
				if (typeof(value.DeviceName) !== 'undefined') 
					tr.append($("<td>").text(value.DeviceName));
				else
					tr.append($("<td>"));
				tr.append($("<td>").text(value.AdressePhysique));
				if (typeof(value.cmdName) !== 'undefined') 
					tr.append($("<td>").text(value.cmdName));
				else
					tr.append($("<td>"));
				tr.append($("<td>").text(value.AdresseGroupe));
				tr.append($("<td>").text(value.DataPointType));
				tr.append($("<td>").text(value.valeur));
             			if($('#table_GadInconue thead th').length == 7){
					tr.append($("<td>")
						.append($('<a class="btn btn-danger btn-xs Gad pull-right" data-action="remove">')
							.append($('<i class="fa fa-minus-circle">'))
							.text('{{Supprimer}}'))
						.append($('<a class="btn btn-primary btn-xs Gad pull-right" data-action="addEqLogic">')
							.append($('<i class="fa fa-check-circle">'))
							.text('{{Ajouter a un equipement}}')));
				}
			      	$('#table_GadInconue tbody').append(tr);
			});				
			$('#table_GadInconue').trigger('update');
			if($('#table_GadInconue thead th').length == 6){
				$(".tablesorter-filter[data-column=1]").val(SelectAddr);
				$(".tablesorter-filter[data-column=1]").trigger('keyup');
				$(".tablesorter-filter[data-column=4]").val(SelectDpt);
				$(".tablesorter-filter[data-column=4]").trigger('keyup');
			}
			if ($('#md_modal').dialog('isOpen') === true) {
				setTimeout(function() {
					getKnxGadInconue()
				}, 1000);
			}
		}
	});
}
$('body').on('click', '.Gad[data-action=addEqLogic]', function(){
	var gad=$(this).closest('tr').find('td:eq(3)').text();
	jeedom.eqLogic.getSelectModal({},function (result) {
		removeInCache(gad,result.id);
	}); 
	$(this).closest('tr').remove();
});
$('body').on('click', '.Gad[data-action=remove]', function(){
	var gad=$(this).closest('tr').find('td:eq(3)').text();
	removeInCache(gad, false);
	$(this).closest('tr').remove();
});	
$('body').on('click', '#table_GadInconue tbody tr', function(){
	SelectGad=$(this).closest('tr').find('td:eq(3)').text();
	SelectAddr=$(this).closest('tr').find('td:eq(1)').text();
});	
function removeInCache(gad, destination){
	$.ajax({
		type: 'POST',
		async: false,
		url: 'plugins/eibd/core/ajax/eibd.ajax.php',
		data: {
			action: 'setCacheGadInconue',
			gad:gad,
			eqLogic:destination
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			if(data.result != false){
				bootbox.confirm('{{Souhaitez vous aller a la page de configuration de l\'équipement}}', function (result) {
					if (result)
						$(location).attr('href',$(location).attr('href')+'&id='+data.result)
				});
			}
		}
	});
}
</script>
