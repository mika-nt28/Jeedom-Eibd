function ImportEts(merge){
	var html $('<form class="form-horizontal" onsubmit="return false;">  ')
		.append($('<div class="form-group">')
			.append($('<label class="col-md-4 control-label">')
				.append('{{Type de fichier}}')
				.append($('<sup>')
					.append($('<i class="fa fa-question-circle tooltips" title="{{Sélectioner le type de fichier}}">'))))
			.append($('<select class=" EtsParseParameter" data-l1key="ProjetType">')
				.append($('<option value="ETS">')
					.append('{{ETS}}'))
				.append($('<option value="TX100">')
					.append('{{TX100}}'))));

	bootbox.dialog({
		title: "{{Importer votre projet KNX}}",
		message: html,
		buttons: {
			"Annuler": {
				className: "btn-default",
				callback: function () {
				}
			},
			success: {
				label: "Valider",
				className: "btn-primary",
				callback: function () {
					$.ajax({
						type: 'POST',   
						url: 'plugins/eibd/core/ajax/eibd.ajax.php',
						data:
						{
							action: 'AnalyseEtsProj',
							merge: merge,
							ProjetType: $('.EtsParseParameter[data-l1key=ProjetType]').val()
						},
						dataType: 'json',
						global: true,
						error: function(request, status, error) {},
						success: function(data) {
							bootbox.confirm({
								message: "This is a confirm with custom button text and color! Do you like it?",
								buttons: {
									confirm: {
										label: '{{Oui}}',
										className: 'btn-success'
									},
									cancel: {
										label: '{{Non}}',
										className: 'btn-danger'
									}
								},
								callback: function (result) {
									if(result){
										ImportEts(true);
									}else{
										CreateArboressance(data.result.Devices,$('.MyDeviceGroup'),true);
										CreateArboressance(data.result.GAD,$('.MyAdressGroup'),true);
										CreateArboressance(data.result.Locations,$('.MyLocationsGroup'),true);
									}
								}
							});
						}
					});
				}
			},
		}
	});
}
