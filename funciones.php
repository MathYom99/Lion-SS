<?php
class funciones{

	function asistenciaClases($idClase){
		$empresa = $_SESSION['empresa'];
		$bd = new MYSQLIFunctions();
		$reservados = $bd->select("SELECT COUNT(Id) as variable FROM `relaciones` WHERE Tipo='ClaseAtleta' and Valor1='".$idClase."' and Empresa='".$empresa."' and Estatus=1")['variable'];
		$cupo = $bd->select("SELECT Cupo AS cupo FROM clases WHERE Id='".$idClase."' ")['cupo'];
		$disponibles=$cupo-$reservados;
		if($disponibles<1)$color='danger';
		else if($disponibles<=3)$color='warning';
		else $color='success';
		$cupo='<span class="text-'.$color.'">'.$reservados.' de '.$cupo.'</span>';
		return $cupo;

	}

	function optimizar_imagen($origen, $destino, $calidad) {

		$info = getimagesize($origen);

		if ($info['mime'] == 'image/jpeg'){
			$imagen = imagecreatefromjpeg($origen);
		}
		else if ($info['mime'] == 'image/gif'){
			$imagen = imagecreatefromgif($origen);
		}
			else if ($info['mime'] == 'image/png'){
			$imagen = imagecreatefrompng($origen);
		}

		imagejpeg($imagen, $destino, $calidad);
		return $destino;
	}

	function fecha_ch($fecha){
		if($fecha!="0000-00-00" and $fecha!=""){
		$particion=explode("-",$fecha);
		$newFecha=$particion[2].'/'.$particion[1].'/'.$particion[0];
		}else{
			$newFecha="";
		}
		return $newFecha;
	}

	public static function mesCH($mes){
		$meses=["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
		$siglas=$meses[$mes-1];
		return $siglas;
	}

	function edad($fechaN){
	  list($ano,$mes,$dia) = explode("-",$fechaN);
	  $ano_diferencia  = date("Y") - $ano;
	  $mes_diferencia = date("m") - $mes;
	  $dia_diferencia   = date("d") - $dia;
	  if ($dia_diferencia < 0 || $mes_diferencia < 0)
	    $ano_diferencia--;
	  return $ano_diferencia;
	}

	function fecha_md($fecha){
		if($fecha!="0000-00-00" and $fecha!=""){
		$particion=explode("-",$fecha);
		$newFecha=$particion[2].' '.self::mesCH($particion[1]).' '.$particion[0];
		}else{
			$newFecha="";
		}
		return $newFecha;
	}

	public static function mesCom($mes){
		$meses=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
		$newMes=$meses[$mes-1];
		return $newMes;
	}

	function fecha_gr($fecha){
		if($fecha!="0000-00-00" and $fecha!=""){
		$particion=explode("-",$fecha);
		$newFecha=$particion[2].' de '.self::mesCom($particion[1]).' de '.$particion[0];
		}else{
			$newFecha="";
		}
		return $newFecha;
	}

	function fecha_DiaMes($fecha){
		if($fecha!="0000-00-00" and $fecha!=""){
		$particion=explode("-",$fecha);
		$newFecha=$particion[2].' '.self::mesCom($particion[1]);
		}else{
			$newFecha="";
		}
		return $newFecha;
	}

	function fecha_normal($fecha){
		if($fecha!="0000-00-00" and $fecha!=""){
		$particion=explode("-",$fecha);
		$newFecha=$particion[2].' '.self::mesCom($particion[1]).' '.$particion[0];
		}else{
			$newFecha="";
		}
		return $newFecha;
	}

	function nombreDia($fecha){
		$fechats = strtotime($fecha);
		switch (date('w', $fechats)){
			case 0: return "Domingo"; break;
			case 1: return "Lunes"; break;
			case 2: return "Martes"; break;
			case 3: return "Miercoles"; break;
			case 4: return "Jueves"; break;
			case 5: return "Viernes"; break;
			case 6: return "Sabado"; break;
		}
	}

	function diaSemana($fecha, $diaPago){
		$fecha = strtotime($fecha);
		$fecha = date('w', $fecha);
		return $diaPago - $fecha;
	}

	function cadenaRandom($tam){
		$caracteres='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$longpalabra=$tam;
		for($cadena='', $n=strlen($caracteres)-1; strlen($cadena) < $longpalabra;){
			$x = rand(0,$n);
			$cadena.= $caracteres[$x];
		}
		return $cadena;
	}

	function dias_transcurridos($fechaI, $fechaF){
			$date1 = new DateTime($fechaI);
			$date2 = new DateTime($fechaF);
			$diff = $date1->diff($date2);
			return $diff->days;
	}

	function tiempoDiferencia($fechaI, $fechaT){ // No importa orden de fechas
        $time = (strtotime($fechaT)-strtotime($fechaI))/60;
        $time = abs($time);
        $time = floor($time);
				if($time>=1440){
        	$dias = floor($time/1440);
        	$tiempo='Hace <strong>'.$dias.' d&iacute;as</strong>';
        }else if($time>=60){
            $horas=floor($time/60);
            $minutosRestantes=$time-($horas*60);
            $tiempo='Hace <strong>'.$horas.' horas</strong>';
        }else{
            $tiempo='Hace <strong>'.$time.' minutos</strong>';
        }
        return $tiempo;
  	}

	function fechaNormal($fecha){
		$meses = array("01"=>"Enero", "02"=>"Febrero", "03"=>"Marzo" , "04"=>"Abril" , "05"=>"Mayo" , "06"=>"Junio" , "07"=>"Julio" , "08"=>"Agosto" , "09"=>"Septiembre" , "10"=>"Octubre" , "11"=>"Noviembre", "12"=>"Diciembre");
		$newFecha = explode('-', $fecha);

		if($fecha!="") return $newFecha[2].' de '.$meses[$newFecha[1]].' '.$newFecha[0];
		else return "";
	}

	function horaCorta($hora){
		if($hora!="") $horaCorta = date("h:i a", strtotime($hora));
		else $horaCorta = "";
		return $horaCorta;
	}

	function numberFormat($number, $noDecimales){
		return number_format($number, $noDecimales , ".", ",");
	}

	function dosFechas($desde, $hasta){
		$particionD = explode("-", $desde);
		$particionH = explode("-", $hasta);
		if($desde == $hasta){ //misma fecha
			$newFecha = $particionD[2].' '.self::mesCom($particionD[1]).' '.$particionD[0];
		}else if($particionD[0]==$particionH[0] && $particionD[1]==$particionH[1]){ //misma mes y mismo año
			$newFecha = $particionD[2].' al '.$particionH[2].' '.self::mesCom($particionD[1]).' '.$particionD[0];
		}else if($particionD[0]==$particionH[0]){  //mismo año
			$newFecha = $particionD[2].' '.self::mesCom($particionD[1]).' al '.$particionH[2].' '.self::mesCom($particionH[1]).' '.$particionD[0];
		}else{ //ninguna de las anteriores
			$newFecha = $particionD[2].' '.self::mesCom($particionD[1]).' '.$particionD[0].' al '.$particionH[2].' '.self::mesCom($particionH[1]).' '.$particionH[0];
		}

		return $newFecha;
	}

	function siguienteDiaSemana($fechaInicial, $operador, $dia){
		for ($i=0; $i < 7; $i++) {
			$aux = date("Y-m-d", strtotime($fechaInicial." ".$operador.$i." days"));
			$w = strtotime($aux);
			$w = date('w', $w);
			if($w == $dia) $nuevaFecha = $aux;
		}
		return $nuevaFecha;
	}

}
?>
