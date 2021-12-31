<?php
    error_reporting(0);
    include 'conexion.php';
    include 'funciones.php';

    $bd = new MYSQLIFunctions();
    $funciones = new funciones();
    $tipo = $_POST['tipo'];
    date_default_timezone_set('America/Mexico_City');
    $hoy=date('Y-m-d');
    $hora=date('H:i:s');
    $fechaHora=date('Y-m-d H:i:s', time());
    $meses = array("01"=>"Enero", "02"=>"Febrero", "03"=>"Marzo" , "04"=>"Abril" , "05"=>"Mayo" , "06"=>"Junio" , "07"=>"Julio" , "08"=>"Agosto" , "09"=>"Septiembre" , "10"=>"Octubre" , "11"=>"Noviembre", "12"=>"Diciembre");
    $diasSemana = array("Domingo", "Lunes", "Martes", "Miércoles" , "Jueves" , "Viernes" , "Sábado");

     if($tipo == 'registrarSala'){
        $nombre=$_POST['nombre'];
        $capacidad=$_POST['capacidad'];

        $exist=$bd->select("SELECT Id as variable FROM `salas` WHERE Nombre='".$nombre."'")['variable'];
        if($exist>0){
            echo "exist";
            return;
        }

        $insert="INSERT INTO `salas` (`Nombre`, `Capacidad`, `Estatus`) VALUES ('".$nombre."', '".$capacidad."', 1)";
        if($bd->query($insert)){
            echo "ok";
        }
    }

    else if($tipo == 'tablaSalas'){
        $x=0;
        $array = array();
        $consulta=$bd->query("SELECT * FROM salas ORDER BY Nombre ASC");
        while($res=$bd->fassoc($consulta)){
            $array[$x]['nombre'] = $res['Nombre'];
            $array[$x]['capacidad'] = $res['Capacidad'];
            if($res['Estatus']==1) $array[$x]['estatus']='<span class="text-success">Disponible</span>';
            if($res['Estatus']==0) $array[$x]['estatus']='<span class="text-warning">En Mantenimiento</span>';
            $array[$x]['edit'] = '<a onclick="infoEditaSala('.$res['Id'].')" data-toggle="modal" data-target="#editSalaModal" class="btn btn-primary shadow btn-xs sharp mr-1"><i class="fa fa-pen" style="padding-top:3px"></i></a>';
            $array[$x]['delete'] = '<a onclick="eliminarSala('.$res['Id'].')" class="btn btn-danger shadow btn-xs sharp mr-1" title="Eliminar Sala"><i class="fa fa-trash-alt" style="padding-top:3px"></i></a>';
            $x++;
        }
        echo json_encode($array);
    }

    else if($tipo == 'infoEditaSala'){
        $id=$_POST['id'];
        $dire=0;
        $array = array();
        $consulta=$bd->query("SELECT * FROM salas WHERE Id='".$id."' ");
        while($res=$bd->fassoc($consulta)){
            $array[1]['nombre'] = $res['Nombre'];
            $array[1]['id'] = $res['Id'];
            $array[1]['capacidad'] = $res['Capacidad'];
            $array[1]['estatus'] = $res['Estatus'];

        }
        echo json_encode($array);
    }

    else if($tipo == 'editaSala'){
        $nombre=$_POST['nombre'];
        $id=$_POST['id'];
        $capacidad=$_POST['capacidad'];
        $estatus=$_POST['estatus'];

        $exist=$bd->select("SELECT Id as variable FROM `salas` WHERE Nombre='".$nombre."' and Id!='".$id."' ")['variable'];
        if($exist>0){
            echo "exist";
            return;
        }

        $query="UPDATE `salas` SET `Nombre`='".$nombre."', `Capacidad`='".$capacidad."', `Estatus`='".$estatus."' WHERE Id='".$id."' ";
        if($bd->query($query)){
            echo "ok";
        }
    }

    else if($tipo == 'salasSelect'){
        $x=0;
        $array = array();
        $consulta=$bd->query("SELECT Id, Nombre FROM salas WHERE Estatus>0 ");
        while($res=$bd->fassoc($consulta)){
            $array[$x]['Id'] = $res['Id'];
            $array[$x]['Nombre'] = $res['Nombre'];
            $x++;
        }

        echo json_encode($array);
    }

    else if($tipo == 'eliminarSala'){
        $id = $_POST['id'];
        $del="DELETE FROM `salas` WHERE Id='".$id."'";
        if($bd->query($del)) {
            $array[0]['respuesta'] = 'ok';
        }
        echo json_encode($array);
    }

    else if ($tipo == 'calendarioRes') {
            $desde = $_POST['desde'];
            $hasta = $_POST['hasta'];
            $array = array();
            $x = 0;
            $minHora = $bd->select("SELECT MIN(HoraI) as variable FROM clases WHERE Empresa='".$empresa."' AND Sucursal='".$sucursal."' AND Fecha>='".$desde."' AND Fecha<='".$hasta."' AND Estatus>0 ")['variable'];
            $consulta = $bd->query("SELECT Id, Clase, Fecha, HoraI, HoraT, Cupo FROM clases WHERE Empresa='".$empresa."' AND Sucursal='".$sucursal."' AND Fecha>='".$desde."' AND Fecha<='".$hasta."' AND Estatus>0 ");
            while ($res = $bd->fassoc($consulta)) {
                $clase = $bd->select("SELECT Valor, Descripcion, Atributo2 FROM catalogos WHERE Tipo='Clase' AND Id='".$res['Clase']."' ");
                $reservados = $bd->select("SELECT COUNT(Id) as variable FROM `relaciones` WHERE Tipo='ClaseAtleta' and Valor1='".$res['Id']."' and Empresa='".$empresa."' and Estatus=1")['variable'];

                $start = $res['Fecha'].' '.$res['HoraI'];
                $end   = $res['Fecha'].' '.$res['HoraT'];

                $array[$x]['id']        = $res['Id'];
                $array[$x]['title']     = utf8_encode($clase['Valor'].' ('.$reservados.' de '.$res['Cupo'].')');
                $array[$x]['start']     = $start;
                $array[$x]['end']       = $end;
                $array[$x]['minHora']   = $minHora;
                $array[$x]['color']     = $clase['Atributo2'];
                $array[$x]['textColor'] = "#FFFFFF";

                $x++;
            }
            echo json_encode($array);
        }




?>
