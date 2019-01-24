<?php
if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
$eqLogics = eibd::byType('eibd');
?>
<div class="row">
	<table id="table_healthEibd" class="table table-bordered table-condensed tablesorter">
		<thead>
			<tr>
				<th></th>
				<th>{{ID}}</th>
				<th>{{Module}}</th>
				<th>{{Adresse Physique}}</th>
				<th>{{Statut}}</th>
				<th>{{Batterie}}</th>
				<th>{{Dernière communication}}</th>
				<th>{{Date création}}</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($eqLogics as $eqLogic) {
				$file='plugins/eibd/core/config/devices/'.$eqLogic->getConfiguration('typeTemplate').'.png';
				if(file_exists($file))
					echo '<td><img src="'.$file.'" height="55"  /></td>';
				else
					echo '<td><img src="plugins/eibd/plugin_info/eibd_icon.png" height="55" /></td>';
				echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getId() . '</span></td>';
				echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getHumanName() . '</span></td>';
				echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getLogicalId() . '</span></td>';
				$status = '<span class="label label-success" style="font-size : 1em;cursor:default;">{{OK}}</span>';
				if ($eqLogic->getStatus('state') == 'nok') {
					$status = '<span class="label label-danger" style="font-size : 1em;cursor:default;">{{NOK}}</span>';
				}
				echo '<td>' . $status . '</td>';
				$battery_status = '<span class="label label-success" style="font-size : 1em;">{{OK}}</span>';
				if ($eqLogic->getStatus('battery') < 20 && $eqLogic->getStatus('battery') != '') {
					$battery_status = '<span class="label label-danger" style="font-size : 1em;">' . $eqLogic->getStatus('battery') . '%</span>';
				} elseif ($eqLogic->getStatus('battery') < 60 && $eqLogic->getStatus('battery') != '') {
					$battery_status = '<span class="label label-warning" style="font-size : 1em;">' . $eqLogic->getStatus('battery') . '%</span>';
				} elseif ($eqLogic->getStatus('battery') > 60 && $eqLogic->getStatus('battery') != '') {
					$battery_status = '<span class="label label-success" style="font-size : 1em;">' . $eqLogic->getStatus('battery') . '%</span>';
				} else {
					$battery_status = '<span class="label label-primary" style="font-size : 1em;" title="{{Secteur}}"><i class="fa fa-plug"></i></span>';
				}
				echo '<td>' . $battery_status . '</td>';
				echo '<td><span class="label label-info" style="font-size : 1em;cursor:default;">' . $eqLogic->getStatus('lastCommunication') . '</span></td>';
				echo '<td><span class="label label-info" style="font-size : 1em;cursor:default;">' . $eqLogic->getConfiguration('createtime') . '</span></td></tr>';
			}
		?>
		</tbody>
	</table>
</div>
