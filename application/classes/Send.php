<?php defined('SYSPATH') OR die('No direct script access.');

include_once APPPATH . 'classes/send/class.mail.php';
include_once APPPATH . 'classes/send/SendMailSmtpClass.php';
include_once APPPATH . 'classes/send/pushover.php';
include_once APPPATH . 'classes/send/class.pushbullet.php';

class Send {

	// ответ об выполнении
	private $answer	= null;
	
	private $username;
	private $password;
	private $host;
	private $port;
	private $API_KEY =[
		 'pushbullet' => ''
		,'pushover'   => ''
	];
	
	private $data = array();
	private $lang = array(
		'email'			=> 'E-mail',
		'subject'		=> 'Тема',
		'name'			=> 'Имя',
		'phone'			=> 'Телефон',
		'message'	 	=> 'Сообщение',
		'organization'	=> 'Организация'
	);

	private static $instance = null;
	public static function getInstance($data){
		if (is_null(self::$instance)) {
		self::$instance = new self($data);
		}
	return self::$instance;
	}
	private function __construct($data){
	// подгрузим конфиг для отправки письма через smtp
	// читаем login:pass
	$this->username = isset($data['username']) ? $data['username'] : '';
	$this->password = isset($data['username']) ? $data['password'] : '';
	$this->host     = isset($data['username']) ? $data['host'] : '';
	$this->port     = isset($data['username']) ? $data['port'] : '';
	
	$this->API_KEY['pushbullet'] = $data['pushbullet'];
	$this->API_KEY['pushover']   = $data['pushover'];
	
	foreach ($_POST as $key => $value):

		$value = htmlspecialchars($value);
		$key   = htmlspecialchars($key);
		$key   = preg_replace ('/\d/','',$key);
		
		if( $value != '' ) $this->data[$key] = $value;

	endforeach;
	
	// обязательные поля
	$this->data['name'] = isset($this->data['name']) ? $this->data['name'] : $_SERVER['HTTP_HOST'];
	}

	// Защищаем объект
	private function __clone() { 
		/* Защищаем от создания через клонирование */
	}
	
	// вспомогательная функция для форматирования тела сообщения
	private function getMessage(){
	$message='';

	foreach ($this->data as $key => $value):
		$key		 = isset($this->lang[$key]) ? $this->lang[$key] : $key;
		$message .= "$key: $value\r\n";
	endforeach;
	return $message;
}
	
	public function mail($email){
		
		//$emailTo	= $this->emailAdmin;
		$nameTo		= $_SERVER['HTTP_HOST'];
		$subject	= "Сообщение от \"{$this->data['name']}\" с сайта http://$_SERVER[HTTP_HOST]";
		$message	= $this->getMessage();
		$nameFrom	= $this->data['name'];
		$emailFrom	= isset ($this->data['email']) ? $this->data['email'] : "admin@$_SERVER[HTTP_HOST]";
		return $this->smtpmail2($nameTo, $email, $subject, $message, $nameFrom, $emailFrom);
	}
	public function pushbullet(){
		
		$API_KEY = $this->API_KEY['pushbullet'];
		
		$subject = "Сообщение от \"{$this->data['name']}\" с сайта http://$_SERVER[HTTP_HOST]";
		$message = $this->getMessage();
		$message = str_replace('<br />','',$message);
		
		// начинаем
		$p = new PushBullet($API_KEY);
		
		return $p->pushNote(NULL, $subject, $message);
	}
	public function pushover(){
		$subject = "Сообщение от \"{$this->data['name']}\" с сайта http://$_SERVER[HTTP_HOST]";
		$message = $this->getMessage();
		$message=str_replace('<br />','',$message);
		
		$ch = curl_init();
		
		// $FROM, $TO
		extract($this->API_KEY['pushover']);
		
		curl_setopt_array($ch = curl_init(), array(
			CURLOPT_URL => "https://api.pushover.net/1/messages.json",
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POSTFIELDS => array(
				"token"   => $token,
				"user"    => $user,
				"title"   => $subject, 
				"message" => $message,
				"sound"   => 'magic',
				"url"     => $_SERVER['HTTP_HOST'],
				
				//"priority"  => "2",
				//"url_title" => "посетить сайт ". $_SERVER[HTTP_HOST]
		)));

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		$answer = curl_exec($ch);
		curl_close($ch);

		if ( preg_match('/errors/i', $answer) ) 
			return false; 
		else
			return 'Pushover';		
	}

	// использует класс SendMailSmtpClass.php корректно работает с yandex и mail ssl
	private function smtpmail($nameTo, $emailTo, $subject, $message, $nameFrom, $emailFrom){
	
	if( !isset($this->data['email']) ) $this->data['email'] = $this->emailTo;
	
	// создаем экземпляр класса	
	$mailSMTP = new SendMailSmtpClass(
		$this->username,				// логин
		$this->password,				// пароль 
		$this->host,					// хост
		$nameFrom,						// имя отправителя
		$this->port
	); 	
 
	// заголовок письма text/html
	$headers= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=utf-8\r\n"; 		// кодировка письма
	$headers .= "From: $nameFrom <$emailFrom>\r\n"; 				// от кого письмо
	$headers .= "To: $nameTo <$emailTo>\r\n";						// кому
	
	// отправляем письмо
	$answer =  $mailSMTP->send(
		$emailTo,							// Кому письмо
		$subject, 							// Тема письма
		$message, 							// Текст письма
		$headers 							// Заголовки письма
	);
	
	if($answer === true) $this->answer = "Письмо успешно отправлено";
		else $this->answer = "Письмо не отправлено. Ошибка: " . $answer;

	return $answer;

}

	// использует класс mail.php - не работает с yandex и mail ssl
	private function smtpmail2($nameTo, $emailTo, $subject, $message, $nameFrom, $emailFrom){
		
	// начинаем
	$_mail = new Mail('UTF-8');
	
	// от кого отправляется почта
	$_mail->From( "$nameFrom;$emailFrom" );
	
	// кому адресованно
	$_mail->To( "$nameTo;$emailTo" );
	
	$_mail->Subject( $subject );
	$_mail->Body( $message );   
	
	// приоритет письма
	$_mail->Priority(3) ;
	
	// если мы указали почту то $_mail->smtp_on
	if ($this->username ){
		$_mail->smtp_on(
			$this->host,
			$this->username,
			$this->password,
			$this->port
		);
	}
	
	// а теперь пошла отправка
	$answer = $_mail->Send();

	echo $_mail->Get();
	return $answer;
	
	}	
}
