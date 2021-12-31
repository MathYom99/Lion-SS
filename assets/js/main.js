type = ['', 'info', 'success', 'warning', 'danger'];

var calendarRes = '';

Number.prototype.formatMoney = function(c, d, t) {
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function validaentero(ev) {
    var Event = ev || window.event; //Le asignamos a una variable el evento que se haya generado
    var key = Event.keyCode || Event.which; //Asignamos el código de la tecla presionada por el usuario
    key = String.fromCharCode(key); //convertimos de ASCII a string
    var permitidos = /[0-9]/; //Una expresión regular que solo permitirá números del 0 al 9, si cambiamos /[^0-9]/ significa que podemos poner cualquier valor menos los números del 0-9
    if (!permitidos.test(key)) { //Validamos que la tecla pulsada por el usuario coincida con lo mencionado en el patron
        Event.returnValue = false; //si no coincide cancelamos el evento y no se imprime su valor
        if (Event.preventDefault) {
            Event.preventDefault();
        }
    }
}

function validadecimal(ev) {
    var Event = ev || window.event;
    var key = Event.keyCode || Event.which;
    key = String.fromCharCode(key);
    var permitidos = /[0-9\.\b]+/g;
    if (!permitidos.test(key)) {
        Event.returnValue = false;
        if (Event.preventDefault) Event.preventDefault();
    }
}

function mensaje(from, align, time, color, mensaje, icono) {
    if (icono == undefined) icono = '';
    $.notify({
        message: icono + ' ' + mensaje
    }, {
        type: type[color],
        delay: time,
        z_index: 10000,
        placement: {
            from: from,
            align: align
        }
    });
}


/*---------- Inician funciones de usuarios  ------*/

function registrarSala(nombre, capacidad) {
    $('#spanNombreS').fadeOut();
    $('#spanCapacidadS').fadeOut();

    if (nombre.trim() == "") {
        mensaje('top', 'center', 2000, 3, 'Ingrese el nombre de la sala por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
        $('#nombreS').focus();
        $('#spanNombreS').html('* Se requiere el nombre');
        $('#spanNombreS').fadeIn();
        return;
    } else if (capacidad.trim() == "") {
        mensaje('top', 'center', 2000, 3, 'Ingrese la capacidad de la sala por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
        $('#capacidadS').focus();
        $('#spanCapacidadS').html('* Se requiere la capacidad');
        $('#spanCapacidadS').fadeIn();
        return;
    }

    $.ajax({
        beforeSend: function() {
            $('#btnRegistraSala').prop("disabled", true);
            $('#btnRegistraSala').html('<i class="fa fa-circle-notch fa-spin"></i> Registrando...');
        },
        url: 'main.php',
        type: 'POST',
        data: { nombre, capacidad, tipo: 'registrarSala' },
        success: function(respuesta) {
            console.log(respuesta);
            if (respuesta == "ok") {
                swal("Registrada!", "Sala registrada correctamente", "success")
                tablaSalas();
                salasSelect();
                $('#nuevaSala').modal('hide');

                //Limpiar Modal
                $('#nombreS').val('');
                $('#capacidadS').val('');
            } else if (respuesta == "exist") {
                sweetAlert("Ya existe...", "El nombre de esta sala ya se encuentra registrado, por favor intente con otro nombre", "error");
            } else {
                sweetAlert("Error...", "Error al registrar sala", "error");
            }

            $('#btnRegistraSala').prop("disabled", false);
            $('#btnRegistraSala').html('Registrar Sala');
        },
        error: function(respuesta) {
            console.log("Error en Ajax");
            sweetAlert("Error...", "Error al procesar la solicitud", "error");
        },
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log("Falla en Ajax");
        sweetAlert("Error...", "Falló la solicitud de la información", "error");
    });
}

function tablaSalas() {
    $.ajax({
        beforeSend: function() {
            if ($.fn.DataTable.isDataTable('#tablaSalas')) {
                $('#tablaSalas').dataTable().fnClearTable();
                $('#tablaSalas').dataTable().fnDestroy();
            }
            $('#tablaSalas').html('<center><img src="assets/img/loader.gif" style="margin-top: 50px;"></center>');
        },
        url: 'main.php',
        type: 'POST',
        data: { tipo: 'tablaSalas' },
        success: function(respuesta) {
            console.log(respuesta);
            var json = JSON.parse(respuesta);
            $('#tablaSalas').empty();
            $('#tablaSalas').append('<thead class="thead-light"><tr> <th>Nombre</th>  <th>Capacidad</th> <th>Estatus</th> <th>Editar</th>  <th>Eliminar</th> </tr></thead>');
            $('#tablaSalas').append('<tbody>');
            $.each(json, function(index, val) {
                $('#tablaSalas').append('<tr> <td>' + val['nombre'] +'</td> <td>' + val['capacidad'] + '</td> <td>' + val['estatus'] + '</td> <td>' + val['edit'] + '</td> <td>' + val['delete'] + '</td> </tr>');
            });
            $('#tablaSalas').append('</tbody>');
            $('#tablaSalas').DataTable();
        },
        error: function(respuesta) {
            console.log("Error en Ajax");
            sweetAlert("Error...", "Error al procesar la solicitud", "error");
        },
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log("Falla en Ajax");
        sweetAlert("Error...", "Falló la solicitud de la información", "error");
    });
}

function infoEditaSala(id) {
    $.ajax({
        url: 'main.php',
        type: 'POST',
        data: { id, tipo: 'infoEditaSala' },
        success: function(respuesta) {
            console.log(respuesta);
            var json = JSON.parse(respuesta);
            $.each(json, function(index, val) {
                $('#nombreSE').val(val['nombre']);
                $('#idSalaE').val(val['id']);
                $('#capacidadSE').val(val['capacidad']);
                $('#estatusSE').val(val['estatus']);

            })
        },
        error: function(respuesta) {
            console.log("Error en Ajax");
            sweetAlert("Error...", "Error al procesar la solicitud", "error");
        },
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log("Falla en Ajax");
        sweetAlert("Error...", "Falló la solicitud de la información", "error");
    });
}

function editaSala(nombre, capacidad, estatus, id) {
  $('#spanNombreSE').fadeOut();
  $('#spanCapacidadSE').fadeOut();

  if (nombre.trim() == "") {
      mensaje('top', 'center', 2000, 3, 'Ingrese el nombre de la sala por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
      $('#nombreSE').focus();
      $('#spanNombreSE').html('* Se requiere el nombre');
      $('#spanNombreSE').fadeIn();
      return;
  } else if (capacidad.trim() == "") {
      mensaje('top', 'center', 2000, 3, 'Ingrese la capacidad de la sala por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
      $('#capacidadSE').focus();
      $('#spanCapacidadSE').html('* Se requiere la capacidad');
      $('#spanCapacidadSE').fadeIn();
      return;
  }


    $.ajax({
        beforeSend: function() {
            $('#btnEditarSala').prop("disabled", true);
            $('#btnEditarSala').html('<i class="fa fa-circle-notch fa-spin"></i> Guardando...');
        },
        url: 'main.php',
        type: 'POST',
        data: { nombre, capacidad, id, estatus, tipo: 'editaSala' },
        success: function(respuesta) {
            console.log(respuesta);
            if (respuesta == "ok") {
                swal("Actualizada!", "Datos actualizados correctamente", "success");
                tablaSalas();
                salasSelect();
                $('#editSalaModal').modal('hide');
            } else if (respuesta == "exist") {
                sweetAlert("Ya existe...", "El nombre de la sala ya existe, favor de intentar con otro nombre", "error");
            } else {
                sweetAlert("Error...", "Error al guardar los cambios", "error");
            }

            $('#btnEditarSala').prop("disabled", false);
            $('#btnEditarSala').html('Guardar Cambios');
        },
        error: function(respuesta) {
            console.log("Error en Ajax");
            sweetAlert("Error...", "Error al procesar la solicitud", "error");
        },
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.log("Falla en Ajax");
        sweetAlert("Error...", "Falló la solicitud de la información", "error");
    });
}

function salasSelect() {
    $.ajax({
        url: 'main.php',
        type: 'POST',
        data: { tipo: "salasSelect" },
        success: function(respuesta) {
            var json = JSON.parse(respuesta);
            $('#salaR').empty();
            $('#salaR').append('<option value="no">Seleccione una sala</option>');

            $.each(json, function(index, val) {
                $('#salaR').append('<option value="' + val['Id'] + '">' + val['Nombre'] + '</option>');
            });
        },
    });
}

function eliminarSala(id) {
    swal({
        title: "Eliminar Sala",
        text: "¿Seguro que desea eliminar esta sala?",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#1EA7C5",
        confirmButtonText: "Si, eliminar sala",
        cancelButtonText: "No, regresar"
      }).then(result => {
          if (result.value) {
            $.ajax({
                url: 'main.php',
                type: 'POST',
                data: { id, tipo: 'eliminarSala' },
                success: function(respuesta) {
                    var json = JSON.parse(respuesta);
                    $.each(json, function(index, val) {
                        if (val['respuesta'] == "ok") {
                            swal("Eliminada!", "Sala eliminada correctamente", "success")
                            tablaSalas();
                            salasSelect();
                        } else {
                            sweetAlert("Error...", "Error al eliminar sala", "error");
                        }
                    });
                },
                error: function(respuesta) {
                    console.log("Error en Ajax");
                    sweetAlert("Error...", "Error al procesar la solicitud", "error");
                },
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("Falla en Ajax");
                sweetAlert("Error...", "Falló la solicitud de la información", "error");
            });
        }
    })
}

function diferenciaHoras(hora1, hora2){
  var formatohora = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
  if (!(hora1.match(formatohora)&& hora2.match(formatohora))){
    mensaje('top', 'center', 2000, 3, 'Ingrese un horario correcto por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
    $("#horaI").focus();
    $('#btnRegistraRes').prop("disabled", true);
    return;
  }
  var min1 = hora1.split(':').reduce((p, c) => parseInt(p) * 60 + parseInt(c));
  var min2 = hora2.split(':').reduce((p, c) => parseInt(p) * 60 + parseInt(c));

  if (min2<min1){
    mensaje('top', 'center', 2000, 3, 'La hora de termino no debe ser menor a la inicial, ingrese un horario correcto por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
    $("#horaT").focus();
    $('#btnRegistraRes').prop("disabled", true);
    return;
  }
  var diferencia = min2 - min1;
  if(diferencia>120){
    mensaje('top', 'center', 2000, 3, 'El horario excede las dos horas permitidas, ingrese un horario correcto por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
    $("#horaT").focus();
    $('#btnRegistraRes').prop("disabled", true);
    return;
  }
  $('#btnRegistraRes').prop("disabled", false);

}

function registraRes(sala, fecha, horaI, horaT) {
    if (sala=='no') {
        mensaje('top', 'center', 2000, 3, 'Seleccione una sala por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
        $("#salaR").focus();
        return;
    } else if (fecha=='') {
        mensaje('top', 'center', 2000, 3, 'Ingrese la fecha de la reservación por favor', '<i class="fa fa-exclamation-triangle fa-lg"></i>');
        $("#fecha").focus();
        return;
    }
    $.ajax({
        beforeSend: function() {
            $('#btnRegistraRes').prop("disabled", true);
            $('#btnRegistraRes').html('<i class="fa fa-circle-notch fa-spin"></i> Registrando...');
        },
        url: 'main.php',
        type: 'POST',
        data: { sala, fecha, horaI, horaT, tipo: "registraRes" },
        success: function(respuesta) {
            console.log(respuesta);
            if (respuesta == "ok") {
                swal("Registrada!", "Reservación registrada correctamente", "success");
                $("#nuevaRes").modal('hide');
                //recargaCalendarioRes();
                //limpiamos formulario
                $("#salaR").val('no');
            } else if (respuesta == "exist") {
                sweetAlert("Sala Ocupada...", "La sala no esta disponible para el horario seleccionado, por favor intente con un horario diferente o con otra sala", "error");
            } else {
                sweetAlert("Error...", "Error al registrar la reservación", "error");
            }
            $('#btnRegistraRes').prop("disabled", false);
            $('#btnRegistraRes').html('Registrar Reservación');
        },
    });
}


function calendarioRes(desde, hasta, vista) {
    let eventos = [];

    $.ajax({
        beforeSend: function() {
            $('#calendar').html('<center><img src="assets/img/loader.gif" style="margin-top: 50px;"></center>');
        },
        url: 'main.php',
        type: 'POST',
        data: { desde, hasta, tipo: 'calendarioRes' },
        success: function(respuesta) {
            console.log(respuesta);
            const json = JSON.parse(respuesta);
            eventos = {
                events: [{
                    title: 'nada',
                    start: "2000-01-01"
                }]
            };
            var minHora = "07:00:00";
            $.each(json, function(index, val) {
                eventos.events.push({
                    id: val['id'],
                    title: val['title'],
                    description: '1234',
                    start: val['start'],
                    end: val['end'],
                    color: val['color'],
                    textColor: val['textColor']
                });
                if (val['minHora'] != "") minHora = val['minHora'];
            });
            console.log(eventos);

            $('#calendar').empty();
            var calendarEl = document.getElementById('calendar');
            calendarRes = new FullCalendar.Calendar(calendarEl, {
                locale: "es-ES",
                initialDate: desde,
                initialView: vista,
                nowIndicator: true,
                customButtons: {
                    prev: {
                        click: function() {
                            calendarRes.prev();
                            recargaCalendarioRes();
                        }
                    },
                    next: {
                        click: function() {
                            calendarRes.next();
                            recargaCalendarioRes();
                        }
                    },
                    today: {
                        text: 'Hoy',
                        click: function() {
                            calendarRes.today();
                            recargaCalendarioRes();
                        }
                    },
                    dayGridMonth: {
                        text: 'Mes',
                        click: function() {
                            var view = calendarRes.view;
                            var des = view.currentStart.toISOString().slice(0, 8);
                            des = des + '01';
                            var has = view.currentEnd.toISOString().slice(0, 8);
                            has = has + '31';
                            var vista = view.type;
                            calendarioRes(des, has, 'dayGridMonth');
                        }
                    }
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                dayHeaderFormat: {
                    weekday: 'long',
                    month: 'short',
                    day: 'numeric',
                    omitCommas: true
                },
                displayEventTime: false,
                allDaySlot: false,
                slotMinTime: minHora,
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                selectable: false,
                selectMirror: true,
                dayMaxEvents: true, // allow "more" link when too many events
                events: eventos,
                eventClick: function(info) {
                    infoClase(info.event.id);
                    $('#datosFecha').modal('show');
                },

            });
            calendarRes.render();
        }
    });
}

function recargaCalendarioRes() {
    var view = calendarRes.view;
    var des = view.currentStart.toISOString().slice(0, 10);
    var has = view.currentEnd.toISOString().slice(0, 10);
    var vista = view.type;
    calendarioRes(des, has, vista);
}
