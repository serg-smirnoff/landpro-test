<?php

class AMO{
	
	function __construct(){
		
		$file = file_get_contents('params.txt');
		$params = explode(':', $file);

		$this->auth = [
			'USER_LOGIN'	=>	$params[0], 
			'USER_HASH'		=>	$params[2],
		];
		
		$this->subdomain = $params[3];
	}

	public function getAuth(){
		
		$auth = $this->auth;
		$subdomain = $this->subdomain;
		
		$link = "https://".$subdomain.".amocrm.ru/private/api/auth.php?type=json";

		$curl = curl_init();
		
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($auth));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		
		$out = curl_exec($curl);
		$code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);

		$code = (int)$code;

		$errors = array(
			301=>'Moved permanently',
			400=>'Bad request',
			401=>'Unauthorized',
			403=>'Forbidden',
			404=>'Not found',
			500=>'Internal server error',
			502=>'Bad gateway',
			503=>'Service unavailable'
		);

		try
		{
			if ($code != 200 && $code != 204)
				throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		}
		catch(Exception $E)
		{
			die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}

		$Response = json_decode($out,true);
		$Response = $Response['response'];

		if(isset($Response['auth'])){
				echo "Авторизация прошла успешно";
				return true;
			} else {
				echo "Авторизация не удалась";
				return false;
			}
	}
	
	public function getLeads(){
		
		$auth = $this->auth;
		$subdomain = $this->subdomain;

		$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?limit_rows=50';
		
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?limit_rows=50&limit_offset=2';
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?query=test@mail.com';
		
		$curl=curl_init();
		
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: Mon, 01 Aug 2013 07:07:23'));
		$out = curl_exec($curl);
		$code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		$code = (int)$code;
		
		$errors = array(
			  301 => 'Moved permanently',
			  400 => 'Bad request',
			  401 => 'Unauthorized',
			  403 => 'Forbidden',
			  404 => 'Not found',
			  500 => 'Internal server error',
			  502 => 'Bad gateway',
			  503 => 'Service unavailable'
		);
		try
		{
		  if($code != 200 && $code != 204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  }
		}
		catch(Exception $E)
		{
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}

		$Response = json_decode($out,true);
		$Response = $Response['_embedded']['items'];
		
		var_dump($Response);
		
	}
	
}


$amo = new Amo();
$amo->getAuth();

$amo->getLeads();

//var_dump(get_class_methods($amo));
//var_dump(get_class_vars($amo));



?>