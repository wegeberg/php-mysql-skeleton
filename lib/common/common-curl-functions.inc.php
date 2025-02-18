<?php
if (!defined ("CURL_FUNCTIONS_INCLUDED")) {
    define ("CURL_FUNCTIONS_INCLUDED", true);

    if (!function_exists ("curlPost")) {
        function curlPost($url, $data, $headers = null): array
        {
            // https://blog.cpming.top/p/php-curl-post-multipart
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true, // return the transfer as a string of the return value
                CURLOPT_TIMEOUT => 0,   // The maximum number of seconds to allow cURL functions to execute.
                CURLOPT_POST => true,   // This line must be placed before CURLOPT_POSTFIELDS
                CURLOPT_POSTFIELDS => $data // The full data to post
            ));
            // Set Header
            if ($headers) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            $response = curl_exec($ch);
            $errno = curl_errno($ch);
            $error =  curl_error($ch);
            curl_close($ch);
            if (!$response) {
                return [
                    "data"  => null,
                    "error" => "Der skete en fejl. {$errno} {$error}"
                ];
            }
            return [
                "data"  => $response,
                "error" => null
            ];
        }
    }

    if (!function_exists ("curlGet")) {
        function curlGet($url, $data = null, $headers = null): array
        {
            // https://blog.cpming.top/p/php-curl-post-multipart
            $geturl = $data ? "{$url}?".http_build_query($data) : $url;
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $geturl,
                CURLOPT_RETURNTRANSFER => true, // return the transfer as a string of the return value
                CURLOPT_TIMEOUT => 0,   // The maximum number of seconds to allow cURL functions to execute.
            ));
            // Set Header
            if ($headers) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $info = curl_getinfo($ch);
            $errno = curl_errno($ch);
            $error =  curl_error($ch);
            curl_close($ch);
            if (!$response || $status > 399) {
                $data = json_decode ($response, true);
                return [
                    "data"  	=> null,
                    "info"		=> $info,
                    "status"	=> $status,
                    "error" 	=> $data["message"] ?? "{$errno} {$error}",
                    "geturl"	=> $geturl
                ];
            }
            return [
                "data"  	=> json_decode ($response, true),
                "error" 	=> null,
                "info"		=> $info,
                "status"	=> $status,
                "geturl"	=> $geturl
            ];
        }
    }

}
