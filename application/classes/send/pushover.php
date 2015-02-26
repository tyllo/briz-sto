<?php
function pushover($subject='Сообщение с сайта rosimport',$message='Что-то случилось'){
	$rosimport = "aYxat5dA9aPYpN7FMtmXgaRxa1tDxC";
	$spyder = "uUjZWjuWahkYqQZXgsyQZvzoSomc7k";
	$ch = curl_init();
	
	$message=str_replace('<br />','',$message);
	
	curl_setopt_array($ch = curl_init(), array(
		CURLOPT_URL => "https://api.pushover.net/1/messages.json",  CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_POSTFIELDS => array(
		"token" => "$rosimport",
		"user" => "$spyder",
		"title" => "$subject", 
		"message" => "$message",
		//"priority" => "2",
		"sound" => "magic"
		//"url" => "http:www.briz-sto.ru",
		//"url_title" => "посетить сайт www.briz-sto.ru"
	)));

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	$answer=curl_exec($ch);
	curl_close($ch);
	if ( preg_match('/errors/i', $answer) ) return false; 
		else return 'Pushover';//$answer;
}
?>