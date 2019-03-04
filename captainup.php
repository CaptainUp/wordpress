<?php
/*
Plugin Name: Captain Up
Plugin URI: https://www.captainup.com
Description: Add Game Mechanics to your site and increase your engagement and retention. 2 minutes install: Simply add your free Captain Up API Key and you are good to go. The plugin also adds widgets you can use to show leaderboards and activities within your site.
Version: 3.0.5
Author: Captain Up Team
License: GPL2
*/

require_once 'utils.php';
require_once 'admin-settings.php';
require_once 'site-embed.php';


// Add Admin Panel CSS and JS Files
// ------------------------------------------------------------------------------
function cptup_settings_files($page) {
	// Return unless we're in the Captain Up admins settings page.
	// I swear to god this is what WordPress Codex suggests to do
	if ($page != "toplevel_page_cptup-config-menu") return;
	// Add the scripts
	wp_enqueue_style('cpt-css');
	wp_enqueue_script('jquery');
	wp_enqueue_script('cpt-js');
}

// Add relevant Captain Up action links to the Captain Up plugin listing in the
// installed plugins page, right next to the 'deactivate' link.
function cptup_add_action_links($links) {
	$captain_up_links = array(
		'<a href="'.admin_url('admin.php?page=cptup-config-menu').'">Settings</a>'
	);
	return array_merge($captain_up_links, $links);
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'cptup_add_action_links');


// Setup Admin Panel Resources
// -------------------------------------------------------------------------------
function cptup_config() {
	// Add Captain Up to the Menu
	add_menu_page(
		'Captain Up Settings - Game Mechanics',
		'Captain Up',
		'manage_options', 'cptup-config-menu', 'cptup_admin_settings'
	);
	// Register additional CSS and JS files
	wp_register_style('cpt-css', plugins_url('css/captainup.css', __FILE__));
	wp_register_script('cpt-js', plugins_url('captainup.js', __FILE__), '', '1.5', true);
	add_action('admin_enqueue_scripts', 'cptup_settings_files');
}

// Add Captain Up to the main menu of the WordPress admin panel
add_action('admin_menu', 'cptup_config');


// Add the Captain Up Script to the Site
// -------------------------------------------------------------------------------

// Initializes the Captain Up script, if the API key was set properly.
function cptup_print() {
	$captain_key = get_option('captain-api-key');
	if ($captain_key != '') {
		add_action('wp_footer', 'captain_start', 1000);
	}
}


// Given the current `$page_path`, `cptup_is_in_path_list` goes over the paths
// in `$path_list` and determines whether that path is listed there.
// @param $page_path - {String} the URL to check
// @param $path_list - {Array} list of URLs to check against
// @return {Boolean} indicating whether the `$page_path` is on the list.
function cptup_is_in_path_list($page_path, $path_list) {

		foreach ($path_list as $path) {
				// handle the case where the URI is with the trailing wildcard '*'
				if (substr($path, -1) === "*") {
					// check if the current path starts with this path.
					// substr the disabled path to check without the asterisk *.
					if (strpos($page_path, substr($path, 0, -1)) === 0) {
							return true;
					}
				} else {
					// if it's not a wildcard, check if the disabled path is strictly
					// equal to the current path.
					if ($page_path === $path) {
							return true;
					}
				}
		}
		// if the current path wasn't found in the `$path_list` array,
		// return false.
		return false;
}


// Determine whether we should display Captain Up on the page or not, using
// either the whitelist or the blacklist (whichever is enabled).
if (get_option('captain-show-paths-type') === 'whitelist') {
	// Get the enabled paths
	$enabled_paths = explode(',', get_option('captain-enabled-paths'));
	// Check if we should display Captain Up on the current page
	$should_display = cptup_is_in_path_list($_SERVER["REQUEST_URI"], $enabled_paths);
} else {
	// Get the disabled paths
	$disabled_paths = explode(',', get_option('captain-disabled-paths'));
	// If the 'hide on homepage' checkbox was checked, add '/' to
	// the `disabled_path` array.
	if (get_option('captain-hide-on-homepage-checkbox') === 'checked') {
			$disabled_paths[] = '/';
	}
	// Check if we should display Captain Up on the current page
	$should_display = !cptup_is_in_path_list($_SERVER["REQUEST_URI"], $disabled_paths);
}

// Add the Captain Up script unless (1) we're inside the admin panel;
// (2) the page is on the blacklist (3) the page is not on the whitelist.
if (!is_admin() && $should_display) {
	add_action('wp_print_scripts', 'cptup_print');
}


// Widgets
// ---------------------------------------------------------------------------

// Enqueue scripts to handle editing the Widgets options in
// the widgets admin panel tab.
function cptup_widgets_edit_script($hook) {
	// Only enqueue the script in the widgets tab
	if('widgets.php' != $hook) return;

	wp_enqueue_script(
		'cptup_widgets_edit',
		plugins_url('/js/cptup_widgets_edit.js', __FILE__),
		array('jquery')
	);
}
add_action('admin_enqueue_scripts', 'cptup_widgets_edit_script');


class Captainup_Widget extends WP_Widget {

	/** constructor */
	function __construct() {
		parent::__construct('cptup_widget', 'Captain Up Widget', array(
			'description' => 'Captain Up Leaderboards and Recent Activity'
		));
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract($args);
		$type = $instance['type'];
		$height = $instance['height'];
		$default_leaderboard = $instance['default_leaderboard'];

		echo $before_widget;
		?>

		<div class='captain-<?php echo $type; ?>-widget' <?php if($type=='leaderboard') echo 'data-cpt-leaderboard='.$default_leaderboard ?> style='width: auto; height: <?php echo $height; ?>px; display: none;'>
		</div>

		<?php
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['type']   = strip_tags($new_instance['type']);
		$instance['css']    = strip_tags($new_instance['css']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['default_leaderboard'] = strip_tags($new_instance['default_leaderboard']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		$type   = esc_attr($instance['type']);
		$css    = esc_attr($instance['css']);
		$height = esc_attr($instance['height']);

		if (!isset($type)) $type = 'leaderboard';
		if (!isset($css)) $css = 'height: 300px; margin-top: 20px;';
		if (!isset($height)) $height = '350';
		if (!isset($default_leaderboard)) $default_leaderboard = 'monthly_ranking';

		?>

		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>">
				<?php _e('Widget type'); ?>
			</label>
			<select id="<?php echo $this->get_field_id('type'); ?>" class="cpt-widget-type-select" name="<?php echo $this->get_field_name('type'); ?>">

				<option <?php if($type == "activity") { echo "selected"; }; ?> value="activity">
					Activity Widget
				</option>
				<option <?php if($type == "leaderboard") { echo "selected"; }; ?> value="leaderboard">
					Leaderboard Widget
				</option>

			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('height'); ?>">
				<?php _e('Height:'); ?>
			</label>
			<input size="4" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />px
		</p>

		<p class='cpt-select-leaderboard-type'>
			<label for="<?php echo $this->get_field_id('default_leaderboard'); ?>">
				<?php _e('Default view:') ?>
			</label>

			<select id="<?php echo $this->get_field_id('default_leaderboard'); ?>" name="<?php echo $this->get_field_name('default_leaderboard'); ?>">

				<option value="all-time-ranking" <?php if($default_leaderboard == 'all-time-ranking') echo 'selected'?>>
					All Time
				</option>

				<option value="monthly-ranking" <?php if($default_leaderboard == 'monthly-ranking') echo 'selected'?>>
					Monthly
				</option>

				<option value="weekly-ranking" <?php if($default_leaderboard == 'weekly-ranking') echo 'selected'?>>
					Weekly
				</option>

				<option value="daily-ranking" <?php if($default_leaderboard == 'daily-ranking') echo 'selected'?>>
					Daily
				</option>

			</select>
		</p>

		<script>
			jQuery(function() {
				// Run over all the Captain Up widgets and call the
				// `toggle_leaderboard_option` on them. The function is
				// defined under `cptup_widgets_edit.js`. We call it here as
				// well as the widget form gets re-rendered every time it is
				// saved.
				jQuery('.cpt-widget-type-select').each(function(index, element) {
					toggle_leaderboard_option(element);
				});
			});
		</script>

		<?php
	}
}

// Initialize the Widget
function cptup_init_widget(){
	return register_widget('CaptainUp_Widget');
}
add_action('widgets_init', 'cptup_init_widget' );


// Shortcodes
// -------------------------------------------------------------------------------

// Leaderboard Widget Shortcode
// [captain-leaderboard width="300" height="400" title="Hello" leaderboard="all-time-ranking"]
// Options:
// - width - CSS attribute. by default 300px
// - height - CSS attribute. by default 500px
// - leaderboard - the default ranking view (all time, monthly, weekly, daily),
//   by default set to the monthly leaderboard.
// - title - the title of the widget, by default 'Leaderboard' in the current
//   locale language.
function cptup_leaderboard_shortcode($atts) {
	extract(shortcode_atts(
		array(
			'width' => '300',
			'height' => '500',
			'title' => false,
			'leaderboard' => 'monthly-ranking'
		), $atts
	));
	return "<div style='margin: 20px auto; width: $width"."px; height: $height"."px;' class='captain-leaderboard-widget' data-cpt-leaderboard='" . str_replace("-", "_", $leaderboard) . "' data-cpt-title='$title'></div>";
}
add_shortcode('captain-leaderboard', 'cptup_leaderboard_shortcode');

// Activity Widget Shortcode
// [captain-activity width="500" height="400" title="Hello"]
// Options:
// - width - CSS attribute. by default 300px
// - height - CSS attribute. by default 500px
// - title - the title of the widget, by default 'Activities' in the current locale
//   language
function cptup_activity_shortcode($atts) {
	extract(shortcode_atts(
		array(
			'width' => '300',
			'height' => '500',
			'title' => false
		), $atts
	));
	return "<div style='margin: 20px auto; width: $width"."px; height: $height"."px;' class='captain-activity-widget' data-cpt-title='$title'></div>";
}
add_shortcode('captain-activity', 'cptup_activity_shortcode');

// Sign Up Link Shortcode
// [captain-sign-up text="Hello"]
//
// Options:
// - text - the text of the link, by default "Sign Up Now"
//
function cptup_sign_up_link_shortcode($atts) {
	extract(shortcode_atts(
		array(
			'text' => 'Sign Up Now',
		), $atts
	));
	return "<a style='cursor: pointer' class='captain-sign-up-link'>$text</a>";
}
add_shortcode('captain-sign-up', 'cptup_sign_up_link_shortcode');


// WordPress Comments Integration
// -----------------------------------------------------------------------------
//
// The flow for detecting a new WordPress comment and sending
// to Captain Up goes like this: First, we add an action hook to
// `comment_post` that notifies us when comments are saved to the
// database. Then, if the comment was approved, we create a cookie
// called `wp_cpt_new_comment`. We do this since after a comment
// is POSTed WordPress redirects back to the post. It's like a bad
// man's flash messaging. We check if this cookie exists on every
// request. If it does, we remove it, hook to the <head> element
// and add a JavaScript variable there called `_cpt_wordpress_events`
// that stores the new comment event. The Captain Up embed code
// does the rest of the work.
//
// NOTE: This process -will- change and will be moved to a server-side
// flow.

// Setup a hook to get a notification after a new comment has been posted.
add_action('comment_post', 'cptup_mark_new_comment', 10, 2);

// `cptup_mark_new_comment` is called from the `comment_post` WordPress
// hook. It receives $comment_id and the $approval status of the comment,
// and stores a cookie telling us in the follow up request (after the
// redirection) that a comment was created.
function cptup_mark_new_comment($comment_id, $approval) {
	// $approval can either be 'spam', 0 for disapproved or 1 for approved.
	// We give points for approved and disapproved (held for moderation)
	// comments but not for spam.
	if ($approval == 1 || $approval == 0) {
		// we need to mark this in a cookie since WordPress has no built-in
		// session or flash support and after a comment is posted WordPress
		// redirects the user.
		setcookie("wp_cpt_new_comment", $comment_id, time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
	}
}

// `cptup_add_new_comment` adds a new JS snippet to the page with
// the `_cpt_wordpress_events` variable. The Captain Up embed picks
// this up later and then syncs the new comment action to our servers.
function cptup_add_new_comment() {
	?>
	<script data-cfasync='false' type='text/javascript'>
		window._cpt_wordpress_events = {
			new_comment: true
		};
	</script>
	<?php
}

// On every request, check if the new comment cookie is set
if (isset($_COOKIE['wp_cpt_new_comment'])) {
	// Clean the Cookie
	setcookie("wp_cpt_new_comment", "", time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
	// hook into the <head> of the page to insert our JS snippet
	// that tells the Captain Up embed a new comment was created.
	add_action('wp_head', 'cptup_add_new_comment');
}

