<?php
if (!defined("FORM_FUNCTIONS_INCLUDED")) {
    define ("FORM_FUNCTIONS_INCLUDED", true);

    if (!function_exists ("editable_container")) {
        function editable_container(
            $tableName,
            $fieldName,
            $id,
            $value = null,
            $numerisk = false,
            $decimaler = 0,
            $class = ""
        ): string
        {
            $erNumerisk = $numerisk ? "1" : "0";
            // echo "ec_value: {$value}";
            $retur = "<div id=\"editable-container-{$fieldName}-{$tableName}-{$id}\">";
            if (strlen (trim ($value)) == 0) {
                $class .= " text-muted";
            }
            $retur .= "<div
                class=\"editable {$class}\"
                data-row-id=\"{$id}\"
                data-field-name=\"{$fieldName}\"
                data-table-name=\"{$tableName}\"
                data-numerisk=\"{$erNumerisk}\"
                data-decimaler=\"{$decimaler}\"
                data-container=\"editable-container-{$fieldName}-{$tableName}-{$id}\"
                title=\"Klik for at redigere\"
            >";
            $retur .= strlen (trim ($value)) > 0
                ? ($erNumerisk
                    ? dnum($value, $decimaler)
                    : $value )
                : "-- Ikke angivet --";
            $retur .= "</div>
                </div>";
            return $retur;
        }
    }

    if (!function_exists ("assign_defaults")) {
        function assign_defaults($data, $defaults) {
            $newData = $data;
            foreach ($defaults as $key=>$val) {
                $newData[$key] = $data[$key] ?? $val;
            }
            return $newData;
        }
    }
    if (!function_exists ("classString")) {
        function classString($parameters): string
        {
            $classes = [];
            if (is_array ($parameters)) {
                foreach ($parameters as $parameter) {
                    $classes[] = $parameter;
                }
            } else {
                $classes = explode (" ", $parameters);
            }
            return implode (" ", $classes);
        }
    }
    if (!function_exists ("textarea")) {
        function textarea($parameters): string
        {
            $defaults = [
                "name"          => "",
                "noLabel"       => false,
                "noPlaceholder" => false,
                "required"      => false,
                "label"         => "",
                "placeholder"   => "",
                "style"			=> "",
                "formControl"   => true,
                "rows"          => "5",
                "disabled"      => false,
                "classes"       => [],
                "styles"        => null,
                "labelclass"    => "",
                "wrapperClass"  => null,
                "dataTags"      => null,
                "extras"      => null,
            ];
            $data = assign_defaults($parameters, $defaults);
            $id = $data["id"] ?? $data["name"];

            if ($data["classes"] && !is_array ($data["classes"])) {
                $data["classes"] = explode (" ", $data["classes"]);
            }
            if (!in_array ("form-control", $data["classes"])) {
                $data["classes"][] = "form-control";
            }
            $classString = ' class="'.implode (" ", $data["classes"]).'"';
            $styleString = $data["styles"]
                ? ' style="'.implode ("; ", $data["styles"]).';"'
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
            $requiredString = "";
            if (isset ($data["required"]) && $data["required"]) {
                $requiredString = ' required="true"';
            }
            $label = $data["label"]
                ?: ucfirst(str_replace("_", " ", $data["name"]));
            $labelClassString = $data["labelclass"]
                ? " class=\"{$data["labelclass"]}\""
                : "";
            $labelString = !$data["noLabel"]
                ?  PHP_EOL."<label for=\"{$id}\"{$labelClassString}>{$label}</label>"
                : "";
            if (!$data["placeholder"]) $data["placeholder"] = $data["label"];
            $placeholder = $data["placeholder"]
                ?: ucfirst (str_replace ("_", " ", $data["name"]));
            $placeholderString = $data["noPlaceholder"]
                ? ""
                : " placeholder=\"{$placeholder}\"";

            $valueString = isset ($data["value"]) ? htmlspecialchars ($data["value"]) : "";
            $rowsString = " rows=\"{$data["rows"]}\"";

            $content = <<<CONTENT
    <textarea id="{$id}" name="{$data["name"]}"{$requiredString} {$placeholderString}{$classString}{$styleString}{$dataString}{$extrasString}{$rowsString}>{$valueString}</textarea>
    {$labelString}
CONTENT;
            if ($data["wrapperClass"]) {
                return <<<RETURN
            <div class="{$data["wrapperClass"]}">
                $content
            </div>
RETURN;
            }
            return $content;
        }
    } // textarea SLUT

//    if (!function_exists("editable")) {
//        function editable($params): ?string {
//            if (!$params["id"]) return null;
//            $defaults = [
//
//            ];
//        }
//    }


    if (!function_exists ("input_field")) {
        function input_field($params): ?string
        {
            if (!$params["name"]) return null;
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
            $id = $data["id"] ?? $data["name"];

            if ($data["classes"] && !is_array ($data["classes"])) {
                $data["classes"] = explode (" ", $data["classes"]);
            }
            if ($data["styles"] && !is_array ($data["styles"])) {
                $data["styles"] = explode (";", $data["styles"]);
            }
            if (!in_array("form-control", $data["classes"])) {
                $data["classes"][] = "form-control";
            }
            $classString = ' class="'.implode (" ", $data["classes"]).'"';
            $styleString = $data["styles"]
                ? ' style="'.implode ("; ", $data["styles"]).';"'
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
            $requiredString = "";
            if (isset ($data["required"]) && $data["required"]) {
                $requiredString = ' required="true"';
            }
            $label = $data["label"]
                ?: ucfirst(str_replace("_", " ", $data["name"]));
            $labelClassString = $data["labelclass"]
                ? " class=\"{$data["labelclass"]}\""
                : "";
            $labelString = !$data["noLabel"]
                ?  PHP_EOL."<label for=\"{$id}\"{$labelClassString}>{$label}</label>"
                : "";
            if (!$data["placeholder"]) $data["placeholder"] = $data["label"];
            $placeholder = $data["placeholder"]
                ?: ucfirst (str_replace ("_", " ", $data["name"]));
            $placeholderString = $data["noPlaceholder"]
                ? ""
                : " placeholder=\"{$placeholder}\"";
            return <<<INPUT_FIELD
            <input id="{$id}" name="{$data["name"]}"{$requiredString} value="{$data["value"]}" type="{$data["type"]}"{$placeholderString}{$classString}{$styleString}{$dataString}{$extrasString} />
            {$labelString}
INPUT_FIELD;
        }
    } // function input_field SLUT

    if (!function_exists ("checkbox")) {
        function checkbox($params): string
        {
            $defaults = [
                "name"              => null,
                "id"                => null,
                "noLabel"           => false,
                "label"             => null,
                "value"             => "1",
                "checked"           => false,
                "classes"           => [],
                "styles"            => null,
                "dataTags"          => null,
                "extras"            => null,
                "labelclass"        => null,
                "addStandardClass"  => true
            ];
            $data = assign_defaults($params, $defaults);
            $id = $data["id"] ?? $data["name"];
            if (!is_array ($data["classes"])) {
                $data["classes"] = explode (" ", $data["classes"]);
            }
            if ($data["styles"] && !is_array ($data["styles"])) {
                $data["styles"] = explode (";", $data["styles"]);
            }
            if ($data["addStandardClass"]) {
                $data["classes"][] = "form-check-input";
                $divClass = "form-check d-flex align-items-center";
            } else {
                $divClass = "";
            }
            $classString = ' class="'.implode (" ", $data["classes"]).'"';
            $styleString = $data["styles"]
                ? ' style="'.implode ("; ", $data["styles"]).';"'
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
                ?: ucfirst(str_replace("_", " ", $data["name"]));
            $labelClassString = $data["labelclass"]
                ? " class=\"{$data["labelclass"]}\""
                : "";
            $labelString = !$data["noLabel"]
                ?  PHP_EOL."<label for=\"{$id}\"{$labelClassString} class=\"form-check-label\">{$label}</label>"
                : "";
            $checkedString = $data["checked"] ? " checked" : "";
            $idString = $id ? " id=\"{$id}\"" : "";
            $nameString = $data["name"] ? " name=\"{$data["name"]}\"" : "";
            $valueString = $data["value"] ? " value=\"{$data["value"]}\"" : "";
            return <<<CHECKBOX
            <div class="{$divClass}">
                <input type="checkbox"
                    {$idString}{$nameString}{$valueString}{$classString}{$styleString}{$dataString}{$extrasString}{$checkedString}
                >
                {$labelString}
            </div>
CHECKBOX;
        }
    } // function checkbox SLUT

    if (!function_exists ("select")) {
        function select($params): ?string
        {
            if (!$params["options"]) return null;
            if (!$params["name"]) return null;

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

            if (isset ($params["valueFields"]) && $params["valueFields"]) {
                $data["valueField"] = $params["valueFields"][0];
            } else if (isset ($params["valueField"]) && $params["valueField"]) {
                $data["valueFields"] = [ $params["valueField"] ];
            }

            if (empty ($data["classes"]) && isset ($data["class"])) {
                $data["classes"] = explode (" ", $data["class"]);
            }

            if (!is_array ($data["classes"])) {
                $data["classes"] = explode (" ", $data["classes"]);
            }
            if (!in_array ("form-control", $data["classes"])) {
                $data["classes"][] = "form-control";
            }
            if ($data["styles"] && !is_array ($data["styles"])) {
                $data["styles"] = explode (";", $data["styles"]);
            }

            $id = $data["id"] ?? $data["name"];
            $optionsHTML = $data["showFirst"]
                ?   "<option value=\"{$data["firstValue"]}\">-- {$data["firstText"]} --</option>"
                :   "";

            // Sidste del af condition nedenfor checker at der faktisk er tale om "fullOptions"
            if ($data["fullOptions"] && isset ($data["options"][0]["id"])) {
                foreach ($data["options"] as $option) {
                    $selectedText = $data["selected"] != null && $option["id"] == $data["selected"] ? " selected" : "";
                    $classText = isset ($option["class"]) ? " class=\"{$option["class"]}\"" : "";
                    $styleText = isset ($option["style"]) ? " style=\"{$option["style"]}\"" : "";

                    $label = $option[$data["valueFields"][0]];
                    if (count($data["valueFields"]) > 1) {
                        for ($i = 1; $i < count($data["valueFields"]); $i++) {
                            if ($option[$data["valueFields"][$i]]) {
                                $label .= " ({$option[ $data["valueFields"][$i] ]})";
                            }
                        }
                    }

                    $optionsHTML .= <<<OPTION
<option data-full="true" value="{$option["id"]}"${classText}{$styleText}{$selectedText}>{$label}</option>\n
OPTION;
                }
            } else {
                foreach ($data["options"] as $key => $option) {
                    $selectedText = $data["selected"] != null && $key == $data["selected"] ? " selected" : "";
                    $classText = isset ($option["class"]) ? " class=\"{$option["class"]}\"" : "";
                    $styleText = isset ($option["style"]) ? " style=\"{$option["style"]}\"" : "";
                    $optionsHTML .= <<<OPTION
<option data-full="false" value="{$key}"${classText}{$styleText}{$selectedText}>{$option}</option>\n
OPTION;
                }
            }
            $classString = ' class="'.implode (" ", $data["classes"]).'"';
            $styleString = $data["styles"]
                ? ' style="'.implode ("; ", $data["styles"]).';"'
                : "";
            $dataString = "";
            if ($data["dataTags"]) {
                $dataString = " ";
                foreach ($data["dataTags"] as $key => $val) {
                    $dataString .= "data-{$key}=\"".addslashes($val)."\" ";
                }
            }
            $multipleString = isset ($data["multiple"]) && $data["multiple"]
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
    } // function select SLUT

    if (!function_exists ("filterSelect")) {
        function filterSelect($options, $ental, $flertal = null, $firstText = null): string
        {
            if (!$options) {
                return "";
            }
            global $soegevaerdier;
            if (!$flertal) {
                $flertal = $ental;
            }
            $soegenavn = "{$ental}Soegning";
            $select = select([
                "name"          => $soegenavn,
                "options"       => $options,
                "fullOptions"   => false,
                "firstText"     => $firstText ?: "Alle {$flertal}",
                "selected"      => $soegevaerdier[$soegenavn] ?? 0,
                "classes"       => [
                    "dt-trigger",
                    "form-control",
                    "selectpicker"
                ],
                "extras"        => [
                    "data-live-search"  =>count ($options) > 9 ? "true" : "false",
                    "data-style"        => isset ($soegevaerdier[$soegenavn]) && $soegevaerdier[$soegenavn] ? "bg-lightyellow" : "btn-light"
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
