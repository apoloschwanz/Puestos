<?php

class Entidad  {
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
	public $existe ;																								// by DZ 2015-08-18 - agregado lista de datos
  public function __construct()
  	{
			$this->existe = false ;
			$this->Pone_nombre () ;
			$this->Pone_nombre_pag () ;
			$this->Pone_prefijo_campo() ;
			$this->desde = 0 ;																					// by DZ 2015-08-14 - agregado lista de datos
			$this->cuenta = 15 ;																				// by DZ 2015-08-14 - agregado lista de datos
			// by DZ 2016-01-22 $this->Pone_Clase_Detalle();																// by DZ 2016-01-21 - agregado clase detalle
			// by DZ 2016-01-22$this->Pone_Lista_Campos();																	// by DZ 2016-01-21 - agregado clase detalle
			//

			// Acciones Extra para texto_mostrar_abm
			$this->acciones = array() ;
			//$this->acciones[] = array( 'nombre'=>'okOtraAcc' , 'texto'=>'Otra Accion' ) ;
			$this->lista_detalle = array() ;
			$this->tiene_lista_detalle = false ; // se activa en rutina de lista detalle
			$this->lista_detalle_enc_columnas = array();
  	}
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
	$this->strsql = "SELECT campo1, campo2
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
	}
	protected function Carga_Sql_Lista()
	{
	$this->strsql = "SELECT clave, color, campo1, campo2
			FROM Tabla 
			INNER JOIN Otra_Tabla 
			ON Tabla.FK = Otra_Tabla.PK
			LIMIT " .$this->desde.",".$this->cuenta ;
		$this->lista_campos_descrip=array() ;
		$this->lista_campos_tipo=array() ;
		$this->lista_campos_descrip[]='Campo Uno' ;
		$this->lista_campos_descrip[]='Campo Dos' ;
		$this->lista_campos_tipo[]='text' ; // 'pk' 'fk' 'otro' 'date' 'datetime' 'time' 'number' 'email' 'url' 'password' 
		$this->lista_campos_tipo[]='text';
		
	}
	public function Leer_Todo()
	{ $this->Carga_Sql_Lectura_Total();		
		$cn=new Conexion();
		$this->registros=mysqli_query($cn->conexion,$this->strsql) or
				die("Problemas en el select de lectura total de".$this->nombre_tabla.": ".mysqli_error($cn->conexion). " id = ".$this->id. " <br><br> Sql= ".$this->strsql );
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
	public function texto_actualizar()
		{
			$this->error= false ;
			$this->Leer();
			if ( $this->existe == false )
			{ 
				$this->error = true ;
				$this->textoError = "El registro con Id: ".$this->ID." no se encuentra en la base de datos " ;
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
					$txt = '<table>';
					if ( $reg=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
						{
							$txt=$txt.'<tr>';
							$txt=$txt.'<td></td><td><input type="hidden" name="'.$this->prefijo_campo.'id" value="'.$this->id.'"></td>';
							$txt=$txt.'</tr>';
							for($i=0;$i<count($reg);$i++)
								{
									$txt=$txt.'<tr>';
									$txt=$txt.'<td>';
								  $txt=$txt.$this->lista_campos_descrip[$i];
								  $txt=$txt.'</td>';
									$txt=$txt.'<td>';
									if( $this->lista_campos_tipo[$i] == 'pk' or $this->lista_campos_tipo[$i] == 'fk' or $this->lista_campos_tipo[$i] == 'otro' )
										{ 
											$txt = $txt.$reg[$i] ;
										}
									elseif(  $this->lista_campos_tipo[$i] == 'textarea' )
										{
											$txt.= '<textarea  ' ; 
											$txt.= ' name="'.$this->prefijo_campo.'cpoNro'.$i.'_" ' ;
											$txt.= ' rows="5" cols="50">' ; 
											$txt.= $reg[$i] ;
											$txt.= '</textarea>';
										}
									else
										{ 
											$txt=$txt.'<input type="'.$this->lista_campos_tipo[$i].'" name="'.$this->prefijo_campo.'cpoNro'.$i.'_" value="'.$reg[$i].'">';
										}
								  $txt=$txt.'</td>';
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
	public function texto_mostrar_abm()
		{
			$this->leer_filtros();
			$this->leer_lista();
			$cntcols = count($this->lista_campos_descrip)+count($this->lista_detalle_enc_columnas)+2 ;
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
				//$txt .= '</td>';
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
			$txt=$txt.'<th> </th>';
			for($i=0;$i<count($this->lista_campos_descrip);$i++)
				{
				$txt=$txt.'<th>';
				$txt=$txt.$this->lista_campos_descrip[$i];
				$txt=$txt.'</th>';
				}
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
				$txt=$txt.'<td>';
				$txt=$txt.'<input type="checkbox" name="'.$this->prefijo_campo.'_Id'.$reg[0].'">';
				$txt=$txt.'</td>';
				//
				// Datos
				for($f=2;$f<count($reg);$f++)
					{
					$txt=$txt.'<td>';
					$txt=$txt.$reg[$f];
					$txt=$txt.'</td>';
					}
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
				$txt=$txt.' <a href="'.$this->nombre_pagina.'?'.$this->prefijo_campo.'_Id='.$reg[0].'&'.$this->prefijo_campo.'okVer=1">Ver</a>' ;
				$txt=$txt.' <a href="'.$this->nombre_pagina.'?'.$this->prefijo_campo.'_Id='.$reg[0].'&'.$this->prefijo_campo.'okModificar=1">Modificar</a>' ;
				foreach( $this->acciones as $accion )
					{
						$txt=$txt.' <a href="'.$this->nombre_pagina.'?'.$this->prefijo_campo.'_Id='.$reg[0].'&'.$this->prefijo_campo.$accion['nombre'].'=1">'.$accion['texto'].'</a>' ;
					} 
					$txt=$txt.'</td>' ;
					$txt=$txt.'</tr>';
				}
			if ( $this->existe == true )
				{ 
					$txt=$txt.'<tr><td colspan="'.$cntcols.'">';
    			$txt=$txt.'<input type="submit" value="Agregar" name="'.$this->prefijo_campo.'_okAgregar">';
    			$txt=$txt.'<input type="submit" value="Borrar" name="'.$this->prefijo_campo.'_okBorrar">';
    			$txt=$txt.'</td></tr>'; 
				}
			else
				{
					$txt=$txt.'<tr><td colspan="'.$cntcols.'">';
    			$txt=$txt.'<input type="submit" value="Agregar" name="'.$this->prefijo_campo.'_okAgregar">';
    			$txt=$txt.'</td></tr>'; 
				}
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
	
	public function texto_impresion()
		{
			$this->error= false ;
			$this->Leer();
			if ( $this->existe == false )
			{ 
				$this->error = true ;
				$this->textoError = "El registro con Id: ".$this->ID." no se encuentra en la base de datos " ;
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
					$txt = '<table>';
					if ( $reg=mysqli_fetch_array($this->registros,MYSQLI_NUM) )
						{
							$txt=$txt.'<tr>';
							$txt=$txt.'<td></td><td><input type="hidden" name="'.$this->prefijo_campo.'id" value="'.$this->id.'"></td>';
							$txt=$txt.'</tr>';
							for($i=0;$i<count($reg);$i++)
								{
									$txt=$txt.'<tr>';
									$txt=$txt.'<td>';
								  $txt=$txt.$this->lista_campos_descrip[$i];
								  $txt=$txt.'</td>';
									$txt=$txt.'<td>';
									if( $this->lista_campos_tipo[$i] == 'pk' or $this->lista_campos_tipo[$i] == 'fk' or $this->lista_campos_tipo[$i] == 'otro' )
										{ 
											$txt = $txt.$reg[$i] ;
										}
									else
										{ 
											$txt=$txt.'<input type="'.$this->lista_campos_tipo[$i].'" name="'.$this->prefijo_campo.'cpoNro'.$i.'_" value="'.$reg[$i].'">';
										}
								  $txt=$txt.'</td>';
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
	
}

?>
