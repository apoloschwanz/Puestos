<?php
	class llegada extends entidadj {
 		protected function Pone_Datos_Fijos_No_Heredables()
		{	
			//
			// Lista de Campos
			//
			// tipos:  'pk' 'fk' 'otro' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password'
			//								el tipo 'fk' espera que se defina una clase 
			//$this->clave_manual_activar() ; // La clave de la entidad se ingresa manualment
			$this->lista_campos_lista=array();
			$this->lista_campos_lista[]=new campo_entidad( 'llegada.Id' 			, 'pk' 		, '#' , NULL ,true) ;
			$this->lista_campos_lista[]=new campo_entidad( 'Corredor_Id' 	, 'fk' 	, 'Corredor'  ,new corredor()) ;
			$this->lista_campos_lista[1]->pone_busqueda() ;
			$this->lista_campos_lista[]=new campo_entidad( 'corredor.Nombre' 	, 'otro' 	, ' '  ) ;
			$this->lista_campos_lista[2]->pone_busqueda() ;
			$this->lista_campos_lista[]=new campo_entidad( 'Tiempo' 	, 'datetime' 	, 'Llegada'  ) ;
			$this->lista_campos_lista[]=new campo_entidad( ' TIMEDIFF( Tiempo, Inicio ) AS Tpo' 	, 'time' 	, 'Tiempo'  ) ;
			//
			//
			$this->lista_campos_lectura=array();
			$this->lista_campos_lectura[]=new campo_entidad( 'Id' 			, 'pk' 		, '#' , NULL ,true) ;
			$this->lista_campos_lectura[]=new campo_entidad( 'Corredor_Id' 	, 'number' 	, 'Corredor'  ) ;
			$this->lista_campos_lectura[1]->pone_autofocus() ;
			$this->lista_campos_lectura[]=new campo_entidad( 'Tiempo' 	, 'timestamp' 	, 'Tiempo'  ) ;
			$this->lista_campos_lectura[2]->pone_readonly();
			//
			// Nombre de la tabla
			$this->nombre_tabla = "Llegadas" ;
			//$this->nombre_fisico_tabla = "llegada" ;																		
			$this->nombre_fisico_tabla = ' llegada ' ;
			//$this->nombre_fisico_tabla .= ' LEFT JOIN corredor ON llegada.Corredor_Id = corredor.Id ' ;
			//$this->nombre_fisico_tabla .= ' left join carrera on Carrera_Id = carrera.Id ' ;
			//
			//
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
						$txt=$txt.' <a href="'.$this->nombre_pagina.'?'.$this->prefijo_campo.'_Id='.$reg[0].'&'.$this->prefijo_campo.$accion['nombre'].'=1">'.$accion['texto'].'</a>' ;
					} 
					$txt=$txt.'</td>' ;
					$txt=$txt.'</tr>';
				}
			//
			// Botones
			$txt=$txt.'<tr><td colspan="'.$cntcols.'">';
			/*
			$txt=$txt.'<input type="submit" value="Agregar" name="'.$this->okAgregar.'">';
			if ( $this->existe == true )
				{ 
				if ( $this->borrar_con_seleccion )
    			$txt=$txt.'<input type="submit" value="Borrar" name="'.$this->prefijo_campo.'_okBorrar">';
    			}
			$txt=$txt.'<input type="submit" value="<<" name="'.$this->okListaPrimero.'">';
			if ( $this->desde > 0 )
				$txt=$txt.'<input type="submit" value="<" name="'.$this->okListaAnterior.'">';
			else
			$txt=$txt.'<input type="submit" value="<"  disabled >';
			if ( $this->existe == true )
				$txt=$txt.'<input type="submit" value=">" name="'.$this->okListaSiguiente.'">';
			*/
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
							if( $this->lista_campos_lectura[$i]->autofocus() )
							$cpo->pone_autofocus() ;
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
		$this->strsql .= ' LEFT JOIN corredor ON llegada.Corredor_Id = corredor.Id ' ;
		$this->strsql .= ' left join carrera on Carrera_Id = carrera.Id ' ;
			
		//
		// where
		//$this->strsql .=  'WHERE Carrera_Id = '."'".$this->carrera_id."' " ;
		//
		// orden
		$this->strsql .=  ' Order By Tiempo DESC ' ;
		//
		// paginado
		$this->strsql .= ' LIMIT 15 ' ;
		
		
	}

		public function mostrar_pagina_alta()
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
			
			//
			//
			$tb_armar_pagina = false ;
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
			elseif ( isset( $_POST[$this->okGrabar] ) )
			{
				// Graba Modificaciones
				$this->texto_actualizar_okGrabar();
				if ( $this->hay_error() == true ) $this->muestra_error() ;
				else $tb_armar_pagina = true ;// $this->muestra_ok('Registro # '.$this->id().' actualizado') ;
			}
			elseif ( isset($_POST[$this->okGrabaBorrarUno]) )
			{
				//
				// Confirma Borrar
				$this->texto_eliminar_okGrabar();
				if ( $this->hay_error() == true ) $this->muestra_error() ;
				else $tb_armar_pagina = true ;// $this->muestra_ok('Registro # '.$this->id().' eliminado') ;
			}
			elseif ( isset($_POST[$this->okListaSiguiente]) )
			{
				//
				// Mostrar Lista Siguiente
				$this->desde += $this->cuenta ;
				$tb_armar_pagina = true ;
			}
			elseif ( isset($_POST[$this->okListaAnterior]) )
			{
				//
				// Mostrar Lista Anterior
				$this->desde -= $this->cuenta ;
				$tb_armar_pagina = true ;
			}
			elseif ( isset($_POST[$this->okListaPrimero]) )
			{
				//
				// Mostrar Lista Primero
				$this->desde = 0 ;
				$tb_armar_pagina = true ;
			}
			elseif ( isset($_POST[$this->okListaUltimo]) )
			{
				//
				// Mostrar Lista Ultimo
				$tb_armar_pagina = true ;
			}
			elseif ( isset( $_POST[$this->okSalir] ) )
			{
				$this->ok_Salir() ;
			}
			elseif ( isset( $_POST[$this->okGrabaAgregar] ) )
			{
				// Graba Modificaciones
				$this->texto_agregar_okGrabar();
				if ( $this->hay_error() == true ) $this->muestra_error() ;
				else $tb_armar_pagina = true ;//$this->muestra_ok('Registro # '.$this->id().' agregado') ;
			}
			elseif ( isset($_REQUEST['okSalir'] ) )
			{
				$this->ok_Salir() ;
			}
			elseif ( isset($_REQUEST[$this->prefijo_campo.'cpoNro1_']) and ! empty($_REQUEST[$this->prefijo_campo.'cpoNro1_']) ) 
			{
				// Graba Modificaciones
				$this->texto_agregar_okGrabar();
				if ( $this->hay_error() == true ) $this->muestra_error() ;
				else $tb_armar_pagina = true ;//$this->muestra_ok('Registro # '.$this->id().' agregado') ;//die('No apreto nada pero ingreso un codigo!!!!');
			}
			else
			{
				$tb_armar_pagina = true ;
			}
			if( $tb_armar_pagina )
			{
				//
				// Arma la página para agregar		
				//$pagina=new Paginaj($this->nombre_tabla ,'<input type="submit" value="Grabar" name="'.$this->okGrabaAgregar.'"><input type="submit" value="Salir" name="'.$this->okSalir.'">');
				$pagina=new Paginaj($this->nombre_tabla ,'<input type="submit" value="Grabar" name="'.$this->okGrabaAgregar.'">');
				//$txt = $this->texto_Ver_Lado_Uno();
				//$pagina->insertarCuerpo($txt);
				//
				// Muestra Menu
				$texto = '<a href="accueil.php" target="_blank"> Menu </a>' ;
				//
				// Agregar
				$txt = 	$this->texto_agregar();
				$pagina->insertarCuerpo($txt);
				//
				// Lista
				$pagina->insertarCuerpo($texto);
				$txt = $this->texto_mostrar_abm() ;
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
					$readonly = $campo->readonly() ;
					//
					// tipos de camos validos
					if ( $tp != 'otro' and ( $tp != 'pk' or $this->clave_manual ) and !$readonly)
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
			$strsql = ' INSERT INTO llegada ( '.$lst_cmp.' )  VALUES ( '.$lst_val. ' ) ';
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

		protected function crear_tabla ()
		{
			$this->strsql = "
								CREATE TABLE llegada
								( 	Id INT PRIMARY KEY AUTO_INCREMENT ,
									Corredor_Id INT ,
									Tiempo TIMESTAMP DEFAULT CURRENT_TIMESTAMP
								) ;
							" ;
		}
	}
?>
