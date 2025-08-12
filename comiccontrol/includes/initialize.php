<?php
// Common subroutines used by backend and main comic engine

// Get the slug from the page's slug array
// 0 Page type
// 1 Navigation
// 2 Action
// 3 Userid
// 4 Validate 
function getSlug($slugnum) {

	global $ccpage;
	return ($ccpage->slugarr[$slugnum] ?? '');
}

// Standardize slugs for case and spaces
function toSlug($input = '') {
	// URL spaces become dashes
	$input = str_replace('%20','-',$input);

	// Remove all non-alpha-numeric characters
	$input = preg_replace('/[^A-Za-z0-9 \-]/','',$input);

	// Remove leading/trailing whitespace
	$input = trim($input);

	// Spaces become dashes for stragglers
	$input = str_replace(' ','-',$input);

	// Convert the mess to lowercase
	return strtolower($input);
}

// Take a URL and convert it to https if available
function setProtocol($url) {
	// Extract //website.com from https://website.com
	$baseurl = substr($url,strpos($url,'/'));

	// Is SSL enabled?
	if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] == 443)) {
		// Return https://website.com
		return "https:" . $baseurl;
	}
	// Return http://website.com
	return "http:" . $baseurl;
}

// With a URL, download the file and return the contents
// as a string
function get_info($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	// Load the URL
	$output = curl_exec($curl);
	curl_close($curl);

	// Return the string
	return $output;
}

// Download a file from a URL
function get_file($url,$fileloc) {

	// Open the file and lock it in case of multi-threading
	$fp = fopen($fileloc, 'w');
	flock($fp, LOCK_EX);

	// Load the data from the URL
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_FILE, $fp);
	curl_exec($curl);
	curl_close($curl);

	// Release the file and close
	flock($fp, LOCK_UN);
	fclose($fp);
}

// Init the language array
$lang = array();

// Include the classes (The actual work)
require_once('classes.php');

// Create objects
$ccsite = new CC_Site();
$ccuser = new CC_User();
date_default_timezone_set($ccsite->timezone);

// Quick access URL string
$siteurl = $ccsite->root;
$ccurl = $ccsite->root.$ccsite->ccroot;

//quick access functions
function getModuleOption($optionname){
	global $ccpage;
	
	return $ccpage->module->options[$optionname];
}

function buildButton($classes,$link,$text){
	echo '<a class="cc-btn f-c ' . $classes . '" href="' . $link . '">' . $text . '</a>';
}

function quickLinks($links){
	echo '<div id="context-links">';
	foreach($links as $link){
		echo '<a href="' . $link['link'] . '">&gt; ' . $link['text'] . '</a>';
	}
	echo '<div style="clear:both;"></div></div>';
}

?>