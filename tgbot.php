<?php 
include "config.php";

$data = file_get_contents('php://input');
$data = json_decode($data, true);
 
include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

#################################

if (isset($data['message']['chat']['id']))
{
	$chat_id = $data['message']['chat']['id'];
}
elseif (isset($data['callback_query']['message']['chat']['id']))
{
	$chat_id = $data['callback_query']['message']['chat']['id'];
}
elseif(isset($data['inline_query']['from']['id']))
{
	$chat_id = $data['inline_query']['from']['id'];
}

// Register new user in DB
if(isset($data['callback_query']['message']['chat']['username']) && $data['callback_query']['message']['chat']['username'] != ''){
	$fname = $data['callback_query']['message']['chat']['first_name'];
	$lname = $data['callback_query']['message']['chat']['last_name'];
	$uname = $data['callback_query']['message']['chat']['username'];
} else{
	$fname = $data['message']['from']['first_name'];
	$lname = $data['message']['from']['last_name'];
	$uname = $data['message']['from']['username'];	
}
$time = time();
if($chat_id != ''){
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	if(mysqli_num_rows($result) == 0){
		$str2ins = "INSERT INTO `users` (`chatid`,`fname`,`lname`,`username`) VALUES ('$chat_id','".addslashes($fname)."','".addslashes($lname)."','$uname')";
		mysqli_query($link, $str2ins);	
		$result = mysqli_query($link, $str2select);
	}
	$row = @mysqli_fetch_object($result);	
}
// Register new user in DB

// LANGUAGE
$str3select = "SELECT `lang` FROM `users` WHERE `chatid`='$chat_id'";
$result3 = mysqli_query($link, $str3select);
$row3 = @mysqli_fetch_object($result3);
if($row3->lang != ''){
	$langcode = $row3->lang;
}else{
	$langcode = 0;	
}
###################
$langcode = langCode($langcode);
###################
require "lang.php";

for ($i = 0; $i < count($text); $i++) {
	
	for ($k = 0; $k < count($text[$i]); $k++) {
		$text[$i][$k] = str_replace("&#13;&#10;", "
", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#9;", "", $text[$i][$k]);
	} // end FOR
	
} // end FOR

// LANGUAGE

checkInlineQuery();

############### START ###############
if( preg_match("/\/start/i", $data['message']['text'] )){

//register subscriber
$newrecord = $chat_id."|".addslashes($data['message']['from']['first_name'])." ".addslashes($data['message']['from']['last_name'])."|".addslashes($data['message']['from']['username']);
if(file_exists('subscribers.php')) include 'subscribers.php';
if(isset($user) && count($user) > 0){
	if(!in_array($newrecord, $user)){
		$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
		
	}
}else{
	$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
} // end IF-ELSE count($user) > 0

if(isset($towrite) && $towrite != ''){
	if($file = fopen("subscribers.php", "a+")){
		fputs($file,$towrite);
		fclose($file);
	} // end frite to file
}
//register subscriber

// record referral
$ref = trim(str_replace("/start", "", $data['message']['text']));
if($ref != ''){
	if($ref != $chat_id){
		$str2select = "SELECT `ref` FROM `users` WHERE `chatid`='$chat_id'";
		$result = mysqli_query($link, $str2select);
		$row = @mysqli_fetch_object($result);
		if($row->ref < 10){
			$str2upd = "UPDATE `users` SET `ref`='$ref' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
		}
	}
}
// record referral

#mainMenu();
chooseLang();

}
elseif( preg_match("/".$text[$langcode][2]."/", $data['message']['text'] )){

	$str12select = "SELECT * FROM `users` WHERE `ref`='$chat_id'";
	$result12 = mysqli_query($link, $str12select);
	$totalReferals = mysqli_num_rows($result12);
	
	$refbalance = ($row->refbalance > 0) ? $row->refbalance : "0.00";
	
	$numOfReferals = floor($refbalance * 10);
	
	//non Active
	$nonActiveReferals = $totalReferals - $numOfReferals;
	
	$tomessage = str_replace("%NFTRefPercent%", "", $text[$langcode][8]);
	$tomessage = str_replace("%numOfReferals%", $numOfReferals, $tomessage);
	$tomessage = str_replace("%nonActiveReferals%", $nonActiveReferals, $tomessage);
	$tomessage = str_replace("%refbalance%", $refbalance, $tomessage);		
	$tomessage = str_replace("%chat_id%", $chat_id, $tomessage);			
	
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $tomessage,
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');	
		
		send2('sendMessage',
			[
				'chat_id' => $chat_id,
				'text' => $text[$langcode][42],
				'reply_markup' =>
				[
					'inline_keyboard' =>
					[
						[
							[
								'text' => $text[$langcode][43],
								'switch_inline_query' => ''
							]
						]
					]
				]
			]);

}
elseif( preg_match("/AirDrop Token/", $data['message']['text'] )){
	
	$str22select = "SELECT * FROM `wallets` WHERE `chatid`='$chat_id'";
	$result22 = mysqli_query($link, $str22select);
	$row22 = @mysqli_fetch_object($result22);
	if(mysqli_num_rows($result22) > 0){
		$walsign = " ✅";
	}else{
		$walsign = " ❌";	
	}

	$btnurl[0] = $twitterLNK;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][26];
	$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($btnurl[0]);
	$btnurl[1] = $TGchannel1;
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][27];
	$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($btnurl[1]);
	$btnurl[2] = $TGchannel2;
	$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][28];
	$arInfo["inline_keyboard"][2][0]["url"] = rawurldecode($btnurl[2]);	
	$btnurl[3] = $VKLNK;
	$arInfo["inline_keyboard"][3][0]["text"] = $text[$langcode][29];
	$arInfo["inline_keyboard"][3][0]["url"] = rawurldecode($btnurl[3]);
	$btnurl[4] = $MetaMaskLNK;
	$arInfo["inline_keyboard"][4][0]["text"] = $text[$langcode][30];
	$arInfo["inline_keyboard"][4][0]["url"] = rawurldecode($btnurl[4]);		
	$arInfo["inline_keyboard"][5][0]["callback_data"] = 5;
	$arInfo["inline_keyboard"][5][0]["text"] = $text[$langcode][31].$walsign;		
	send($chat_id, $text[$langcode][32], $arInfo);	
		
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][33],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');	
		
}
elseif( preg_match("/".$text[$langcode][0]."/", $data['message']['text'] )){

	$arInfo["inline_keyboard"][0][0]["callback_data"] = 1;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][9];
	send($chat_id, $text[$langcode][10], $arInfo); 

}
elseif( preg_match("/".$text[$langcode][1]."/", $data['message']['text'] )){

	$str3select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result3 = mysqli_query($link, $str3select);
	$row3 = @mysqli_fetch_object($result3);
	
	if($row3->verified == 1){
	
		if($row3->wallet != ''){
		
			walletMessage($row3->wallet);
		
		}else{
			
		clean_temp_sess();
		save2temp("action", "wait4wallet");
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][11],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
		}
			
	}else{
		$arInfo["inline_keyboard"][0][0]["callback_data"] = 1;
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][9];
		send($chat_id, $text[$langcode][12], $arInfo); 		
	}

}
else{
	
	if(isset($data['callback_query']['data']) && $data['callback_query']['data'] != ''){

		if( $data['callback_query']['data'] == 1 ){	
		
			$channel_url1 = "https://t.me/".str_replace("@", "", $channel_id1);
			$channel_url2 = "https://t.me/".str_replace("@", "", $channel_id2);
			$channel_url3 = "https://t.me/".str_replace("@", "", $channel_id3);						
		
			$arInfo["inline_keyboard"][0][0]["text"] = "TON NFT Tegro Cat 🐈";
			$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($channel_url2);	
			$arInfo["inline_keyboard"][1][0]["text"] = "TON NFT Tegro Dog 🐕";
			$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($channel_url1);	
			$arInfo["inline_keyboard"][2][0]["text"] = "Команда Tegro TON 🇷🇺";
			$arInfo["inline_keyboard"][2][0]["url"] = rawurldecode($channel_url3);				
			$arInfo["inline_keyboard"][3][0]["callback_data"] = 2;
			$arInfo["inline_keyboard"][3][0]["text"] = $text[$langcode][13]." ✅";		
			send($chat_id, $text[$langcode][14], $arInfo); 			
		}
		elseif( $data['callback_query']['data'] == 2 ){	
		
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id1, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
			$res = json_decode($res, true);
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id2, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res2 = curl_exec($ch);
			curl_close($ch);
			$res2 = json_decode($res2, true);
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id3, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res3 = curl_exec($ch);
			curl_close($ch);
			$res3 = json_decode($res3, true);						
			
			if ($res['ok'] == true && $res['result']['status'] != "left" && $res2['ok'] == true && $res2['result']['status'] != "left"  && $res3['ok'] == true && $res3['result']['status'] != "left") {
		
				$str2upd = "UPDATE `users` SET `verified`='1' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);
				
				####### REFERAL ###########
				if($row->ref > 1){
					$earnRef = 0.1;
					$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$earnRef WHERE `chatid`='".$row->ref."'";
					mysqli_query($link, $str10upd);	
					
					$reftxt = str_replace("%ref%", $row->ref, $text[$langcode][15]);
					
					$response = array(
							'chat_id' => $row->ref,
							'text' => hex2bin('F09F92B0').' '.$fname.' '.$lname.$reftxt);
					sendit($response, 'sendMessage');					
				}				
				####### REFERAL ###########				
				
				$points = getPoints();
				$refpoints = $row->refbalance;
				$points = $points + $refpoints;
				
				$msgtxt = str_replace("%chat_id%", $chat_id, $text[$langcode][16]);
				$msgtxt = str_replace("%points%", $points, $msgtxt);
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "👍 ".$msgtxt,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');					
		
			}
			elseif($res['result']['status'] == "left"){
		
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/answerCallbackQuery');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $data['callback_query']['id'], 'text' => $text[$langcode][17], 'show_alert' => 1, 'cache_time' => 0));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
		
			} else {
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/answerCallbackQuery');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $data['callback_query']['id'], 'text' => $text[$langcode][17], 'show_alert' => 1, 'cache_time' => 0));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
			
			}	
		}
		elseif( $data['callback_query']['data'] == 3 ){						
			
			$walletno = $row->wallet;
			
			$toButtons = str_replace("%recepientWallet%", $recepientWallet, $text[$langcode][20]);
			
			$tonurl = "ton://transfer/$recepientWallet";
			$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($tonurl);	
			$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][18];			
			$arInfo["inline_keyboard"][1][0]["callback_data"] = "checkpay".$walletno;
			$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][19];
			send($chat_id, $toButtons, $arInfo); 		
		
		}
		elseif( $data['callback_query']['data'] == 4 ){						

			clean_temp_sess();
			save2temp("action", "wait4wallet");
			$response = array(
				'chat_id' => $chat_id, 
				'text' => $text[$langcode][21],
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');
			
		}
		elseif( $data['callback_query']['data'] == 5 ){	
		
			processWallet();
		
		}
		elseif( $data['callback_query']['data']  == 6){
		
			$response = array(
				'chat_id' => $chat_id, 
				'text' => $text[$langcode][34],
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');
						
		}
		elseif( $data['callback_query']['data']  == 7){			
		
			processWallet2();			
					
		}
		elseif( $data['callback_query']['data'] > 99  && $data['callback_query']['data'] < 102){
			
			$langcode = $data['callback_query']['data'] - 100;
		
			$str2upd = "UPDATE `users` SET `lang`='".$langcode."' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			###################
			$langcode = langCode($langcode);
			###################
			
			mainMenu();
		}
		elseif( preg_match("/checkpay/", $data['callback_query']['data'])){	

			// Check payment
			$senderid = str_replace("checkpay", "", $data['callback_query']['data']);

/*			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getTransactions");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"address=".$senderid);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$senderid);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$XAPIKey));		
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			$verified = 0;
			$paidSumForNFT = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($verified == 1) continue;
				if($res["result"][$i]["out_msgs"][0]["destination"] == $recepientWallet){
						$verified = 1;
						$nanosum = $res["result"][$i]["out_msgs"][0]["value"];
						$xvostNFT = substr($nanosum, -9);
						$nachaloNFT = str_replace($xvostNFT, "", $nanosum);
						$paidSumForNFT = $nachaloNFT.".".$xvostNFT;	
				}
			} // end FOR
			
			if($verified == 1){
				
				clean_temp_sess();
				delMessage("", $data['callback_query']['message']['message_id']);
				
				######## SAVE TRANSACTION ###########
				$date_time = date("j-m-Y G:i");
				$str2ins = "INSERT INTO `transactions` (`chatid`,`sender`,`date_time`,`ton`) VALUES ('$chat_id','$senderid','$date_time','$paidSumForNFT')";
				mysqli_query($link, $str2ins);
				######## SAVE TRANSACTION ###########				
				
				##### GET POINTS ######
				$pointsnow = floor($paidSumForNFT);
				$tomsg = str_replace("%pointsnow%", $pointsnow, $text[$langcode][22]);
				##### GET POINTS ######				
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $tomsg,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
				
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "❌ ".$text[$langcode][23],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}		
			// Check payment
						
		}
		
	}else{
	
		$str5select = "SELECT `action` FROM `temp_sess` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
		$result5 = mysqli_query($link, $str5select);
		$row5 = @mysqli_fetch_object($result5);
		
		if(preg_match("/wait4wallet/", $row5->action)){	

			if(strlen(trim($data['message']['text'])) < 20){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "❌ ".$text[$langcode][24],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
				
			}else{
			
			//Wallet verify
			$walletno = trim($data['message']['text']);
			
/*			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getAddressInformation");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"address=".$walletno);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$XAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			if($res['ok'] == false){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "❌ ".$text[$langcode][25],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
							
			} else {
				
				$str2upd = "UPDATE `users` SET `wallet`='$walletno' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);	
				
				walletMessage($walletno);				
			
			}
		
		}
	
	}
	elseif(preg_match("/wait4ad_wallet/", $row5->action)){

			if(strlen(trim($data['message']['text'])) < 20){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][35],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
				
			}else{
			
			//Wallet verify
			$walletno = trim($data['message']['text']);
			$validated = 0;
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$XAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			if($res['ok'] == false){
				
				if(strlen($walletno) >= 40 && preg_match("/^0x/", $walletno)){
					$validated = 1;
				}
							
			} else {
				
				$validated = 1;
				
			} // end IF res = OK
	
			if($validated == 1){
				
				$str2del = "DELETE FROM `wallets` WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2del);	
				$str2ins = "INSERT INTO `wallets` (`chatid`,`wallet`) VALUES ('$chat_id','$walletno')";
				mysqli_query($link, $str2ins);	
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][36],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');	
				
				clean_temp_sess();			
				
			}else{
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][37],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');			
				
			}
		
			} // end IF strlen > 20		
	
	}
	}

} // if-else /start
 
exit('ok'); //Обязательно возвращаем "ok", чтобы телеграмм не подумал, что запрос не дошёл

function sendit($response, $restype){
	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/'.$restype);  
	curl_setopt($ch, CURLOPT_POST, 1);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_exec($ch);
	curl_close($ch);	
}

function send($id, $message, $keyboard) {   
		
		//Удаление клавы
		if($keyboard == "DEL"){		
			$keyboard = array(
				'remove_keyboard' => true
			);
		}
		if($keyboard){
			//Отправка клавиатуры
			$encodedMarkup = json_encode($keyboard);
			
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'reply_markup' => $encodedMarkup,
				'parse_mode' => 'HTML',
				'disable_web_page_preview' => True
			);
		}else{
			//Отправка сообщения
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'parse_mode' => 'HTML',
				'disable_web_page_preview' => True				
			);
		}
       
        $out = sendit($data, 'sendMessage');       
        return $out;
}     

function mainMenu(){
	global $chat_id, $link, $langcode, $text;
	
	$arInfo["keyboard"][0][0]["text"] = "🆓 ".$text[$langcode][0];
	$arInfo["keyboard"][0][1]["text"] = "💹 ".$text[$langcode][1];
	$arInfo["keyboard"][1][0]["text"] = "🪙 AirDrop Token";	
	$arInfo["keyboard"][1][1]["text"] = "🎁 ".$text[$langcode][2];
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $text[$langcode][3], $arInfo); 	
}

function clean_temp_sess(){
	global $chat_id, $link;
	
	$str2del = "DELETE FROM `temp_sess` WHERE `chatid` = '$chat_id'";
	mysqli_query($link, $str2del);
}

function save2temp($field, $val){

	global $link, $chat_id;
	$curtime = time();
	
	$str2ins = "INSERT INTO `temp_sess` (`chatid`,`$field`, `times`) VALUES ('$chat_id','$val', '$curtime')";
	mysqli_query($link, $str2ins);	

}

function delMessage($mid, $cid){
	global $chat_id;
		if($mid != ''){
			$message_id = $mid-1;
		}
		elseif($cid != ''){
			$message_id = $cid;
		}

		$ch2 = curl_init('https://api.telegram.org/bot' . TOKEN . '/deleteMessage');  
		curl_setopt($ch2, CURLOPT_POST, 1);  
		curl_setopt($ch2, CURLOPT_POSTFIELDS, array('chat_id' => $chat_id, 'message_id' => $message_id));
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch2, CURLOPT_HEADER, false);
		$res2 = curl_exec($ch2);
		curl_close($ch2);		
}

function getPoints(){
	global $link, $chat_id;
	
		$str8select = "SELECT SUM(ton) AS totalpoints FROM `transactions` WHERE `chatid`='$chat_id'";
		$result8 = mysqli_query($link, $str8select);
		$row8 = @mysqli_fetch_object($result8);		
		
		$points = floor($row8->totalpoints) + 1;
		if($points > 51) $points = 51;
		if($points < 1) $points = 1;
		return $points;
}

function walletMessage($walletno){
	global $chat_id, $langcode, $text, $lang;
	
	$toButton = str_replace("%walletno%", $walletno, $text[$langcode][7]);	
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 3;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][5];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 4;
	$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][6];				
	send($chat_id, $toButton, $arInfo); 	

}

function langCode($langcode){
	if($langcode > 1) $langcode = 0;
	return $langcode;
}

function chooseLang(){
	global $chat_id, $link, $langcode, $text, $lang;
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 100;
	$arInfo["inline_keyboard"][0][0]["text"] = $lang[0];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 101;
	$arInfo["inline_keyboard"][0][1]["text"] = $lang[1]; 
	send($chat_id, hex2bin('F09F92AD')." ".$text[$langcode][4], $arInfo); 	
}

function processWallet(){
	global $chat_id, $link, $langcode, $text;
	
	$str2select = "SELECT * FROM `wallets` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	if(strlen($row->wallet) > 10){
		$toButton = str_replace("%walletno%", $row->wallet, $text[$langcode][38]);	
		
		$arInfo["inline_keyboard"][0][0]["callback_data"] = 6;
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][39];
		$arInfo["inline_keyboard"][0][1]["callback_data"] = 7;
		$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][40];				
		send($chat_id, $toButton, $arInfo);
		 		
	}else{
		
		processWallet2();
		
	}
	
}

function processWallet2(){
	global $chat_id, $link, $langcode, $text;

	clean_temp_sess();
	save2temp("action", "wait4ad_wallet");
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][41],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	

}

function send2($method, $request)
{

	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/' . $method);
	curl_setopt_array($ch,
		[
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($request),
			CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
			CURLOPT_SSL_VERIFYPEER => false,
		]
	);
	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}
	
function uuid()
{
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),

		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}	

function checkInlineQuery()
{
	global $langcode, $text;	
	$request = json_decode(file_get_contents('php://input'));

	if (isset($request->inline_query))
	{
		
		$chatid = $request->inline_query->from->id;
		
		#file_put_contents('debug', print_r($request, true) . PHP_EOL . json_encode($request) . PHP_EOL . $result . PHP_EOL, FILE_APPEND);
		
		// https://core.telegram.org/bots/api#answerinlinequery
		send2('answerInlineQuery',
			[
				'inline_query_id' => $request->inline_query->id,

				// InlineQueryResult https://core.telegram.org/bots/api#inlinequeryresult
				'results' =>
				[
					[
						// InlineQueryResultArticle https://core.telegram.org/bots/api#inlinequeryresultarticle
						'type' => 'article',
						'id' => uuid(),
						// 'id' => 0,
						'title' => $text[$langcode][44],
						'description' => $text[$langcode][47],
						'thumb_url' => 'https://lucknftbot.ru/LuckNFTbot/avatar100.jpg',

						// InputMessageContent https://core.telegram.org/bots/api#inputmessagecontent
						'input_message_content' =>
						[
							// InputTextMessageContent https://core.telegram.org/bots/api#inputtextmessagecontent
							'message_text' => $text[$langcode][45],
						],

						// InlineKeyboardMarkup https://core.telegram.org/bots/api#inlinekeyboardmarkup
						'reply_markup' =>
						[
							'inline_keyboard' =>
							[
								// InlineKeyboardButton https://core.telegram.org/bots/api#inlinekeyboardbutton
								[
									[
										'text' => $text[$langcode][46],
										'url' => 'https://t.me/LuckNFTbot?start='.$chatid,
									],
								],
							],
						],
					],
				],
			]
		);
	}
}