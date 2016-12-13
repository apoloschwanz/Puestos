<?php

class Entidadi extends Entidad  {
	protected $pagina_url_anterior ;
	protected $lista_campos_lista ;
	protected $lista_campos_lectura ;
	protected $pagina_titulo ;
	protected $okGrabar  ;
	protected $okReleer  ;
	protected $okSalir ;
	protected $botones_extra_edicion ;
  // by DZ 2016-01-22 - protected $lista_campos_descrip ;																
	// by DZ 2016-01-22 - sacar ---- protected $lista_campos_tipo ;																	
	// by DZ 2016-01-22 - protected $lista_campos_nombre ;																
 		protected function Pone_Datos_Fijos_No_Heredables()
		{	
			//
			// Prefijo campo
			$this->prefijo_campo = 'm_ent_' ;
			//
			// Lista de Campos
			//
			// tipos:  'pk' 'fk' 'otro' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password'
			//								el tipo 'fk' espera que se defina una clase 
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
			// Nombre de la pagina
			$this->nombre_pagina = $_SERVER['PHP_SELF'] ;
			//
			// Paginacion
			$this->desde = 0 ;																					// by DZ 2015-08-14 - agregado lista de datos
			$this->cuenta = 15 ;																				// by DZ 2015-08-14 - agregado lista de datos		
			//
			// Acciones Extra para texto_mostrar_abm
			//$this->acciones[] = array( 'nombre'=>'okAsignarDte' , 'texto'=>'AsignarDte' ) ;
			//
			// Botones extra edicion
			$this->botones_extra_edicion = array();
			$this->botones_extra_edicion[] = array( 'name'=> '_Rel1' ,
													'value'=>'Salir' ,
													'link'=>'salir.php' ) ; // '<input type="submit" name="'.$this->prefijo_campo.'_Rel1" value="Salir" autofocus>
			//
			// Filtros
			$this->con_filtro_fecha = false;
			$this->con_filtro_general = false;
			//
		}	
		//
		// Funciones que no hace falta redefinir
		//
	public function __construct()
  	{
			//
			// Inicializacion de Variables
			$this->pagina_url_anterior = 'acceuil.php' ;
			$this->existe = false ;
			$this->acciones = array() ;
			//$this->acciones[] = array( 'nombre'=>'okOtraAcc' , 'texto'=>'Otra Accion' ) ;
			$this->lista_detalle = array() ;
			$this->tiene_lista_detalle = false ; // se activa en rutina de lista detalle
			$this->lista_detalle_enc_columnas = array();
			//
			// Personalizacion de variables
			$this->Pone_Datos_Fijos_No_Heredables() ; 									// by DZ 2015-08-14 - agregado lista de datos
			// Acciones Extra para texto_mostrar_abm
  	}
	protected function Carga_Sql_Lectura_Total()
	{
		//
		// Lee todos los registros y todos los campos
		$this->strsql = " SELECT " ;
		$f_1ro = true ;
		foreach( $this->lista_campos_lectura as $ts_campo )
		{
			if( $f_1ro )
				$f_1ro = false ;
			else
				$this->strsql .= ', ' ;
				
			$this->strsql .= $ts_campo['nombre'] ;
		}
		$this->strsql .= " FROM ".$this->nombre_fisico_tabla." " ; 
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
				// Arma lista de campos a actualizar
				$primerCampo = true;
				$where = 'false' ;
				$strsql = ' UPDATE '.$this->nombre_fisico_tabla.' SET ' ;
				$i = 0 ;
				foreach ( $this->lista_campos_lectura as $campo )
					{
						$tp = $campo['tipo'] ;
						if ( $tp == 'pk' )
							{
							$where = ' '.$campo['nombre']." = '".$this->id."' " ;
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
							//
							// Nombre de campo
							$nomCtrl = $this->prefijo_campo.'cpoNro'.$i.'_'  ;
							//
							// Valor a reemplazar en el campo
							if ( $tp == 'time' ) $valor = '1899-12-30 '.$_POST[$nomCtrl] ;
							else $valor = $_POST[$nomCtrl] ;
							//
							// Instruccion set...
							$strsql = $strsql. $campo['nombre']. " = '".$valor."' " ;
							}
						$i++;
					}
				$strsql = $strsql.' WHERE '.$where.' ' ;
				//
				// Cierra la conexion
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
								  $txt=$txt.$this->lista_campos_lectura[$i]['descripcion'];
								  $txt=$txt.'</td>';
									$cpo->pone_nombre( $this->prefijo_campo.'cpoNro'.$i.'_' ) ;
									$cpo->pone_valor( $reg[$i] ) ;
									if( $this->lista_campos_lectura[$i]['tipo'] == 'pk' or $this->lista_campos_lectura[$i]['tipo'] == 'otro' )
										{ 
											$cpo->pone_tipo( 'text' ) ;
											$txt = $txt.$cpo->txtMostrarEtiqueta() ;
										}
									elseif( $this->lista_campos_lectura[$i]['tipo'] == 'fk' )
										{
											//
											// Lista de fk
											//
											$cpo->pone_tipo( 'select' ) ;
											$lista_fk = $this->lista_campos_lectura[$i]['clase']->Obtener_Lista() ;
											$cpo->pone_lista( $lista_fk ) ;
											$cpo->pone_posicion_codigo( 0 ) ;
											$cpo->pone_posicion_descrip( 1 ) ;
											$cpo->pone_mostar_nulo_en_si() ;
											$txt = $txt.$cpo->txtMostrarParaModificar() ;
										}
									else
										{ 
											$cpo->pone_tipo( $this->lista_campos_lectura[$i]['tipo'] ) ;
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
								  $txt=$txt.$this->lista_campos_lectura[$i]['descripcion'];
								  $txt=$txt.'</td>';
									$cpo->pone_nombre( $this->prefijo_campo.'cpoNro'.$i.'_' ) ;
									$cpo->pone_valor( $reg[$i] ) ;
									if( $this->lista_campos_lectura[$i]['tipo'] == 'pk' or $this->lista_campos_lectura[$i]['tipo'] == 'otro' )
										{ 
											$cpo->pone_tipo( 'text' ) ;
											$txt = $txt.$cpo->txtMostrarEtiqueta() ;
										}
									elseif( $this->lista_campos_lectura[$i]['tipo'] == 'fk' )
										{
											//
											// Lista de fk
											//
											$cpo->pone_tipo( 'select' ) ;
											$lista_fk = $this->lista_campos_lectura[$i]['clase']->Obtener_Lista() ;
											$cpo->pone_lista( $lista_fk ) ;
											$cpo->pone_posicion_codigo( 0 ) ;
											$cpo->pone_posicion_descrip( 1 ) ;
											$cpo->pone_mostar_nulo_en_si() ;
											$txt = $txt.$cpo->txtMostrarParaVer() ;
										}
									else
										{ 
											$cpo->pone_tipo( $this->lista_campos_lectura[$i]['tipo'] ) ;
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
			for($i=1;$i<count($this->lista_campos_lectura);$i++)
				{
					$txt=$txt.'<tr>';
					$txt=$txt.'<td>';
				  $txt=$txt.$this->lista_campos_lectura[$i]['descripcion'];
				  $txt=$txt.'</td>';
					$cpo->pone_nombre( $this->prefijo_campo.'cpoNro'.$i.'_' ) ;
					$cpo->pone_valor( '' ) ;
					if( $this->lista_campos_lectura[$i]['tipo'] == 'pk' or $this->lista_campos_lectura[$i]['tipo'] == 'otro' )
						{ 
							//$cpo->pone_tipo( 'text' ) ;
							//$txt = $txt.$cpo->txtMostrarEtiqueta() ;
						}
					elseif( $this->lista_campos_lectura[$i]['tipo'] == 'fk' )
						{
							//
							// Lista de fk
							//
							$cpo->pone_tipo( 'select' ) ;
							$lista_fk = $this->lista_campos_lectura[$i]['clase']->Obtener_Lista() ;
							$cpo->pone_lista( $lista_fk ) ;
							$cpo->pone_posicion_codigo( 0 ) ;
							$cpo->pone_posicion_descrip( 1 ) ;
							$cpo->pone_mostar_nulo_en_si() ;
							$txt = $txt.$cpo->txtMostrarParaModificar() ;
						}
					else
						{ 
							$cpo->pone_tipo( $this->lista_campos_lectura[$i]['tipo'] ) ;
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
			foreach ( $this->lista_campos_lectura as $campo )
				{
					//
					// tipo de campo
					$tp = $campo['tipo'] ;
					//
					// tipos de camos validos
					if ( $tp != 'otro' and $tp != 'pk' )
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
						//
						// Lista campos
						$lst_cmp = $lst_cmp. $campo['nombre'] ;
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
					$result = $cn->conexion->query('SELECT last_insert_id()');
					$reg = $result->fetch_array(MYSQLI_NUM);
					$this->id = $reg[0];
					$result->free();
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
		public function ok_salir()
		{
			header('Location: '.$this->pagina_url_anterior);

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
			$botones = '<input type="submit" name="'.$this->okSalir.'" value="Salir" autofocus>';
			$botones .= '<input type="submit" name="'.$this->okReleer.'" value="Revertir" >';
			$botones .= '<input type="submit" name="'.$this->okGrabar.'" value="Grabar" >';
			$botones .= $btn_extra ;
			$pagina = new Paginai($this->pagina_titulo,$hidden.$botones) ;
			$pagina->sinborde();
			//
			// Muestra la cabecera
			$texto = $this->texto_actualizar();
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
			
			if ( isset($_REQUEST['okSalir'] ) )
			{
				$this->ok_Salir() ;
			}
			else
			{
				$this->mostrar_lista_abm() ;
			}
		}
		public function mostrar_lista_abm()
		{
			$hidden = '' ;
			$pagina = new Paginai($this->nombre_tabla ,$hidden.'<input type="submit" name="okSalir" value="Salir" autofocus>') ;
			//
			// Muestra la cabecera
			$texto = $this->texto_mostrar_abm() ;
			$pagina->insertarCuerpo($texto);
			//
			// Grafica la página
			$pagina->graficar_c_form($_SERVER['PHP_SELF']);
		}
		public function exportar_a_archivo($archivo)
		{
			//$file = fopen("archivo.txt", "w");
			$file = fopen($archivo,"w");
			//
			// Encabezado
			$ts_enc = "";
			foreach ( $this->lista_campos_lectura as $campo )
			{
				$ts_enc .= '"'.$campo['nombre'] . '"; ' ;
			}
			fwrite($file, $ts_enc . PHP_EOL );
			//
			// Recorre los registros
			$this->Leer_Todo();
			mysqli_data_seek ( $this->registros , 0 ) ;	
			while( $this->registro=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
			{
				//*****-----//
				for($i=0;$i<count($this->registro);$i++)
				{
					fwrite($file, '"'.$this->registro[$i].'";' ) ;
				}
				fwrite($file, PHP_EOL );
			}
		
			fwrite($file, "Backup al ".date("Y-m-d h:i:sa") . PHP_EOL);
			fclose($file);
		}		
}
?>
