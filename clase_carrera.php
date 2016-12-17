<?php
	class carrera extends entidadj {
 		protected function Pone_Datos_Fijos_No_Heredables()
		{	
			//
			// Lista de Campos
			//
			// tipos:  'pk' 'fk' 'otro' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password'
			//								el tipo 'fk' espera que se defina una clase 
			//$this->clave_manual_activar() ; // La clave de la entidad se ingresa manualment
			$this->lista_campos_lista=array();
			$this->lista_campos_lista[]=new campo_entidad( 'Id' 			, 'pk' 		, '#' , NULL ,true) ;
			$this->lista_campos_lista[]=new campo_entidad( 'Nombre' 	, 'text' 	, 'Nombre'  ) ;
			$this->lista_campos_lista[]=new campo_entidad( 'Inicio'		, 'datetime' , 'Inicio' ) ;
			//
			//
			$this->lista_campos_lectura=array();
			$this->lista_campos_lectura[]=new campo_entidad( 'Id' 			, 'pk' 		, '#' , NULL ,true) ;
			$this->lista_campos_lectura[]=new campo_entidad( 'Nombre' 	, 'text' 	, 'Nombre'  ) ;
			$this->lista_campos_lectura[]=new campo_entidad( 'Inicio'		, 'datetime' , 'Inicio' ) ;
			$this->lista_campos_lectura[2]->readonly();
			//
			// Nombre de la tabla
			$this->nombre_tabla = "Carrera" ;
			$this->nombre_fisico_tabla = "carrera" ;																		
			//
			//
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
		$this->acciones[] = array( 'nombre'=>'okIniciaCarrera' , 'texto'=>'IniciarCarrera' ) ;
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
	protected function maneja_evento_accion_especial()
		{  
			/* $tts_aux = '<br>Selecciono accion especial: ' .  $this->accion_ok ;
				$tts_aux .= '<br> Para el Id : '.$this->id ;
				$tts_aux .= '<br> Modificando el evento maneja_evento_accion_especial ' ;
				echo $tts_aux  ;
			*/
			// UPDATE `table` SET the_col = current_timestamp //
			// "update `table` set date_date=now()"
			$this->strsql = ' UPDATE '.$this->nombre_fisico_tabla. ' ' ;
			$this->strsql .= ' SET Inicio = current_timestamp ' ; 
			//$this->strsql .= ' WHERE Id = '.$this->id ;
			$this->ejecuta_sql();
			// Vuelve a la pantalla
			$this->mostrar_lista_abm() ;
		}

		protected function crear_tabla ()
		{
			$this->strsql = "
								CREATE TABLE carrera
								( 	Id INT PRIMARY KEY AUTO_INCREMENT ,
									Nombre varchar(150) ,
									Inicio timestamp
								) ;
							" ;
		}
	}
?>
