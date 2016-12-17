<?php
	class corredor extends entidadj {
 		protected function Pone_Datos_Fijos_No_Heredables()
		{	
			//
			// Lista de Campos
			//
			// tipos:  'pk' 'fk' 'otro' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password'
			//								el tipo 'fk' espera que se defina una clase 
			$this->clave_manual_activar() ; // La clave de la entidad se ingresa manualment
			$this->lista_campos_lista=array();
			$this->lista_campos_lista[]=new campo_entidad( 'Id' 			, 'pk' 		, '#' , NULL ,true) ;
			$this->lista_campos_lista[]=new campo_entidad( 'Nombre' 	, 'text' 	, 'Nombre'  ) ;
			$this->lista_campos_lista[1]->pone_busqueda() ;
			$this->lista_campos_lista[]=new campo_entidad( 'Carrera_Id' 	, 'fk' 	, 'Carrera', new carrera()  ) ;
			//
			//
			$this->lista_campos_lectura=array();
			$this->lista_campos_lectura[]=new campo_entidad( 'Id' 			, 'pk' 		, '#' , NULL ,true) ;
			$this->lista_campos_lectura[]=new campo_entidad( 'Nombre' 	, 'text' 	, 'Nombre'  ) ;
			$this->lista_campos_lectura[]=new campo_entidad( 'Carrera_Id' 	, 'fk' 	, 'Carrera', new carrera()  ) ;
			//
			// Nombre de la tabla
			$this->nombre_tabla = "Corredor" ;
			$this->nombre_fisico_tabla = "corredor" ;																		
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

		protected function crear_tabla ()
		{
			$this->strsql = "
								CREATE TABLE corredor
								( 	Id INT PRIMARY KEY AUTO_INCREMENT ,
									Nombre varchar(150) ,
									Carrera_Id INT
								) ;
							" ;
		}
	}
?>
