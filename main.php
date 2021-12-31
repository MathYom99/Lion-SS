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
        $color=$_POST['color'];

        $exist=$bd->select("SELECT Id as variable FROM `salas` WHERE Nombre='".$nombre."'")['variable'];
        if($exist>0){
            echo "exist";
            return;
        }

        $insert="INSERT INTO `salas` (`Nombre`, `Capacidad`, `Color`, `Estatus`) VALUES ('".$nombre."', '".$capacidad."', '".$color."', 1)";
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
            $array[$x]['color']='<i class="fa fa-square" style="padding-top:3px; color: '.$res['Color'].'; text-shadow: 0 0 3px #000;"></i>';
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
            $array[1]['color'] = $res['Color'];

        }
        echo json_encode($array);
    }

    else if($tipo == 'editaSala'){
        $nombre=$_POST['nombre'];
        $id=$_POST['id'];
        $capacidad=$_POST['capacidad'];
        $color=$_POST['color'];

        $exist=$bd->select("SELECT Id as variable FROM `salas` WHERE Nombre='".$nombre."' and Id!='".$id."' ")['variable'];
        if($exist>0){
            echo "exist";
            return;
        }

        $query="UPDATE `salas` SET `Nombre`='".$nombre."', `Capacidad`='".$capacidad."', `Color`='".$color."' WHERE Id='".$id."' ";
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

    else if($tipo == 'registraRes'){
       $sala=$_POST['sala'];
       $fecha=$_POST['fecha'];
       $horaI=$_POST['horaI'];
       $horaT=$_POST['horaT'];

       $exist=$bd->select("SELECT Id as variable FROM `reservaciones` WHERE Sala='".$sala."' and Fecha='".$fecha."' AND (('".$horaI."'>=HoraI AND '".$horaT."'<=HoraT) OR ('".$horaI."'>=HoraI AND '".$horaI."'<=HoraT) OR ('".$horaT."'>=HoraI AND '".$horaT."'<=HoraT) OR (HoraI>='".$horaI."' and HoraT<='".$horaT."')) and Estatus>0")['variable'];
       if($exist>0){
           echo "exist";
           return;
       }

       $insert="INSERT INTO `reservaciones` (`Sala`, `Fecha`, `HoraI`, `HoraT`, `FechaRegistro`, `Estatus`) VALUES ('".$sala."', '".$fecha."', '".$horaI."', '".$horaT."', '".$hoy."', 1)";
       if($bd->query($insert)){
           echo "ok";
       }
   }

   else if ($tipo == 'calendarioRes') {
           $desde = $_POST['desde'];
           $hasta = $_POST['hasta'];
           $array = array();
           $x = 0;
           $minHora = $bd->select("SELECT MIN(HoraI) as variable FROM reservaciones WHERE Fecha>='".$desde."' AND Fecha<='".$hasta."' ")['variable'];
           $consulta = $bd->query("SELECT * FROM reservaciones WHERE Fecha>='".$desde."' AND Fecha<='".$hasta."'");
           while ($res = $bd->fassoc($consulta)) {
               $sala = $bd->select("SELECT Nombre, Color FROM salas WHERE Id='".$res['Sala']."' ");

               $start = $res['Fecha'].' '.$res['HoraI'];
               $end   = $res['Fecha'].' '.$res['HoraT'];

               $array[$x]['id']        = $res['Id'];
               $array[$x]['title']     = $sala['Nombre'];
               $array[$x]['start']     = $start;
               $array[$x]['end']       = $end;
               $array[$x]['minHora']   = $minHora;
               $array[$x]['estatus']   = $res['Estatus'];
               $array[$x]['textColor'] = "#FFFFFF";
               $array[$x]['color']     = $sala['Color'];
               if($res['Estatus']==0)$array[$x]['color']     = "grey";

               $x++;
           }
           echo json_encode($array);
       }

       else if($tipo == 'infoRes'){
           $id=$_POST['id'];
           $array = array();
           $consulta=$bd->query("SELECT * FROM reservaciones WHERE Id='".$id."' ");
           while($res=$bd->fassoc($consulta)){
               $sala = $bd->select("SELECT Nombre, Capacidad FROM salas WHERE Id='".$res['Sala']."' ");
               $array[1]['nombre'] = $sala['Nombre'];
               $array[1]['capacidad'] = $sala['Capacidad'];
               $array[1]['fecha'] = $funciones->fecha_gr($res['FechaRegistro']);
               $array[1]['estatus'] = $res['Estatus'];

           }
           echo json_encode($array);
       }

       else if($tipo == 'liberarSala'){
           $id=$_POST['id'];

           $query="UPDATE `reservaciones` SET `Estatus`='0' WHERE Id='".$id."' ";
           if($bd->query($query)){
               echo "ok";
           }
       }

       else if($tipo == 'liberarAuto'){
           $array = array();
           $libre='no';
           $consulta=$bd->query("SELECT * FROM reservaciones WHERE Estatus>0 ");
           while($res=$bd->fassoc($consulta)){
             if($res['Fecha']<$hoy){
               $query="UPDATE `reservaciones` SET `Estatus`='0' WHERE Id='".$res['Id']."' ";
               if($bd->query($query)){
                   $libre='libre';
               }
             }else if($res['Fecha']==$hoy && $res['HoraT']<=$hora){
               $query="UPDATE `reservaciones` SET `Estatus`='0' WHERE Id='".$res['Id']."' ";
               if($bd->query($query)){
                   $libre='libre';
               }
             }

           }
           echo $libre;
       }





?>
