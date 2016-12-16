<?php

require_once 'class_paginaj.php' ;

class campo_entidad {
	protected $nombre; 
	protected $tipo;
	protected $descripcion ;
	protected $objeto ; 
	protected $mostrar ;
	protected $busqueda ;
	protected $valor ;
	protected $readonly ;
	public function __construct($nombre_del_campo,$tipo = 'text',$descripcion=NULL,$objeto_del_campo=NULL,$forzar_mostrar=false)
	{
		$this->nombre = $nombre_del_campo ;
		$this->tipo = $tipo ;
		$this->descripcion = $descripcion ;
		$this->objeto = $objeto_del_campo ;	
		if ( $forzar_mostrar == false and $this->tipo == 'pk' )
			$this->mostrar = false ;
		else
			$this->mostrar = true ;
		$this->busqueda = false ;
		$this->readonly = false ;
	}
	public function nombre() { return $this->nombre ; }
	public function tipo() { return $this->tipo ; }
	public function descripcion() { return $this->descripcion ; }
	public function objeto() { return $this->objeto ; }
	public function mostrar() { return $this->mostrar ; }
	public function pone_busqueda() { $this->busqueda = true ; }
	public function busqueda() { return $this->busqueda ; }
	public function pone_valor($valor) { $this->valor = $valor ; }
	public function valor() { return $this->valor ; }
	public function pone_readonly() { $this->readonly = true ;}
	public function readonly() { return $this->readonly ; }
	public function valor_sql()
	{
		$tv_valor = $this->valor_saneado() ;
		if ( empty( $tv_valor ) )
			{
				$ts_valor_sql = 'NULL' ;
			}
		else
			{
				if ( $this->tipo == 'time' ) 
					$ts_valor_sql = '1899-12-30 '.$tv_valor ;
				else
					$ts_valor_sql =  $tv_valor ;
				//$ts_valor_sql = htmlspecialchars( $ts_valor_sql  ) ;
				$ts_valor_sql = $this->sanear_string( $ts_valor_sql ) ;
				$ts_valor_sql = " '".$ts_valor_sql."' " ;
			}
		//
		return $ts_valor_sql ; 
		
	}
	public function valor_saneado()
	{
		$valor_saneado = NULL ;
		// 'pk' 'fk' 'otro' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password'
		if ( $this->tipo == 'pk' or $this->tipo == 'fk') { 
			$valor_saneado = filter_var($this->valor, FILTER_SANITIZE_NUMBER_INT ); 
		}
		elseif( $this->tipo == 'number' ) {
			$valor_saneado = filter_var($this->valor, FILTER_SANITIZE_NUMBER_FLOAT ) ;
		}
		elseif( $this->tipo == 'email' ) {
			$valor_saneado = filter_var($this->valor, FILTER_SANITIZE_EMAIL ) ; 
		}
		elseif( $this->tipo == 'url' ) {
			$valor_saneado = filter_var($this->valor, FILTER_SANITIZE_URL ) ; 
		}
		else { 
			$valor_saneado = filter_var($this->valor, FILTER_SANITIZE_STRING ) ; 
		}
		return $valor_saneado ;
	}
	protected function sanear_fecha($string)
	{
	 
		$string = trim($string);
	 
		//$string = str_replace(
		//	array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
		//	array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
		//	$string
		//);
	 
	 
		//Esta parte se encarga de eliminar cualquier caracter extraño
		$string = str_replace(
			array("\\" , "¨", "º", "~",
				 "#",  "|", "!", "\"",
				 "\·", "\$", "%", "\&", 
				 "(", ")", "?", "'", "¡",
				 "¿", "[", "^", "<code>", "]",
				 "+", "}", "{", "¨", "´",
				 ">", "<", ";", ",", ":",
				 "·" , "&" , "=" , "*" ),
			'', $string );
		return $string ;
	}
	protected function sanear_num($string)
	{
		if ( intval($string) <> 0 )
		{
			$string = str_replace(
			array("\\" , "¨", "~",
				 "|", "\"",
				 "\·", "\$", "%", "\&", "/",
				 "'", 
				 "[", "^", "<code>", "]",
				 "+", "}", "{", "¨", "´",
				 ">", "<", ";", ",", ":",
				 "·" , "&" , "=" , "*" ),
			'', $string );
			return $string ;
		}
		else
			return 0 ;
	}
	protected function sanear_string($string)
	{
	 
		$string = trim($string);
	 
		//$string = str_replace(
		//	array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
		//	array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
		//	$string
		//);
	 
	 
		//Esta parte se encarga de eliminar cualquier caracter extraño
		//$string = str_replace(
			//array("\\" , "¨", "~",
			//	 "|", "\"",
			//	 "\·", "\$", "%", "\&", "/",
			//	 "'", 
			//	 "[", "^", "<code>", "]",
			//	 "+", "}", "{", "¨", "´",
			//	 ">", "<", ";", ",", ":",
			//	 "·" , "&" , "=" , "*" ),
			//'', $string );
		$string = str_replace( array("\\" ,  "\"", "'" ) , '' , $string ) ;
		return $string ;
	}
		
}

class entidadj {
	protected $pagina_url_anterior ;
	protected $lista_campos_lista ;
	protected $lista_campos_lectura ;
	protected $pagina_titulo ;
	protected $okBorrarUno;
	protected $okGrabaAgregar ;
	protected $okGrabaBorrarUno ;
	protected $okAgregar ;
	protected $okModificar ;
	protected $okGrabar  ;
	protected $okReleer  ;
	protected $okSalir ;
	protected $okListaSiguiente ;
	protected $okListaAnterior ;
	protected $okListaPrimero ;
	protected $okListaUltimo ;
	protected $okVer ;
	protected $okListaPosicion;
	public $okExportar ;
	protected $botones_extra_edicion ;
/////////////////////////////////////-------- variables de la clase entidad --------- //////////////////////////////////
	protected $strsql ;
	protected $desde ;
	protected $cuenta ;																							// by DZ 2015-08-18 - agregado lista de datos
	protected $id;
	protected $registros ;
	protected $registro ;
	protected $nombre_tabla;
	protected $nombre_fisico_tabla ;
	protected $nombre_pagina;
	protected $lista_campos_descrip ;																// by DZ 2016-01-18 - agregado lista de datos
	protected $lista_campos_tipo ;																	// by DZ 2016-01-18 - agregado lista de datos
	protected $lista_campos_nombre ;																// by DZ 2016-01-22 - agregado alta seleccion
	protected $prefijo_campo ; 
	protected $error ;
	protected $textoError ;
	protected $detalle ;
	protected $filtro_f_desde ;
	protected $filtro_f_hasta ;
	protected $filtro_gral ;
	protected $filtro_id;
	protected $con_filtro_fecha ;
	protected $con_filtro_general ;
	protected $lista_detalle ;
	protected $tiene_lista_detalle ;
	protected $lista_detalle_enc_columnas ;
	protected $acciones;
	protected $accion_ok;
	protected $accion_especial_seleccionada;
	protected $existe ;			
	protected $clave_manual;				
	protected $borrar_con_seleccion;			
/////////////////////////////////////-------- variables de la clase entidad --------- //////////////////////////////////
  // by DZ 2016-01-22 - protected $lista_campos_descrip ;																
	// by DZ 2016-01-22 - sacar ---- protected $lista_campos_tipo ;																	
	// by DZ 2016-01-22 - protected $lista_campos_nombre ;																
 		protected function Pone_Datos_Fijos_No_Heredables()
		{	
			//
			// Lista de Campos
			//
			// tipos:  'pk' 'fk' 'otro' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password'
			//								el tipo 'fk' espera que se defina una clase 
			$this->clave_manual_activar();
			$this->lista_campos_lista=array();
			$this->lista_campos_lista[]=array( 'nombre'=>'id' 			, 'tipo'=>'pk' 		, 'descripcion'=>'Identificador' , 'clase'=>NULL ) ;
			$this->lista_campos_lista[]=array( 'nombre'=>'descrip' 	, 'tipo'=>'text' 	, 'descripcion'=>'Identificador' , 'clase'=>NULL ) ;
			//$this->lista_campos_lista[]=array( 'nombre'=>'descrip' 	, 'tipo'=>'text' 	, 'descripcion'=>'Identificador' , 'clase'=>new Entidadi() ) ;
			//
			//
			$this->lista_campos_lectura=array();
			$this->lista_campos_lectura[]=array( 'nombre'=>'id' 			, 'tipo'=>'pk' 		, 'descripcion'=>'Identificador' , 'clase'=>NULL ) ;
			$this->lista_campos_lectura[]=array( 'nombre'=>'descrip' 	, 'tipo'=>'text' 	, 'descripcion'=>'Identificador' , 'clase'=>NULL ) ;
			//$this->lista_campos_lectura[]=array( 'nombre'=>'descrip' 	, 'tipo'=>'text' 	, 'descripcion'=>'Identificador' , 'clase'=>new Entidadi() ) ;
						
			//
			// Nombre de la tabla
			$this->nombre_tabla = "Nombre de la_tabla" ;
			$this->nombre_fisico_tabla = "nombre_de_la_tabla" ;
			//
		}
	protected function Pone_Datos_Fijos_Personalizables()
	{
		//
		// Prefijo campo
		$this->prefijo_campo = 'm_'.get_class($this).'_' ;
		//
		// Nombre de la pagina
		$this->nombre_pagina = $_SERVER['PHP_SELF'] ;
		//
		// Paginacion
		$this->desde = 0 ;																					// by DZ 2015-08-14 - agregado lista de datos
		$this->cuenta = 10 ;																				// by DZ 2015-08-14 - agregado lista de datos		
		//
		// Acciones Extra para texto_mostrar_abm
		//$this->acciones = array( 'nombre'=>'okAsignarDte' , 'texto'=>'AsignarDte' ) ;
		//
		// Botones Extra para texto_mostrar_abm
		//$this->botones_extra_abm[] = array( 'nombre'=>$this->prefijo_campo.'_okExportar' , 'texto'=>'Exportar' ) ;
		
		//
		// Botones extra edicion
		//$this->botones_extra_edicion[] = array( 'name'=> '_Rel1' ,
		//										'value'=>'Salir' ,
		//										'link'=>'salir.php' ) ; // '<input type="submit" name="'.$this->prefijo_campo.'_Rel1" value="Salir" autofocus>
		//
		// Filtros
		$this->con_filtro_fecha = false;
		//
		//
	}
	protected function clave_manual_activar()
	{
		$this->clave_manual = true ;
	}
	protected function existe()
	{
		return $this->existe ;
	}
	protected function maneja_evento_accion_especial()
		{  
			$tts_aux = '<br>Selecciono accion especial: ' .  $this->accion_ok ;
				$tts_aux .= '<br> Para el Id : '.$this->id ;
				$tts_aux .= '<br> Modifique el evento maneja_evento_accion_especial ' ;
				die( $tts_aux ) ;
		}
		//
		// Funciones que no hace falta redefinir
		//
	public function __construct()
  	{
			//
			// Inicializacion de Variables
			$pagina_url_anterior = 'acceuil.php' ;
			$this->existe = false ;
			$this->borrar_con_seleccion = false ;
			$this->botones_extra_edicion = array();
			$this->botones_extra_abm = array() ;
			$this->acciones = array() ;
			//$this->acciones[] = array( 'nombre'=>'okOtraAcc' , 'texto'=>'Otra Accion' ) ;
			$this->lista_detalle = array() ;
			$this->tiene_lista_detalle = false ; // se activa en rutina de lista detalle
			$this->lista_detalle_enc_columnas = array();
			$this->clave_manual = false ; // se activa en $this->clave_manual_activar() ;
			$this->pagina_titulo = NULL ;
			//
			// Datos Personalizables
			$this->Pone_Datos_Fijos_Personalizables() ; // by DZ 2016-10-24
			//
			// Personalizacion de variables
			$this->Pone_Datos_Fijos_No_Heredables() ; // by DZ 2016-10-11
			//
			// Datos que no fueron seteados y dependen de otros
			if( ! $this->pagina_titulo ) $this->pagina_titulo = $this->nombre_tabla ;
			//
			// Botones:
			$this->okSalir= $this->prefijo_campo. '_okSalir';
			$this->okReleer= $this->prefijo_campo. '_okReleer';
			$this->okGrabar= $this->prefijo_campo. '_okGrabar';
			$this->okModificar=$this->prefijo_campo. '_okModificar';
			$this->okBorrarUno=$this->prefijo_campo. '_okBorrarUno';
			$this->okGrabaBorrarUno=$this->prefijo_campo. '_okGrabaBorrarUno';
			$this->okAgregar=$this->prefijo_campo. '_okAgregar';
			$this->okVer=$this->prefijo_campo. '_okVer';
			$this->okGrabaAgregar= $this->prefijo_campo. '_okGrabaAgregar';
			$this->okExportar = $this->prefijo_campo. '_okExportar' ;
			$this->okListaSiguiente = $this->prefijo_campo. '_okListaSiguiente' ;
			$this->okListaAnterior = $this->prefijo_campo. '_okListaAnterior' ;
			$this->okListaPrimero = $this->prefijo_campo. '_okListaPrimero' ;
			$this->okListaUltimo = $this->prefijo_campo. '_okListaUltimo' ;
			$this->okListaPrimero = $this->prefijo_campo. '_okListaPrimero' ;
			$this->okListaPosicion = $this->prefijo_campo. '_okListaPosicion' ;
			
			//
			// Personalizacion de variables
			// by DZ 2015-08-15 $this->Pone_Datos_Fijos_No_Heredables() ; 									// by DZ 2015-08-14 - agregado lista de datos
			$this->con_filtro_fecha = false ;
			foreach( $this->lista_campos_lista as $campo )
			{
				if ( $campo->nombre() ) $this->con_filtro_general = true;
				break ;
			}
  	}
  	protected function leer_post_de_campos()
  	{
		$i = 0 ;
		foreach ( $this->lista_campos_lectura as $campo )
		{
			//
			// Nombre de campo
			$ts_nomCtrl = $this->prefijo_campo.'cpoNro'.$i.'_'  ;
			//
			// Valor del campo
			$campo->pone_valor($_POST[$ts_nomCtrl]) ;
			$i++;
		}
	}

	public function texto_actualizar_okGrabar()
		{
			$nomid = $this->prefijo_campo.'id';
			$this->Set_id($_POST[$nomid]);
			$this->error= false ;
			$this->Leer();
			if ( $this->existe == false )
			{ 
				$this->error = true ;
				$this->textoError = "El registro con Id: ".$this->id." no se encuentra en la base de datos " ;
			}
			if ($this->error == false )
			{
				//
				// Abre la conexión con la base de datos
				$cn=new Conexion();
				//
				// Levanta los valores de los campos
				$this->leer_post_de_campos() ;
				//
				// Arma lista de campos a actualizar
				$primerCampo = true;
				$where = 'false' ;
				$strsql = ' UPDATE '.$this->nombre_fisico_tabla.' SET ' ;
				$i = 0 ;
				foreach ( $this->lista_campos_lectura as $campo )
					{
						$tp = $campo->tipo() ;
						if ( $tp == 'pk' )
							{
							$where = ' '.$campo->nombre()." = '".$this->id."' " ;
							}
						//
						// tipos de camos validos
						elseif ( $tp != 'otro' )
							{
							//
							// Agrega coma
							if ( $primerCampo == false )
								{
									$strsql = $strsql.', ' ;
								}
							else
								{
									$primerCampo = false ;
									
								}
							/*
							//
							// Nombre de campo
							$nomCtrl = $this->prefijo_campo.'cpoNro'.$i.'_'  ;
							//
							// Valor a reemplazar en el campo
							if ( $tp == 'time' ) $valor = '1899-12-30 '.$_POST[$nomCtrl] ;
							else $valor = $_POST[$nomCtrl] ;
							//
							// Instruccion set...
							if ( empty( $valor ) )
							$strsql .= $campo->nombre(). " = NULL " ;
							else
							$strsql .= $campo->nombre(). " = '".$valor."' " ;
							* 
							*/
							$strsql .= $campo->nombre(). ' = ' . $campo->valor_sql() ;
							}
						$i++;
					}
				$strsql = $strsql.' WHERE '.$where.' ' ;
				//
				// Cierra la conexion
				$cn->conexion->query("SET NAMES 'utf8'");
				$actualizado = $cn->conexion->query($strsql) ;
				if( ! $actualizado ) die( "Problemas en el update de ".$this->nombre_tabla." : ".$cn->conexion->error.$strsql ) ;
				$cn->cerrar();
			}
		}	
	public function texto_actualizar()
		{
			$this->error= false ;
			$cpo = new Campo();
			$this->Leer();
			if ( $this->existe == false )
			{ 
				$this->error = true ;
				$this->textoError = "El registro con Id: ".$this->id." no se encuentra en la base de datos " ;
			}
			//
			// Otra validacion
			//
			//if ($this->Error == false )
			//	{ 
			//		//.....validacion
			//		if ( condicion de error )
			//			{
			//			$this->Error = true;

			//			$this->TextoError = ' Texto del error ' ;
			//			}
			//		
			//	}
			if ( $this->error == true )
				{
					$txt = '<td>'.$this->textoError.'</td>';
				}
			else
				{
					//
					// Abre tabla
					$txt = '<table class="tablacampos">';
					if ( $reg=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
						{
							$txt=$txt.'<tr>';
							$txt=$txt.'<td></td><td><input type="hidden" name="'.$this->prefijo_campo.'id" value="'.$this->id.'"></td>';
							$txt=$txt.'</tr>';
							for($i=0;$i<count($reg);$i++)
								{
									$txt=$txt.'<tr>';
									$txt=$txt.'<td>';
								  //$txt=$txt.$this->lista_campos_lectura[$i]['descripcion'];
								  $txt.= $this->lista_campos_lectura[$i]->descripcion() ;
								  $txt=$txt.'</td>';
									$cpo->pone_nombre( $this->prefijo_campo.'cpoNro'.$i.'_' ) ;
									$cpo->pone_valor( $reg[$i] ) ;
									if( $this->lista_campos_lectura[$i]->tipo() == 'pk' or $this->lista_campos_lectura[$i]->tipo() == 'otro' )
										{ 
											$cpo->pone_tipo( 'text' ) ;
											$txt .= $cpo->txtMostrarOcultoyEtiqueta() ;
											//$txt .= $cpo->txtMostrarEtiqueta() ;
										}
									elseif( $this->lista_campos_lectura[$i]->tipo() == 'fk' )
										{
											//
											// Lista de fk
											//
											$cpo->pone_tipo( 'select' ) ;
											$to_objeto_campo = $this->lista_campos_lectura[$i]->objeto() ;
											$lista_fk = $to_objeto_campo->Obtener_Lista() ;
											// by DZ 2016-10-25 $lista_fk = $this->lista_campos_lectura[$i]['clase']->Obtener_Lista() ;
											$cpo->pone_lista( $lista_fk ) ;
											$cpo->pone_posicion_codigo( 0 ) ;
											$cpo->pone_posicion_descrip( 1 ) ;
											$cpo->pone_mostar_nulo_en_si() ;
											$txt = $txt.$cpo->txtMostrarParaModificar() ;
										}
									else
										{ 
											$cpo->pone_tipo( $this->lista_campos_lectura[$i]->tipo() ) ;
											$txt = $txt.$cpo->txtMostrarParaModificar() ;
											//$txt=$txt.'<input type="'.$this->lista_campos_tipo[$i].'" name="'.$nom_campo.'" value="'.$reg[$i].'">';
											//$txt=$txt.'</td>';
										}
									$txt=$txt.'</tr>';
								}
						}
					else
						{
							$txt=$txt.'<td> mysqli_fetch_array No encontro registro </td>';
						}
					//
					// Cierra tabla
					$txt = $txt.'</table>';
				}
				return $txt ;
		}
		public function texto_eliminar()
		{
			$this->error= false ;
			$cpo = new Campo();
			$this->Leer();
			if ( $this->existe == false )
			{ 
				$this->error = true ;
				$this->textoError = "El registro con Id: ".$this->id." no se encuentra en la base de datos " ;
			}
			//
			// Otra validacion
			//
			//if ($this->Error == false )
			//	{ 
			//		//.....validacion
			//		if ( condicion de error )
			//			{
			//			$this->Error = true;

			//			$this->TextoError = ' Texto del error ' ;
			//			}
			//		
			//	}
			if ( $this->error == true )
				{
					$txt = '<td>'.$this->textoError.'</td>';
				}
			else
				{
					//
					// Abre tabla
					$txt = '<table class="tablacampos">';
					if ( $reg=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
						{
							$txt=$txt.'<tr>';
							$txt=$txt.'<td></td><td><input type="hidden" name="'.$this->prefijo_campo.'id" value="'.$this->id.'"></td>';
							$txt=$txt.'</tr>';
							for($i=0;$i<count($reg);$i++)
								{
									$txt=$txt.'<tr>';
									$txt=$txt.'<td>';
								  $txt=$txt.$this->lista_campos_lectura[$i]->descripcion();
								  $txt=$txt.'</td>';
									$cpo->pone_nombre( $this->prefijo_campo.'cpoNro'.$i.'_' ) ;
									$cpo->pone_valor( $reg[$i] ) ;
									if( $this->lista_campos_lectura[$i]->tipo() == 'pk' or $this->lista_campos_lectura[$i]->tipo() == 'otro' )
										{ 
											$cpo->pone_tipo( 'text' ) ;
											$txt = $txt.$cpo->txtMostrarEtiqueta() ;
										}
									elseif( $this->lista_campos_lectura[$i]->tipo() == 'fk' )
										{
											//
											// Lista de fk
											//
											$cpo->pone_tipo( 'select' ) ;
											$lista_fk = $this->lista_campos_lectura[$i]->objeto()->Obtener_Lista() ;
											$cpo->pone_lista( $lista_fk ) ;
											$cpo->pone_posicion_codigo( 0 ) ;
											$cpo->pone_posicion_descrip( 1 ) ;
											$cpo->pone_mostar_nulo_en_si() ;
											$txt = $txt.$cpo->txtMostrarParaVer() ;
										}
									else
										{ 
											$cpo->pone_tipo( $this->lista_campos_lectura[$i]->tipo() ) ;
											$txt = $txt.$cpo->txtMostrarParaVer() ;
										}
									$txt=$txt.'</tr>';
								}
						}
					else
						{
							$txt=$txt.'<td> mysqli_fetch_array No encontro registro </td>';
						}
					//
					// Cierra tabla
					$txt = $txt.'</table>';
				}
				return $txt ;
		}

	public function texto_ver()
		{
			$this->error= false ;
			$cpo = new Campo();
			$this->Leer();
			if ( $this->existe == false )
			{ 
				$this->error = true ;
				$this->textoError = "El registro con Id: ".$this->id." no se encuentra en la base de datos " ;
			}
			//
			// Otra validacion
			//
			//if ($this->Error == false )
			//	{ 
			//		//.....validacion
			//		if ( condicion de error )
			//			{
			//			$this->Error = true;

			//			$this->TextoError = ' Texto del error ' ;
			//			}
			//		
			//	}
			if ( $this->error == true )
				{
					$txt = '<td>'.$this->textoError.'</td>';
				}
			else
				{
					//
					// Abre tabla
					$txt = '<table class="tablacampos">';
					if ( $reg=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
						{
							$txt=$txt.'<tr>';
							$txt=$txt.'<td></td><td><input type="hidden" name="'.$this->prefijo_campo.'id" value="'.$this->id.'"></td>';
							$txt=$txt.'</tr>';
							for($i=0;$i<count($reg);$i++)
								{
									$txt=$txt.'<tr>';
									$txt=$txt.'<td>';
								  $txt=$txt.$this->lista_campos_lectura[$i]->descripcion();
								  $txt=$txt.'</td>';
									$cpo->pone_nombre( $this->prefijo_campo.'cpoNro'.$i.'_' ) ;
									$cpo->pone_valor( $reg[$i] ) ;
									if( $this->lista_campos_lectura[$i]->tipo() == 'pk' or $this->lista_campos_lectura[$i]->tipo() == 'otro' )
										{ 
											$cpo->pone_tipo( 'text' ) ;
											$txt = $txt.$cpo->txtMostrarEtiqueta() ;
										}
									elseif( $this->lista_campos_lectura[$i]->tipo() == 'fk' )
										{
											//
											// Lista de fk
											//
											$cpo->pone_tipo( 'select' ) ;
											$lista_fk = $this->lista_campos_lectura[$i]->objeto()->Obtener_Lista() ;
											$cpo->pone_lista( $lista_fk ) ;
											$cpo->pone_posicion_codigo( 0 ) ;
											$cpo->pone_posicion_descrip( 1 ) ;
											$cpo->pone_mostar_nulo_en_si() ;
											$txt = $txt.$cpo->txtMostrarParaVer() ;
										}
									else
										{ 
											$cpo->pone_tipo( $this->lista_campos_lectura[$i]->tipo() ) ;
											$txt = $txt.$cpo->txtMostrarParaVer() ;
										}
									$txt=$txt.'</tr>';
								}
						}
					else
						{
							$txt=$txt.'<td> mysqli_fetch_array No encontro registro </td>';
						}
					//
					// Cierra tabla
					$txt = $txt.'</table>';
				}
				return $txt ;
		}

	public function texto_agregar()
		{
			$this->error= false ;
			$cpo = new Campo();
			//
			// Abre tabla
			$txt = '<table>';
			for($i=0;$i<count($this->lista_campos_lectura);$i++)
				{
					$txt=$txt.'<tr>';
					$txt=$txt.'<td>';
				  $txt=$txt.$this->lista_campos_lectura[$i]->descripcion();
				  $txt=$txt.'</td>';
					$cpo->pone_nombre( $this->prefijo_campo.'cpoNro'.$i.'_' ) ;
					$cpo->pone_valor( '' ) ;
					if( $this->lista_campos_lectura[$i]->tipo() == 'pk' and $this->clave_manual )
					{
						$cpo->pone_tipo( 'number' ) ;
						$txt .= $cpo->txtMostrarOculto() ;
						$txt = $txt.$cpo->txtMostrarParaModificar() ;
					}
					elseif( $this->lista_campos_lectura[$i]->tipo() == 'pk' )
					{
						$cpo->pone_valor( 'nuevo' );
						$cpo->pone_tipo( 'text' ) ;
						$txt .= $cpo->txtMostrarOculto() ;
						$txt = $txt.$cpo->txtMostrarEtiqueta() ;
					}
					elseif( $this->lista_campos_lectura[$i]->tipo() == 'otro' or $this->lista_campos_lectura[$i]->readonly() )
						{ 
							$cpo->pone_tipo( 'text' ) ;
							$txt = $txt.$cpo->txtMostrarEtiqueta() ;
						}
					elseif( $this->lista_campos_lectura[$i]->tipo() == 'fk' )
						{
							//
							// Lista de fk
							//
							$cpo->pone_tipo( 'select' ) ;
							$lista_fk = $this->lista_campos_lectura[$i]->objeto()->Obtener_Lista() ;
							$cpo->pone_lista( $lista_fk ) ;
							$cpo->pone_posicion_codigo( 0 ) ;
							$cpo->pone_posicion_descrip( 1 ) ;
							$cpo->pone_mostar_nulo_en_si() ;
							$txt = $txt.$cpo->txtMostrarParaModificar() ;
						}
					else
						{ 
							$cpo->pone_tipo( $this->lista_campos_lectura[$i]->tipo() ) ;
							$txt = $txt.$cpo->txtMostrarParaModificar() ;
							//$txt=$txt.'<input type="'.$this->lista_campos_tipo[$i].'" name="'.$nom_campo.'" value="'.$reg[$i].'">';
							//$txt=$txt.'</td>';
						}
					$txt=$txt.'</tr>';
				}
			//
			// Cierra tabla
			$txt = $txt.'</table>';
				return $txt ;
		}

		public function texto_agregar_okGrabar() 
		{
			//$nomid = $this->prefijo_campo.'id';
			//$this->Set_id($_POST[$nomid]);
			$this->error= false ;
			//$this->Leer();
			//
			// Abre la conexión con la base de datos
			$cn=new Conexion();
			//
			// Arma lista de campos a agregar
			$lst_cmp = '';
			$lst_val = '';
			$primerCampo = true;
			$i = 0 ;
			$tn_valor_id = 0 ;
			foreach ( $this->lista_campos_lectura as $campo )
				{
					//
					// tipo de campo
					$tp = $campo->tipo() ;
					//
					// tipos de camos validos
					if ( $tp != 'otro' and ( $tp != 'pk' or $this->clave_manual ) )
						{
						//
						// Agrega coma
						if ( $primerCampo == false )
							{
								$lst_cmp = $lst_cmp.', ' ;
								$lst_val = $lst_val.', ' ;
							}
						else
							{
								$primerCampo = false ;
								
							}
						//
						// Nombre de campo
						$nomCtrl = $this->prefijo_campo.'cpoNro'.$i.'_'  ;
						//
						// Valor a reemplazar en el campo
						if ( $tp == 'time' ) $valor = '1899-12-30 '.$_POST[$nomCtrl] ;
						else $valor = $_POST[$nomCtrl] ;
						if ( $tp == 'pk' ) $this->id = $valor ;
						//
						// Lista campos
						$lst_cmp = $lst_cmp. $campo->nombre() ;
						//
						// Lista valores
						$lst_val = $lst_val."'".$valor."'" ;
						}
					$i++;
				}
			$strsql = ' INSERT INTO '.$this->nombre_fisico_tabla.' ( '.$lst_cmp.' )  VALUES ( '.$lst_val. ' ) ';
			//
			// Cierra la conexion
			$insertado = $cn->conexion->query($strsql) ;
			if ( $insertado ) 
				{ 
					if ( ! $this->clave_manual )
					{
						$result = $cn->conexion->query('SELECT last_insert_id()');
						$reg = $result->fetch_array(MYSQLI_NUM);
						$this->id = $reg[0];
						$result->free();
					}
				}
			else
				{
					// die( "Problemas en el insert de ".$this->nombre_tabla." : ".$cn->conexion->error.$strsql ) ;
					$this->error = true ;
					$this->textoError = "Problemas en el insert de ".$this->nombre_tabla." : ".$cn->conexion->error.' '.$strsql ;
				}
			$cn->cerrar();
		}	


		public function pagina_pone_url_anterior($url)
		{
			$this->pagina_url_anterior = $url ;
		}
		public function pagina_pone_titulo($tit)
		{
			$this->pagina_titulo = $tit ;
		}
		public function pagina_edicion_leer_eventos()
		{
		//
		// Varibales de accion
		//
		// Solicita grabar
		$this->okGrabar = $this->prefijo_campo.'_okGrabar' ;
		//
		// Solicita releer
		$this->okReleer = $this->prefijo_campo.'_okReleer' ;
		$this->okSalir = $this->prefijo_campo.'_okSalir' ;
		//
		// Verifica que exista 
		$this->Leer();
		if ( ! $this->existe ) die( 'No se encuentra registro con el id '.$this->id );
		//
		// Eventos
		//
		if ( isset($_POST[$this->okSalir] ) )
			{
			$this->ok_Salir() ;
			}
		elseif ( isset( $_POST[$this->okGrabar] ) )
			{
				$this->texto_actualizar_okGrabar() ;
				if( $this->error ) die ( $this->relacion->textoError() ) ;
				else $this->mostrar_edicion() ;
			}
		elseif ( isset( $_POST[$this->okReleer] ) )
			{
				$this->mostrar_edicion() ;
			}
		else
			{
				//
				// Eventos extra
				$linkextra = '' ;
				foreach ( $this->botones_extra_edicion as $boton )
				{
					if ( isset($_POST[$this->prefijo_campo.$boton['name']]))
						$linkextra = $boton['link'] ;
				}
				if ( empty( $linkextra ) )					
					$this->mostrar_edicion() ;
				else
					header('Location: '.$linkextra);
			}
		}
		protected function ok_salir()
		{
			if( empty( $this->pagina_url_anterior ) )
						$ts_location = 'accueil.php';
			else
				$ts_location = $this->pagina_url_anterior ;
			header('Location: '.$ts_location);

		}
		protected function mostrar_alta()
		{
			$botones = '<input type="submit" name="ok" value="Salir" autofocus>';
			$botones .= '<input type="submit" name="'.$this->okReleer.'" value="Revertir" >';
			$botones .= '<input type="submit" name="'.$this->okGrabaAgregar.'" value="Agregar" >';
			$pagina=new paginaj($this->pagina_titulo,$botones);
			$pagina->pone_valor_oculto( $this->okListaPosicion , $this->desde );
			$txt = 	$this->texto_agregar();
			$pagina->insertarCuerpo($txt);
			$pagina->sinborde();
			$pagina->graficar_c_form($_SERVER['PHP_SELF']);
				
		}
		protected function mostrar_edicion()
		{
			//
			// Botones extra
			$btn_extra = '' ;
			foreach ( $this->botones_extra_edicion as $boton )
			{
				$btn_extra.= '<input type = "submit" name="'.$this->prefijo_campo.$boton['name'].'" value="'.$boton['value'].'">'  ;
			}
			//
			// Muestra pantalla para editar datos
			$hidden = '<input type="hidden" name="'.$this->prefijo_campo.'_id'.'" value="'.$this->id.'" > ' ;
			$botones = '<input type="submit" name="ok" value="Volver" autofocus>';
			$botones .= '<input type="submit" name="'.$this->okReleer.'" value="Revertir" >';
			$botones .= '<input type="submit" name="'.$this->okGrabar.'" value="Grabar" >';
			$botones .= $btn_extra ;
			$pagina = new paginaj($this->pagina_titulo,$hidden.$botones) ;
			$pagina->sinborde();
			//
			// Muestra la cabecera
			$texto = $this->texto_actualizar();
			$pagina->insertarCuerpo($texto);
			//
			// Grafica la página
			$pagina->graficar_c_form($_SERVER['PHP_SELF']);
		}
		protected function mostrar_eliminacion()
		{
			//
			// Muestra pantalla para editar datos
			$hidden = '<input type="hidden" name="'.$this->prefijo_campo.'_id'.'" value="'.$this->id.'" > ' ;
			$botones = '<input type="submit" name="ok" value="Volver" autofocus>';
			$botones .= '<input type="submit" name="'.$this->okGrabaBorrarUno.'" value="Borrar" >';
			$pagina = new paginaj($this->pagina_titulo,$hidden.$botones) ;
			$pagina->sinborde();
			//
			// Subtitulo
			$texto = ' Va a borrar los siguientes datos : ';
			$pagina->insertarCuerpo($texto);
			//
			// Muestra la cabecera
			$texto = $this->texto_eliminar();
			$pagina->insertarCuerpo($texto);
			//
			// Grafica la página
			$pagina->graficar_c_form($_SERVER['PHP_SELF']);
		}
		protected function mostrar_vista()
		{
			//
			// Botones extra
			//
			// Muestra pantalla para editar datos
			$hidden = '<input type="hidden" name="'.$this->prefijo_campo.'_id'.'" value="'.$this->id.'" > ' ;
			$botones = '<input type="submit" name="ok" value="Volver" autofocus>';
			$pagina = new paginaj($this->pagina_titulo,$hidden.$botones) ;
			$pagina->sinborde();
			//
			// Muestra la cabecera
			$texto = $this->texto_ver();
			$pagina->insertarCuerpo($texto);
			//
			// Grafica la página
			$pagina->graficar_c_form($_SERVER['PHP_SELF']);
		}
		//
		// Armado de Pagina
		//
		public function mostrar_pagina_lista()
		{  
			//
			// Acciones Especiales
			$this->accion_especial_seleccionada = false ;
			$this->accion_ok = '' ;
			foreach( $this->acciones as $accion )
			{
				$okAccionEspecial = $this->prefijo_campo.$accion['nombre'] ;
				if ( isset( $_REQUEST[$okAccionEspecial] ) )
				{
					$this->accion_especial_seleccionada = true ;
					$this->accion_ok = $accion['nombre'] ;
					$this->id = 0 ;
					$ts_nom_request_id = $this->prefijo_campo.'_Id' ; 
					if ( isset( $_REQUEST[$ts_nom_request_id] ) ) 
						$this->id =  $_REQUEST[$ts_nom_request_id] ;
						
				}
			}
			//
			// Post Generales
			if (isset($_REQUEST[$this->okListaPosicion]))
			  $this->desde = $_REQUEST[$this->okListaPosicion];
			else
			  $this->desde=0;
			
			//
			// Acciones Clasicas 
			
			/* by dz 2016-10-25 $okVer = $this->obtiene_prefijo_campo().'okVer' ;
			$okModificar = $this->obtiene_prefijo_campo().'okModificar' ;
			$okagregar = $this->obtiene_prefijo_campo().'_okAgregar' ;
			$okborrar  = $this->obtiene_prefijo_campo().'_okBorrar' ;
			*/
			//
			//
			if ( $this->accion_especial_seleccionada )
			{
				$this->maneja_evento_accion_especial();
			}
			// Agregar
			elseif ( isset($_POST[$this->okAgregar]) )
			{
				$this->mostrar_alta();
			}
			// Modificar
			elseif ( isset($_GET[$this->okModificar]) )
			{
				//
				// Edita
				$nomid = $this->obtiene_prefijo_campo().'_Id' ;
				$this->Set_id($_REQUEST[$nomid]) ;
				$this->mostrar_edicion();
				//muestra_modificar($Entidad) ;
			}
			elseif ( isset($_POST[$this->okBorrarUno]) )
			{
				//
				// Edita
				$this->Set_id($_REQUEST[$this->okBorrarUno]) ;
				$this->mostrar_eliminacion();
				//muestra_modificar($Entidad) ;
			}
			elseif ( isset($_GET[$this->okVer]) )
			{
				//
				// Edita
				$nomid = $this->obtiene_prefijo_campo().'_Id' ;
				$this->Set_id($_REQUEST[$nomid]) ;
				$this->mostrar_vista();
			}
			elseif ( isset( $_POST[$this->okSalir] ) )
			{
				$this->ok_Salir() ;
			}
			elseif ( isset( $_POST[$this->okReleer] ) )
				die('-------- !') ;
			elseif ( isset( $_POST[$this->okGrabar] ) )
			{
				// Graba Modificaciones
				$this->texto_actualizar_okGrabar();
				if ( $this->hay_error() == true ) $this->muestra_error() ;
				else $this->muestra_ok('Registro # '.$this->id().' actualizado') ;
			}
			elseif ( isset( $_POST[$this->okGrabaAgregar] ) )
			{
				$this->texto_agregar_okGrabar();
				if ( $this->hay_error() == true ) $this->muestra_error() ;
				else $this->muestra_ok('Registro # '.$this->id().' agregado') ;
			}
			elseif ( isset($_POST[$this->okGrabaBorrarUno]) )
			{
				//
				// Confirma Borrar
				$this->texto_eliminar_okGrabar();
				if ( $this->hay_error() == true ) $this->muestra_error() ;
				else $this->muestra_ok('Registro # '.$this->id().' eliminado') ;
			}
			elseif ( isset($_POST[$this->okListaSiguiente]) )
			{
				//
				// Mostrar Lista Siguiente
				$this->desde += $this->cuenta ;
				$this->mostrar_lista_abm() ;
			}
			elseif ( isset($_POST[$this->okListaAnterior]) )
			{
				//
				// Mostrar Lista Anterior
				$this->desde -= $this->cuenta ;
				$this->mostrar_lista_abm() ;
			}
			elseif ( isset($_POST[$this->okListaPrimero]) )
			{
				//
				// Mostrar Lista Primero
				$this->desde = 0 ;
				$this->mostrar_lista_abm() ;
			}
			elseif ( isset($_POST[$this->okListaUltimo]) )
			{
				//
				// Mostrar Lista Ultimo
				die('Mostrar Lista Ultimo');
				$this->mostrar_lista_abm() ;
			}
			else
			{
				$this->mostrar_lista_abm() ;
			}
		}
		public function mostrar_pagina_alta()
		{	
			//
			//
			if ( isset( $_POST[$this->okSalir] ) )
			{
				$this->ok_Salir() ;
			}
			elseif ( isset( $_POST[$this->okGrabaAgregar] ) )
			{
				// Graba Modificaciones
				$this->texto_agregar_okGrabar();
				if ( $this->hay_error() == true ) $this->muestra_error() ;
				else $this->muestra_ok('Registro # '.$this->id().' agregado') ;
			}
			elseif ( isset($_REQUEST['okSalir'] ) )
			{
				$this->ok_Salir() ;
			}
			else
			{
				//
				// Arma la página para agregar		
				$pagina=new Paginaj($this->nombre_tabla ,'<input type="submit" value="Grabar" name="'.$this->okGrabaAgregar.'"><input type="submit" value="Salir" name="'.$this->okSalir.'">');
				//$txt = $this->texto_Ver_Lado_Uno();
				//$pagina->insertarCuerpo($txt);
				
				$txt = $this->texto_mostrar_abm() ;
				$pagina->insertarCuerpo($txt);
				//
				$txt = 	$this->texto_agregar();
				$pagina->insertarCuerpo($txt);
				$pagina->graficar_c_form($_SERVER['PHP_SELF']);
				

			}
		}

		public function mostrar_lista_abm()
		{
			$hidden = '' ;
			$pagina = new paginaj($this->nombre_tabla ,$hidden.'<input type="submit" name="'.$this->okSalir.'" value="Salir" autofocus>') ;
			$pagina->pone_valor_oculto( $this->okListaPosicion , $this->desde ) ;
			//
			// Muestra la cabecera
			$texto = $this->texto_mostrar_abm() ;
			$pagina->insertarCuerpo($texto);
			//
			// Grafica la página
			$pagina->graficar_c_form($_SERVER['PHP_SELF']);
		}
		protected function muestra_error() 
		{
			$hidden = '' ;
			$botones = '<input type="submit" name="ok" value="Ok" autofocus>';
			$pagina = new paginaj($this->pagina_titulo,$hidden.$botones) ;
			$pagina->sinborde();
			//
			// Muestra el error
			$txt = 	$this->textoError ;
			$pagina->insertarCuerpo($txt);
			//
			// Grafica la página
			$pagina->graficar_c_form($_SERVER['PHP_SELF']);
		}
		protected function muestra_ok($ps_mje) 
		{
			$hidden = '' ;
			$botones = '<input type="submit" name="ok" value="Ok" autofocus>';
			$pagina = new paginaj($this->pagina_titulo,$hidden.$botones) ;
			$pagina->sinborde();
			//
			// Muestra el error
			$txt = 	$ps_mje ;
			$pagina->insertarCuerpo($txt);
			//
			// Grafica la página
			$pagina->graficar_c_form($_SERVER['PHP_SELF']);
		
		}		
		public function exportar_a_archivo()
        {
			$stem = $this->prefijo_campo. date("Y-m-d-h_i_sa");
			$stem = 'c';
            $archivo = tempnam( 'archivos', $stem ) ;
            $file = fopen($archivo,"w");
            //
            // Encabezado
            $ts_enc = "";
            foreach ( $this->lista_campos_lista as $campo )
            {
                $ts_enc .= '"'.$campo['nombre'] . '"; ' ;
            }
            fwrite($file, $ts_enc . PHP_EOL );
            //
            // Recorre los registros
            $this->leer_filtros();
			$this->leer_lista();
            mysqli_data_seek ( $this->registros , 0 ) ;   
            while( $this->registro=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
            {
                //*****-----//
                for($i=0;$i<count($this->registro);$i++)
                {
					$ts_valor = $this->registro[$i] ;
					$ts_valor = mb_convert_encoding($ts_valor, "Windows-1252","UTF-8");
					fwrite($file, '"'.$ts_valor.'";' ) ;
                }
                fwrite($file, PHP_EOL );
            }
       
            fwrite($file, "Generado el ".date("Y-m-d h:i:sa") . PHP_EOL);
            fclose($file);
            return $archivo ;
        }    
        public function bajar_archivo($ps_file_name)
        {
			// make sure it's a file before doing anything!
			if(is_file($ps_file_name)) {

				/*
					Do any processing you'd like here:
					1.  Increment a counter
					2.  Do something with the DB
					3.  Check user permissions
					4.  Anything you want!
				*/

				// required for IE
				if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}

				// get the file mime type using the file extension
				switch(strtolower(substr(strrchr($ps_file_name, '.'), 1))) {
					case 'pdf': $mime = 'application/pdf'; break;
					case 'zip': $mime = 'application/zip'; break;
					case 'jpeg':
					case 'jpg': $mime = 'image/jpg'; break;
					default: $mime = 'application/force-download';
				}
				header('Pragma: public'); 	// required
				header('Expires: 0');		// no cache
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($ps_file_name)).' GMT');
				header('Cache-Control: private',false);
				header('Content-Type: '.$mime);
				header('Content-Disposition: attachment; filename="'.basename($ps_file_name).'.csv"');
				header('Content-Transfer-Encoding: binary');
				//header('Content-type: text/plain; charset=utf-8');


				header('Content-Length: '.filesize($ps_file_name));	// provide file size
				header('Connection: close');
				readfile($ps_file_name);		// push it out
				exit();

			}
		}    
//////////////////////////////////////////----- metodos de la clase entidad ------ ///////////////////////////////////////
	public function id()
		{
			return $this->id;
		}
	public function textoError()
		{
			return $this->textoError ;
		}
	public function hay_error()																			// by DZ 2016-01-22 - agrega seleeccion en clase relacion
		{
			return $this->error;
		}
	protected function Pone_nombre ()
		{
			$this->nombre_tabla = "Nombre de la_tabla" ;
			$this->nombre_fisico_tabla = "nombre_de_la_tabla" ;
		}
	protected function Pone_Lista_Campos()
		{
		//
		// Descomentar si se redefine la funcion
		//
		//$this->lista_campos_descrip=array() ;
		//$this->lista_campos_tipo=array() ;
		//$this->lista_campos_nombre=array() ;
		//$this->lista_campos_descrip[]='Identificador' ;
		//$this->lista_campos_descrip[]='Descripcion de Entidad' ;
		//$this->lista_campos_tipo[]='pk' ; // 'pk' 'fk' 'other' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password' 
		//$this->lista_campos_tipo[]='text';
		//$this->lista_campos_nombre[]='Id' ;
		//$this->lista_campos_nombre[]='Descrip' ;
		
		}	
	protected function Pone_nombre_pag ()														// by DZ 2015-08-18 - agregado lista de datos
		{
			$this->nombre_pagina = $_SERVER['PHP_SELF'] ;
		}
	protected function Pone_Clase_Detalle()													// by DZ 2016-01-21 - agregado clase detalle
		{
			// $this->detalle = new relacion() ;
		}	
	public function obtiene_prefijo_campo ()
		{
		return $this->prefijo_campo ;
		}
	protected function Pone_prefijo_campo ()												// by DZ 2016-01-18 - agregado lista de datos
		{
			$this->prefijo_campo = 'm_ent_' ;
		}	
	public function Set_id($nro_id)
	{
	$this->id = $nro_id;
	}
	public function pone_desde ($indice)
		{
			$this->desde = $indice ;
		}
	public function pone_cuenta ($cuenta)
		{
			$this->cuenta = $cuenta ;
		}
	protected function Carga_Sql_Lectura()
	{
	$this->strsql = ' SELECT ' ;
	$tf_primero = true ;
	foreach ( $this->lista_campos_lectura as $campo )
	{
		if ( $tf_primero )
		{
			$tf_primero = false;
			$ts_pk = $campo->nombre() ;
		}
		else
			$this->strsql .= ' , ';
		$this->strsql .= $campo->nombre() ;
	}
	$this->strsql .= ' FROM '.$this->nombre_fisico_tabla. ' ' ;
	$this->strsql .= ' WHERE '.$ts_pk." = '" .$this->id."' " ;
	/*
	$this-> strsql = " SELECT campo1, campo2
			FROM Tabla 
			INNER JOIN Otra_Tabla 
			ON Tabla.FK = Otra_Tabla.PK
			WHERE Tabla.PK = " .$this->id ;
			$this->lista_campos_descrip=array() ;
			$this->lista_campos_tipo=array() ;
			$this->lista_campos_descrip[]='Campo Uno' ;
			$this->lista_campos_descrip[]='Campo Dos' ;
			$this->lista_campos_tipo[]='text' ; // 'pk' 'fk' 'otro' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password' 
			$this->lista_campos_tipo[]='text';
	*/
	}
	protected function Carga_Sql_Lista()
	{
		$this->strsql = ' SELECT ' ;
		$tf_primero = true ;
		$tf_filtra_por_codigo = false ;
		foreach ( $this->lista_campos_lista as $campo )
		{
			if ( $tf_primero )
			{
				$tf_primero = false;
				$ts_pk = $campo->nombre() ; // by dz 2016-10-24  $campo['nombre'] ;
			}
			else
				$this->strsql .= ' , ';
			$this->strsql .= $campo->nombre(); // by dz 2016-10-24  $campo['nombre'] ;
			//
			// PK
			if ( $campo->tipo() == 'pk' )
			{
				if ( $campo->busqueda() == true and ! empty( $this->filtro_id) ) 
					$tf_filtra_por_codigo = true ;
			}
		}
		
		$this->strsql .= ' FROM '.$this->nombre_fisico_tabla. ' ' ;
		// by dz 2016-10-24
		// -->
		if ( $tf_filtra_por_codigo ) 
		{
			$ts_where = ' WHERE ' ;
			$ts_where .= $ts_pk . ' = '." '".$this->filtro_id."' " ;
			$this->strsql .= $ts_where ;
		}
		//
		// Filtro por campos de búsqueda
		if ( ! empty( $this->filtro_gral ) and ! $tf_filtra_por_codigo)
		{
			$tn_campos_busqueda = 0 ;
			$ts_where = ' WHERE ' ;
			foreach( $this->lista_campos_lista as $campo )
			{
				if($campo->busqueda() )
				{
					 $tn_campos_busqueda ++ ;
					 if ( $tn_campos_busqueda > 1 )
						$ts_where .= ' or ' ;
					 $ts_where .= $campo->nombre() ;
					 $ts_where .= ' LIKE ' ;
					 $ts_where .= " '%" .$this->filtro_gral. "%' " ;
					 
				}
			}
			if( $tn_campos_busqueda > 0 )
			{
				$this->strsql .= $ts_where ;
			}
		}
		// <--
		// by dz 2016-10-24
		if ( empty( $this->desde ) or $this->desde < 0 )
			$this->desde = 0 ;
		if ( $this->cuenta )
			$this->strsql .= ' LIMIT '. $this->desde . ' , ' . $this->cuenta ;
	}
	public function Leer()
	{ $this->Carga_Sql_Lectura();		
		$cn=new Conexion();
		$this->registros=mysqli_query($cn->conexion,$this->strsql) or
				die("Problemas en el select de lectura de".$this->nombre_tabla.": ".mysqli_error($cn->conexion). " id = ".$this->id. " <br><br> Sql= ".$this->strsql );
		$cn->cerrar();
		if ( $this->registro=mysqli_fetch_array($this->registros) )
			{
				$this->existe = true ;
				mysqli_data_seek ( $this->registros , 0 ) ;	
			}
		else
			{
				$this->existe = false ;
			}
		$this->Leer_Detalle();
	}
	public function leer_sql_precargado(){
		//
		// Lee segun sql cargado previamente
		//
		// Abre la conexión
		$cn=new Conexion();
		//
		// Lee con la instrucción sql cargada
		$this->registros=mysqli_query($cn->conexion,$this->strsql) or
				die("Problemas en el select de la lista de ".$this->nombre_tabla.": ".mysqli_error($cn->conexion). " id = ".$this->id. " <br><br> Sql= ".$this->strsql );
		//
		// Cierra la conexión
		$cn->cerrar();
		//
		// Carga la bandera 'Existe'
		if ( $this->registro=mysqli_fetch_array($this->registros) )
			{
				$this->existe = true ;
				mysqli_data_seek ( $this->registros , 0 ) ;					
			}
		else
			{
				$this->existe = false ;
			}
	}
	public function vaciar_lista()
	{
		mysqli_free_result($this->registros) ;
		$this->existe = false ;
	}
	public function leer_lista()
	{ $this->Carga_Sql_Lista();		
		$cn=new Conexion();
		$this->registros=mysqli_query($cn->conexion,$this->strsql) or
				die("Problemas en el select de la lista de ".$this->nombre_tabla.": ".mysqli_error($cn->conexion). " id = ".$this->id. " <br><br> Sql= ".$this->strsql );
		$this->Leer_Detalle_Lista($cn->conexion); // by ZD 2016_06_22 // 
		$cn->cerrar();
		//
		// Determina si hay registros
		mysqli_data_seek ( $this->registros , 0 ) ;
		if ( $this->registro=mysqli_fetch_array($this->registros) )
			{
				$this->existe = true ;
				mysqli_data_seek ( $this->registros , 0 ) ;
			}
		else
			{
				$this->existe = false ;
			}
		// by ZD 2016_06_22 // $this->Leer_Detalle();
	}
	protected function Leer_Detalle_Lista($conexion)
	{
		//
		// Activa el flag de detalle
		//$this->tiene_lista_detalle = true ;
		// $this->lista_detalle_enc_columnas = array( 'titulo uno' , 'titulo dos' ) ;
		//
		// Lee detalle para los elementos de la lista
		// mysqli_data_seek ( $this->registros , 0 ) ;
		//while( $this->registro=mysqli_fetch_array($this->registros) )
		//	{
		//		//
		//		// Leer detalle para cada elemento de la lista
		//$regsdet=mysqli_query($conexion,$this->strsql) or
		//		die("Problemas en el select del detalle de ".$this->nombre_tabla.": ".mysqli_error($conexion). " <br><br> Sql= ".$this->strsql );
		//	}
	}
	protected function Leer_Detalle()
	{
		// Lee Otros registros relacionados
		//if ( ! is_null( $this->detalle ) )
		//	{
		//		$this->detalle->set_id_lado_uno($this->id) ;
		//		$this->detalle->leer_lista() ;
		//	}
	}
	public function mostrar_registros($puede_borrar,$puede_agregar) {
		//
		// Recorre los registros leidos
		while ($this->existe == true and $reg=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
				{
        	echo '<tr>';
				//
				// Por cada campo
				for($f=0;$f<count($reg);$f++)
					{
           	echo '<td>';
            echo $reg[$f];
            echo '</td>';
					}
					if ( $puede_borrar == true )
						{
						echo '<td>';
      			echo '<a href="'.$this->nombre_pagina.'_baja.php?m_docente_nro='.$reg[0].'&crn='.$reg[1].'">Borrar</a>';
      			echo '</td>';
      			echo '</tr>';
						}
				}
			if ( $this->existe == true and $puede_agregar == true )
				{ 
					$cntcols = count($reg) + 2 ;
					echo '<tr><td colspan="'.$cntcols.'">';
    			echo '<a href="crono.php?m_id='.$this->id.'">Agrega nueva '.$this->nombre_tabla.'</a>';
    			echo '</td></tr>'; 
				}
	}
	public function Mostrar()
	{
	$this->Leer();
	echo '<tr><td> Nombre de campo1 </td><td colspan="3">'.$this->registro['Nombre_Campo1'].'</td></tr>' ; 
	echo '<tr><td> Nombre de campo2 </td><td colspan="3">'.$this->registro['Nombre_Campo2'].'</td></tr>' ;
	}
	public function Obtener_Registro()
		{
		if ( $this->existe == true )
			{				
				return $this->registro;
			}
		else
			{
				return null ;
			}
		}
	public function Obtener_Datos()
		{
		$this->Leer();
		if ( $this->existe == true )
			{				
				return $this->registro;
			}
		else
			{
				return null ;
			}
		}
	public function Obtener_Lista()
		{
			$this->leer_lista();
			if ( $this->existe == true )
				{
					mysqli_data_seek ( $this->registros , 0 ) ;
					return $this->registros;
				}
			else
				{ return null ;
				}
		}

	public function leer_filtros()
	{
		if ( $this->con_filtro_fecha )
		{
			if ( isset( $_REQUEST[$this->prefijo_campo.'filtro_f_desde'] ) )
				$this->filtro_f_desde = $_REQUEST[$this->prefijo_campo.'filtro_f_desde'] ;
			else
				$this->filtro_f_desde = date('Y-m-d');
			if ( isset( $_REQUEST[$this->prefijo_campo.'filtro_f_hasta'] ) )
				$this->filtro_f_hasta = $_REQUEST[$this->prefijo_campo.'filtro_f_hasta'] ;
			else
				$this->filtro_f_hasta = date('Y-m-d');
		}
		if ( $this->con_filtro_general )
		{
			if ( isset( $_REQUEST[$this->prefijo_campo.'filtro_general'] ) )
				$this->filtro_gral = $_REQUEST[$this->prefijo_campo.'filtro_general'] ;
			else
				$this->filtro_gral = "" ;
		}
		if ( $this->con_filtro_fecha or $this->con_filtro_general )
		{
			if ( isset( $_REQUEST[$this->prefijo_campo.'filtro_id'] ) )
				$this->filtro_id = $_REQUEST[$this->prefijo_campo.'filtro_id'] ;
			else
				$this->filtro_id = '' ;
		}
			
	}
	public function texto_eliminar_okGrabar()
	{
		$nomid = $this->prefijo_campo.'id';
		$this->Set_id($_POST[$nomid]);
		$this->error = false ;
		$this->Leer();
		if ( $this->existe == false )
		{ 
			$this->error = true ;
			$this->textoError = "El registro con Id: ".$this->id." no se encuentra en la base de datos " ;
		}
		if ($this->error == false )
		{
			//
			// Abre la conexión con la base de datos
			$cn=new Conexion();
			//
			// Arma lista de campos a actualizar
			$where = 'false' ;
			$strsql = ' DELETE FROM '.$this->nombre_fisico_tabla.' ' ;
			$i = 0 ;
			foreach ( $this->lista_campos_lectura as $campo )
				{
					$tp = $campo->tipo() ;
					if ( $tp == 'pk' )
						{
						$where = ' '.$campo->nombre()." = '".$this->id."' " ;
						}
					$i++;
				}
			$strsql = $strsql.' WHERE '.$where.' ' ;
			//
			// Cierra la conexion
			$borrado = $cn->conexion->query($strsql) ;
			if( ! $borrado ) die( "Problemas en el delete de ".$this->nombre_tabla." : ".$cn->conexion->error.$strsql ) ;
			$cn->cerrar();
		}
	}	
	public function texto_mostrar_abm()
		{
			$this->leer_filtros();
			$this->leer_lista();
			// by DZ 2016-10-24 
			$tn_cols_principales = 0 ;
			foreach( $this->lista_campos_lista as $to_campo )
				if( $to_campo->mostrar() ) $tn_cols_principales ++ ;
			$cntcols = $tn_cols_principales +count($this->lista_detalle_enc_columnas)+2 ;
			$txt = '';
			$txt=$txt.'<table>';
			//
			// Filtros
			if ( $this->con_filtro_fecha or $this->con_filtro_general)
			{
				$txt .= '<tr>';
				$txt .= '<td colspan="'.$cntcols.'">';
				$txt .= '<table>' ;
				$txt .= '<tr>';
				$txt .= '<td style="border: none;">Filtros:</td>';
				$cpo = new Campo();
				if ( $this->con_filtro_fecha )
				{
					$cpo->pone_tipo( 'date' ) ;
					$txt .='<td>Fecha Desde:</td>';
					$cpo->pone_nombre( $this->prefijo_campo.'filtro_f_desde' ) ;
					$cpo->pone_valor( $this->filtro_f_desde ) ;
					$txt = $txt.$cpo->txtMostrarParaModificar() ;
					$txt .='<td>Fecha Hasta:</td>' ;
					$cpo->pone_nombre( $this->prefijo_campo.'filtro_f_hasta' ) ;
					$cpo->pone_valor( $this->filtro_f_hasta) ;
					$txt = $txt.$cpo->txtMostrarParaModificar() ;
					
				}
				if ( $this->con_filtro_general )
				{
					$cpo->pone_tipo( 'text' );
					$txt .='<td>Nombre:</td>';
					$cpo->pone_nombre( $this->prefijo_campo.'filtro_general' ) ;
					$cpo->pone_valor( $this->filtro_gral ) ;
					$txt = $txt.$cpo->txtMostrarParaModificar() ;
					
				}
				if ( $this->con_filtro_fecha or $this->con_filtro_general )
				{
					$cpo->pone_tipo( 'number' );
					$txt .='<td>#:</td>';
					$cpo->pone_nombre( $this->prefijo_campo.'filtro_id' ) ;
					$cpo->pone_valor( $this->filtro_id ) ;
					$txt.= $cpo->txtMostrarParaModificar() ;
				}
				$txt .= '</td>';
				$txt .= '<td>' ;
				$txt .= '<input type="submit" value="Filtrar" name="'.$this->prefijo_campo.'_okFiltrar">' ;
				$txt .= '</td>';
				$txt .= '</tr>';
				$txt .='</table>';
				$txt .='</tr>';
			}
			//
			// Encabezados
			$txt=$txt.'<tr>' ;
			// --> by DZ 2016-10-24
			$i = 0 ;
			foreach( $this->lista_campos_lista as $to_campo )
				{
					if( $to_campo->mostrar() )
					{
						$txt .= '<th>' ;
						$txt .= $to_campo->descripcion() ;
						$txt .= '</th>' ;
						$i++ ;
					}
				}
			/* 
				--> by DZ 2016-10-24 $txt=$txt.'<th> </th>';
			for($i=0;$i<count($this->lista_campos_descrip);$i++)
				{
				$txt=$txt.'<th>';
				$txt=$txt.$this->lista_campos_descrip[$i];
				$txt=$txt.'</th>';
				}
				<-- by DZ 2016-10-24 
			*/
			//
			// Encabezados detalle
			foreach( $this->lista_detalle_enc_columnas as $tit )
				{
				$txt.='<th>';
				$txt.=$tit;
				$txt.='</th>';
				}
			$txt.='<th>Acciones</th>';
			$txt=$txt.'</tr>';
			//
			// Registros
			$numdet = 0 ;
			while ($this->existe == true and $reg=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
				{
				$txt=$txt.'<tr>';
				/* by DZ 2016-10-24
				 * $txt=$txt.'<td>';
					$txt=$txt.'<input type="checkbox" name="'.$this->prefijo_campo.'_Id'.$reg[0].'">';
					$txt=$txt.'</td>'; 
				*/
				if ( $this->borrar_con_seleccion )
				{
					$txt.= '<td>' ;
					$txt.= '<input type="checkbox" name="'.$this->prefijo_campo.'_Id'.$reg[0].'">';
					$txt.= '</td>' ;
				}
				
				//
				// Datos
				$i = 0 ;
				foreach( $this->lista_campos_lista as $to_campo )
					{
						if( $to_campo->mostrar() )
						{
							$txt .= '<td>' ;
							$txt .= $reg[$i] ;
							$txt .= '</td>' ;
						}
						$i++ ;
					}
				/* by DZ 2016-10-24
				for($f=2;$f<count($reg);$f++)
					{
					$txt=$txt.'<td>';
					$txt=$txt.$reg[$f];
					$txt=$txt.'</td>';
					}
				<--	*/
				//
				// Detalle
				//
				if ( $this->tiene_lista_detalle )
				{
					$arreglo_detalle = $this->lista_detalle[$numdet] ;
					foreach( $arreglo_detalle as $det )
					{
						$txt.='<td>';
						$txt.=$det;
						$txt.='</td>';
					}
					$numdet++;
				}
				
				// Acciones
				$txt=$txt.'<td>' ;
				$txt=$txt.' <a href="'.$this->nombre_pagina.'?'.$this->prefijo_campo.'_Id='.$reg[0].'&'.$this->okVer.'=1"><button type="button">Ver</button></a>' ;
				$txt=$txt.' <a href="'.$this->nombre_pagina.'?'.$this->prefijo_campo.'_Id='.$reg[0].'&'.$this->okModificar.'=1"><button type="button">Modificar</button></a>' ;
				if ( ! $this->borrar_con_seleccion )
				{
					$txt.='<button type="submit" value="'.$reg[0].'" name="'.$this->okBorrarUno.'">Borrar</button>';
					//$txt.=' <a href="'.$this->nombre_pagina.'?'.$this->prefijo_campo.'_Id='.$reg[0].'&'.$this->okBorrarUno.'=1">Borrar</a>' ;
				}
				foreach( $this->acciones as $accion )
					{
						//$txt=$txt.' <a href="'.$this->nombre_pagina.'?'.$this->prefijo_campo.'_Id='.$reg[0].'&'.$this->prefijo_campo.$accion['nombre'].'=1">'.$accion['texto'].'</a>' ;
					} 
					$txt=$txt.'</td>' ;
					$txt=$txt.'</tr>';
				}
			//
			// Botones
			$txt=$txt.'<tr><td colspan="'.$cntcols.'">';
			$txt=$txt.'<input type="submit" value="Agregar" name="'.$this->okAgregar.'">';
			if ( $this->existe == true )
				{ 
					//$txt=$txt.'<tr><td colspan="'.$cntcols.'">';
    			//$txt=$txt.'<input type="submit" value="Agregar" name="'.$this->prefijo_campo.'_okAgregar">';
    			if ( $this->borrar_con_seleccion )
    			$txt=$txt.'<input type="submit" value="Borrar" name="'.$this->prefijo_campo.'_okBorrar">';
    			//$txt=$txt.'</td></tr>'; 
				//$txt=$txt.'<input type="submit" value=">>" name="'.$this->okListaUltimo.'">';
				}
			else
				{
					//$txt=$txt.'<tr><td colspan="'.$cntcols.'">';
    			//$txt=$txt.'<input type="submit" value="Agregar" name="'.$this->prefijo_campo.'_okAgregar">';
    			//$txt=$txt.'</td></tr>'; 
				}
			$txt=$txt.'<input type="submit" value="<<" name="'.$this->okListaPrimero.'">';
			if ( $this->desde > 0 )
				$txt=$txt.'<input type="submit" value="<" name="'.$this->okListaAnterior.'">';
			else
			$txt=$txt.'<input type="submit" value="<"  disabled >';
			if ( $this->existe == true )
				$txt=$txt.'<input type="submit" value=">" name="'.$this->okListaSiguiente.'">';
			foreach( $this->botones_extra_abm as $boton )
			{
				$txt.='<input type="submit" value="'.$boton['texto'].'" name="'.$boton['nombre'].'">';
			}
			$txt=$txt.'</td></tr>';
			//
			// Cierra Tabla
			$txt=$txt.'</table>';
			return $txt;
		}
		public function texto_okBorrarSeleccion()
		{
			//
			// Validaciones
			//
			$this->error= false ;
			$regs = $this->Obtener_Lista(); // 2016-02-10 dz
			//
			// Abre conexion con la base de datos
			$cn=new Conexion();
			while ( $this->existe == true and $reg=mysqli_fetch_array($regs,MYSQLI_NUM) ) // 2016-02-10 dz 
					{
						$nom_check = $this->prefijo_campo.'_Id'.$reg[0] ;
						if (  isset($_REQUEST [$nom_check] ) )
							{
								//
								// borra la seleccion
								$strsql = "DELETE FROM ".$this->nombre_fisico_tabla." 
															WHERE ".$this->lista_campos_lista[0]['nombre']." = '".$reg[0]."' " ;
								$ok = $cn->conexion->query($strsql) ;
								if ( ! $ok ) 	die("Problemas en el delete de ".$this->nombre_tabla." : ".$cn->conexion->error.$strsql );
							}				
					}
			//
			// Cierra la conexion
			$cn->cerrar();

		}

	public function mostrar_abm()
  	{
  		$this->leer_lista();
      while ($this->existe == true and $reg=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
				{
        	echo '<tr>';
				for($f=0;$f<count($reg);$f++)
					{
           	echo '<td>';
            echo $reg[$f];
            echo '</td>';
					}
					echo '<td>';
      		echo '<a href="'.$this->nombre_pagina.'_baja.php?codigo='.$reg[0].'">Borrar</a>';
      		echo '</td>';
      		echo '<td>';
      		echo '<a href=="'.$this->nombre_pagina.'_modifica.php?codigo='.$reg[0].'">Modificar</a>';
      		echo '</td>';
					echo '</tr>';
				}
			if ( $this->existe == true )
				{ 
					$cntcols = count($reg) + 2 ;
					echo '<tr><td colspan="'.$cntcols.'">';
    			echo '<a href="'.$this->nombre_pagina.'_alta.php">Agrega un nuevo artículo?</a>';
    			echo '</td></tr>'; 
				}
			
	}
	protected function sanear_fecha($string)
	{
	 
		$string = trim($string);
	 
		//$string = str_replace(
		//	array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
		//	array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
		//	$string
		//);
	 
	 
		//Esta parte se encarga de eliminar cualquier caracter extraño
		$string = str_replace(
			array("\\" , "¨", "º", "~",
				 "#",  "|", "!", "\"",
				 "\·", "\$", "%", "\&", 
				 "(", ")", "?", "'", "¡",
				 "¿", "[", "^", "<code>", "]",
				 "+", "}", "{", "¨", "´",
				 ">", "<", ";", ",", ":",
				 "·" , "&" , "=" , "*" ),
			'', $string );
		return $string ;
	}
	protected function sanear_num($string)
	{
		if ( intval($string) <> 0 )
		{
			$string = str_replace(
			array("\\" , "¨", "~",
				 "|", "\"",
				 "\·", "\$", "%", "\&", "/",
				 "'", 
				 "[", "^", "<code>", "]",
				 "+", "}", "{", "¨", "´",
				 ">", "<", ";", ",", ":",
				 "·" , "&" , "=" , "*" ),
			'', $string );
			return $string ;
		}
		else
			return 0 ;
	}
	protected function sanear_string($string)
	{
	 
		$string = trim($string);
	 
		//$string = str_replace(
		//	array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
		//	array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
		//	$string
		//);
	 
	 
		//Esta parte se encarga de eliminar cualquier caracter extraño
		//$string = str_replace(
			//array("\\" , "¨", "~",
			//	 "|", "\"",
			//	 "\·", "\$", "%", "\&", "/",
			//	 "'", 
			//	 "[", "^", "<code>", "]",
			//	 "+", "}", "{", "¨", "´",
			//	 ">", "<", ";", ",", ":",
			//	 "·" , "&" , "=" , "*" ),
			//'', $string );
		$string = str_replace( array("\\" ,  "\"", "'" ) , '' , $string ) ;
		return $string ;
	}

//////////////////////////////////////////----- metodos de la clase entidad ------ ///////////////////////////////////////
}
?>
