<?php

/* 
 * Fichero de conexión a la base de datos
 * Toma los valores del fichero config.php
 */

define("GLPI_ROOT", 'ok');

include '../glpi/inc/dbmysql.class.php';
include '../glpi/config/config_db.php';

$db = new DB();

$conexion = new mysqli($db->dbhost, $db->dbuser, $db->dbpassword, $db->dbdefault);

if ($conexion->connect_errno) { // Si se produce algún error finaliza con mensaje de error
    die("Error de Conexión: " . $conexion->connect_error);
}
$conexion->set_charset("utf8");

