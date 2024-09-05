<?php
require '../vendor/autoload.php';

use OpenApi\Generator;

$openapi = Generator::scan(['../FisierePHP']);
header('Content-Type: application/json');
echo $openapi->toJson();
?>
