<?php

class Campo {
	protected $nombre ;
	protected $tipo ;
	protected $valor ;
	protected $lista ;
	protected $posicion_codigo ;
	protected $posicion_descrip ;
	protected $colspan_dato ;
	protected $mostrar_nulo ;
	protected $autofocus ;
	protected $en_celda ;
	public function nombre()
	{
		return $this->nombre ;
	}
	public function valor()
	{
		return $this->valor ;
	}
	public function sin_celda()
	{
		$this->en_celda = false ;
	}
	public function __construct($nombre=NULL, $valor=NULL, $tipo='text')
	{
		$this->nombre = $nombre ;
		$this->valor = $valor ;
		$this->tipo = $tipo ;
		$this->colspan_dato = 1 ;
		$this->autofocus = '' ;
		$this->en_celda = true ; 
	}
	public function pone_autofocus()
	{
		$this->autofocus = 'autofocus' ;
	}
	public function saca_autofocus()
	{
		$this->autofocus = '' ;
	}
	public function pone_colspan_dato($colspan)
	{
		$this->colspan_dato = $colspan ;
	}
	public function pone_nombre ($nombre)
	{
		$this->nombre = $nombre;
	}
	public function pone_tipo ($tipo)
	{
		$this->tipo = $tipo;
	}
	public function pone_valor_ultimo_ingresado()
	{
		if (isset( $_REQUEST[$this->nombre] ) )
		{
			$this->valor=$_REQUEST[$this->nombre] ;
		}
		elseif ( isset( $this->valor_default ) )
		{
			$this->valor= $this->valor_default;
		}
		elseif ( $this->tipo == 'date' )
		{
			$this->valor = date('Y-m-d') ;
		}
		elseif ( $this->tipo == 'number')
		{
			$this->valor = 0 ;
		}
		else
		{
			$this->valor = "" ;
		}
	}
	public function pone_valor ($valor)
	{
		$this->valor = $valor;
	}
	public function pone_lista ($lista)
	{
		$this->lista = $lista;
	}
	public function pone_posicion_codigo ($pos)
	{
		$this->posicion_codigo = $pos ;
	}
	public function pone_posicion_descrip ($pos)
	{
		$this->posicion_descrip = $pos ;
	}
	public function pone_mostar_nulo_en_si ()
	{
		$this->mostar_nulo = true ;
	}
	public function pone_mostar_nulo_en_no ()
	{
		$this->mostar_nulo = false ;
	}
	public function txtMostrarParaVer()
	{
		$txt = '';
		if ( $this->tipo == 'select')
			{
				$txt=$txt.'<td>' ;
				while ($reg=mysqli_fetch_array($this->lista))
				{ 
					if ( $reg[$this->posicion_codigo] == $this->valor ) 
						{
						$txt = $txt.$reg[$this->posicion_descrip] ;
						}
				}
				$txt=$txt.'</td>';
			}
		else
			{
				$txt=$txt.'<td>' ;
				$txt=$txt.$this->valor ;
				$txt=$txt.'</td>';
			}
		return $txt ;
	}
	public function txtMostrarParaModificar()
	{
		$txt = '';
		if ( $this->tipo == 'select')
			{
				$txt=$txt.'<td>' ;
				$txt=$txt.'<select name="'.$this->nombre.'">' ;
				if ( $this->mostrar_nulo == true )											// dz 2015_10_09 -->
					{
						$txt=$txt.'  <option value="">-----</option> ' ;
					}																							// dz 2015_10_09 <--
				while ($reg=mysqli_fetch_array($this->lista))
				{ 
					$sel = '' ;
					if ( $reg[$this->posicion_codigo] == $this->valor ) 
						{
						$sel = 'selected' ;
						}
					$txt=$txt.'  <option value="'.$reg[$this->posicion_codigo].'" '.$sel.'>'.$reg[$this->posicion_descrip].'</option> ' ;
				}
				$txt=$txt.'  </select> ' ;
				//
				//	
				$txt=$txt.'</td>';

			}
		elseif($this->tipo == 'textarea')
			{
				$txt=$txt.'<td>' ;
				$txt=$txt.'<textarea ';
				$txt=$txt.' name="'.$this->nombre.'" ' ;
				$txt.='rows="4" cols="50">';
				$txt.=$this->valor;
				$txt.='</textarea>'; 
				$txt=$txt.'</td>';
			}
		else
			{
				$txt=$txt.'<td>' ;
				$txt=$txt.'<input ' ;
				$txt=$txt.' name="'.$this->nombre.'" ' ;
				$txt=$txt.' type="'.$this->tipo.'" ' ;
				$txt=$txt.' value="'.$this->valor.'" ' ;
				$txt=$txt.$this->autofocus.' ' ;
				$txt=$txt.'>' ;
				$txt=$txt.'</td>';
			}
		return $txt ;
	}
	public function txtMostrarOculto()
	{
				$txt='';
				if ( $this->en_celda ) $txt.='<td>' ;
				$txt.='<input ' ;
				$txt.=' name="'.$this->nombre.'" ' ;
				$txt.=' type="hidden" ' ;
				$txt.=' value="'.$this->valor.'" ' ;
				$txt.='>' ;
				if ( $this->en_celda ) $txt.='</td>';
		return $txt ;
	}
	public function txtMostrarOcultoyEtiqueta()
	{
				$txt='';
				$txt.='<td>' ;
				$txt.='<input ' ;
				$txt.=' name="'.$this->nombre.'" ' ;
				$txt.=' type="hidden" ' ;
				$txt.=' value="'.$this->valor.'" ' ;
				$txt.='>' ;
				$txt.=$this->valor ;
				$txt.='</td>';
		return $txt ;
	}
	public function txtMostrarEtiqueta()
	{
		$txt = '';
		$txt=$txt.'<td>' ;
		$txt=$txt.$this->valor ;
		$txt=$txt.'</td>';
		return $txt ;
	}
	public function Mostrar ( $etiqueta, $nombre, $valor )
	{
	echo '<tr>' ;
	echo '<td>' ;
	echo $etiqueta ;
	echo '</td>';
	echo '<td colspan="'.$this->colspan_dato.'">' ;
	echo '<input type="text" ' ; 
	echo 'name="m_'.$nombre.'" ' ; 
	echo 'value="'.$valor.'" '.$this->autofocus.' >' ;
	echo '</td>';
	echo '</tr>';
	$this->saca_autofocus();
			
	}
	public function MostrarComentario ( $etiqueta, $nombre, $valor )
	{
	echo '<tr>' ;
	echo '<td>' ;
	echo $etiqueta ;
	echo '</td>';
	echo '<td colspan="'.$this->colspan_dato.'">' ;
	echo '<textarea  ' ; 
	echo 'name="m_'.$nombre.'" rows="5" cols="50"> ' ; 
	echo $valor ;
	echo '</textarea>';
	echo '</td>';
	echo '</tr>';

	}
	public function MostrarFecha ( $etiqueta, $nombre, $valor )
	{
	echo '<tr>' ;
	echo '<td>' ;
	echo $etiqueta ;
	echo '</td>';
	echo '<td colspan="'.$this->colspan_dato.'">' ;
	echo '<input type="date" ' ; 
	echo 'name="m_'.$nombre.'" ' ; 
	echo 'value="'.$valor.'" '.$this->autofocus.'>' ;
	echo '</td>';
	echo '</tr>';
			
	}
}

?>
