<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/* función que le pasa un parámetro y devulve el número del aula.
 * La etiqueta comienza por ROOM y le sigue un numero de tres dígitos 001 a 999
 * que es lo que devolverá la función.
 * Si no comienza por ROOM devuelve 0
 */

function is_code_room($room) {
    if (substr($room, 0, 4) <> "ROOM") {
        return 0;
    } else {
        return intval(substr($room, 4)); //si hay un error en la conversión devuelve 0
    }
}

function process_codes($codes) {
    foreach ($codes as $code) {
        print "PC: ".$code . "<br>";
    }
}
session_start();

// incluir la conexión a la base de datos
//include 'conexion.php';
$codigo = isset($_GET['code']) ? $_GET['code'] : "368921"; //un parámetro del aula a evaluar



print "sesión: (".$_SESSION[ROOM].")". "<br>";

if (isset($_SESSION[ROOM])) {
    //Estamos inventariando un aula
    print "sesion ya iniciada: ".$_SESSION[ROOM]. "<br>";
    if ($room = is_code_room($codigo)) {
        //finaliza la captura de código y procesa
        print "procesamos $codigo". "<br>";
        process_codes($_SESSION[codes]);
        session_destroy();
    } else {
        print "añadimos código $codigo". "<br>";
        $codes = $_SESSION["codes"];
        array_push($codes, $codigo);
        $_SESSION["codes"]=$codes;
    }
} elseif ($room = is_code_room($codigo)) {
    $_SESSION["ROOM"] = $room;
    $codes = array();
    $_SESSION["codes"]=$codes;
    print "inicio sesión $room". "<br>";
    print "sesión: (".$_SESSION[ROOM].")". "<br>";
} else {
    //search.php
}