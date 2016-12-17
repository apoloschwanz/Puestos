<?php

require_once('db.php');
require_once('clases_base.php');
require_once('class_paginai.php');
require_once('clase_entidadj.php');
require_once('clase_carrera.php');
require_once('clase_corredor.php');
require_once('clase_posicion.php');

$entidad = new posicion() ;

$entidad->mostrar_posiciones();

//include_once('carga_llegada.view.php');

//if ( isset( $_REQUEST['ok_tiempo'] ) )
/*	{
		
	echo 'registro tiempo' ;
	
*/

?>
