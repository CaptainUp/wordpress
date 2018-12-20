<?php

// WordPress Admin Panel settings for the Captain Up plugin
// --------------------------------------------------------------------------------
function cptup_admin_settings() {

	if (!current_user_can('administrator')) {
		wp_die('You do not have permissions to access this page', 'Unauthorized');
	}

	if (isset($_POST['submit'])) {
		// Mark whether this is a new install of Captain Up. We'll
		// later display different messages based on this.
		if (get_option('captain-api-key') == "") {
			update_option('captain-first-install', true);
		} else {
			update_option('captain-first-install', false);
		}
		// Update the plugin's API key. This must happen before we retrieve
		// the app data.
		$captain_api_key = sanitize_text_field($_POST['captain-api-key']);
		update_option('captain-api-key', $captain_api_key);
	}

	// Retrieve the Captain Up API key
	$captain_api_key = get_option('captain-api-key');
	// Retrieve the app data, based on the app's API key
	$app_data = CaptainUtils::get_app_data($captain_api_key);
	// If we retrieved the app data successfully, use its information to pick
	// the current pricing plan for the app. Otherwise, mark the app as using
	// the free plan.
	if ($app_data) {
		$is_free_plan = CaptainUtils::is_free_plan($app_data);
	} else {
		$is_free_plan = true;
	}

	// Save the Captain Up options on POST
	if (isset($_POST['submit'])) {
		// Update the Captain Up locale setting
		$captain_locale = sanitize_text_field($_POST['captain-locale']);
		update_option('captain-locale', $captain_locale);

		// Only update the disabled paths if they are set, to prevent us from
		// erasing the data if the input was disabled.
		if (isset($_POST['captain-disabled-paths'])) {
			$captain_disabled_paths = sanitize_text_field($_POST['captain-disabled-paths']);
			update_option('captain-disabled-paths', $captain_disabled_paths);
		}
		if (isset($_POST['captain-enabled-paths'])) {
			$captain_enabled_paths = sanitize_text_field($_POST['captain-enabled-paths']);
			update_option('captain-enabled-paths', $captain_enabled_paths);
		}

		// Save the path configuration option, whether we are using a whitelist
		// or a blacklist for the paths.
		if (isset($_POST['captain-show-paths-type']) &&
			$_POST['captain-show-paths-type'] == 'whitelist') {
			update_option('captain-show-paths-type', 'whitelist');
		} else {
			update_option('captain-show-paths-type', 'blacklist');
		}

		// Update whether we should hide Captain Up on the homepage
		if (isset($_POST['captain-hide-on-homepage-checkbox']) &&
			$_POST['captain-hide-on-homepage-checkbox'] == 'Yes') {
			update_option('captain-hide-on-homepage-checkbox', 'checked');
		} else {
			update_option('captain-hide-on-homepage-checkbox', '');
		}

		// Only save and update advanced options for using on our paid plans
		if (!$is_free_plan) {
			// Update the API secret and Client token of the plugin
			$captain_api_secret = sanitize_text_field($_POST['captain-api-secret']);
			$captain_client_token = sanitize_text_field($_POST['captain-client-token']);
			update_option('captain-api-secret', trim($captain_api_secret));
			update_option('captain-client-token', trim($captain_client_token));
			// Update whether user integration is enabled or not
			if (isset($_POST['captain-user-integration-checkbox']) &&
				$_POST['captain-user-integration-checkbox'] == 'Yes') {
				update_option('captain-user-integration-checkbox', 'checked');
			} else {
				update_option('captain-user-integration-checkbox', '');
			}
		}

		// We're using JavaScript to redirect back to the page as a GET
		// request. The reason we do that in JS instead of PHP is that
		// the headers were already sent. This is a hack, like everything
		// in WordPress.
		?>
			<script>
				var url = location.href;
				url +=  ~url.indexOf('submitted=true') ? '' : '&submitted=true';
				location.replace(url);
			</script>
		<?php
	}

	// Get the Captain Up API Secret
	$captain_api_secret = get_option('captain-api-secret');
	// Get the Captain Up Client Token
	$captain_client_token = get_option('captain-client-token');

	// get the `captain-user-integration-checkbox` status
	$captain_user_integration = get_option('captain-user-integration-checkbox');

	// Get the status of the radio button that controls whether we show the blacklist
	// or the whitelist as currently enabled.
	if (get_option('captain-show-paths-type') == 'whitelist') {
		$captain_whitelist_check = 'checked';
		$captain_blacklist_check = '';
	} else {
		$captain_whitelist_check = '';
		$captain_blacklist_check = 'checked';
	}
	// get the `hide-on-homepage-checkbox` status
	$captain_hide_on_homepage = get_option('captain-hide-on-homepage-checkbox');

	// Get the enabled and disabled paths and convert them to a stringified
	// JSON array, then post them under the `captain` JS namespace.
	$captain_disabled_paths = get_option('captain-disabled-paths');
	$captain_disabled_paths = explode(',', $captain_disabled_paths);
	$captain_disabled_paths = json_encode($captain_disabled_paths);
	$captain_enabled_paths = get_option('captain-enabled-paths');
	$captain_enabled_paths = explode(',', $captain_enabled_paths);
	$captain_enabled_paths = json_encode($captain_enabled_paths);

	?>

	<script>
	window.captain = {};
	window.captain.disabled_paths = <?php echo($captain_disabled_paths); ?>;
	window.captain.enabled_paths = <?php echo($captain_enabled_paths); ?>;
	</script>

	<?php

	// Get the Captain Up Locale
	$captain_locale = get_option('captain-locale');

	// Get the `first-install` status
	$captain_first_install = get_option('captain-first-install');

	// Add a message to the page, indicating that the form has been submitted
	// successfully, either (1) For enabling Captain Up (2) Disabling Captain Up
	// or (3) For changing the settings.
	if (isset($_GET['submitted'])) {
		if ($captain_first_install === true && $captain_api_key != "") {
			echo "<div id='update' class='updated'><p>Rock on! Captain Up is now available on your site, <a target='_blank' href='".get_home_url()."'>go check it out ⇒</a></p></div>\n";
		} else if ($captain_api_key == "") {
			echo "<div id='update' class='updated'>".
				"<p>Captain Up has been <em>disabled</em>. If any problem ".
				"occurred or you have any questions, ".
				"<a href='https://captainup.com/help/contact-us'>".
				"contact our support team</a></p></div>\n";
		} else {
			echo "<div id='update' class='updated'><p>Your settings have been updated, <a target='_blank' href='".get_home_url()."'>see how everything looks ⇒</a></p></div>\n";
		}
	}

	?>

	<div class="wrap" id="cpt-wrap">
		<div class="cpt-stripe cpt-colors captain-admin-header">
			<img id="cpt-logo" src="<?php echo plugins_url('img/cptup_logo.png', __FILE__); ?>" />
			<h1>Captain Up - Game Mechanics</h1>
			<p class="captain-description">
				Engage your website and blog visitors with Captain Up - an engagement and gamification platform. Quickly add badges, rewards and leaderboards. Fully customizable and easy to manage.
			</p>
		</div>

		<div class="postbox-container ">
			<div class="metabox-holder ">
				<div class="meta-box-sortables">
					<form id="captainup-settings-form" action="" method="post">
						<div class="postbox cpt-colors">
							<div class="inside">
								<h2>Configure Captain Up</h2>

								<p>Copy your API key from the <a href='https://captainup.com/manage/settings' target='_blank'>Settings tab</a> in your Captain Up admin panel and paste it here. You need to <a href='mailto:team@captainup.com' target='_blank'>contact us</a> if you don't have a Captain Up account.</p>

								<div id='cpt-api'>
									<label for='captain-api-key'>Your API Key:</label>
									<input id='captain-api-key' name='captain-api-key' type='text' size='50' value='<?php echo $captain_api_key; ?>' />
								</div>

								<script src='https://captainup.com/assets/web-available-languages.js'></script>

								<script>
									// Add all the language options, the web-available-languages
									// script loaded them into __cpt_available_languages.
									(function($) {
										// grab the selected language, default to english
										var selected_language = "<?php echo get_option('captain-locale', 'en'); ?>";
										$(function() {
											// Break execution if `web-available-languages` failed to
											// load on the page. In that odd case, only English will
											// be available for selection.
											if (! window.__cpt_available_languages) return;

											// Grab the language <select> options and empty it
											var $select = $('select#captain-locale').empty();
											// Run on all the languages
											for (code in __cpt_available_languages) {
												var lang = __cpt_available_languages[code];
												// Add an option for each language, its value
												// is the key of __cpt_available languages and
												// its text is the value of it.
												var $option = "<option value='" + code + "'";
												// Add a 'selected' attribute if needed
												if (selected_language === code) {
													$option += " selected";
												}
												$option += ">" + lang + "</option>";
												// Append it to the select box
												$select.append($option);
											}
										});
									})(jQuery);
								</script>

								<div id="cpt-language">
									<label for="captain-language">Language:</label>
									<select id='captain-locale' name='captain-locale'>
										<option value='en' selected>English</option>
									</select>
								</div>

								<div id='cpt-submit'>
									<input type="submit" class="cpt-button padded" name="submit" value="Save" />
								</div>

								<hr />
								<div class="captain-admin-action-buttons">
									<a href="https://captainup.com/manage/settings" target="_blank">
										<div class="cpt-button padded">Customize settings</div>
									</a>
									<a href="https://captainup.com/manage/badges" target="_blank">
										<div class="cpt-button padded">Edit badges and levels</div>
									</a>
								</div>

								<hr />

								<h2>Choose on which pages you want Captain Up to appear</h2>

								<p class="captain-hide-on-homepage">
									<label>
										<input type="checkbox" name="captain-hide-on-homepage-checkbox" class="captain-hide-on-homepage-checkbox" value='Yes' <?php echo($captain_hide_on_homepage); ?>>
										Hide Captain Up on your Homepage
									</label>
								</p>

								<div class="captain-show-paths-box">
									<p>
										<label>
											<input type="radio" name="captain-show-paths-type" class="captain-show-paths-type" value='blacklist' <?php echo($captain_blacklist_check); ?>>
											Hide Captain Up on these URLs:
										</label>
										<input type='text' name='captain-disabled-paths' id='captain-disabled-paths' class='captain-disabled-paths'>
										<div class='cpt-help'>
										Add full links to pages you don't want captain up to show on, e.g. http://mysite.com/some-page <br />
										Use an asterisk to include subpages, e.g. http://mysite.com/guest-posts/*
										</div>
									</p>

									<span class="captain-or-rule">or</span>

									<p>
										<label>
											<input type="radio" name="captain-show-paths-type" class="captain-show-paths-type" value='whitelist' <?php echo($captain_whitelist_check); ?>>
											Show Captain Up only on these URLs:
										</label><br>
										<input type='text' name='captain-enabled-paths' id='captain-enabled-paths' class='captain-enabled-paths'>
										<div class='cpt-help'>
										Add full links to pages you want captain up to show on, e.g. http://mysite.com/some-page) <br />
										Use an asterisk to include subpages, e.g. http://mysite.com/guest-posts/*
										</div>
									</p>
								</div>


								<div id='cpt-submit'>
									<input type="submit" class="cpt-button padded" name="submit" value="Save" />
								</div>

								<hr />

								<h2>Automatic user integration</h2>
								<p class="cpt-help" style="padding-top: 15px;">
										Captain Up allows you to seamlessly integrate your WordPress user accounts with Captain Up user accounts.
										This means users who are logged in to your WordPress site will be automatically signed in to Captain Up using their WordPress data,
										including their name and profile image. <a target='_blank' href="https://captainup.com/help/javascript/user-integration">Learn more</a>.
								</p>
								<?php
									if($is_free_plan) {
								?>
									<div class="premium-feature-notice">
										<h3>Automatic user integration is only available to users on our paid plans</h3>
										<a href="https://captainup.com/billing" target="_blank">
											<div class="cpt-button padded">Upgrade your account</div>
										</a>
									</div>
									<div class="premium-feature">
								<?php
									} else {
								?>
								<div>
								<?php
									}
								?>
									<p>
										Copy the API Secret and Client Token from the <a href='https://captainup.com/manage/settings' target='_blank'>Settings tab</a> in your Captain Up admin panel and paste it here.
									</p>
									<div id='cpt-secret'>
										<label for='captain-api-secret'>API Secret:</label>
										<input id='captain-api-secret' name='captain-api-secret' type='text' size='50' value='<?php echo $captain_api_secret; ?>' />
									</div>
									<div id='cpt-token'>
										<label for='captain-client-token'>API Client Token:</label>
										<input id='captain-client-token' name='captain-client-token' type='text' size='50' value='<?php echo $captain_client_token; ?>' />
									</div>

									<p>
										<label>
											<input type="checkbox" name="captain-user-integration-checkbox" class="captain-user-integration-checkbox" value='Yes' <?php echo($captain_user_integration); ?>>
											Enable automatic user integration
										</label>
									</p>

									<div id='cpt-submit'>
									<?php
										if($is_free_plan) {
									?>
										<div class="cpt-button padded">Save</div>
									<?php
										} else {
									?>
										<input type="submit" class="cpt-button padded" name="submit" value="Save" />
									<?php
										}
									?>
									</div>
								</div>

								<hr />

								<div id='cpt-quick-links'>
									<h2>Quick Links and Support</h2>
									<div id='cpt-footer'>
										<a href='https://captainup.com/manage' target='_blank'>Dashboard</a>
										<span class='cpt-sep'>|</span>

										<a href='https://captainup.com/help' target='_blank'>Help & Support</a>
										<span class='cpt-sep'>|</span>

										<a href='https://captainup.com/manage/badges' target='_blank'>Edit Badges</a>
										<span class='cpt-sep'>|</span>

										<a href='https://captainup.com/manage/levels' target='_blank'>Edit Levels</a>
										<span class='cpt-sep'>|</span>

										<a href='https://captainup.com/manage/users' target='_blank'>View Users</a>
										<span class='cpt-sep'>|</span>

										<a href='https://blog.captainup.com' target='_blank'>Blog</a>
										<span class='cpt-sep'>|</span>

										<a href='http://twitter.com/cptup' target='_blank'>Twitter</a>
										<span class='cpt-sep'>|</span>

										<a href='https://captainup.com/help/contact-us' target='_blank'>Contact Us</a>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php
}

