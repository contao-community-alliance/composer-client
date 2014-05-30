
$(window).addEvent('domready', function() {
	var inputs = $$('table.composer_package_list input[name="select"]');
	inputs.removeProperty('disabled').addEvent('change', function() {
		var names = [];
		inputs.each(function(input) {
			if (input.checked) {
				names.push(input.value);
			}
		});
		$('package_names').value = names.join(',');
	});
});
