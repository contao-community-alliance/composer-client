$(window).addEvent('domready', function() {
	$$('.tabs').each(function(container) {
		var tabs  = container.getElements('> ul > li > a');
		var panes = container.getElements('> section');

		tabs.addEvent('click', function(e) {
			var tab  = $(e.target);
			var href = tab.getAttribute('href');
			var id   = href.substring(1);
			var pane = $(id);

			tabs.removeClass('active');
			tab.addClass('active');

			panes.removeClass('active');
			pane.addClass('active');

			e.preventDefault();
		});
		console.log(tabs);
		console.log(panes);
	});
});