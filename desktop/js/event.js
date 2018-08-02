$('body').off().on('eibd::GadInconnue', function (_event,_options) {
	var value=jQuery.parseJSON(newGadOption);
  	$.load('index.php?v=d&modal=eibd.gadInconnue&plugin=eibd&type=eibd', function() {
		bootbox.dialog({
			title: "{{Gad inconnue détecté}}",
			height: "800px",
			width: "auto",
			message: Html,
			buttons: {
				"Annuler": {
					className: "btn-default",
					callback: function () {
						//el.atCaret('insert', result.human);
					}
				},
				success: {
					label: "Ajouter a un equipement",
					className: "btn-primary",
					callback: function () {
						//input.closest('.modal-body').find('.EqLogicTemplateAttr[data-l1key=logicalId]').val(SelectAddr);	
						//input.val(SelectGad);
					}
				},
			}
		});
	}
});
