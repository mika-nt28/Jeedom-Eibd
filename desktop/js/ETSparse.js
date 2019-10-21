function ImportEts(){
  return $('<form class="form-horizontal" onsubmit="return false;">  ')
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
}
	bootbox.dialog({
		title: "{{Importer votre projet KNX}}",
    message: html,
		buttons: {
			"Annuler": {
				className: "btn-default",
				callback: function () {
					//el.atCaret('insert', result.human);
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
							option: $('body .EtsParserDiv').getValues('.EtsParseParameter')
						},
						dataType: 'json',
						global: true,
						error: function(request, status, error) {},
						success: function(data) {
							if($('body .EtsParserDiv .EtsParseParameter[data-l1key=createEqLogic]').is(':checked')){
								window.location.reload();
							}else{
								//UpdateDeviceTable(data.result.Devices);
								CreateArboressance(data.result.Devices,$('.MyDeviceGroup'),true);
								CreateArboressance(data.result.GAD,$('.MyAdressGroup'),true);
								CreateArboressance(data.result.Locations,$('.MyLocationsGroup'),true);
							}
						}
					});
				}
			},
		}
	});
});
