<?php
    class MYSQLIfunctions{
        private $conexion;

        private $server = "localhost";
        private $usuario = "root";
        private $contrasena = "";
        private $base = "bd_salas";

        public function __construct(){
            $this->conexion = mysqli_connect($this->server, $this->usuario, $this->contrasena, $this->base) or die("Error (Conexión): ".mysqli_connect_error());
        }

        public function __destruct(){
            mysqli_close($this->conexion);
        }

        public function query($consulta){
            $resultado = mysqli_query($this->conexion, $consulta);

            if(!$resultado){
                 echo "Error: ".mysqli_error($this->conexion);
            }
            return $resultado;
        }

        public function select($consulta){
            $resultado = mysqli_query($this->conexion, $consulta);
            $arrayAsociativo = array();
            while($res = mysqli_fetch_assoc($resultado)){
                $arrayAsociativo = $res;
            }
            return $arrayAsociativo;
        }

        public function arraySelect($consulta){
            $resultado = mysqli_query($this->conexion, $consulta);
            //$arrayAsociativo = array();
            while($res = mysqli_fetch_assoc($resultado)){
                $arrayAsociativo[] = $res;
            }
            return $arrayAsociativo;
        }

        public function rows($consulta){
            return mysqli_num_rows($consulta);
        }

        public function farray($consulta){
            return mysqli_fetch_array($consulta);
        }

        public function fassoc($consulta){
            return mysqli_fetch_assoc($consulta);
        }

        public function lastid(){
            return mysqli_insert_id($this->conexion);
        }

        public function escapestr($str){
            return mysqli_real_escape_string($this->conexion, $str);
        }
    }
?>
