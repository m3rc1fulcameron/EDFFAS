$(document).ready(function() {
	alert(formDefaults);
	
	if (formDefaults) {
		for (var form in formDefaults) {
			console.log('Setting values for form: ' + form)
			for (var field in formDefaults[form]) {
				if (formDefaults[form][field]) {
					console.log('Setting value: ' + formDefaults[form][field] + ' to field: ' + field);
					var selector = '#' + form + ' #' + field;
					console.log('Autofill using selector: ' + selector);
					$(selector).val(formDefaults[form][field]);
				}
			}
		}
	} else {
		console.log("No form defaults.");
	}
});