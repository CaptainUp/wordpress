(function($) {

	// `toggle_leaderboard_option` simply shows or hides the 'select
	// default leaderboard' dropdown based on the type of the leaderboard.
	// It's called whenever a change is made in the dropdown to select
	// the widget type and when the page is initialized.
	window.toggle_leaderboard_option = function(element) {
		var $type_select = $(element);
		// Get the widget value, either 'activity' or 'leaderboard'
		var widget_type = $type_select.val();
		// Grab the `<p/>` where the dropdown to select a default
		// leaderboard (monthly, weekly, etc.) is shown.
		var leaderboard_default = $type_select
			.parent().parent().find('.cpt-select-leaderboard-type');

		// If the widget is a leaderboard widget show the dropdown
		// to select the default leaderboard, otherwise hide it.
		if (widget_type === 'leaderboard') {
			leaderboard_default.show();
		} else {
			leaderboard_default.hide();
		}
	};


	// On DOM Ready
	$(function() {
		// Every time the Widget Type dropdown option is changed, show or
		// hide the 'Select Leaderboard Type' option.
		$('body').on('change', '.cpt-widget-type-select', function(event) {
			toggle_leaderboard_option(event.currentTarget);
		});
	});

})(jQuery, undefined)