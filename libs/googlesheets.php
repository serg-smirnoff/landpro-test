<?php

namespace GOOGLESHEETS;

class Googlesheets{
		
	function __construct(){
		
		$file = file_get_contents('config/params-google.txt');
		$params = explode(':', $file);
		
		$this->DeveloperKey = $params[0];
		$this->ApplicationName = $params[1];
		$this->AuthConfig = $params[2];
		$this->SpreadsheetId = $params[3];
		
	}

	public function getClient($client)	
	{
		
		$client = new \Google_Client();
		
		$client->setDeveloperKey($this->DeveloperKey);
		$client->setApplicationName($this->ApplicationName);
		$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
		$client->setAuthConfig($this->AuthConfig);
		$client->setAccessType('offline');
		$client->setPrompt('select_account consent');
				
		$tokenPath = 'token.json';
		
		if (file_exists($tokenPath)) {
			$accessToken = json_decode(file_get_contents($tokenPath), true);
			$client->setAccessToken($accessToken);
		}
		if ($client->isAccessTokenExpired()) {
			if ($client->getRefreshToken()) {
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			} else {
				$authUrl = $client->createAuthUrl();
				printf("Open the following link in your browser:\n%s\n", $authUrl);
				print 'Enter verification code: ';
				$authCode = trim(fgets(STDIN));
				$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
				$client->setAccessToken($accessToken);
				if (array_key_exists('error', $accessToken)) {
					throw new Exception(join(', ', $accessToken));
				}
			}
			if (!file_exists(dirname($tokenPath))) {
				mkdir(dirname($tokenPath), 0700, true);
			}
			file_put_contents($tokenPath, json_encode($client->getAccessToken()));
		}		
		
		return $client;
	
	}

	public function setSheetParams($leads) {
		
		$service = new \Google_Service_Sheets($this->getClient());

		$spreadsheetId = $this->SpreadsheetId;

		$range = 'A2:F';
		
		/*
			Данный алгоритм требует доработки
			*/
				
		foreach ($leads as $k => $v){
			$i=0;
			foreach ($v as $k2 => $v2){
				$ret[$k][$i] = $v2;
				$i++;
			}
		}
		
		$body = new \Google_Service_Sheets_ValueRange([
			'values' => $ret
		]);
		
		try
		{
			if (!$result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, ['valueInputOption' => 'RAW']))
				throw new Exception('Error'); else {
					echo "Данные записаны в <a href=\"https://docs.google.com/spreadsheets/d/1jMs7oIQTOc1vPPTrdz7WGsgR1b8siizM-pgb5pRAZ78/edit#gid=0\">таблицу Google SpreadSheets</a>";
				}
		}
		catch(Exception $E)
		{
			die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}
				
	}
	
}

?>