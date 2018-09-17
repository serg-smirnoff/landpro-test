<?php

require_once ('libs/amo.php');
require_once ('libs/googlesheets.php');

use AMO;
use GOOGLESHEETS;

$amo = new \AMO\Amo();
$amo->getAuth();
print_r($amo->getLeads());

//var_dump(get_class_methods($amo));
//var_dump(get_class_vars($amo));