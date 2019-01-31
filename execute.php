<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$userId = isset($message['from']['id']) ? $message['from']['id'] : "";
$firstname = isset($message['from']['first_name']) ? $message['from']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['from']['username']) ? $message['from']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$text = trim($text);
$text = strtolower($text);

header("Content-Type: application/json");
$response = '';
$group_TestBot = -267586313;
$group_NordEstLegit = -1001187994497;

// Create connection
$conn = new mysqli("db4free.net", "trial4life", "16021993", "tradepkmn");
//$conn = new PDO("mysql:host=db4free.net;dbname=tradepkmn;charset=UTF8", 'trial4life', '16021993');
// Check connection

if ($conn->connect_error) {
	$response = "Connection failed: " . $conn->connect_error;
}

/*
// Create connection
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
$conn = new PDO("mysql:host={'db4free.net:3306/tradepkmn'};dbname={'tradepkmn'};charset=utf8", 'trial4life', '16021993', $options);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}*/


/* TEST GETCHATMEMEBR
$params = [
   'chat_id'  => '@Trial4life',
   'user_id'  => '158754689',
];

public function getChatMember(array $params)
   {
      $response = $this->post('getChatMember', $params);
      return new ChatMember($response->getDecodedBody());
   }

getChatMember($params);

//$output = json_decode(getChatMember('@Trial4life'), TRUE);
//$test = $output['ChatMember']['user']['id'];
*/


elseif($chatId === $group_TestBot or $chatId === $group_NordEstLegit) {

	if(strpos($text, "/cerco") === 0 )
	{
		$arr = explode('/cerco ', $text);
		$pokemon = str_replace('*',' shiny',ucfirst ($arr[1]));

		if (stristr($pokemon, ",")) {
			$pokemon_arr = explode(', ', $pokemon);
			$pokemon_arr_size = sizeof($pokemon_arr);
			$append_resp = array();

			for ($i = 0; $i <= $pokemon_arr_size-1; $i++) {
				// CERCA NEL DATABASE
				$query = "SELECT * FROM `$chatId` WHERE `pokemon` = '$pokemon_arr[$i]'";
				$result = mysqli_query($conn,$query);
				$row = mysqli_fetch_assoc($result);
				$pkmnID = $row['ID'];
				$pokemon = $row['pokemon'];
				$currUsers_C = $row['cerco'];

				// REGISTRA UTENTE NEL DATABASE
				if (!stristr($currUsers_C, $username)) {
					mysqli_query($conn,"UPDATE `$chatId` SET cerco = concat('$currUsers_C', '$userId','@','$firstname','@','$username','|') WHERE ID = $pkmnID");

				if ($pkmnID == "") { $err_resp = TRUE; } else { array_push($append_resp, $pokemon_arr[$i]); }
				}
			}

			// INVIA MESSAGGIO
			if ($err_resp == TRUE) {
				$response = "Uno o più Pokémon non trovati. Riprovare.";
			}
			else {
				$response = "Pokémon aggiunti alla lista di @" . $username . ".";
			}
		}
		else {
			// CERCA NEL DATABASE
			$query = "SELECT * FROM `$chatId` WHERE `pokemon` = '$pokemon'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$pkmnID = $row['ID'];
			$pokemon = $row['pokemon'];
			$currUsers_C = $row['cerco'];
			$currUsers_S = $row['scambio'];
			$currUsers_S_arr = array(); $currUsers_S_arr = explode('|', $currUsers_S);

			// REGISTRA UTENTE NEL DATABASE
			if (!stristr($currUsers_C, $username)) {
				mysqli_query($conn,"UPDATE `$chatId` SET cerco = concat('$currUsers_C', '$userId','@','$firstname','@','$username','|') WHERE ID = $pkmnID");
			}

			// INVIA MESSAGGIO
			if ($pokemon == "") {
				$response = "Digitare il nome di un Pokémon dopo il comando.";
			}
			elseif ($pkmnID == "") {
				$response = "Pokémon *" . $pokemon . "* non trovato.";
			}
			elseif ($currUsers_S_arr[0]=="") { $response = "Al momento nessun allenatore vuole scambiare *" . $pokemon . "*."; }
			else {
				$response1 = "Allenatori che scambiano *" . $pokemon . "*:";
				$response2 = "";
				$usersNum = sizeof($currUsers_S_arr);
				for ($i = 0; $i <= $usersNum-2; $i++) {
					$currentUser = array(); $currentUser = explode('@', $currUsers_S_arr[$i]);
					$response2 = $response2 . "\n− [".$currentUser[1]."](tg://user?id=".$currentUser[0].")";
				}

				$response = $response1 . $response2;
			};
		}
	}

	elseif(strpos($text, "/scambio") === 0 )
	{
		$arr = explode('/scambio ', $text);
		$pokemon = str_replace('*',' shiny',ucfirst ($arr[1]));

		if (stristr($pokemon, ",")) {
			$pokemon_arr = explode(', ', $pokemon);
			$pokemon_arr_size = sizeof($pokemon_arr);
			$append_resp = array();

			for ($i = 0; $i <= $pokemon_arr_size-1; $i++) {
				// CERCA NEL DATABASE
				$query = "SELECT * FROM `$chatId` WHERE `pokemon` = '$pokemon_arr[$i]'";
				$result = mysqli_query($conn,$query);
				$row = mysqli_fetch_assoc($result);
				$pkmnID = $row['ID'];
				$pokemon = $row['pokemon'];
				$currUsers_S = $row['scambio'];

				// REGISTRA UTENTE NEL DATABASE
				if (!stristr($currUsers_S, $username)) {
				mysqli_query($conn,"UPDATE `$chatId` SET scambio = concat('$currUsers_S', '$userId','@','$firstname','@','$username','|') WHERE ID = $pkmnID");

				if ($pkmnID == "") { $err_resp = TRUE; } else { array_push($append_resp, $pokemon_arr[$i]); }
				}
			}

			// INVIA MESSAGGIO
			if ($err_resp == TRUE) {
				$response = "Uno o più Pokémon non trovati. Riprovare.";
			}
			else {
				$response = "Pokémon aggiunti alla lista di @" . $username . ".";
			}
		}
		else {
			// CERCA NEL DATABASE
			$query = "SELECT * FROM `$chatId` WHERE `pokemon` = '$pokemon'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$pkmnID = $row['ID'];
			$pokemon = $row['pokemon'];
			$currUsers_C = $row['cerco'];
			$currUsers_S = $row['scambio'];
			$currUsers_C_arr = array(); $currUsers_C_arr = explode('|', $currUsers_C);

			// REGISTRA UTENTE NEL DATABASE
			if (!stristr($currUsers_S, $username)) {
				mysqli_query($conn,"UPDATE `$chatId` SET scambio = concat('$currUsers_S', '$userId','@','$firstname','@','$username','|') WHERE ID = $pkmnID");
			}

			// INVIA MESSAGGIO
			if ($pokemon == "") {
				$response = "Digitare il nome di un Pokémon dopo il comando.";
			}
			elseif ($pkmnID == "") {
				$response = "Pokémon *" . $pokemon . "* non trovato.";
			}
			elseif ($currUsers_C_arr[0]=="") { $response = "Al momento nessun allenatore sta cercando *" . $pokemon . "*."; }
			else {
				$response1 = "Allenatori che cercano *" . $pokemon . "*:";
				$response2 = "";
				$usersNum = sizeof($currUsers_C_arr);
				for ($i = 0; $i <= $usersNum-2; $i++) {
					$currentUser = array(); $currentUser = explode('@', $currUsers_C_arr[$i]);
					$response2 = $response2 . "\n− [".$currentUser[1]."](tg://user?id=".$currentUser[0].")";
				}
				$response = $response1 . $response2;
			}
		}
	}

	// ELENCO
	elseif(strpos($text, "/elenco ") === 0 )
	{
	$arr = explode('/elenco ', $text);
	$allenatore = $arr[1];
	// $allenatore_name = FUNCTION($allenatore);
	// $allenatore_Id = FUNCTION($allenatore);

	// CERCA NEL DATABASE
	$query = "SELECT * FROM `$chatId`";
	$result = mysqli_query($conn,$query);
	$pokemonFound_C = array();
	$pokemonFound_S = array();
	$counter = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		$curr_PkMn = $row['pokemon'];
		$currUsers_C = $row['cerco'];
		$currUsers_S = $row['scambio'];
		if ($counter >= 1700) {
        break;
    	}
		if (stristr($currUsers_C, $allenatore)) {
			array_push($pokemonFound_C,$curr_PkMn);
		}
		if (stristr($currUsers_S, $allenatore)) {
			array_push($pokemonFound_S,$curr_PkMn);
		}
		$counter++;
	}

	// INVIA MESSAGGIO
	//if ($pkmnID == "") { $response = "Allenatore *" . $pokemon . "* non trovato."; }
	if (!empty($pokemonFound_C) or !empty($pokemonFound_S)) {
		$response2 = "";
		$response4 = "";
		if (!empty($pokemonFound_S)) {
			$response1 = "Pokémon scambiati da ".$allenatore.":";
			$pokemonFound_S_num = sizeof($pokemonFound_S);
			for ($i = 0; $i <= $pokemonFound_S_num-1; $i++) {  // OCCHIO AL -2
				$response2 = $response2 . "\n− *".$pokemonFound_S[$i]."*";
			}
		} else { $response1 = ""; $response2 = ""; }

		if (!empty($pokemonFound_C)) {
			if (!empty($pokemonFound_S)) {
				$response3 = "\n------------------------------\nPokémon cercati da ".$allenatore.":";
			} else { $response3 = "Pokémon cercati da ".$allenatore.":"; }
			$pokemonFound_C_num = sizeof($pokemonFound_C);
			for ($i = 0; $i <= $pokemonFound_C_num-1; $i++) {  // OCCHIO AL -2
				$response4 = $response4 . "\n− *".$pokemonFound_C[$i]."*";
			}
		}
		else { $response3 = ""; $response4 = ""; }

		$response = $response1 . $response2 . $response3 . $response4;
	} else { $response = $allenatore." non sta cercando/scambiando nessun Pokémon al momento."; }
	}

	// RIMOZIONE
	elseif(strpos($text, "/cancella ") === 0 )
	{
	$arr = explode('/cancella ', $text);
	$pokemon = str_replace('*',' shiny',ucfirst ($arr[1]));

	if (stristr($pokemon, ",")) {
		$pokemon_arr = explode(', ', $pokemon);
		$pokemon_arr_size = sizeof($pokemon_arr);
		$append_resp = array();

		for ($i = 0; $i <= $pokemon_arr_size-1; $i++) {
			// CERCA NEL DATABASE
			$query = "SELECT * FROM `$chatId` WHERE `pokemon` = '$pokemon_arr[$i]'";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_assoc($result);
			$pkmnID = $row['ID'];
			$currUsers_C = $row['cerco'];
			$currUsers_S = $row['scambio'];

			// ELIMINA UTENTE CORRISPONDENTE AL POKÈMON DAL DATABASE
			mysqli_query($conn,"UPDATE `$chatId` SET cerco = replace('$currUsers_C',concat('$userId','@','$firstname','@','$username','|'),'') WHERE ID = $pkmnID");
			mysqli_query($conn,"UPDATE `$chatId` SET scambio = replace('$currUsers_S',concat('$userId','@','$firstname','@','$username','|'),'') WHERE ID = $pkmnID");

			if ($pkmnID == "") { $err_resp = TRUE; } else { array_push($append_resp, $pokemon_arr[$i]); }
		}

		// INVIA MESSAGGIO
		if ($err_resp == TRUE) {
			$response = "Uno o più Pokémon non trovati. Riprovare.";
		}
		else {
			$response1 = "";
			for ($i = 0; $i <= sizeof($append_resp)-1; $i++) {
				$response1 = $response1 . ucfirst($append_resp[$i]);
				if ($i < sizeof($append_resp)-1) { $response1 = $response1 . ", "; } else {$response1 = $response1 . " "; }
			}
			$response = $response1 . "rimossi dalla lista di @" . $username . ".";
		}
	}
	else {
		// CERCA NEL DATABASE
		$query = "SELECT * FROM `$chatId` WHERE `pokemon` = '$pokemon'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$pkmnID = $row['ID'];
		$currUsers_C = $row['cerco'];
		$currUsers_S = $row['scambio'];

		// ELIMINA UTENTE CORRISPONDENTE AL POKÈMON DAL DATABASE
		mysqli_query($conn,"UPDATE `$chatId` SET cerco = replace('$currUsers_C',concat('$userId','@','$firstname','@','$username','|'),'') WHERE ID = $pkmnID");
		mysqli_query($conn,"UPDATE `$chatId` SET scambio = replace('$currUsers_S',concat('$userId','@','$firstname','@','$username','|'),'') WHERE ID = $pkmnID");

		// INVIA MESSAGGIO
		if ($pkmnID == "") { $response = "Pokémon *" . $pokemon . "* non trovato."; } else { $response = "*".$pokemon . "* di [" . $firstname . "](tg://user?id=".$userId.") rimosso.";};
	}
	}
	/*
	elseif($text == "/rimuovitutto")
	{

	// ELIMINA UTENTE DAL DATABASE
	$query = "SELECT * FROM `$chatId`";
	$result = mysqli_query($conn,$query);
	$counter = 0;
	while (($row = mysqli_fetch_assoc($result))) {
		if ($counter >= 902) {
        break;
    	}
		$iterID = $row['ID'];
		$currUsers_C = $row['cerco'];
		$currUsers_S = $row['scambio'];
	//	mysqli_query($conn,"UPDATE `$chatId` SET cerco = replace('$currUsers_C',concat('$userId','@','$firstname','@','$username','|'),'') WHERE ID = $iterID");
   // mysqli_query($conn,"UPDATE `$chatId` SET scambio = replace('$currUsers_S',concat('$userId','@','$firstname','@','$username','|'),'') WHERE ID = $iterID");
	   mysqli_query($conn,"UPDATE `$chatId` SET cerco = 'test' WHERE ID = $iterID");
	   mysqli_query($conn,"UPDATE `$chatId` SET scambio = 'test' WHERE ID = $iterID");
		$counter++;
	}

	// INVIA MESSAGGIO
	$response = "Pokémon di [" . $firstname . "](tg://user?id=".$userId.") rimossi.";
	}*/

	elseif(strpos($text, "/reset ") === 0)
	{
	if ($username == "Trial4life") {
		$arr = explode('/reset ', $text);
		$pokemon = str_replace('*',' shiny',ucfirst ($arr[1]));

		// CERCA NEL DATABASE
		$query = "SELECT * FROM `$chatId` WHERE `pokemon` = '$pokemon'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_assoc($result);
		$pkmnID = $row['ID'];

		// RESETTA POKÈMON DEL DATABASE
		mysqli_query($conn,"UPDATE `$chatId` SET cerco = '' WHERE ID = $pkmnID");
		mysqli_query($conn,"UPDATE `$chatId` SET scambio = '' WHERE ID = $pkmnID");

		// INVIA MESSAGGIO
		if ($pkmnID == "") { $response = "Pokémon *" . $pokemon . "* non trovato."; } else { $response = "*".$pokemon . "* resettato.";};
	} else { $response = "Non sei autorizzato a usare questo comando."; }
	}

	elseif($text == "/resetall")
	{
	if ($username == "Trial4life") {
		// RESETTA TUTTI I POKÈMON DEL DATABASE	// ELIMINA UTENTE DAL DATABASE
		mysqli_query($conn,"UPDATE `$chatId` SET cerco = ''");
		mysqli_query($conn,"UPDATE `$chatId` SET scambio = ''");

		// INVIA MESSAGGIO
		$response = "*Database resettato!*";
	} else { $response = "Non sei autorizzato a usare questo comando."; }
	}

}

else {
	$response = "Gruppo non autorizzato. Contattare l'admin.";
};

//close the mySQL connection
$conn->close();

$parameters = array('chat_id' => $chatId, "text" => $response, "parse_mode" => "markdown");
$parameters["method"] = "sendMessage";
echo json_encode($parameters);
