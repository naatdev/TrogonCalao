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

/*
    newSpawn permet de créer un emplacement de stockage pour un futur utilisateur
    Ceci est facultatif car la fonction est appellée automatiquement lorsque setDataFor est utilisée
*/
$db->newSpawn(array(
    'key'   => 'user', // key est la façon dont cet utilisateur sera identifié au sein de la base
    'value' => 'flo' // value est son identifiant au sein de la base
));
/*
    setDataFor permet de sauvegarder une donnée pour un utilisateur
    ou bien de modifier une de ses données déjà existantes
*/
$db->setDataFor(array(
    'key'   => 'user', // key est la façon dont cet utilisateur sera identifié au sein de la base
    'value' => 'flo', // value est son identifiant au sein de la base
    'data_key'  => 'test', // data_key est la façon dont cette donnée sera identifiée au sein de la base
    'data_value' => 'hello,world!' // data_value est la donnée elle même
));
/*
    unSpawn permet de supprimer un utilisateur du registre
    attention ceci ne supprime pas ses données mais juste son enregistrement dans la base
*/
$db->unSpawn(array(
    'key' => 'user', // key est la façon dont cet utilisateur est identifié au sein de la base
    'value' => 'a' // value est son identifiant au sein de la base
));
/*
    getDataFor permet de récuperer une donnée d'un utilisateur
    la fonction renvoie False en cas de non existance de cette donnée
*/
echo $db->getDataFor(array(
    'key'   => 'user', // key est la façon dont cet utilisateur est identifié au sein de la base
    'value' => 'flo', // value est son identifiant au sein de la base
    'data'  => 'test' // data est le nom de la donnée
));
$db->statsDb();
$db->version();