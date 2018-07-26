<?php
/*
--  8""""8 8""""8 8     8""""8 8"""88    8""""8 8""""8   
--  8    " 8    8 8     8    8 8    8    8    8 8    8   
--  8e     8eeee8 8e    8eeee8 8    8    8e   8 8eeee8ee 
--  88     88   8 88    88   8 8    8    88   8 88     8 
--  88   e 88   8 88    88   8 8    8    88   8 88     8 
--  88eee8 88   8 88eee 88   8 8eeee8    88eee8 88eeeee8 
--                                                       
*/
/*
    On définit le charset sur utf-8 et on crée la session PHP
*/
header('Content-Type: text/html; charset=utf-8');
session_start();

/*
    Options de dev:
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'index.php';

$db = new DB_();
echo $db->getPlaceFor(
    array(
        'key'   => 'user',
        'value' => 'florian',
        'return'=> 'all',
        'cache' => True
    )
);
$db->setDataFor(array(
    'key'   => 'user',
    'value' => 'florian',
    'data_key'  => 'test',
    'data_value' => 'hello,world!',
    'edit' => True
));
echo "<br />";
echo $db->getDataFor(array(
    'key'   => 'user',
    'value' => 'florian',
    'data'  => 'test'
));