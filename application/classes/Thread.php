<?php

	/*  PHP Threads by Andrzej Wielski  */
	/*    [ http://vk.com/wielski ]    */

	//require_once('Closure.php');
	
	Class Thread {
		private $salt        = "DfEQn8*#^2n!9jdsfsfda4346LKH&1~_&%I_ErF";
		private $password    = 'asfdv12341234LMPDFMSDF235346SDFGwerwerf';
		private $max_threads = 6;
		private $url;
		
		public function __construct($url=false){
			if ($url) $this->url = $url;
			//if(isset($_SERVER['HTTP_PHPTHREADS'])){
			if (isset($_POST['HTTP_PHPTHREADS'])){
				$password = $_POST['HTTP_PHPTHREADS'];
				$password = $this->strcode(base64_decode($password), $this->password);	
				
				// для паранои проверим пароль
				if($password != $this->password) return false; //die('bad password');

				$object = $_POST['PHPThreads_Object'];
				$object = $this->strcode(base64_decode($object), $this->password);	
				$object = unserialize($object);
				//print_r($object); echo"\n";
				
				$method = $_POST['PHPThreads_Method'];
				$method = $this->strcode(base64_decode($method), $this->password);	
				$method = unserialize($method);
				//echo "\$method=$method\n";
				
				$vars = $_POST['PHPThreads_Vars'];
				$vars = $this->strcode(base64_decode($vars), $this->password);	
				$vars = unserialize($vars);
					
				$session = $_POST['PHPThreads_Session'];
				$session = $this->strcode(base64_decode($session), $this->password);	
				$session = unserialize($session);
					
				ob_start();
				$_SESSION = $session;
				
				if( is_array($vars))
					$response = call_user_func_array(array($object, $method), $vars);
				elseif( $vars )
					$response = call_user_func(array($object, $method), $vars);
				else
					$response = $object->$method();

				$echo = ob_get_contents();
				ob_end_clean();
					
				echo json_encode(array(
					'return' => $response,
					'echo' => $echo
				));
				die();
			} //else echo "HTTP_PHPTHREADS\n<br />";
		}
		
		public function Create($object, $method, $vars = false){
			if(gettype($object) != 'object'):
				echo "<!--error--><br /><b>Threads Error</b>: Thread must be a object.<br />\n";
				return false;
			endif;
			
			//echo "\$method=$method\n";
			//print_r($object);
			//print_r($vars);
			
			// доступен ли метод и его __call
			// if ($method === false) $method = 'init';
			if ( !is_callable(array($object, $method))):
				echo "<!--error--><br /><b>Threads Error</b>: Thread ($".get_class($object)."->{$method}) must be a method.<br />\n";
				return false;
			endif;
				
			//$thread =  new SuperClosure($func);
			$serialized_object = serialize($object);
			$serialized_method = serialize($method);
			$serialized_vars = serialize($vars);
			$this->threads[] = array(
				'Object' => $serialized_object,
				'Method' => $serialized_method,
				'Vars'	 => $serialized_vars
			);
		}
		
		public function Clear(){
			unset($this->threads);
		}
		
		public function Run($echo = false){
			// можно задать callback функцию, если нужно для 
			// немедленной обработки результата когда страница скачается
			$callback = false;

			if(!is_array($this->threads)) return false;
			//$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
			$url = $this->url;
			if (!$url):
				echo "<!--error--><br /><b>Threads Error</b>: Thread not set url\n";
				return false;
			endif;
			
			session_start();
			$session = serialize($_SESSION);
			session_write_close();
			
			$threadsArr = array_chunk($this->threads, $this->max_threads);
			foreach ($threadsArr as $threads):
			
				//Start
				$cmh = curl_multi_init();
				$tasks = array();
				
				foreach ($threads as $i=>$thread):
					
					// Инициализирую CURL
					$tasks[$i] = ( $ch = curl_init() );
					
					curl_setopt($ch, CURLOPT_URL, $url);
					#curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
					curl_setopt($ch, CURLOPT_TIMEOUT, 20);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('PHPThreads: true'));
					// это необходимо, чтобы cURL не высылал заголовок на ожидание
					curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:  "));
					//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   
					//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
					curl_setopt($ch, CURLOPT_POST, 1);
					
					$Post = array(
						'PHPThreads_Object' => base64_encode($this->strcode($thread['Object'], $this->password)),
						'PHPThreads_Method' => base64_encode($this->strcode($thread['Method'], $this->password)),
						'PHPThreads_Vars'   => base64_encode($this->strcode($thread['Vars'], $this->password)),
						'HTTP_PHPTHREADS'   => base64_encode($this->strcode($this->password, $this->password)),
						'PHPThreads_Session'=> base64_encode($this->strcode($session, $this->password))
					);
					
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($Post));
					
					curl_multi_add_handle($cmh, $ch);
				
				endforeach;
				
				$active = null;

				do {
					curl_multi_exec($cmh, $active);			
					
					// получаю информацию о текущих соединениях
					do {	
						$info = curl_multi_info_read($cmh);
						if ( is_array( $info ) && ( $ch = $info['handle'] ) ): 
						
							// получаю содержимое загруженной страницы

							$result = curl_multi_getcontent($ch);
							$curl_result = json_decode($result, true);
							
							if( !is_array($curl_result) ) :
								$curl_result = array();
								$curl_result['echo'] = $result;
								$curl_result['return'] = null;
							endif;
							
							// скаченная ссылка
							$url = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
							
							if ($echo == 'echo') echo $curl_result['echo'];
							
							// вызов callback-обработчика
							if ( $callback !== false ):
								$callback( $url, $curl_result['return'], $info['result'], $ch );
							else:
								$results['debug'][] = array(
									'url'		  => $url,
									'echo'	  => $curl_result['echo'],
									'return'	  => $curl_result['return'],
									'status'	  => $info['result'],
									'status_text' => curl_error( $ch ) 
								);
								$results['return'][] = $curl_result['return'];
							endif;
							
							// wait for 0.2 seconds
							usleep(200000);
						endif;
					
					} while ($info);

				} while ($active > 0);

				//close the handles
				foreach ( $tasks as $ch ):
					curl_multi_remove_handle($cmh, $ch);
					curl_close( $ch );
				endforeach;

				curl_multi_close($cmh);
			endforeach;
			
			//session_start();
			// Clear Threads after run
			$this->Clear();
			
			if(is_array($results)) ksort($results);
			
			return ( $echo == 'debug' ) ? $results['debug']:$results['return'];
			// End
		}
		
		private function strcode($str, $passw=""){
			
			$len = strlen($str);
			$gamma = '';
			$n = $len>100 ? 8 : 2;
			while( strlen($gamma)<$len ){
				$gamma .= substr(pack('H*', sha1($passw.$gamma.$this->salt)), 0, $n);
			}
			return $str^$gamma;
		} //Encode decode string by pass
		
		
	}

$Thread = new Thread();
