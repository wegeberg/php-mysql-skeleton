<?php
if(!function_exists("fjernFraMailingliste")) {
	function fjernFraMailingliste($email, $mailingliste_id, &$db) {
		global $devMsgs;

		$mailmodtager = $db->get_row("mailmodtagere", 0, "email LIKE '{$email}'");
		$devMsgs[] = $db->sql;

		$mailingliste = $db->get_row("mailinglister", $mailingliste_id);
		$devMsgs[] = $db->sql;

		if(!empty($mailmodtager)) {
			$db->do_query("DELETE FROM mailmodtager_mailingliste_rel WHERE mailmodtager_id = {$mailmodtager["id"]} AND mailingliste_id = {$mailingliste_id}");
			$devMsgs[] = $db->sql;
			// Check om sidste abonnement
			$erTilmeldt = $db->get_row_count("mailmodtager_mailingliste_rel", "mailmodtager_id = {$mailmodtager["id"]}");
			$devMsgs[] = $db->sql;
			if(!$erTilmeldt) {
				$db->delete("mailmodtagere", $mailmodtager["id"]);
				$devMsgs[] = $db->sql;
			}
		}

		$mailgunUrl = MAILGUN_ENDPOINT."/v3/lists/{$mailingliste["mailingliste_alias"]}@".MAILGUN_DOMAIN."/members";
		try {
			$session = curl_init($mailgunUrl."/{$email}");
			curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($session, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);
			curl_setopt($session, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
			$devMsgs[] = curl_exec($session);
			return true;
		} catch(PDOException $e) {
			$devMsgs[] = $e->getMessage();
			return false;
		}

		return false;
	}
}

if (!function_exists("tilfoejTilMailingliste")) {
	function tilfoejTilMailingliste($mailmodtager, $mailingliste_id, &$db) {
		$devMsgs = [];
		if(empty($mailmodtager) || !$mailmodtager["email"]) {
			return ["success"=>false, "error"=>"Mailmodtager mangler"];
		}
		if(!$mailingliste_id) {
			return ["success"=>false, "error"=>"mailingliste_id mangler"];
		}
		$mailingliste = $db->get_row("mailinglister", intval($mailingliste_id));
		if(empty($mailingliste)) {
			return ["success"=>false, "error"=>"mailingliste kunne ikke findes ({$mailingliste_id})"];
		}
		// Check om helt ny bruger
		if(!isset($mailmodtager["id"]) || !$mailmodtager["id"]) {
			// Ny mailmodtager
			$eksisterendeMailmodtager = $db->get_row(
				"mailmodtagere",
				0,
				"email LIKE '{$mailmodtager["email"]}'"
			);
			if(empty($eksisterendeMailmodtager)) {
				$db->insert("mailmodtagere", $mailmodtager);
				$mailmodtager["id"] = $db->lastid();
				$devMsgs[] = $db->sql;
			} else {
				// Mailmodtager med den email findes
				$db->update("mailmodtagere", $eksisterendeMailmodtager["id"], $mailmodtager);
				$devMsgs[] = $db->sql;
				$mailmodtager["id"] = $eksisterendeMailmodtager["id"];
			}
		}
		// Slet evt. relationer
		$db->do_query("DELETE FROM mailmodtager_mailingliste_rel WHERE mailingliste_id = {$mailingliste_id} AND mailmodtager_id = {$mailmodtager["id"]}");
		$devMsgs[] = $db->sql;
		
		$db->insert("mailmodtager_mailingliste_rel", [
			"mailingliste_id"	=> $mailingliste_id,
			"mailmodtager_id"	=> $mailmodtager["id"]
		]);
		$devMsgs[] = $db->sql;
		
		// Opret hos Mailgun
		$mailgunUrl = MAILGUN_ENDPOINT."/v3/lists/{$mailingliste["mailingliste_alias"]}@".MAILGUN_DOMAIN."/members";
		$userData = [
			"name"          => $mailmodtager["navn"],
			"address"       => $mailmodtager["email"],
			"Subscribed"    => "yes",
			"vars"          => json_encode($mailmodtager)
	
		];
		try {
			$session = curl_init($mailgunUrl);
			curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($session, CURLOPT_USERPWD, 'api:'.MAILGUN_API_KEY);
			curl_setopt($session, CURLOPT_POSTFIELDS, $userData);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
			$devMsgs[] = curl_exec($session);
			curl_close($session);
		} catch(PDOException $e) {
            return ["success"=>false, "error"=>$e->getMessage(), "debug"=>$devMsgs];
		}
		return ["success"=>true, "debug"=>$devMsgs];
	}
}
