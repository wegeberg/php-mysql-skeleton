<?php
if(!function_exists("sendNoCacheHeaders")) {
	function sendNoCacheHeaders($origin = "*") {
		header("Access-Control-Allow-Origin: {$origin}");
		header("Content-Type: application/json");
		header("Expires: 0");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
}
if(!function_exists("rens_query")) {
	function rens_query(&$query) {
		if(is_array($query)) {
			return;
		}
		$query = preg_replace('/\s+/', ' ', $query);
	}
}
