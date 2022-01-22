<?php
if(!defined("FORM_FUNCTIONS_INCLUDED")) {
    define("FORM_FUNCTIONS_INCLUDED", true);
        
    if(!function_exists("assign_defaults")) {
        function assign_defaults($data, $defaults) {
            $newData = $data;
            foreach($defaults as $key=>$val) {
                $newData[$key] = isset($data[$key]) ? $data[$key] : $val;
            }
            return $newData;
        }
    }
    if(!function_exists("classString")) {
        function classString($parameters) {
            $classes = [];
            if(is_array($parameters)) {
                foreach($parameters as $parameter) {
                    $classes[] = $parameter;
                } 
            } else {
                $classes = explode(" ", $parameters);
            }
            return implode(" ", $classes);
        }
    }
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
                "labelclass"    => null,
                "classes"       => [],
                "styles"		=> null,
                "dataTags"      => null,
                "extras"        => null,
                "value"         => ""
            ];
            $data = assign_defaults($parameters, $defaults);
            $id = isset($data["id"]) ? $data["id"] : $data["name"];
            $data = assign_defaults($parameters, $defaults);
            $id = isset($data["id"]) ? $data["id"] : $data["name"];
            if ($data["classes"]) {
                if (!is_array($data["classes"])) {
                $data["classes"] = explode(" ", $data["classes"]);
                }
            } else if (isset ($data["class"])) {
                $data["classes"] = explode(" ", $data["class"]);
            }
            if ($data["formControl"]) {
                $classses[] = "form-control";
            }
            $classString = $data["classes"]
                ?   ' class="'.implode(" ", $data["classes"]).'"'
                :   '';
            $styleString = $data["styles"]
                ? ' style="'.implode("; ", $data["styles"]).';"'
                : "";
            $dataString = "";
            if ($data["dataTags"]) {
                $dataString = " ";
                foreach ($data["dataTags"] as $key => $val) {
                    $dataString .= "data-{$key}=\"".addslashes($val)."\" ";
                }
            }
            $extrasString = "";
            if ($data["extras"]) {
                foreach ($data["extras"] as $key => $val) {
                    $extrasString .= " {$key}=\"{$val}\"";
                }
            }
            $label = $data["label"]
                ? $data["label"]
                : ucfirst(str_replace("_", " ", $data["name"]));
            $labelClassString = $data["labelclass"] 
                ? " class=\"{$data["labelclass"]}\"" 
                : "";
            $labelString = !$data["noLabel"] 
                ?  PHP_EOL."<label for=\"{$id}\"{$labelClassString}>{$label}</label>"
                : "";
            if (!$data["placeholder"]) $data["placeholder"] = $data["label"];
            $placeholder = $data["placeholder"]
                ? $data["placeholder"]
                : ucfirst(str_replace("_", " ", $data["name"]));
            $placeholderString = $data["noPlaceholder"]
                ? ""
                : " placeholder=\"{$placeholder}\"";

            return <<<TEXTAREA
            <textarea id="{$id}" name="{$data["name"]}"{$classString}{$styleString}{$dataString}{$extrasString}{$placeholderString}>{$data["value"]}</textarea>
            {$labelString}
TEXTAREA;
        }
    } // unction textarea END

    if(!function_exists("input_field")) {
        function input_field($params) {
            if(!$params["name"]) return null;
            $defaults = [
                "name"          => null,
                "type"          => "text",
                "noLabel"       => false,
                "noPlaceholder" => false,
                "required"      => false,
                "placeholder"   => null,
                "label"         => null,
                "labelclass"    => null,
                "classes"		=> [],
                "styles"		=> null,
                "disabled"      => false,
                "dataTags"      => null,
                "extras"        => null,
                "value"         => ""
            ];
            $data = assign_defaults($params, $defaults);
            $id = isset($data["id"]) ? $data["id"] : $data["name"];

            if ($data["classes"] && !is_array($data["classes"])) {
                $data["classes"] = explode(" ", $data["classes"]);
            }
            if ($data["styles"] && !is_array($data["styles"])) {
                $data["styles"] = explode(";", $data["styles"]);
            }
            if (!in_array("form-control", $data["classes"])) {
                $data["classes"][] = "form-control";
            }
            $classString = ' class="'.implode(" ", $data["classes"]).'"';
            $styleString = $data["styles"]
                ? ' style="'.implode("; ", $data["styles"]).';"'
                : "";
            $dataString = "";
            if ($data["dataTags"]) {
                $dataString = " ";
                foreach ($data["dataTags"] as $key => $val) {
                    $dataString .= "data-{$key}=\"".addslashes($val)."\" ";
                }
            }
            $extrasString = "";
            if ($data["extras"]) {
                foreach ($data["extras"] as $key => $val) {
                    $extrasString .= " {$key}=\"{$val}\"";
                }
            }
            $label = $data["label"]
                ? $data["label"]
                : ucfirst(str_replace("_", " ", $data["name"]));
            $labelClassString = $data["labelclass"] 
                ? " class=\"{$data["labelclass"]}\"" 
                : "";
            $labelString = !$data["noLabel"] 
                ?  PHP_EOL."<label for=\"{$id}\"{$labelClassString}>{$label}</label>"
                : "";
            if (!$data["placeholder"]) $data["placeholder"] = $data["label"];
            $placeholder = $data["placeholder"]
                ? $data["placeholder"]
                : ucfirst(str_replace("_", " ", $data["name"]));
            $placeholderString = $data["noPlaceholder"]
                ? ""
                : " placeholder=\"{$placeholder}\"";
            return <<<INPUT_FIELD
            <input id="{$id}" name="{$data["name"]}" value="{$data["value"]}" type="{$data["type"]}"{$placeholderString}{$classString}{$styleString}{$dataString}{$extrasString} />
            {$labelString}
INPUT_FIELD;
        }
    } // function input_field END

    if(!function_exists("checkbox")) {
        function checkbox($params) {
            $defaults = [
                "name"          => null,
                "id"            => null,
                "noLabel"       => false,
                "label"         => null,
                "value"         => "1",
                "checked"       => false,
                "classes"       => [],
                "styles"        => null,
                "dataTags"      => null,
                "extras"        => null,
                "labelclass"    => null,
            ];
            $data = assign_defaults($params, $defaults);
            $id = isset($data["id"]) ? $data["id"] : $data["name"];
            if (!is_array($data["classes"])) {
                $data["classes"] = explode(" ", $data["classes"]);
            }
            if ($data["styles"] && !is_array($data["styles"])) {
                $data["styles"] = explode(";", $data["styles"]);
            }
            if (!in_array("form-control", $data["classes"])) {
                $data["classes"][] = "form-check-input";
            }
            $classString = ' class="'.implode(" ", $data["classes"]).'"';
            $styleString = $data["styles"]
                ? ' style="'.implode("; ", $data["styles"]).';"'
                : "";
            $dataString = "";
            if ($data["dataTags"]) {
                $dataString = " ";
                foreach ($data["dataTags"] as $key => $val) {
                    $dataString .= "data-{$key}=\"".addslashes($val)."\" ";
                }
            }
            $extrasString = "";
            if ($data["extras"]) {
                foreach ($data["extras"] as $key => $val) {
                    $extrasString .= " {$key}=\"{$val}\"";
                }
            }
            $label = $data["label"]
                ? $data["label"]
                : ucfirst(str_replace("_", " ", $data["name"]));
            $labelClassString = $data["labelclass"] 
                ? " class=\"{$data["labelclass"]}\"" 
                : "";
            $labelString = !$data["noLabel"] 
                ?  PHP_EOL."<label for=\"{$id}\"{$labelClassString} class=\"form-check-label\">{$label}</label>"
                : "";
            $checkedString = $data["checked"] ? " checked" : "";
            $idString = $data["id"] ? " id=\"{$data["id"]}\"" : "";
            $nameString = $data["name"] ? " name=\"{$data["name"]}\"" : "";
            $valueString = $data["value"] ? " value=\"{$data["value"]}\"" : "";
            return <<<CHECKBOX
            <div class="form-check">
                <input type="checkbox" 
                    {$idString}{$nameString}{$valueString}{$classString}{$styleString}{$dataString}{$extrasString}{$checkedString}
                >
                {$labelString}
            </div>
CHECKBOX;
        }
    } // function checkbox END
    
    if (!function_exists("select")) {
        function select($params) {
            if(!$params["options"]) return null;
            if(!$params["name"]) return null;

            $defaults = [
                "name"			=> "",
                "label"			=> null,
                "labelclass"    => null,
                "selected"		=> null,
                "required"		=> false,
                "options"		=> [],
                "firstValue"	=> 0,
                "firstText"		=> "",
                "showFirst"		=> true,
                "valueFields"   => [ "navn" ],
                "fullOptions"   => true,
                "multiple"      => false,
                "classes"       => [],
                "styles"        => null,
                "dataTags"      => null,
                "extras"        => null,
            ];
            $data = assign_defaults($params, $defaults);

            if (isset($params["valueFields"]) && $params["valueFields"]) {
                $data["valueField"] = $params["valueFields"][0];
            } else if (isset($params["valueField"]) && $params["valueField"]) {
                $data["valueFields"] = [ $params["valueField"] ];
            }

            if (!is_array($data["classes"])) {
                $data["classes"] = explode(" ", $data["classes"]);
            }
            if ($data["styles"] && !is_array($data["styles"])) {
                $data["styles"] = explode(";", $data["styles"]);
            }

            $id = isset($data["id"]) ? $data["id"] : $data["name"];
            if (!in_array("form-control", $data["classes"])) {
                $data["classes"][] = "form-control";
            }
            $optionsHTML = $data["showFirst"]
                ?   "<option value=\"{$data["firstValue"]}\">-- {$data["firstText"]} --</option>"
                :   "";
            ;
            // Sidste del af condition nedenfor checker at der faktisk er tale om "fullOptions"
            if ($data["fullOptions"] && isset($data["options"][0]["id"])) {
                foreach($data["options"] as $option) {
                    $selectedText = $data["selected"] != null && $option["id"] == $data["selected"] ? " selected" : "";
                    $classText = isset($option["class"]) ? " class=\"{$option["class"]}\"" : "";
                    $styleText = isset($option["style"]) ? " style=\"{$option["style"]}\"" : "";

                    $label = $option[$data["valueFields"][ 0 ]];
                    if (count($data["valueFields"]) > 1) {
                        for ($i = 1; $i < count($data["valueFields"]); $i++) {
                            $label .= " ({$option[ $data["valueFields"][$i] ]})";
                        }
                    }

                    $optionsHTML .= <<<OPTION
<option data-full="true" value="{$option["id"]}"${classText}{$styleText}{$selectedText}>{$label}</option>\n
OPTION;
                }
            } else {
                foreach($data["options"] as $key => $option) {
                    $selectedText = $data["selected"] != null && $key == $data["selected"] ? " selected" : "";
                    $classText = isset($option["class"]) ? " class=\"{$option["class"]}\"" : "";
                    $styleText = isset($option["style"]) ? " style=\"{$option["style"]}\"" : "";
                    $optionsHTML .= <<<OPTION
<option data-full="false" value="{$key}"${classText}{$styleText}{$selectedText}>{$option}</option>\n
OPTION;
                }    
            }
            $classString = ' class="'.implode(" ", $data["classes"]).'"';
            $styleString = $data["styles"]
                ? ' style="'.implode("; ", $data["styles"]).';"'
                : "";
            $dataString = "";
            if ($data["dataTags"]) {
                $dataString = " ";
                foreach ($data["dataTags"] as $key => $val) {
                    $dataString .= "data-{$key}=\"".addslashes($val)."\" ";
                }
            }
            $multipleString = isset($data["multiple"]) && $data["multiple"]
                ? ' multiple="multiple"'
                : '';
            $extrasString = "";
            if ($data["extras"]) {
                foreach ($data["extras"] as $key => $val) {
                    $extrasString .= " {$key}=\"{$val}\"";
                }
            }
            $labelClassString = $data["labelclass"] 
                ? " class=\"{$data["labelclass"]}\"" 
                : "";
            $labelString = $data["label"] 
                ?  PHP_EOL."<label for=\"{$id}\"{$labelClassString}>{$data["label"]}</label>"
                : "";
            return <<<SELECT
<select id="{$id}"name="{$data["name"]}"{$classString}{$styleString}{$dataString}{$multipleString}{$extrasString}>
    {$optionsHTML}
</select>
{$labelString}
SELECT;
        }
    } // function select END

    if(!function_exists("filterSelect")) {
        function filterSelect($options, $ental, $flertal = null, $firstText = null) {
            if(!$options) {
                return "";
            }
            global $soegevaerdier;
            if(!$flertal) {
                $flertal = $ental;
            }
            $soegenavn = "{$ental}Soegning";
            $select = select([
                "name"          => $soegenavn,
                "options"       => $options,
                "fullOptions"   => false,
                "firstText"     => $firstText ? $firstText : "Alle {$flertal}",
                "selected"      => isset($soegevaerdier[$soegenavn])
                    ? $soegevaerdier[$soegenavn]
                    : 0,
                "classes"       => [
                    "dt-trigger",
                    "form-control",
                    "selectpicker"
                ],
                "extras"        => [
                    "data-live-search"  => count($options) > 9 ? "true" : "false",
                    "data-style"        => isset($soegevaerdier[$soegenavn]) && $soegevaerdier[$soegenavn] ? "bg-lightyellow" : "btn-light"
                ] // Hvis der er 10 eller flere valgmuligheder: lav søgeboks
            ]);
            return <<<SELECTFILTER
            <div class="col-12 col-md-3 pb-1">
                {$select}
            </div>
            SELECTFILTER;
        }
    }
}