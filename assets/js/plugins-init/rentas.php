<?php
    error_reporting(0);
    session_start();
    include 'conexion.php';
    $bd = new MYSQLIFunctions();
    include 'funciones.php';
    $funciones = new funciones();

    if(!$funciones->validaSesion()){
        ?><script type="text/javascript">
            location.href='login.php';
        </script><?php
        exit;
    }

    $modulo=basename($_SERVER['PHP_SELF']);
    $moduloName= str_replace(".php", "", $modulo);
	$_SESSION['modulo']=$moduloName;
	$consulta=$bd->query("SELECT permisos.Id FROM permisos INNER JOIN modulos ON modulos.Modulo='".$moduloName."' and permisos.Usuario=".$_SESSION['user']." and permisos.Modulo=modulos.Id");
	if($bd->rows($consulta)==0){
	  	header("Location: index.php");
	  	exit;
	}

    $idUser = $_SESSION['user'];
    $empresa = $_SESSION['empresa'];
    $sucursal = $_SESSION['sucursal'];
    $cuentaSel = $_SESSION['cuenta'];
    date_default_timezone_set('America/Mexico_City');
    $hoy=date('Y-m-d');
    $particion = explode("-", $hoy);
    $year = $particion[0];
    $primerDiaMes = $particion[0].'-'.$particion[1].'-01';
?>
<html lang="es">
  <head>
        <!--=============== basic  ===============-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>Rentas | Olivia</title>
        <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon.png">
        <script src="assets/js/sha256.js"></script>
        <!--=============== css  ===============-->
        <link href="assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
        <link href="assets/css/style.css?v=<?php echo rand(); ?>" rel="stylesheet">
        <link href="assets/css/estilos.css?v=<?php echo rand(); ?>" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
        <link href="assets/vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
        <!--=============== datatable  ===============-->
        <link href="assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
        <!--=============== fontawesome  ===============-->
        <link rel="stylesheet" href="assets/vendor/font-awesome/css/all.css">
        <!--=============== LIGHTBOX ===============-->
        <link rel="stylesheet" href="assets/vendor/lightbox/src/css/lightbox.css">
        <script src="assets/vendor/lightbox/src/js/lightbox.js" defer></script>
        <!-- Daterange picker -->
        <link href="assets/vendor/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
        <!-- Pick date -->
        <link rel="stylesheet" href="assets/vendor/pickadate/themes/default.css">
        <link rel="stylesheet" href="assets/vendor/pickadate/themes/default.date.css">

  </head>

  <body onload="validaPermiso(1, 'optionInsert'), setInterval('validaSesionActiva()', 60000)">

      <!--Preloader-->
      <div id="preloader">
          <div class="sk-three-bounce">
              <div class="sk-child sk-bounce1"></div>
              <div class="sk-child sk-bounce2"></div>
              <div class="sk-child sk-bounce3"></div>
          </div>
      </div>

      <!--Main wrapper start-->
      <div id="main-wrapper">

          <!--menú -->
          <?php  include 'menu.php' ?>
          <!--menú -->

            <!--Content body start-->
            <div class="content-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Rentas</h4>
                                    <button type="button" class="btn btn-square btn-outline-primary" style="padding: 0.5rem 1.2rem;" title="Lista de clientes" data-toggle="modal" data-target="#clientes" onclick="tablaClientes()"><i class="fa fa-address-book fa-lg mb-1"></i><br>Clientes</button>
                                    <button type="button" class="btn btn-square btn-outline-info" style="padding: 0.5rem 1.2rem;" title="Lista de clientes" data-toggle="modal" data-target="#pagos" ><i class="fa fa-cash-register fa-lg mb-1"></i><br>Pagos & Abonos</button>
                                    <button type="button" class="btn light btn-success pull-right optionInsert" data-toggle="modal" data-target="#nuevaRenta" onclick="">Nueva Renta <i class="fa fa-plus"></i></button>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
              </div>
          </div>
          <!--Content body end-->
      </div>
      <!--Main wrapper end-->

      <!-- MODALES -->



      <!-- Clientes  -->
      <div class="modal fade bd-example-modal-xl" id="clientes" tabindex="1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-xl">
              <div class="modal-content">
                  <div class="modal-header">
                      <h3 class="modal-title">Mis Clientes</h3>
                      <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">

                     <div class="row">
                         <div class="col-lg-2">
                         </div>
                         <div class="col-lg-2">
                         </div>
                         <div class="col-lg-8">
                           <button type="button" class="btn light btn-success pull-right optionInsert" data-toggle="modal" data-target="#nuevoCliente">Nuevo Cliente <i class="fa fa-plus"></i></button>
                         </div>

                         <div class="table-responsive">
                           <table id="tablaClientes" class="table display min-w850">
                           </table>
                         </div>

                     </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-light light" data-dismiss="modal">Cerrar</button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Nuevo Cliente -->
      <div class="modal fade bd-example-modal-xl optionInsert" id="nuevoCliente" tabindex="1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-md">
              <div class="modal-content">
                  <div class="modal-header">
                      <h3 class="modal-title">Nuevo Cliente</h3>
                      <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-row">
                          <div class="form-group col-lg-12">
                              <label>Nombre: <span class="text-danger">*</span></label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-id-card"></i> </span></div>
                                  <input type="text" id="nombreCliente" class="form-control" placeholder="Nombre del cliente" maxlength="300">
                              </div>
                              <span class="text-danger" id="spanNombreCliente" style="display: none;"></span>
                          </div>
                          <div class="form-group col-lg-12">
                              <label>Teléfono: </label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-phone-alt"></i> </span></div>
                                  <input type="text" id="telCliente" class="form-control" placeholder="Teléfono del cliente">
                              </div>
                          </div>
                          <div class="form-group col-lg-12">
                              <label>Observaciones: </label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-align-justify"></i> </span></div>
                                  <textarea rows="2" cols="50" class="form-control" id="obsCliente" placeholder="Observaciones" spellcheck="false"></textarea>
                              </div>
                          </div>

                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-light light" data-dismiss="modal">Cancelar</button>
                      <button type="button" class="btn btn-success" id="btnRegistraCliente" onclick="registraCliente(nombreCliente.value, telCliente.value, obsCliente.value)">Registrar Cliente</button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Edita Cliente -->
      <div class="modal fade bd-example-modal-xl optionInsert" id="editClienteModal" tabindex="1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-md">
              <div class="modal-content">
                  <div class="modal-header">
                      <h3 class="modal-title">Editar Cliente</h3>
                      <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <input type="hidden" id="idClienteE">
                      <div class="form-row">
                          <div class="form-group col-lg-12">
                              <label>Nombre: <span class="text-danger">*</span></label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-id-card"></i> </span></div>
                                  <input type="text" id="nombreClienteE" class="form-control" placeholder="Nombre del cliente" maxlength="300">
                              </div>
                              <span class="text-danger" id="spanNombreClienteE" style="display: none;"></span>
                          </div>
                          <div class="form-group col-lg-12">
                              <label>Teléfono: </label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-phone-alt"></i> </span></div>
                                  <input type="text" id="telClienteE" class="form-control" placeholder="Teléfono del cliente">
                              </div>
                          </div>
                          <div class="form-group col-lg-12">
                              <label>Observaciones: </label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-align-justify"></i> </span></div>
                                  <textarea rows="2" cols="50" class="form-control" id="obsClienteE" placeholder="Observaciones" spellcheck="false"></textarea>
                              </div>
                          </div>
                          <div class="form-group col-lg-12">
                              <label>Estatus:</label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-eye"></i> </span></div>
                                  <select class="form-control" id="estatusClienteE">
                        						<option value="1">Activo</option>
                        						<option value="0">Inactivo</option>
                        				  </select>
                              </div>
                          </div>

                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-light light" data-dismiss="modal">Cancelar</button>
                      <button type="button" class="btn btn-success" id="btnEditaCliente" onclick="editaCliente(idClienteE.value, nombreClienteE.value, telClienteE.value, obsClienteE.value, estatusClienteE.value)">Guardar Cambios</button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Nuevo Cliente -->
      <div class="modal fade bd-example-modal-xl optionInsert" id="nuevaRenta" tabindex="1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-xl">
              <div class="modal-content">
                  <div class="modal-header">
                      <h3 class="modal-title">Nueva Renta</h3>
                      <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-row">
                          <div class="form-group col-lg-4">
                              <label>No. de Recibo: </label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-id-card"></i> </span></div>
                                  <input type="text" id="noRecibo" class="form-control" placeholder="No. de Recibo">
                              </div>
                          </div>
                          <div class="form-group col-lg-8">
                              <label>Fecha Renta: </label>
                              <div class="input-group input-info-o">
                                  <input class="form-control input-limit-datepicker" type="text" name="daterange" value="06/01/2021 - 06/07/2021">
                              </div>
                          </div>

                          <div class="form-group col-lg-12">
                              <label>Teléfono: </label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-phone-alt"></i> </span></div>
                                  <input type="text" id="telCliente" class="form-control" placeholder="Teléfono del cliente">
                              </div>
                          </div>
                          <div class="form-group col-lg-12">
                              <label>Observaciones: </label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-align-justify"></i> </span></div>
                                  <textarea rows="2" cols="50" class="form-control" id="obsCliente" placeholder="Observaciones" spellcheck="false"></textarea>
                              </div>
                          </div>

                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-light light" data-dismiss="modal">Cancelar</button>
                      <button type="button" class="btn btn-success" id="btnRegistraCliente" onclick="registraCliente(nombreCliente.value, telCliente.value, obsCliente.value)">Registrar Cliente</button>
                  </div>
              </div>
          </div>
      </div>



      <!-- Scripts -->
      <script src="assets/vendor/global/global.min.js"></script>
  	  <script src="assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
      <script src="assets/js/custom.min.js"></script>
  	  <script src="assets/js/deznav-init.js"></script>

      <script>
        $('.image-upload-wrap').bind('dragover', function () {
            $('.image-upload-wrap').addClass('image-dropping');
        });
        $('.image-upload-wrap').bind('dragleave', function () {
            $('.image-upload-wrap').removeClass('image-dropping');
        });
      </script>

      <script src="assets/js/main.js?v=<?php echo rand(); ?>"></script>
      <script src="assets/js/bootstrap-notify.js"></script>
      <script src="assets/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
      <script src="assets/js/plugins-init/sweetalert.init.js"></script>

      <!-- Datatable -->
      <script src="assets/vendor/datatables/js/jquery.dataTables.min.js"></script>

      <!--=============== unveil imagenes ===============-->
      <script type="text/javascript" src="assets/vendor/unveil/jquery.unveil.js"></script>
  </body>
</html>
