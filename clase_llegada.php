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
			$this->lista_campos_lista[]=new campo_entidad( 'Id' 			, 'pk' 		, '#' , NULL ,true) ;
			$this->lista_campos_lista[]=new campo_entidad( 'Corredor_Id' 	, 'number' 	, 'Corredor'  ) ;
			$this->lista_campos_lista[1]->pone_busqueda() ;
			$this->lista_campos_lista[]=new campo_entidad( 'Tiempo' 	, 'datetime' 	, 'Tiempo'  ) ;
			//
			//
			$this->lista_campos_lectura=array();
			$this->lista_campos_lectura[]=new campo_entidad( 'Id' 			, 'pk' 		, '#' , NULL ,true) ;
			$this->lista_campos_lectura[]=new campo_entidad( 'Corredor_Id' 	, 'number' 	, 'Corredor'  ) ;
			$this->lista_campos_lectura[]=new campo_entidad( 'Tiempo' 	, 'datetime' 	, 'Tiempo'  ) ;
			//
			// Nombre de la tabla
			$this->nombre_tabla = "Llegadas" ;
			$this->nombre_fisico_tabla = "llegada" ;																		
			//
			//
		}	

		protected function crear_tabla ()
		{
			$this->strsql = "
								CREATE TABLE llegada
								( 	Id INT PRIMARY KEY AUTO_INCREMENT ,
									Corredor_Id INT ,
									Tiempo TIMESTAMP
								) ;
							" ;
		}
	}
?>
