<?php
  error_reporting(0);
  session_start();
  include 'conexion.php';
  $bd = new MYSQLIFunctions();
  include 'funciones.php';
  $funciones = new funciones();

  date_default_timezone_set('America/Mexico_City');
  $hoy=date('Y-m-d');
  $desde = $funciones->siguienteDiaSemana($hoy, '-', 1); //lunes pasado
  $hasta = $funciones->siguienteDiaSemana($hoy, '+', 0); //siguiente domingo
?>
<html lang="es">
  <head>
      <!--=============== basic  ===============-->
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
      <title>Salas de Juntas</title>
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

  </head>

  <body onload="tablaSalas(), calendarioRes('<?php echo $desde; ?>', '<?php echo $hasta; ?>', 'timeGridWeek')">

      <!--Preloader-->
      <div id="preloader">
          <div class="sk-three-bounce">
              <div class="sk-child sk-bounce1"></div>
              <div class="sk-child sk-bounce2"></div>
              <div class="sk-child sk-bounce3"></div>
          </div>
      </div>

      <div class="container-fluid">
          <div class="row">
              <div class="col-12">
                  <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">Salas de Juntas</h4>
                      </div>

                      <div class="card-body">
                        <!-- Nav tabs -->
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#navRegistro"><i class="la la-plus-square mr-2"></i> Registro de Salas</a>
                                </li>
                                <li class="nav-item" onclick="">
                                    <a class="nav-link" data-toggle="tab" href="#navReserva"><i class="la la-calendar-check mr-2"></i> Reservación de Salas</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="navRegistro" role="tabpanel">
                                    <br>
                                    <div class="row">
                                       <div class="col-md-12">
                                          <button type="button" class="btn light btn-success pull-right" data-toggle="modal" data-target="#nuevaSala">Nueva Sala  <i class="fa fa-plus"></i></button>
                                       </div>
                                    </div>

                                    <div class="table-responsive">
                                      <table id="tablaSalas" class="table display min-w850">
                                      </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="navReserva">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12"><button type="button" class="btn light btn-success pull-right optionInsert" data-toggle="modal" data-target="#nuevaRes">Nueva Reservación <i class="fa fa-plus"></i></button></div>
                                    </div>
                                    <div>
                                    <div id="calendar" class="app-fullcalendar"></div>
                                </div>

                            </div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- MODALES -->

      <!-- Nueva Reservación -->
      <div class="modal fade bd-example-modal-lg optionInsert" id="nuevaRes" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                      <h3 class="modal-title">Nueva Reservación</h3>
                      <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" id="duracionHidden">
                      <div class="form-row">
                          <div class="form-group col-lg-12">
                              <label style="width: 80%; display: inline-block;">Sala: <span class="text-danger">*</span></label>
                              <i class="text-primary fa fa-plus-square fa-lg pointer pull-right text-info" style="padding-right: 15px; margin-top: 15px;" data-toggle="modal" data-target="#nuevaSala"></i>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-bars"></i> </span></div>
                                  <select class="form-control" id="salaR">
                                      <option value="no">Seleccione una sala</option>
                                      <?php $consulta=$bd->query("SELECT Id, Nombre FROM salas WHERE Estatus>0 ");
                                      while ($res=$bd->fassoc($consulta)) { ?>
                                          <option value="<?php echo $res['Id'] ?>"><?php echo $res['Nombre'] ?></option>
                                      <?php } ?>
                                  </select>
                              </div>
                          </div>

                          <div class="form-group col-lg-12">
                              <label>Fecha:  <span class="text-danger">*</span></label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar-alt"></i> </span></div>
                                  <input type="date" id="fecha" class="form-control" min="<?php echo $hoy; ?>" onchange="">
                              </div>
                          </div>

                          <div class="form-group col-lg-12">
                              <label>Horario (Máximo 2 horas): <span class="text-danger">*</span></label>
                              <div class="form-row">
                                  <div class="form-group col-md-6">
                                      <div class="input-group input-info-o">
                                          <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-clock"></i> </span></div>
                                          <input type="time" id="horaI" class="form-control" value="08:00" onchange="diferenciaHoras(horaI.value, horaT.value)">
                                      </div>
                                  </div>
                                  <div class="form-group col-md-6">
                                      <div class="input-group input-info-o">
                                          <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-clock"></i> </span></div>
                                          <input type="time" id="horaT" class="form-control" value="09:00" onchange="diferenciaHoras(horaI.value, horaT.value)">
                                      </div>
                                  </div>
                                  <span class="text-danger" id="spanHorarioMb" style="display: none;"></span>
                              </div>
                          </div>

                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-light light" data-dismiss="modal">Cancelar</button>
                      <button type="button" class="btn btn-success" id="btnRegistraRes" onclick="registraRes(salaR.value, fecha.value, horaI.value, horaT.value)">Registrar Reservación</button>
                  </div>
              </div>
          </div>
      </div>


        <!-- Nueva Sala -->
        <div class="modal fade bd-example-modal-lg optionInsert" id="nuevaSala" tabindex="3" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                      <h3 class="modal-title">Nueva Sala</h3>
                      <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-row">
                          <div class="form-group col-lg-12">
                              <label>Nombre de la sala: <span class="text-danger">*</span></label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-id-card"></i> </span></div>
                                  <input type="text" id="nombreS" class="form-control" placeholder="Nombre de la sala">
                              </div>
                              <span class="text-danger" id="spanNombreS" style="display: none;"></span>
                          </div>

                          <div class="form-group col-lg-12">
                              <label>Capacidad: <span class="text-danger">*</span></label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-hashtag"></i> </span></div>
                                  <input type="text" id="capacidadS" class="form-control" placeholder="Capacidad total de la sala" onkeypress="validaentero()">
                              </div>
                              <span class="text-danger" id="spanCapacidadS" style="display: none;"></span>
                          </div>

                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-light light" data-dismiss="modal">Cancelar</button>
                      <button type="button" class="btn btn-success" id="btnRegistraSala" onclick="registrarSala(nombreS.value, capacidadS.value)">Registrar Sala</button>
                  </div>
              </div>
          </div>
        </div>

         <!-- Edita Sala -->
         <div class="modal fade bd-example-modal-lg" id="editSalaModal" tabindex="3" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Editar Sala</h3>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="idSalaE">

                        <div class="form-row">
                          <div class="form-group col-lg-12">
                              <label>Nombre de la sala: <span class="text-danger">*</span></label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-id-card"></i> </span></div>
                                  <input type="text" id="nombreSE" class="form-control" placeholder="Nombre de la sala">
                              </div>
                              <span class="text-danger" id="spanNombreSE" style="display: none;"></span>
                          </div>

                          <div class="form-group col-lg-12">
                              <label>Capacidad: <span class="text-danger">*</span></label>
                              <div class="input-group input-info-o">
                                  <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-hashtag"></i> </span></div>
                                  <input type="text" id="capacidadSE" class="form-control" placeholder="Capacidad total de la sala" onkeypress="validaentero()">
                              </div>
                              <span class="text-danger" id="spanCapacidadSE" style="display: none;"></span>
                          </div>

                            <div class="form-group col-lg-12">
                                <label>Estatus:</label>
                                <div class="input-group input-info-o">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-eye"></i> </span></div>
                                    <select class="form-control" id="estatusSE">
                                      <option value="1">Disponible</option>
                          						<option value="0">En Mantenimiento</option>
                          				  </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light light" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="btnEditarSala" onclick="editaSala(nombreSE.value, capacidadSE.value, estatusSE.value, idSalaE.value)">Guardar Cambios</button>
                    </div>
                </div>
            </div>
          </div>



      <!-- Scripts -->
      <script src="assets/vendor/global/global.min.js"></script>
  	  <script src="assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
      <script src="assets/js/custom.min.js"></script>
  	  <script src="assets/js/deznav-init.js"></script>

      <script src="assets/js/main.js?v=<?php echo rand(); ?>"></script>
      <script src="assets/js/bootstrap-notify.js"></script>
      <script src="assets/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
      <script src="assets/js/plugins-init/sweetalert.init.js"></script>

      <!-- Datatable -->
      <script src="assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
  </body>
</html>
