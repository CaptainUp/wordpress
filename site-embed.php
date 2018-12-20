<?php
	// `captain_start` adds the Captain Up script asynchronously to the footer.
	// It's only called from `cptup_print` if the API Key was set properly.
	function captain_start() {

		// Grab a reference to the API Key token and Secret
		$captain_api_key = get_option('captain-api-key');
		$api_secret = get_option('captain-api-secret', false);
		$client_token = get_option('captain-client-token', false);
		// Detect whether the user integration feature is enabled
		$user_integration_enabled = get_option('captain-user-integration-checkbox') == 'checked';
		// Detect whether we have a valid API secret.
		$valid_api_secret = CaptainUtils::is_valid_api_secret($api_secret);

		// Add the locale suffix to the embed script. Note that the default value that
		// `get_option` receives only affects values which are not already set in the
		// database. So if an empty string was saved, we make sure to handle it gracefully.
		$current_lang = get_option('captain-locale', 'en');
		// Set the current locale to English by default
		if (empty($current_lang)) $current_lang = 'en';
		// Add a a dot to the language
		$lang = "." . $current_lang;

		// Retrieve the current user details
		$current_user = wp_get_current_user();
		// Create a $captain_user PHP hash for the user integration, if it's enabled.
		// User integration is only enabled if it was enabled by the admin, if the
		// API secret is valid, and if the current WordPress user is registered.
		if ($user_integration_enabled && $valid_api_secret && $current_user->ID != 0) {
			$captain_user = array(
				'id' => $current_user->ID,
				'name' => $current_user->display_name,
				'first_name' => $current_user->first_name,
				'last_name' => $current_user->last_name,
				'image' => CaptainUtils::get_avatar_url($current_user->ID)
			);
		}

		?>

		<div id='cptup-ready'></div>
		<script data-cfasync='false' type='text/javascript'>
			window.captain = {up: function(fn) { captain.topics.push(fn) }, topics: []};
			captain.up({
				api_key: '<?php echo $captain_api_key; ?>',
				platform: 'wordpress',
				cookie: true,
				<?php if(isset($captain_user) && is_array($captain_user)) { ?>
				user: <?php echo json_encode($captain_user) ?>,
				signed_user: "<?php echo CaptainUtils::sign_user($api_secret, $captain_user) ?>",
				client_token: "<?php echo $client_token ?>"
				<?php } ?>
			});
		</script>

		<script data-cfasync='false' type='text/javascript'>
			(function() {
					var cpt = document.createElement('script'); cpt.type = 'text/javascript'; cpt.async = true;
					cpt.src = '<?php echo CaptainUtils::get_captain_domain(); ?>/assets/embed<?php echo $lang; ?>.js';
					(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(cpt);
			 })();
		</script>

<?php
}

