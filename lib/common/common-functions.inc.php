<?php
include(dirname(__FILE__) . "/common-form-functions.inc.php");
include(dirname(__FILE__) . "/common-curl-functions.inc.php");

if (!function_exists("pretty_dump")) {
	function pretty_dump($arr, $d = 1)
	{
		if ($d == 1) echo "<pre>";    // HTML Only
		if (is_array($arr)) {
			foreach ($arr as $k => $v) {
				for ($i = 0; $i < $d; $i++) {
					echo "\t";
				}
				if (is_array($v)) {
					echo $k . ":" . PHP_EOL;
					pretty_dump($v, $d + 1);
				} else {
					echo $k . "\t" . $v . PHP_EOL;
				}
			}
		}
		if ($d == 1) echo "</pre>";   // HTML Only
	}
}
if (!function_exists("clean_query")) {
	function clean_query(&$query)
	{
		if (is_array($query) || gettype($query) == "object") {
			return;
		}
		$query = preg_replace('/\s+/', ' ', $query);
	}
}
if (!function_exists("assign_defaults")) {
	function assign_defaults($data, $defaults)
	{
		$newData = $data;
		foreach ($defaults as $key => $val) {
			$newData[$key] = isset($data[$key]) ? $data[$key] : $val;
		}
		return $newData;
	}
}
if (!function_exists("sendNoCacheHeaders")) {
	function sendNoCacheHeaders($origin = "*")
	{
		header("Access-Control-Allow-Origin: {$origin}");
		header("Content-Type: application/json");
		header("Expires: 0");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
}
if (!function_exists("sendPostHeaders")) {
	function sendPostHeaders()
	{
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
			exit(0);
		}
		header("Content-Type: application/json");
	}
}
if (!function_exists("returnJson")) {
	function returnJson($params)
	{
		$defaults = [
			"success"	=> false,
			"error"		=> null,
			"result"	=> null,
			"debug"		=> null
		];
		$data = !empty($defaults) ? assign_defaults($params, $defaults) : $params;
		if (!$data["debug"]) {
			unset($data["debug"]);
		} else {
			array_walk($data["debug"], "rens_query");
		}
		echo json_encode($data);
		exit;
	}
}
if (!function_exists('relativeDate')) {
	function relativeDate($date, $divider = ', ')
	{
		if (!$date || $date <= '1970-01-02 00:00:00') :
			return ('');
		endif;
		if (date("Y-m-d", strtotime($date)) == date("Y-m-d")) { // i dag
			$retur = date("k\l. H.i", strtotime($date));
		} elseif (date("Y", strtotime($date)) == date("Y")) { // i år
			$retur = date("j/m, H.i", strtotime($date));
		} else {
			$retur = date("j/m/y, H.i", strtotime($date));
		}
		$retur = str_replace(', ', $divider, $retur);
		return (str_replace('. ', '.&nbsp;', $retur));
	}
}
if (!function_exists("editableDateTimeField")) {
	function editableDateTimeField(
		$table,
		$field,
		$id,
		$value = "",
		$strong = false
	) {
		$formattedDate =  date("d/m/Y H.i", strtotime($value));
		$spanClass = $strong ? "fw-bold" : "";
		return <<<RETUR
		<div id="{$field}_{$id}" class="text-end">
			<span 
				onClick="editDateTimeField('{$field}', '{$id}', '{$table}', '{$strong}');"
				class="{$spanClass} editable"
				style="cursor: pointer;"
			>
				{$formattedDate}
			</span>
		</div>
RETUR;
	}
}

if (!function_exists('zerofill')) {
	function zerofill($mStretch, $iLength = 2)
	{
		$sPrintfString = '%0' . (int)$iLength . 's';
		return sprintf($sPrintfString, $mStretch);
	}
}
if (!function_exists('trim_mb')) {
	function trim_mb($string) {
		return preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $string);
	}
}
if (!function_exists('clean_filename')) {
	function clean_filename($string) {
		// Replaces characters not in acceptable-array with underscores
		if (!strlen($string)) return $string;
		$acceptable = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_', '+', '.');
		$numberOfCharacters = strlen($string);
		$newString = "";
		for ($i = 0; $i < $numberOfCharacters; $i++) {
			if (in_array (strtolower ($string[$i]), $acceptable)) {
				$newString .= $string[$i];
			} else {
				$newString .= "_";
			}
		}
		return $newString;
	}
}
if (!function_exists('dnum')) {
	function dnum($number, $numberOfDecimals = 0, $showZero = true) { 
		// Returns numbers with decimal comma as ,
		if (!$showZero && $number == 0) return '';
		return number_format($number, $numberOfDecimals, ',', '.');
	}
}
if (!function_exists('clean_number')) {
	function clean_number($tal) {
		// Returns number in 'american' format
		return floatval (str_replace (
			",", 
			".", 
			str_replace(".", "", $tal)
		));
	}
}
if (!function_exists("swap_date")) {
	function swap_date($date, $direction = "dkus") {
		// Toggles between dd/mm/yyyy and yyyy-mm-dd date formats
		// $direction: dkus or usdk
		if (!$date) {
			return null;
		}
		if ($direction == 'dkus') {
			$date = str_replace("-", "/", $date);
			$date_expl = explode("/", $date);
			if (strlen($date_expl[2]) == 2) {
				$date_expl[2] = '20' . (string) $date_expl[2];
			}
			if (empty($date_expl[2])) {
				$date_expl[2] = date("Y");
			}
			return ($date_expl[2] . "-" . $date_expl[1] . "-" . $date_expl[0]);
		} else {
			$date_expl = explode("-", $date);
			if (strlen($date_expl[0]) == 2) $date_expl[0] = '20' . (string) $date_expl[0];
			if (empty($date_expl[0])) $date_expl[0] = date("Y");
			return $date_expl[2] . "/" . $date_expl[1] . '/' . $date_expl[0];
		}
	}
}
if (!function_exists('array_sort_by_column')) {
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key => $row) {
			$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $arr);
	}
}
if (!function_exists("print_recursive")) {
	function print_recursive($data, $indent = 0, $linebreak = "<br />")
	{
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				if (is_array($v)) {
					for ($i = 0; $i < $indent * 10; $i++) {
						echo "&nbsp;";
					}
					echo "{$k}: {$linebreak}";
					print_recursive($v, $indent + 1);
				} else {
					for ($i = 0; $i < $indent * 10; $i++) {
						echo "&nbsp;";
					}
					echo "{$k}: $v{$linebreak}";
				}
			}
		} else {
			var_dump($data);
		}
	}
}
if (!function_exists("validateEmail")) {
	function validateEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return (true);
		} else {
			return (false);
		}
	}
}
if (!function_exists("createSlug")) {
	function createSlug($text)
	{
		$text = str_replace("Å", "aa", $text);
		$text = str_replace("Ø", "oe", $text);
		$text = str_replace("å", "aa", $text);
		$text = str_replace("ø", "oe", $text);
		// replace non letter or digits by -
		$text = preg_replace("~[^\pL\d]+~u", "-", $text);
		// transliterate
		$text = iconv("utf-8", "us-ascii//TRANSLIT", $text);
		// remove unwanted characters
		$text = preg_replace("~[^-\w]+~", "", $text);
		// trim
		$text = trim($text, "-");
		// remove duplicate -
		$text = preg_replace("~-+~", "-", $text);
		// lowercase
		$text = strtolower($text);
		if (empty($text)) {
			return "n-a";
		}
		return $text;
	}
}
if (!function_exists("saveSlug")) {
	function saveSlug($string, $table, $id, &$db, $showDebug = false)
	{
		global $devMsgs;
		$devMsgs[] = "SLUGS";
		if (!$id || !$table) {
			return "-- FEJL I SAVESLUG --";
		}
		$createdSlug = createSlug($string);
		if (strlen($createdSlug) > 145) {
			$createdSlug = substr($createdSlug, 0, 145);
		}
		$kombinationFindes = $db->get_row_count(
			"slugs",
			[
				"objekt_id = {$id}",
				"tabel LIKE '{$table}'",
				"slug LIKE '{$createdSlug}'"
			]
		) > 0;
		$devMsgs[] = $db->sql;

		if ($kombinationFindes) {
			$devMsgs[] = "Fandtes i forvejen";
			return;
		}

		$eksisterendeSlugs = $db->get_rows(
			"slugs",
			"id ASC",
			["slug LIKE '{$createdSlug}'"]
		);

		if ($showDebug) {
			$devMsgs[] = "EKS " . $db->sql;
		}

		if (!empty($eksisterendeSlugs)) {
			if ($showDebug) {
				$devMsgs[] = "antalEksisterendeSlugs: " . count($eksisterendeSlugs);
			}
			for ($i = 1; $i < 50; $i++) {
				$eksisterendeSlugs = $db->get_rows(
					"slugs",
					"id ASC",
					["slug LIKE '{$createdSlug}-{$i}'"]
				);
				if ($showDebug) {
					$devMsgs[] = $db->sql;
					$devMsgs[] = "antalEksisterendeSlugs-{$i}: " . count($eksisterendeSlugs);
				}
				if (empty($eksisterendeSlugs)) {
					$createdSlug .= "-{$i}";
					if ($showDebug) {
						$devMsgs[] = "created {$createdSlug}";
					}
					break;
				}
			}
		}
		if ($table && $id) {
			$db->update($table, $id, ["slug" => $createdSlug]);
			if ($showDebug) {
				$devMsgs[] = $db->sql;
			}
		}
		$db->insert("slugs", [
			"slug"		=> $createdSlug,
			"objekt_id"	=> $id,
			"tabel"		=> $table
		]);
		if ($showDebug) {
			$devMsgs[] = $db->sql;
		}
		return $createdSlug;
	}
}
if (!function_exists('http')) {
	function http($link)
	{
		if (!$link) return '';
		if ($link == 'http://' || $link == 'https://') return '';
		if (substr($link, 0, 7) == 'http://') return $link;
		if (substr($link, 0, 8) == 'https://') return $link;
		return 'https://' . $link;
	}
}
if (!function_exists("excel_encode")) {
	function excel_encode(&$value)
	{
		$value = iconv('UTF-8', 'Windows-1252', $value);
	}
}
if (!function_exists("valid_ip")) {
	function valid_ip($ip)
	{
		// return filter_var($ip, FILTER_VALIDATE_IP);
		if (!ip2long($ip)) {
			return false;
		}
		return true;
	}
}
if (!function_exists('create_random_password')) {
	function create_random_password()
	{
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((float) microtime() * 1000000);
		$i = 0;
		$pass = "";
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}
}
