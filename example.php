<?php 

require_once __DIR__.'/vendor/autoload.php';

use SM\N11\N11;

$x = new N11([
	'app_key' => '',
	'app_secret' => ''
]);

var_dump($x->GetAllCategories());