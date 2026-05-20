<?php

$config = array(
	"CONTROLADOR" => array("inicial"),
	"RUTAS_INCLUDE" => array("aplicacion/modelos", "aplicacion/auxiliares", "aplicacion/controladores", "scripts/librerias"),
	"URL_AMIGABLES" => true,
	"VARIABLES" => array(
		"autor" => "Diana Romero",
		"grupo" => "2daw"
	),
	"BD" => array(
		"hay" => true,
		"servidor" => "localhost:3307",
		"usuario" => "Diana",
		"contra" => "2daw",
		"basedatos" => "distarart"
	),
	"sesion" => array("controlAutomatico" => true),
	"ACL" => array("controlAutomatico" => true)

);
