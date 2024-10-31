<?php

//error_reporting(E_ALL);

//security check
$thisReferral = null;
if (isSet($_SERVER["HTTP_REFERER"]) && strlen($_SERVER['HTTP_REFERER']) > 5) {
	$thisReferral = $_SERVER["HTTP_REFERER"];
}

$host = null;
if (isSet($_SERVER['HTTP_HOST']) && strlen($_SERVER['HTTP_HOST']) > 4) {
	$host = $_SERVER['HTTP_HOST'];
} elseif (isSet($_SERVER['SERVER_NAME']) && strlen($_SERVER['SERVER_NAME']) > 4) {
	$host = $_SERVER['SERVER_NAME'];
} else {
	// :(
	// There is no way of getting the server host. Could this happen?
}
if ($host != null && $thisReferral != null) {
	// check if the file is being directly called from a browser or from an external domain
	if (strpos($thisReferral, $host) === false) {
		// the file is called from an external server
		echo("");
		exit();
	}
} else {
	// yep this could happen if the user is hiding the referral variable from his browser
	// or if the file is being called from some kind of bot. I choose not to show the box if the user hides the referral and save cpu/bandwodth by not display anithing to bots
	// if you think different, just comment the next 2 lines
	echo("");
	exit();
}


// looking for the wp-config file, hope to find it in the parent folder of wp-content
$scriptUrl = __FILE__; // /blog/whatever/wp-content/plugins/search-engine-query-in-wp/external.php
$wp_root = "/wp-content";
$scriptUrl = trim(substr($scriptUrl, strpos($scriptUrl, $wp_root) + strlen($wp_root), strlen($scriptUrl))); // /plugins/search-engine-query-in-wp/external.php

$inc_path = "";
$dirs = substr_count($scriptUrl, "/");
for ($i = 0; $i < $dirs; $i++) {
	$inc_path .= "../";
}

if (!function_exists("get_option")) {
	require_once($inc_path."wp-config.php");
}

include_once("inc.php");

load_plugin_textdomain('search-engine-query-in-wp');

$widgetOptions = get_option('seq_in_wp');
$widgetOptions = SEQ_in_wp_checkOptions($widgetOptions, true);

$referer = null;

if (isSet($_GET["referer"]) && strlen(trim($_GET["referer"])) > 4) {
	$referer = urldecode($_GET["referer"]);
}

$postId = null;
if (isSet($_GET["postId"]) && strlen(trim($_GET["postId"])) > 0) {
	$postId = $_GET["postId"];
}
if (!is_numeric($postId)) {
	$postId = null;
}

$seqWidget = SEQ_getRelatedPosts($widgetOptions, $postId, $referer);
SEQ_showRelatedPosts($seqWidget, $postId);

?>