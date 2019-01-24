<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
?>
<div class="row">
	<form class="form-horizontal" onsubmit="return false;" style="height: 500px;">
		<legend>{{Définition de l'equipement}}</legend>
		<div class="col-md-12"  style="height: 40%;">
			<div class="form-group">
				<label class="col-md-5 control-label">
					{{Nom de l'équipement KNX}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{Indiquez le nom de votre équipement}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label>
				<div class="col-md-5">
					<input type="text" class="EqLogicTemplateAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement KNX}}"/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-5 control-label ">{{Adresse Physique de l'équipement}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{Indiquez l'adresse physique de votre équipement. Cette information n'est pas obigatoire mais peut etre utile dans certain cas. Pour la trouver, il faut la retrouver sur le logiciel ETS}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label>
				<div class="col-md-5">
					<input type="text" class="EqLogicTemplateAttr form-control" data-l1key="logicalId"/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-5 control-label" >
					{{Objet parent}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{Indiquez l'objet dans lequel le widget de cette equipement apparaiterai sur le dashboard}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label>
				<div class="col-md-5">
					<select id="sel_object" class="EqLogicTemplateAttr form-control" data-l1key="object_id">
						<?php
						foreach (object::all() as $object) {
							echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-5 control-label" >
					{{Template de votre équipement}}
					<sup>
						<i class="fa fa-question-circle tooltips" title="{{Choisir le template de votre nouvelle equipement}}" style="font-size : 1em;color:grey;"></i>
					</sup>
				</label>
				<div class="col-md-5">
					<select class="EqLogicTemplateAttr form-control" data-l1key="template">
						<option value="">{{Séléctioner votre template}}</option>
						<?php
						foreach (eibd::devicesParameters() as $id => $template) {		
							echo '<option value="' . $id . '">' . $template['name'] . '</option>';
						}
						?>
					</select>
				</div>
			</div>		
		</div>
		<legend>{{Définition des commandes}}</legend>
		<div class="col-md-12" style="height: 40%;overflow: auto;">
			<div class="form-horizontal CmdsTempates">
			</div>
		</div>
	</form>		
	<script>
		$('.EqLogicTemplateAttr[data-l1key=template]').off().on('change', function () {
			var cmds=$('.CmdsTempates');
			cmds.html('');
			$.ajax({
				type: 'POST',   
				url: 'plugins/eibd/core/ajax/eibd.ajax.php',
				data:
				{
					action: 'getTemplate',
					template:$(this).val()
				},
				dataType: 'json',
				global: true,
				error: function(request, status, error) {},
				success: function(data) {
					$.each(data.result.cmd,function(index, value){
						cmds.append($('<div class="form-group">')
							.append($('<label class="col-md-5 control-label" >')
								.text(value.name + " (DPT: " + value.configuration.KnxObjectType + ")"))
							.append($('<div class="col-md-5">')
								.append($('<div class="input-group">')
									.append($('<input type="hidden" class="CmdEqLogicTemplateAttr form-control input-sm" data-l2key="KnxObjectType">')
										.val(value.configuration.KnxObjectType))
									.append($('<input class="CmdEqLogicTemplateAttr form-control input-sm" data-l1key="'+index+'">'))
									.append($('<span class="input-group-btn">')
										.append($('<a class="btn btn-success btn-sm bt_selectGadInconnue">')
											.append($('<i class="fa fa-list-alt">')))))));


					});
				}
			});
		});
	</script>
</div>
