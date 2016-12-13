<?php

include_once 'clase_campo.php';
include_once 'clase_entidad.php';
include_once 'clase_entidadi.php';

class Cabecera {
  private $titulo;
  public function __construct($tit)
  {
    $this->titulo=$tit;
  }
  public function graficar()
  {
    echo '      <table class="tablaext" width="98%" >'."\n";
	echo '                <tr><th style="text-align:center"><h3>'.$this->titulo.'</h3></th></tr>'."\n";
    echo '                <tr><td align="center">'."\n";
    echo '                      <table class="tablacert">' ;
    //echo '<tr><th style="text-align:center"><h2>'.$this->titulo.'</h2></th></tr>';
    echo '<tr><td align="center">';
    echo '<table class="tabladet">' ;
  }
  public function graficar_nb()
  {
    echo '<table class="tablaext" width="98%" >';
	echo '<tr><th style="text-align:center"><h3>'.$this->titulo.'</h3></th></tr>';
    echo '<tr><td align="center">';
    echo '<table class="tablacert">' ;
    //echo '<tr><th style="text-align:center"><h2>'.$this->titulo.'</h2></th></tr>';
    echo '<tr><td align="center">';
    echo '<table class="tabladetnb">' ;
  }
}

class Cuerpo {
  private $lineas=array();
  public function insertarParrafo($li)
  { 
    $this->lineas[]=$li;
  }
  public function graficar()
  {
    for($f=0;$f<count($this->lineas);$f++)
    {
      echo '<tr><td>'.$this->lineas[$f].'</td></tr>';
    }
  }
}

class Pie {
  private $titulo;
  public function __construct($tit)
  {
    $this->pie=$tit;
  }
  public function graficar()
  {
    echo '<tr><td style="text-align:center">'.$this->pie.'</td></tr>';
    echo '</table>';
		echo '</td></tr>';
    echo '</table>';
    echo '</td></tr>';
    echo '</table>';
    
  }
}

class Pagina {
  private $cabecera;
  private $cuerpo;
  private $pie;
  public function __construct($texto1,$texto2)
  {
    $this->cabecera=new Cabecera($texto1);
    $this->cuerpo=new Cuerpo();
    $this->pie=new Pie($texto2);
  }
  public function insertarCuerpo($texto)
  {
    $this->cuerpo->insertarParrafo($texto);
  }
  public function graficar()
  {
    $this->cabecera->graficar();
    $this->cuerpo->graficar();
    $this->pie->graficar();
  }
	public function graficar_cuerpo()
	{
	$this->cuerpo->graficar();
	}
	public function graficar_cabecera()
	{
	$this->cabecera->graficar();
	}
	public function graficar_pie()
	{
	$this->pie->graficar();
	}
}


class Fecha {
	private $fecha;
	public function tohm($fechadb)
		{
			if ($fechadb)
			{
			//$this->cargar($fechadb);
			//return $this->fecha->Format('H:i');
			list($yy,$mm,$dd)=explode("-",$fechadb) ;
			list($dd,$hora)=explode(" ",$dd);
			list($hh,$mn,$ss)=explode(":",$hora);
			return $hh.':'.$mn ;
			}
			else
			{
			return " ";
			}
		}
	public function todmy($fechadb)
		{
			if ($fechadb)
				{
				//$this->cargar($fechadb);
				//return $this->fecha->Format('d-m-y');
				list($yy,$mm,$dd)=explode("-",$fechadb) ;
				list($dd,$hora)=explode(" ",$dd);
				return $dd.'-'.$mm.'-'.$yy ;
				}
			else
				{
				return " " ;
				}
		}
	protected function cargar($fechadb)
		{
		if ( Is_null($fechadb) )
			{
				$this->fecha = Null ;
			}
		else
			{
				list($yy,$mm,$dd)=explode("-",$fechadb);
				list($dd,$hora)=explode(" ",$dd);
				$fecha = new DateTime();
				$fecha->setDate($yy, $mm, $dd);
				//$this->fecha = $fecha->format('d-m-Y') ;
				//$this->fecha = $dd.'-'.$mm.'-'.$yy ;
			} 
		}
}


class Registro {
	public function Abrir()
		{
		echo '<tr>';
		}
	public function Cerrar()
		{
		echo '</tr>';
		}
}
class Celda {
	public function Mostrar( $nombre, $valor )
	{
	echo '<td>' ;
	echo '<input type="text" ' 		; 
	echo 'name="m_'.$nombre.'" ' 	; 
	echo 'value="'.$valor.'">' 		;
	echo '</td>';
	}
	public function Mostrar_Etiqueta($valor)
	{
	echo '<td>' ;
	echo $valor ;
	echo '</td>';
	}
	public function Mostrar_Vacia()
	{
	echo '<td>' ;
	echo '</td>';
	}
	public function Mostrar_Fk( $nombre, $valor , $regs , $poscod, $posdes, $con_blanco)
	{
		echo '<td>' ;
		//
		// Lista Desplegable
		echo '<select name="'.$nombre.'">' ;
		while ($reg=mysqli_fetch_array($regs))
		{ 
			$sel = '' ;
			if ( $reg[$poscod] == $valor ) 
				{
				$sel = 'selected' ;
				echo '  <option value="'.$reg[$poscod].'" '.$sel.'>'.$reg[$posdes].'</option> ' ;
				}
		}
		echo '  </select> ' ;
		//
		//	
		echo '</td>';
	}
	public function Mostrar_Desplegable( $nombre, $valor , $regs , $poscod, $posdes, $con_blanco)
	{
		echo '<td>' ;
		//
		// Lista Desplegable
		echo '<select name="'.$nombre.'">' ;
		if ( $con_blanco == true )											// dz 2015_10_09 -->
			{
				echo '  <option value="">-----</option> ' ;
			}																							// dz 2015_10_09 <--
		while ($reg=mysqli_fetch_array($regs))
		{ 
			$sel = '' ;
			if ( $reg[$poscod] == $valor ) 
				{
				$sel = 'selected' ;
				}
			echo '  <option value="'.$reg[$poscod].'" '.$sel.'>'.$reg[$posdes].'</option> ' ;
		}
		echo '  </select> ' ;
		//
		//	
		echo '</td>';
	}
}


?>
