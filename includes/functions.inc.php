<?php
/* DIVERSE */
if(!function_exists("ikon")) {
	function ikon($class, $extraClass = "") {
		return "<i class=\"{$class} {$extraClass}\"></i>&nbsp;";
	}
}
if (!function_exists("asyncPhpCommand")) {
    function asyncPhpCommand($filepath, $args = null, $logfile = null) {
        if($args) {
            $cmd = "/usr/bin/nohup php {$filepath} ".escapeshellarg($args)." > ".($logfile ? $logfile : "/dev/null")." 2>&1 &";
        } else {
            $cmd = "/usr/bin/nohup php {$filepath} >  ".($logfile ? $logfile : "/dev/null")." 2>&1 &";
        }
        shell_exec($cmd);
        return $cmd;
    }
}
if (!function_exists("getShellArgs")) {
    function getShellArgs($args) {
        // I formatet script.php a=2 b=5 c=6
        $antalArgs = count($args);
        if(!is_array($args) || $antalArgs < 3) {
            // Der skal være mindst tre args: scriptnavnet + et variabel-sæt
            return [];
        }
        $retur = [];
        for($i = 1; $i < $antalArgs; $i++) {
            $split = explode("=", $args[$i]);
            if(count($split == 2)) {
                $retur[$split[0]] = $split[1];
            }
        }
        return $retur;
    }
}    
/* DIVERSE SLUT */


/* DATABASE */
if (!function_exists("createSlug")) {
    function createSlug($text) {
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
if(!function_exists("saveSlug")) {
    function saveSlug($string, $table, $id, &$db, $showDebug = false) {
        $devMsgs = [];
        if(!$id || !$table) {
            return "-- FEJL I SAVESLUG --";
        }
        $createdSlug = createSlug($string);
        if(strlen($createdSlug) > 145) {
            $createdSlug = substr($createdSlug, 0, 145);
        }
        $i = 1;
        while($db->get_row_count("slugs", "slug LIKE '{$createdSlug}' AND tabel LIKE '{$table}' AND id <> {$id}")) {
            if(!$db->get_row_count("slugs", "slug LIKE '".$createdSlug."-".(string) $i."'")) {
                $createdSlug = $createdSlug."-".(string) $i;
                break;
            }
            $devMsgs[] = $db->sql;
            $i++;
        }
        if($table && $id) {
            $db->update($table, $id, ["slug"=>$createdSlug]);
            $devMsgs[] = $db->sql;
        }
        $eksisterendeSlug = $db->get_row("slugs", 0, "tabel LIKE '{$table}' AND objekt_id = {$id}");
        $devMsgs[] = $db->sql;
        if(!empty($eksisterendeSlug)) {
            $db->update("slugs", $eksisterendeSlug["id"], [
                "slug"		=> $createdSlug,
                "objekt_id"	=> $id,
                "tabel"		=> $table
            ]);
        } else {
            $db->insert("slugs", [
                "slug"		=> $createdSlug,
                "objekt_id"	=> $id,
                "tabel"		=> $table
            ]);
        }
        $devMsgs[] = $db->sql;
        if($showDebug && !empty($devMsgs)) {
            echo implode("<br />", $devMsgs);
        }
        return $createdSlug;
    }
}
/* DATABASE SLUT */

/* FORM FIELDS */
if(!function_exists("textarea")) {
    function textarea($parameters) {
        $defaults = [
            "name"          => "",
            "noLabel"       => false,
            "noPlaceholder" => false,
            "required"      => false,
            "label"         => "",
            "style"			=> "",
            "formControl"   => true,
            "rows"          => "5",
            "disabled"      => false,
        ];
        $data = assign_defaults($parameters, $defaults);
        $id = isset($data["id"]) ? $data["id"] : $data["name"];
        $data = assign_defaults($parameters, $defaults);
        $id = isset($data["id"]) ? $data["id"] : $data["name"];
        $class = isset($data["class"]) ? classString($data["class"]) : "";
        if($data["formControl"]) {
            $class .= " form-control";
        }
        $retur = "<textarea id=\"{$id}\" name=\"{$data["name"]}\" class=\"{$class}\" ";
        $retur .= $data["required"] ? "required " : "";
        $retur .= $data["style"] ? "style=\"{$data["style"]}\" " : "";
		if(!$data["noPlaceholder"] && ((isset($data["placeholder"]) && $data["placeholder"]) || (isset($data["name"]) && $data["name"]))) {
			$retur .= ' placeholder="'.ucfirst(isset($data["placeholder"]) && $data["placeholder"] ? $data["placeholder"] : $data["name"]).'"';
		}
        $retur .= "rows=\"{$data["rows"]}\" ";
		$retur .= $data["disabled"] ? " disabled" : "";
        $retur .= ">";
        $retur .= isset($data["value"]) ? htmlspecialchars($data["value"]) : "";
        $retur .= "</textarea>";
		if(!$data["noLabel"] && ($data["label"] || $data["name"])) {
			$retur .= "<label for=\"{$data["name"]}\">".ucfirst($data["label"] ? $data["label"] : $data["name"])."</label>";
		}
        return $retur;
    }
}
if(!function_exists("select")) {
	function select($parameters) {
        $showDebug = false;
        $devMsgs = [];
		$defaults = [
			"name"			=> "",
			"label"			=> null,
			"selected"		=> null,
			"style"			=> "",
			"required"		=> false,
			"options"		=> [],
			"selected"		=> null,
			"firstValue"	=> 0,
			"firstText"		=> "",
            "showFirst"		=> true,
            "fullOptions"   => false,
            "valueFields"   => ["navn"]
		];
		if(empty($parameters["options"])) {
			return null;
		}
		$data = assign_defaults($parameters, $defaults);
		$id = isset($data["id"]) ? $data["id"] : $data["name"];
		// select åbning
		$retur = "<select class=\"form-control"
			.(isset($data["class"]) ? " {$data["class"]}" : "")
			."\""
			.($data["required"] ? " required" : "")
			." name=\"{$data["name"]}\" id=\"{$id}\"";
		if($data["style"]) {
			$retur .= " style=\"{$data["style"]}\"";
		}
		if(isset($data["extras"]) && !empty($data["extras"]) && is_array($data["extras"])) {
			foreach($data["extras"] as $key=> $val) {
				$retur .= " {$key}=\"{$val}\"";
			}
		}
		$retur .= ">\n";
        // Options
        if($data["showFirst"]) {
            $retur .= "<option value=\"{$data["firstValue"]}\">";
            $retur .=  "-- {$data["firstText"]} --";
            $retur .= "</option>\n";
        }
        if($data["fullOptions"]) {
            foreach($data["options"] as $row) {
                $key = $row["id"];
                $retur .= "<option value=\"{$key}\"";
                $retur .= $data["selected"] != null && $data["selected"] == $key ? ' selected="selected"' : '';
                $retur .= ">";
                $values = [];
                foreach($data["valueFields"] as $fieldname) {
                    $values[] = $row[$fieldname];
                }
                $retur .= implode(", ", $values);
                $retur .= "</option>\n";
            }
        } else {
            foreach($data["options"] as $key=>$val) {
                $retur .= "<option value=\"{$key}\"";
                $retur .= $data["selected"] != null && $data["selected"] == $key ? ' selected="selected"' : '';
                $retur .= ">";
                $retur .= $val;
                $retur .= "</option>\n";
            }
        }
		// select lukning
		$retur .="</select>";
		if($data["label"]) {
			$retur .= "\n<label for=\"{$id}\">{$data["label"]}</label>";
        }
        if($showDebug) {
            $retur .= '<div class="alert alert-warning">';
            $retur .= implode("<br />", $devMsgs);
            $retur .= '</div>';
        }
		return $retur;
	}
}
if(!function_exists("input_field")) {
	function input_field($parameters) {
        $defaults = [
            "name"          => "",
            "type"          => "text",
            "noLabel"       => false,
            "noPlaceholder" => false,
            "required"      => false,
            "label"         => "",
            "style"			=> "",
            "formControl"   => true,
            "disabled"      => false,
        ];
        $data = assign_defaults($parameters, $defaults);
        $id = isset($data["id"]) ? $data["id"] : $data["name"];
        $classes = [];
        if($data["formControl"]) {
            $classes[] = "form-control";
        }
        if(isset($data["class"]) && $data["class"]) {
            if(is_array($data["class"])) {
                $classes = array_merge($classes, $data["class"]);
            } else {
                $classes = array_merge($classes, explode(" ", $data["class"]));
            }
        }
        $class = !empty($classes) ? implode(" ", $classes) : "";
		$retur = "<input class=\"{$class}\" type=\"{$data["type"]}\"".($data["required"] ? " required" : "")." name=\"{$data["name"]}\" id=\"{$id}\" value=\"".(isset($data["value"]) ? htmlspecialchars($data["value"]) : "")."\"";
		if(!$data["noPlaceholder"] && ((isset($data["placeholder"]) && $data["placeholder"]) || (isset($data["name"]) && $data["name"]))) {
			$retur .= ' placeholder="'.ucfirst(isset($data["placeholder"]) && $data["placeholder"] ? $data["placeholder"] : $data["name"]).'"';
		}
		if($data["style"]) {
			$retur .= " style=\"{$data["style"]}\"";
		}
		$retur .= $data["disabled"] ? " disabled" : "";
        if(isset($data["extras"]) && !empty($data["extras"]) && is_array($data["extras"])) {
            foreach($data["extras"] as $key=> $val) {
                $retur .= " {$key}=\"{$val}\"";
            }
        }
		$retur .= " />";
		if(!$data["noLabel"] && ($data["label"] || $data["name"])) {
			$retur .= "<label for=\"{$data["name"]}\">".ucfirst($data["label"] ? $data["label"] : $data["name"])."</label>";
		}
		return($retur);
	}
}
if(!function_exists("editable_container")) {
	function editable_container($tableName, $fieldName, $id, $value = "---", $strong = false) {
		$retur = "<div id=\"editable-container-{$fieldName}-{$tableName}-{$id}\">";
		$retur .= "<div
			class=\"editable\"
			data-row-id=\"{$id}\"
			data-field-name=\"{$fieldName}\"
			data-table-name=\"{$tableName}\"
			data-container=\"editable-container-{$fieldName}-{$tableName}-{$id}\"
			title=\"Klik for at redigere\"
			".($strong ? "data-strong=\"1\"" : "")
		.">";
		$value = strlen(trim($value)) > 0 ? $value : "---";
		$retur .= $strong ? "<strong>{$value}</strong>" : $value;
		$retur .= "</div>
			</div>";
		return $retur;
	}
}
/* FORM FIELDS */


/* UTILITIES */
if(!function_exists("print_recursive")) {
	function print_recursive($data, $indent = 0, $linebreak = "<br />"){
		if(is_array($data)){
			foreach($data as $k => $v) {
				if (is_array($v)){
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
			var_dump($data);
		}
	}
}
if(!function_exists("array_sort_by_column")) {
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = [];
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $arr);
	}
}
if(!function_exists("adressetekst")) {
	function adressetekst($adresse, $separator = ",") {
		if(!$adresse) return("");
		$adressefelter = [];
		if(isset($adresse["adresse1"]) && $adresse["adresse1"]) {
			$adressefelter[] = $adresse["adresse1"];
		}
		if(isset($adresse["adresse2"]) && $adresse["adresse2"]) {
			$adressefelter[] = $adresse["adresse2"];
		}
		if(isset($adresse["postnr"]) && $adresse["postnr"]) {
			$adressefelter[] = $adresse["postnr"]." ".$adresse["bynavn"];
		} elseif(isset($adresse["postnummer"]) && $adresse["postnummer"]) {
			$adressefelter[] = $adresse["postnummer"]." ".$adresse["bynavn"];
		}
		return(implode("{$separator} ", $adressefelter));
	}
}
if(!function_exists("swap_dato")) {
	function swap_dato($dato, $retning = "dkus") {
		// Vender dansk slash-datoformat til amerikansk dash-datoformat eller omvendt
		// $retning: dkus eller usdk
		if($retning == "dkus") {
			$dato = str_replace("-", "/", $dato);
			$dato_expl = explode("/", $dato);
			if(strlen($dato_expl[2]) == 2) $dato_expl[2] = "20".(string) $dato_expl[2];
			if(empty($dato_expl[2])) $dato_expl[2] = date("Y");
			return($dato_expl[2]."-".$dato_expl[1]."-".$dato_expl[0]);
		} else {
			$dato_expl = explode("-",$dato);
			if(strlen($dato_expl[0]) == 2) $dato_expl[0] = "20".(string) $dato_expl[0];
			if(empty($dato_expl[0])) $dato_expl[0] = date("Y");
			return $dato_expl[2]."/".$dato_expl[1]."/".$dato_expl[0];
		}
	}
}
if (!function_exists("get_extension")) {
	function get_extension($file_name){
		$ext = explode(".", $file_name);
		$ext = array_pop($ext);
		return strtolower($ext);
	}
}
if(!function_exists("rens_filnavn")) {
	function rens_filnavn($string) {
		// Erstatter karakterer der ikke er indeholdt i acceptable-array med underscores
		if(!strlen($string)) {
			return $string;
		}
		$acceptable = [ "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "_", "+",  "."];
		$antal_karakterer = strlen( $string );
		$ny_string = "";
		for( $i = 0; $i < $antal_karakterer; $i++ ) {
			if(in_array( strtolower($string[$i]), $acceptable)) {
				$ny_string .= $string[$i];
			} else {
				$ny_string .= "_";
			}
		}
		return $ny_string;
	}
}
if(!function_exists("rens_tid")) {
	function rens_tid($tid = "") {
		if(!$tid) {
			return date("H:i:s");
		}
		$udskiftes = array(".", ",", "-");
		$retur = $tid;
		foreach($udskiftes as $tegn) {
			$tid = str_replace($tegn, ":", $tid);
		}
		$tid_expl = explode(':',$tid);
		if(!isset($tid_expl[1])) {
			$tid_expl[1] = "00";
		}
		if(!isset($tid_expl[2])) {
			$tid_expl[2] = "00";
		}
		if((int) $tid_expl[0] > 23) {
			$tid_expl[0] = 23;
		} elseif((int) $tid_expl[0] < 0) {
			$tid_expl[0] = 0;
		}
		if((int) $tid_expl[1] > 59) {
			$tid_expl[1] = 59;
		} elseif((int) $tid_expl[1] < 0) {
			$tid_expl[1] = 0;
		}
		if((int) $tid_expl[2] > 59) {
			$tid_expl[2] = 59;
		} elseif((int) $tid_expl[2] < 0) {
			$tid_expl[2] = 0;
		}
		return(implode(":", $tid_expl));
	}
}
if(!function_exists("rens_tal")) {
	function rens_tal($tal) { // Returnerer tal i amerikansk format
		return floatval(str_replace(",", ".", str_replace(".", "", $tal)));
	}
}
if(!function_exists("validateEmail")) {
	function validateEmail($email) {
		if(filter_var($email,FILTER_VALIDATE_EMAIL)) {
			return(true);
		} else {
			return(false);
		}
	}
}
if(!function_exists("randomPassword")) {
	function randomPassword($len = 8){
		/* Programmed by Christian Haensel
		** christian@chftp.com
		** http://www.chftp.com
		**
		** Exclusively published on weberdev.com.
		** If you like my scripts, please let me know or link to me.
		** You may copy, redistribute, change and alter my scripts as
		** long as this information remains intact.
		**
		** Modified by Josh Hartman on 12/30/2010.
		*/
		if(($len % 2) !== 0){ // Length paramenter must be a multiple of 2
			$len = 8;
		}
		$length = $len - 2; // Makes room for the two-digit number on the end
		$conso = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "v", "w", "x", "y", "z");
		$vocal = array("a", "e", "i", "o", "u");
		$password='';
		srand ((double)microtime()*1000000);
		$max = $length/2;
		for($i = 1; $i <= $max; $i++){
			$password .= $conso[rand(0, 19)];
			$password .= $vocal[rand(0, 4)];
		}
		$password .= rand(10,99);
		$newpass = $password;
		return $newpass;
	}
}
if(!function_exists("dnum")) {
	function dnum($tal, $antal_dec = 0, $vis_nul = true) { // returnerer tal i dansk format
		if(!$vis_nul && $tal == 0) return '';
		return number_format($tal, $antal_dec, ',', '.');
	}
}
if(!function_exists("hjemmeside")) {
	function hjemmeside($url) {
		$url = trim($url);
		if(!$url || substr($url, 0, 7) == "http://" || substr($url, 0, 8) == "https://") {
			return $url;
		}
		return "http://{$url}";
	}
}
if(!function_exists("inputEncode")) {
	function inputEncode(&$value) {
		$value = str_replace('"','&quot;',$value);
		$value = str_replace("'",'&#39;',$value);
	}
}
/* UTILITIES  SLUT */


/* NAMED PARAMETERS */
if(!function_exists("assign_defaults")) {
	function assign_defaults($data, $defaults) {
		$newData = $data;
		foreach($defaults as $key=>$val) {
			$newData[$key] = isset($data[$key]) ? $data[$key] : $val;
		}
		return $newData;
	}
}
if(!function_exists("named_parameters_function")) {
    // Generic function using named parameters
    function named_parameters_function($parameters) {
        if(!is_array($parameters) || empty($parameters) && !$parameters) {
            return null;
        }
        $defaults = [];
        $data = assign_defaults($parameters, $defaults);
        // .... $data[keyname]
        // return $result
    }
}
/* NAMED PARAMETERS SLUT */
?>
