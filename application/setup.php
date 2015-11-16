<?php
//Charset
$charset= 'UTF-8';
//Seteo la codificacion de caracteres, casi siempre es o debe ser UTF-8
ini_set('default_charset', $charset);
//Set Default Time Zone si no esta seteada
$timeZone= 'GMT';
if(! ini_get('date.timezone') || ini_get('date.timezone') != $timeZone){
    date_default_timezone_set($timeZone);
}
//Tipo de configuracion: YAML - PHP - JSON
$configurationType= 'YAML';
//Carpeta de configuracion
$configurationFolder= 'config/';
//Indica si se cachean los archivo de configuracion
$cache= FALSE;