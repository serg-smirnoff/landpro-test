<?php

require_once ('libs/amo.php');
require_once ('vendor/autoload.php');
require_once ('libs/googlesheets.php');

use AMO as AMO;
use GOOGLESHEETS as GOOGLESHEETS;

$amo = new \AMO\Amo();

/*
	Авторизируемся в AMO.CRM
	*/

$amo->getAuth();

/*
	Получаем лиды из AMO.CRM
	*/

$leads = $amo->getLeads();

/*
	Пишем лиды в Google SpreadSheets
	*/

$gss = new \GOOGLESHEETS\Googlesheets();
$gss->setSheetParams($leads);
