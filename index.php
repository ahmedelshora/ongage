<?php

ini_set('display_errors', 1);

require './Ongage.php';

$userName = 'kurt.martin@pmg360.com';
$password ='cP46kBUNdSmwB98';
$accountCode='pmg360';

$object = new Ongage($userName,$password,$accountCode);
 //get campagin
// print_r(json_decode($object->sendRequest('mailings/1055202892')));
// print_r(json_decode($object->sendRequest('emails')));

// ### get list ids 


$object->getList();
$object->getReport();
// var_dump();
// die;


