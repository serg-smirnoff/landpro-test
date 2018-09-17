<?php

require_once ('libs/amo.php');
require_once ('libs/googlesheets.php');

use AMO;
use GOOGLESHEETS;

$amo = new \AMO\Amo();
//var_dump(get_class_methods($amo));
//var_dump(get_class_vars($amo));

$amo->getAuth();
$leads = $amo->getLeads();

$gss = new \GOOGLESHEETS\Googlesheets();
$gss->setDoc($leads);
