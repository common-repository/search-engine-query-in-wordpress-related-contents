<?php
/*
Plugin Name: Search Engine query in Wordpress
Plugin URI: http://www.francesco-castaldo.com/plugins-and-widgets/search-engine-query-in-wordpress/
Description: If the visitor comes from a known search engine, the widget grabs the used search query and shows internal blog posts that match that query.
Author: Francesco Castaldo
Version: 1.2.5
Author URI: http://www.francesco-castaldo.com/
*/

/*
Copyright 2009  Francesco Castaldo  (email : fcastaldo@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//error_reporting(E_ALL);

global $seqInWpPhpVersion;
$seqInWpPhpVersion = phpversion();
if (strpos($seqInWpPhpVersion, '-') !== false) {
	$seqInWpPhpVersion = substr($seqInWpPhpVersion, 0, strpos(phpversion(), '-'));
}
$seqInWpPhpVersion = floatval($seqInWpPhpVersion);

if ($seqInWpPhpVersion >= 5) {
	// causes error in php 4
	require_once("inc.php");
}

if (function_exists("add_action")) {
	register_activation_hook(basename(__FILE__), "SEQ_in_wp_act");
	if ($seqInWpPhpVersion > 5) {
		add_action("init", "SEQ_in_wp_widget_init");
		add_action("wp_head", "SEQ_in_wp_widget_HeadAction");
	}
}

wp_enqueue_script('jquery');

function SEQ_in_wp_act() {
	global $wp_version, $seqInWpPhpVersion;

	if (version_compare($wp_version, '2.5.0', '<')) {
		$message = '<p>This plugin requires WordPress 2.5 or higher, which you do not have. Please upgrade your wordpress core.<p>';
		if (function_exists('deactivate_plugins')) {
			deactivate_plugins(__FILE__);
			$message .= "<p>The plugin has been disabled.</p>";
		} else {
			$message .= '<p><strong>Please deactivate this plugin Immediately</strong></p>';
		}
		die($message);
	} else {
		// wordpress version is ok, let's check php
		if ($seqInWpPhpVersion < 5) {
			$message = '<p>This plugin requires Php5 or higher, your current php version is '.phpversion().'</p>';
			$message .=  '<p>You can download the <strong>Search Engine Query in Wordpress</strong> 1.1.1 release which has less features but is php4 compatible at this location: <a href="http://wordpress.org/extend/plugins/search-engine-query-in-wordpress-related-contents/download/">http://wordpress.org/extend/plugins/search-engine-query-in-wordpress-related-contents/download/</a></p>';
			deactivate_plugins(__FILE__);
			$message .=  "<p>This version of the plugin has already been disabled.</p>";
			die($message); // better way of doing this?
		}

		// everything's fine
		// include again inc.php because of activation_hook scope
		require_once("inc.php");
		SEQ_in_wp_checkOptions();
	}	
}

/**
 * Widget init function
 */
function SEQ_in_wp_widget_init() {
	if (function_exists("register_widget_control")) {
		register_widget_control('Search Engine query in WP', 'SEQ_in_wp_widget_control', 500, 250);
	}
	if (function_exists("register_sidebar_widget")) {
		register_sidebar_widget('Search Engine query in WP', 'SEQ_in_wp');
	}
	if (function_exists("load_plugin_textdomain")) {
		load_plugin_textdomain('search-engine-query-in-wp');
	}
}

/**
 * Adds the widget stylesheet in the <head> section
 */
function SEQ_in_wp_widget_HeadAction()
{
	global $seqiwp_base_url;
	echo '<link rel="stylesheet" href="'.$seqiwp_base_url.'/seq_in_wp.css" type="text/css" />';
}

/**
 * Widget box
 */
function SEQ_in_wp($args = array()) {

	global $user_level, $seqiwp_base_url, $post;

	extract($args);

	$widgetOptions = get_option('seq_in_wp');
	$widgetOptions = SEQ_in_wp_checkOptions($widgetOptions, true);

	$postId = null;
	if (is_single()) {
		$postId = $post->ID;
	}

	if ($widgetOptions->isUseAjax()) {

		$url = $seqiwp_base_url."/";
?>
<!-- search engine query in wp START -->
<?php echo($before_widget.$before_title.$widgetOptions->getTitle().$after_title); ?>
<div id="relatedPosts" class="relatedArticles"></div>
<div id="relatedPostsLoading" class="relatedArticles"></div>
<script type="text/javascript">
//<![CDATA[
var seqInWpUrl = "<?php echo($url); ?>";
var postId = "<?php echo($postId); ?>";
var loadingText = "<?php echo(str_replace('"', "'", __("Fetching data...", "search-engine-query-in-wp"))); ?>";
//]]>
</script>
<script type="text/javascript" src="<?php echo($url."fetch_data.js"); ?>"></script>
<?php
		echo($after_widget);
?>
<!-- search engine query in wp END -->
<?php
	} else {

		$seqWidget = SEQ_getRelatedPosts($widgetOptions);

		// show xhtml
		if ($seqWidget->getRelatedPosts() != null) {
			echo ("<!-- search engine query in wp START -->");
			echo ($before_widget.$before_title.$widgetOptions->getTitle().$after_title);
			echo ('<div class="relatedArticles" id="relatedPosts">');
			SEQ_showRelatedPosts($seqWidget, $postId);
			echo ('</div>');
			echo ($after_widget);
			echo ("<!-- search engine query in wp END -->");
		}
	}
}

/**
 * Widget control panel
 */
function SEQ_in_wp_widget_control() {
	$widgetOptions = get_option('seq_in_wp');
	$widgetOptions = SEQ_in_wp_checkOptions($widgetOptions, true);

	if (isSet($_POST["seq_in_wp-submit"])) {

		if (isSet($_POST["seq_in_wp-title"])) {
			$widgetOptions->setTitle($_POST["seq_in_wp-title"]);
		}
		if (isSet($_POST["seq_in_wp-number"])) {
			$widgetOptions->setNumberOfPosts($_POST["seq_in_wp-number"]);
		}
		if (isSet($_POST["seq_in_wp-show_category"])) {
			$widgetOptions->setShowCategory($_POST["seq_in_wp-show_category"]);
		} else {
			$widgetOptions->setShowCategory(false);
		}
		if (isSet($_POST["seq_in_wp-track_clicks"])) {
			$widgetOptions->setTrackClicks($_POST["seq_in_wp-track_clicks"]);
		} else {
			$widgetOptions->setTrackClicks(false);
		}
		if (isSet($_POST["seq_in_wp-use_ajax"])) {
			$widgetOptions->setUseAjax($_POST["seq_in_wp-use_ajax"]);
		} else {
			$widgetOptions->setUseAjax(false);
		}
		if (isSet($_POST["seq_in_wp-log_lands"])) {
			$widgetOptions->setlogLands($_POST["seq_in_wp-log_lands"]);
		} else {
			$widgetOptions->setlogLands(false);
			$widgetOptions->setLogLandsEmail(null);
		}
		if ($widgetOptions->isLogLands()) {
			if (isSet($_POST["seq_in_wp-log_lands_email"])) {
				$widgetOptions->setLogLandsEmail($_POST["seq_in_wp-log_lands_email"]);
			} else {
				$widgetOptions->setLogLandsEmail(null);
			}
		} else {
			$widgetOptions->setLogLandsEmail(null);
		}
		update_option('seq_in_wp', $widgetOptions);
	}
?>
	<p><label for="seq_in_wp-title"><?php _e('Widget title:', "search-engine-query-in-wp"); ?> <input style="width: 250px;" id="seq_in_wp-title" name="seq_in_wp-title" type="text" value="<?php echo $widgetOptions->getTitle(); ?>" /></label></p>
	<p><label for="seq_in_wp-number"><?php _e('Number of results (numeric value between 1 and 10):', "search-engine-query-in-wp"); ?> <input style="width: 40px;" id="seq_in_wp-number" name="seq_in_wp-number" type="text" value="<?php echo $widgetOptions->getNumberOfPosts(); ?>" /></label></p>
	<p><label for="seq_in_wp-show_category"><?php _e('Show category posts if no results with "search engine query" are found:', "search-engine-query-in-wp"); ?> <input id="seq_in_wp-show_category" name="seq_in_wp-show_category" type="checkbox" value="true" <?php if ($widgetOptions->isShowCategory()) { echo "checked=\"checked\""; } ?> /></label></p>
	<p><label for="seq_in_wp-track_clicks"><?php _e('Track clicks', "search-engine-query-in-wp"); ?> <input id="seq_in_wp-track_clicks" name="seq_in_wp-track_clicks" type="checkbox" value="true" <?php if ($widgetOptions->isTrackClicks()) { echo "checked=\"checked\""; } ?> /></label></p>
	<p><?php _e('If you choose to track clicks on the widget\'s links, the plugin will add to internal links some special parameters that will allow you to understand how much the box is used and in which manner. In your analytics tool you\'ll find a "Search engine query in WP widget" campain with all the data.', "search-engine-query-in-wp"); ?> <a href="http://www.francesco-castaldo.com/plugins/search-engine-query-in-wordpress-related-content/" target="_blank"><?php _e('More information here', "search-engine-query-in-wp"); ?></a></p>
	<p><label for="seq_in_wp-use_ajax"><?php _e('Use Ajax technology', "search-engine-query-in-wp"); ?> <input id="seq_in_wp-use_ajax" name="seq_in_wp-use_ajax" type="checkbox" value="true" <?php if ($widgetOptions->isUseAjax()) { echo "checked=\"checked\""; } ?> /></label></p>
	<p><?php _e('If you choose to use Ajax technology, related posts will be fetched after the page is completely loaded. This way the page loads faster, but the box won\'t be availabe on devices not supporting advanced javascript. The box won\'t even be visible to search engines, but if you have a strong internal linking structure, this point won\'t be relevant.', "search-engine-query-in-wp"); ?></p>
	<p><label for="seq_in_wp-log_lands"><?php _e('Log search engine landings by email', "search-engine-query-in-wp"); ?> <input id="seq_in_wp-log_lands" name="seq_in_wp-log_lands" type="checkbox" value="true" <?php if ($widgetOptions->islogLands()) { echo "checked=\"checked\""; } ?> /></label></p>
	<p><label for="seq_in_wp-log_lands_email"><?php _e('Email to send "lands" to:', "search-engine-query-in-wp"); ?> <input style="width: 250px;" id="seq_in_wp-log_lands_email" name="seq_in_wp-log_lands_email" type="text" value="<?php echo $widgetOptions->getLogLandsEmail(); ?>" /></label></p>
	<p><?php _e('By turning this option on, the mailbox in the previous field will receive an email each time a user will land on this blog from a search engine result page. It\'s a funny way of being aware the widget works and also of what users search for reaching your blog (but it\'s better if you check your stats for this). This option might slow down your blog, it\'s strongly suggested to disable it. No information about the visitor are sent nor stored. If the email address provided is not valid, it won\'t be saved.', "search-engine-query-in-wp"); ?></p>

	<p style="margin-bottom:10px"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2797439" rel="nofollow" target="_blank" title="<?php _e("If this plugin is worth for you, consider donating a very few bucks for the time I spent developing it ;)", "search-engine-query-in-wp"); ?>"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG_global.gif" alt="<?php _e("If this plugin is worth for you, consider donating a very few bucks for the time I spent developing it ;)", "search-engine-query-in-wp"); ?>" /></a>
	<input type="hidden" id="seq_in_wp-submit" name="seq_in_wp-submit" value="1" />
<?php
}

?>