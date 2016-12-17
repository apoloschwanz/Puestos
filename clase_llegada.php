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
			$this->lista_campos_lectura[]=new campo_entidad( 'Tiempo' 	, 'timestamp' 	, 'Tiempo'  ) ;
			$this->lista_campos_lectura[2]->pone_readonly();
			//
			// Nombre de la tabla
			$this->nombre_tabla = "Llegadas" ;
			//$this->nombre_fisico_tabla = "llegada" ;																		
			$this->nombre_fisico_tabla = ' llegada ' ;
			$this->nombre_fisico_tabla .= ' LEFT JOIN corredor ON llegada.Corredor_Id = corredor.Id ' ;
			$this->nombre_fisico_tabla .= ' left join carrera on Carrera_Id = carrera.Id ' ;
			//
			//
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
		public function mostrar_pagina_alta()
		{	
			//
			//
			$tb_armar_pagina = false ;
			if ( isset( $_POST[$this->okSalir] ) )
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
			else
			{
				$tb_armar_pagina = true ;
			}
			if( $tb_armar_pagina )
			{
				//
				// Arma la página para agregar		
				$pagina=new Paginaj($this->nombre_tabla ,'<input type="submit" value="Grabar" name="'.$this->okGrabaAgregar.'"><input type="submit" value="Salir" name="'.$this->okSalir.'">');
				//$txt = $this->texto_Ver_Lado_Uno();
				//$pagina->insertarCuerpo($txt);
				//
				// Muestra Menu
				$texto = '<a href="accueil.php" target="_blank"> Menu </a>' ;
				$pagina->insertarCuerpo($texto);
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
