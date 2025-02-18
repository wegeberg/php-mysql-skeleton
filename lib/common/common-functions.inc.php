<?php
use JetBrains\PhpStorm\NoReturn;

include (dirname (__FILE__)."/common-form-functions.inc.php");
include (dirname (__FILE__)."/common-curl-functions.inc.php");

if (!function_exists (("listemsg"))) {
    function listemsg($msg): void
    {
        echo '<p class="fst-italic text-small">' . $msg . '</p>';
    }
}
if (!function_exists ("fileIcon")) {
    function fileIcon($extension, $weight = "light", $extraClasses = ""): string {
        $icon = match (strtolower ($extension)) {
            "pdf"   => "fa-file-pdf",
            "doc", "docx" => "fa-file-word",
            "xls", "xlsx"  => "fa-file-excel",
            "ppt", "pptx"   => "fa-file-powerpoint",
            "jpg", "gif", "png", "svg"   => "fa-file-image",
            "zip"   => "fa-file-archive",
            default => "fa-file"
        };
        return '<i class="fa-' . $weight . ' ' . $icon . ' ' . $extraClasses . '"></i>';
    }
}
if (!function_exists ("cleanTime")) {
    function cleanTime($tid = ""): string
    {
        if (!$tid) {
            return date ("H:i:s");
        }
        $udskiftes = ['.', ',', '-'];
        foreach ($udskiftes as $tegn) {
            $tid = str_replace ($tegn,':',$tid);
        }
        $tid_expl = explode (':',$tid);
        if (!isset ($tid_expl[1])) {
            $tid_expl[1] = '00';
        }
        if (!isset ($tid_expl[2])) {
            $tid_expl[2] = '00';
        }
        if (intval ($tid_expl[0]) > 23) {
            $tid_expl[0] = 23;
        } elseif (intval ($tid_expl[0]) < 0) {
            $tid_expl[0] = 0;
        }
        if (intval ($tid_expl[1]) > 59) {
            $tid_expl[1] = 59;
        } elseif (intval ($tid_expl[1]) < 0) {
            $tid_expl[1] = 0;
        }
        if (intval ($tid_expl[2]) > 59) {
            $tid_expl[2] = 59;
        } else if (intval ($tid_expl[2]) < 0) {
            $tid_expl[2] = 0;
        }
        return implode (':', $tid_expl);
    }
}
if (!function_exists ("validateDate")) {
    function validateDate($date, $format = 'Y-m-d H:i:s'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
if (!function_exists ("pretty_dump")) {
    function pretty_dump($arr, $indent = 1, $pre = true): void
    {
        $indentChar = $pre ? "\t" : " ";
        if ($pre) echo "<pre>";    // HTML Only
        if (is_array ($arr)) {
            foreach ($arr as $k => $v) {
                for ($i = 0; $i < $indent; $i++) {
                    echo $indentChar;
                }
                if (is_array ($v)){
                    echo $k . $indentChar . ":" . PHP_EOL;
                    pretty_dump($v, $indent + 1);
                } else {
                    echo $k . $indentChar . $v . PHP_EOL;
                }
            }
        }
        if ($pre) echo "</pre>";   // HTML Only
    }
}
if (!function_exists ("daysBetween")) {
    function daysBetween($date1, $date2): int
    {
        $date1_ts = strtotime ($date1);
        $date2_ts = strtotime ($date2);
        $diff = $date2_ts - $date1_ts;
        return round ($diff / 86400);
    }
}
if (!function_exists ('inputEncode')) {
    function inputEncode(&$value): void
    {
        $value = str_replace('"','&quot;',$value);
        $value = str_replace("'",'&#39;',$value);
    }
}
if (!function_exists ("cleanQuery")) {
    function cleanQuery(&$query) {
        if (is_array ($query) || gettype ($query) == "object") {
            return;
        }
        $query = preg_replace ('/\s+/', ' ', $query);
    }
}
if (!function_exists ("assign_defaults")) {
    function assign_defaults($data, $defaults) {
        $newData = $data;
        foreach($defaults as $key=>$val) {
            $newData[$key] = isset($data[$key]) ? $data[$key] : $val;
        }
        return $newData;
    }
}
if (!function_exists ("sendNoCacheHeaders")) {
    function sendNoCacheHeaders($origin = "*"): void
    {
        header ("Access-Control-Allow-Origin: {$origin}");
        header ("Content-Type: application/json");
        header ("Expires: 0");
        header ("Last-Modified: ".gmdate ("D, d M Y H:i:s")." GMT");
        header ("Cache-Control: no-store, no-cache, must-revalidate");
        header ("Cache-Control: post-check=0, pre-check=0", false);
        header ("Pragma: no-cache");
    }
}
if (!function_exists ("sendPostHeaders")) {
    function sendPostHeaders(): void
    {
        // Allow from any origin
        if (isset ($_SERVER['HTTP_ORIGIN'])) {
            header ("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header ('Access-Control-Allow-Credentials: true');
            header ('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header ("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header ("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
        header ("Content-Type: application/json");
    }
}
if (!function_exists ("returnJson")) {
    #[NoReturn] function returnJson($params)
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
            array_walk ($data["debug"], "cleanQuery");
        }
        echo json_encode ($data);
        exit;
    }
}
if (!function_exists("jsonResponse")) {
    #[NoReturn] function jsonResponse($response): void
    {
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }
}if (!function_exists ("jsonSuccess")) {
    #[NoReturn] function jsonSuccess($result = null, $debug = null, $extras = null): void
    {
        $response = [
            "success"	=> true,
            "result"	=> $result,
            "debug"		=> $debug
        ];
        if ($extras) {
            $response = array_merge ($response, $extras);
        }
        returnJson ($response);
    }
}
if (!function_exists ("jsonError")) {
    #[NoReturn] function jsonError($error = null, $debug = null): void
    {
        returnJson([
            "success" => false,
            "error" => $error,
            "debug" => $debug
        ]);
    }
}
if (!function_exists ('relativeDate')) {
    function relativeDate($date,$divider = ', ') :string
    {
        if (!$date || $date <= '1970-01-02 00:00:00'):
            return('');
        endif;
        if (date("Y-m-d",strtotime ($date)) == date ("Y-m-d")) { // i dag
            $retur = date ("k\l. H.i",strtotime ($date));
        } elseif (date("Y",strtotime ($date)) == date ("Y")) { // i år
            $retur =date ("j/m, H.i",strtotime ($date));
        } else {
            $retur = date ("j/m/y, H.i",strtotime ($date));
        }
        $retur = str_replace (', ',$divider,$retur);
        return str_replace ('. ','.&nbsp;',$retur);
    }
}
if (!function_exists ("editableDateTimeField")) {
    function editableDateTimeField (
        $table,
        $field,
        $id,
        $value = "",
        $strong = false
    ): string
    {
        $formateretDato = date ("d/m/Y H.i", strtotime ($value));
        $spanClass = $strong ? "fw-bold" : "";
        return <<<RETUR
		<div id="{$field}_{$id}" class="text-end">
			<span
				onClick="editDateTimeField('{$field}', '{$id}', '{$table}', '{$strong}');"
				class="{$spanClass} editable"
				style="cursor: pointer;"
			>
				{$formateretDato}
			</span>
		</div>
RETUR;
    }
}

if (!function_exists ('zerofill')) {
    function zerofill($mStretch,$iLength = 2): string
    {
        $sPrintfString = '%0' . (int)$iLength . 's';
        return sprintf($sPrintfString,$mStretch);
    }
}
if (!function_exists ("cleanString")) {
    function cleanString($str): array|string
    {
        return str_replace ('"', "", $str);
    }
}
if (!function_exists ('trim_mb')) {
    function trim_mb($string): array|string|null
    {
        return preg_replace ('/^[\pZ\pC]+|[\pZ\pC]+$/u','',$string);
    }
}
if (!function_exists ('cleanFilename')) {
    function cleanFilename( $string ) {
        // Erstatter karakterer der ikke er indeholdt i acceptable-array med underscores
        if (!strlen ($string)) return $string;

        if (mb_strlen ($string, "UTF-8") > 100) {
            $extension = pathinfo ($string, PATHINFO_EXTENSION);
            $string = substr ($string, 0, 100);
            if ($extension) {
                $string .= ".{$extension}";
            }
        }
        $acceptable = array( 'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','-','_','+', '.' );
        $antal_karakterer = strlen( $string );
        $ny_string = '';
        for ($i = 0; $i < $antal_karakterer; $i++) {
            if ( in_array (strtolower ( $string[ $i ] ), $acceptable)) {
                $ny_string .= $string[ $i ];
            } else {
                $ny_string .= '_';
            }
        }
        return $ny_string;
    }
}
if (!function_exists ('dnum')) {
    function dnum(int|float|null $tal, int $antal_dec = 0, bool $vis_nul = true): string
    { // returnerer tal i dansk format
        if (!$tal) $tal = 0;
        if (!$vis_nul && $tal == 0) return "";
        return number_format (floatval ($tal), $antal_dec, ',', '.');
    }
}if (!function_exists ("swapDate")) {
    function swapDate($dato, $retning = "dkus"): ?string
    {
        // Vender dansk slash-datoformat til amerikansk dash-datoformat eller omvendt
        // $retning: dkus eller usdk
        if (!$dato) {
            return null;
        }
        if ($retning == 'dkus') {
            $dato = str_replace ("-", "/" ,$dato);
            $dato_expl = explode ("/", $dato);
            if (strlen ($dato_expl[2]) == 2) {
                $dato_expl[2] = '20'.(string) $dato_expl[2];
            }
            if (empty($dato_expl[2])) {
                $dato_expl[2] =date ("Y");
            }
            return($dato_expl[2]."-".$dato_expl[1]."-".$dato_expl[0]);
        } else {
            $dato_expl = explode ("-", $dato);
            if (strlen ($dato_expl[0]) == 2 ) $dato_expl[0] = '20'.(string) $dato_expl[0];
            if (empty ($dato_expl[0])) $dato_expl[0] = date ("Y");
            return $dato_expl[2]."/".$dato_expl[1].'/'.$dato_expl[0];
        }
    }
}
if (!function_exists ('array_sort_by_column')) {
    function array_sort_by_column(&$arr, $col, $dir = SORT_ASC): void
    {
        $sort_col = array_map (function ($row) use ($col) {
            return $row[$col];
        }, $arr);
        array_multisort ($sort_col, $dir, $arr);
    }
}
if (!function_exists ("print_recursive")) {
    function print_recursive($data, $indent = 0, $linebreak = "<br />"): void
    {
        if (is_array ($data)){
            foreach($data as $k => $v) {
                if (is_array ($v)){
                    for ($i = 0; $i < $indent * 10; $i++){
                        echo "&nbsp;";
                    }
                    echo "{$k}: {$linebreak}";
                    print_recursive($v, $indent + 1);
                } else {
                    for ($i = 0; $i < $indent * 10; $i++){
                        echo "&nbsp;";
                    }
                    echo "{$k}: $v{$linebreak}";
                }
            }
        } else {
            var_dump ($data);
        }
    }
}
if (!function_exists ("validateEmail")) {
    function validateEmail($email): bool
    {
        return filter_var ($email, FILTER_VALIDATE_EMAIL);
    }
}
if (!function_exists ("createSlug")) {
    function createSlug($text): string
    {
        $text = str_replace ("Å", "aa", $text);
        $text = str_replace ("Ø", "oe", $text);
        $text = str_replace ("å", "aa", $text);
        $text = str_replace ("ø", "oe", $text);
        // replace non letter or digits by -
        $text = preg_replace ("~[^\pL\d]+~u", "-", $text);
        // transliterate
        $text = iconv("utf-8", "us-ascii//TRANSLIT", $text);
        // remove unwanted characters
        $text = preg_replace ("~[^-\w]+~", "", $text);
        // trim
        $text = trim($text, "-");
        // remove duplicate -
        $text = preg_replace ("~-+~", "-", $text);
        // lowercase
        $text = strtolower ($text);
        if (empty ($text)) {
            return "n-a";
        }
        return $text;
    }
}
if (!function_exists ("saveSlug")) {
    function saveSlug($string, $table, $id, &$db, $showDebug = false) {
        global $devMsgs;
        $devMsgs[] = "SLUGS";
        if (!$id || !$table) {
            return "-- FEJL I SAVESLUG --";
        }
        $createdSlug = createSlug($string);
        if (!$createdSlug) {
            die ("UPS - fejl i createdSlug");
        }
        if (strlen ($createdSlug) > 145) {
            $createdSlug = substr ($createdSlug, 0, 145);
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
            $db->update($table, $id, ["slug" => $createdSlug]);
            $devMsgs[] = $db->sql;
            return $createdSlug;
        }

        $eksisterendeSlugs = $db->get_rows(
            "slugs",
            "id ASC",
            [ "slug LIKE '{$createdSlug}'" ]
        );

        if ($showDebug) {
            $devMsgs[] = "EKS ".$db->sql;
        }

        if (!empty ($eksisterendeSlugs))  {
            if ($showDebug) {
                $devMsgs[] = "antalEksisterendeSlugs: ".count ($eksisterendeSlugs);
            }
            for ($i = 1; $i < 50; $i++) {
                $eksisterendeSlugs = $db->get_rows(
                    "slugs",
                    "id ASC",
                    [ "slug LIKE '{$createdSlug}-{$i}'" ]
                );
                if ($showDebug) {
                    $devMsgs[] = $db->sql;
                    $devMsgs[] = "antalEksisterendeSlugs-{$i}: ".count ($eksisterendeSlugs);
                }
                if (empty ($eksisterendeSlugs)) {
                    $createdSlug .= "-{$i}";
                    if ($showDebug) {
                        $devMsgs[] = "created {$createdSlug}";
                    }
                    break;
                }
            }
        }
        $db->update($table, $id, ["slug" => $createdSlug]);
        if ($showDebug) {
            $devMsgs[] = $db->sql;
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
if (!function_exists ("tag")) {
    function tag($tekst, $bgcolor = "transparent"): string
    {
        return <<<RETUR
		<div style="background-color: {$bgcolor}" class="float-left tag">{$tekst}</div>
RETUR;
    }
}
if (!function_exists ('http')) {
    function http($url): string
    {
        $url = trim ($url);
        if (!$url) return "";

        if (stripos ($url, "://") === false && stripos ($url, "mailto:") === false) {
            if (stripos ($url, "@") !== false) {
                $url = "mailto:" . $url;
            } else {
                $url = "https://" . $url;
            }
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return "";
        }
        return $url;
    }
}
if (!function_exists ('cleanDanishNumber')) {
    function cleanDanishNumber($tal): float
    { // Returns number in american (,.) format
        return floatval (str_replace (",", ".", str_replace (".", "", $tal)));
    }
}
if (!function_exists('excelEncode')) {
    function excelEncode(&$value): void
    {
        if (!$value) {
            return;
        }
        $value = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $value);
    }
}
if (!function_exists ("valid_ip")) {
    function valid_ip($ip): bool
    {
        // return filter_var($ip, FILTER_VALIDATE_IP);
        if (!ip2long ($ip)){
            return false;
        }
        return true;
    }
}
if (!function_exists ('createRandomPassword')) {
    function createRandomPassword(): string
    {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand ((double) microtime () * 1000000);
        $i = 0;
        $pass = "" ;
        while ($i <= 7) {
            $num = rand () % 33;
            $tmp = substr ($chars, $num, 1);
            $pass = $pass.$tmp;
            $i++;
        }
        return $pass;
    }
}
