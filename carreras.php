<?php

require_once('db.php');
require_once('clases_base.php');
require_once('class_paginai.php');
require_once('clase_entidadj.php');
require_once('clase_carrera.php');

$entidad = new carrera() ;

$entidad->mostrar_pagina_lista();

//include_once('carga_llegada.view.php');

//if ( isset( $_REQUEST['ok_tiempo'] ) )
/*	{
		
	echo 'registro tiempo' ;
	
*/

?>
