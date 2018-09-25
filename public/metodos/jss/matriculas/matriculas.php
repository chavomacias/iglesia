<script>


function filtrarUsuarioPorIdentificacionEnMatricula(){
    var url = $("#rutaBase").text();
    var identificacion = $("#identificacion").val();
    if(identificacion.length < 10){
        $("#mensajeFormIngresoMatricula").html('');
        $("#contenedorDatosMatricula").html('');
        $("#contenedorHorarioSeleccionado").html('');
        $("#contenedorlistahorarios").html('');
    }else{
        $.ajax({
            url : url+'/matriculas/filtrarpersonaporidentificacion',
            type: 'post',
            dataType: 'JSON',
            data: {identificacion:identificacion},
            beforeSend: function(){
                $("#mensajeFormIngresoMatricula").html('');
                $("#idPersonaEncriptado").val('0');
                cargandoMatriculas('#mensajeFormIngresoMatricula');
            },
            uploadProgress: function(event,position,total,percentComplete){
            },
            success: function(data){ 
                if(data.validar == true){
                  $("#contenedorDatosMatricula").html(data.tabla);
                  $("#idPersonaEncriptado").val(data.idPersonaEncriptado);
                    
                }else{
                    $("#contenedorDatosPersona").html('');
                    $("#idPersonaEncriptado").val('0');
                }
                $("#mensajeFormIngresoMatricula").html(data.mensaje);
            },
            complete: function(){
            },
            error: function(xhr, textStatus, errorThrown) {
                $("#idPersonaEncriptado").val('0');
                $("#contenedorDatosMatricula").html('');
                if(xhr.status === 0){
                    $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">NO HAY CONEXIÓN A INTERNET. VERIFICA LA RED</div>');
                }else if(xhr.status == 404){
                    $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">ERROR [404]. PÁGINA NO ENCONTRADA</div>');
                }else if(xhr.status == 500){
                    $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">ERROR DEL SERVIDOR [500]</div>');
                }else if(errorThrown === 'parsererror'){
                    $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN JSON HA FALLADO </div>');
                }else if(errorThrown === 'timeout'){
                    $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">TIEMPO DE ESPERA TERMINADO</div>');
                }else if(errorThrown === 'abort'){
                    $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN AJAX FUE ABORTADA</div>');
                }else{
                    $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>');
                }
            }
        }); 
    }
}
function cargandoMatriculas(contenedor){
    var url = $("#rutaBase").text();
    $(contenedor).html('<img style="margin:0 auto 0 auto; text-aling:center; width: 10%;" class="img-responsive" src="'+url+'/public/librerias/images/pagina/cargando.gif">');
    
}


function seleccionarFila(ID)
{
    var menues2 = $("#tablaMatriculas tbody tr td");
    menues2.removeAttr("style");
    menues2.css({ 'cursor': 'pointer' });
    $("#filaTablaMatriculas" + ID + " td").removeAttr("style");
    $("#filaTablaMatriculas" + ID + " td").css({ 'background-color': 'black', 'color': 'white' });
}


function obtenerMatriculas(){
    var url = $("#rutaBase").text();
    var idConfCurso = $("#selectConfigurarCurso").val();
    if(idConfCurso == 0){
        $("#mensajeTablaMatriculasActuales").html('');
         $("#contenedorTablaMatriculasActuales").html('');
    }else{
    $.ajax({
        url : url+'/matriculas/obtenermatriculas',
        type: 'post',
        dataType: 'JSON',
        data: {id:idConfCurso},
        beforeSend: function(){
            cargandoMatriculas('#contenedorTablaMatriculasActuales');
            $("#mensajeTablaMatriculasActuales").html('');
            
        },
        uploadProgress: function(event,position,total,percentComplete){
        },
        success: function(data){  
            if(data.validar == true){
                $("#contenedorTablaMatriculasActuales").html('<hr><div class="box-body table-responsive no-padding"><table class="table table-hover" id="tablaMatriculas"></table></div>');
                $('#tablaMatriculas').DataTable({
                    destroy: true,
                    order: [],
                    data: data.tabla,
                    'createdRow': function (row, data, dataIndex) {
                        var division = dataIndex % 2;
                        if (division == "0")
                        {
                            $(row).attr('style', 'background-color: #DCFBFF;text-align: center;font-weight: bold;');
                        } else {
                            $(row).attr('style', 'background-color: #CFCFCF;text-align: center;font-weight: bold;');
                        }
                        $(row).attr('onclick', 'seleccionarFila(' + dataIndex + ');');
                        $(row).attr('id', 'filaTablaMatriculas' + dataIndex);
                    },
                    'columnDefs': [
                        {
                           'targets': 2,
                           'createdCell':  function (td, cellData, rowData, row, col) {
                                $(td).attr('id','identificacion'+row); 
                           }
                        }
                     ],
                    columns: [
                        {
                            title: '#',
                            data: '_j'
                        },
                        {
                            title: 'DNI',
                            data: 'identificacion'
                        },
                        {
                            title: 'NOMBRES',
                            data: 'nombres'
                        },
                        {
                            title: 'APELLIDOS',
                            data: 'apellidos'
                        },
                        {
                            title: 'FECHA DE MATRICULA',
                            data: 'fechaMatricula'
                        },
                        {
                            title: 'OPC.',
                            data: 'opciones1'
                        }
                    ],
                });    
                seleccionarFila(0)
            }else{
                $("#contenedorTablaMatriculasActuales").html('');
            }
            $("#mensajeTablaMatriculasActuales").html(data.mensaje);
        },
        complete: function(){
        },
        error: function(xhr, textStatus, errorThrown) {
            $("#contenedorTablaMatriculasActuales").html('');
            if(xhr.status === 0){
                $("#mensajeTablaMatriculasActuales").html('<div class="alert alert-danger text-center" role="alert">NO HAY CONEXIÓN A INTERNET. VERIFICA LA RED</div>');
            }else if(xhr.status == 404){
                $("#mensajeTablaMatriculasActuales").html('<div class="alert alert-danger text-center" role="alert">ERROR [404]. PÁGINA NO ENCONTRADA</div>');
            }else if(xhr.status == 500){
                $("#mensajeTablaMatriculasActuales").html('<div class="alert alert-danger text-center" role="alert">ERROR DEL SERVIDOR [500]</div>');
            }else if(errorThrown === 'parsererror'){
                $("#mensajeTablaMatriculasActuales").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN JSON HA FALLADO </div>');
            }else if(errorThrown === 'timeout'){
                $("#mensajeTablaMatriculasActuales").html('<div class="alert alert-danger text-center" role="alert">TIEMPO DE ESPERA TERMINADO</div>');
            }else if(errorThrown === 'abort'){
                $("#mensajeTablaMatriculasActuales").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN AJAX FUE ABORTADA</div>');
            }else{
                $("#mensajeTablaMatriculasActuales").html('<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>');
            }
        }
    }); 
    }
}

function filtrarlistaHorariosCursoSeleccionado(){
    var url = $("#rutaBase").text();
    var idCurso = $("#selectCurso").val();

    if(idCurso == 0){
        $("#contenedorHorarioSeleccionado").html('');
        $("#contenedorlistahorarios").html('');
        $("#contenedorInfoGeneralHorarioSeleccionado").html("");
    }else{
        $.ajax({
            url : url+'/matriculas/obtenerhorarios',
            type: 'post',
            dataType: 'JSON',
            data: {id:idCurso},
            beforeSend: function(){
                cargandoMatriculas("#contenedorlistahorarios");
                $("#mensajeContenedorHorario").html('');
                $("#contenedorlistahorarios").html("");
                $("#contenedorHorarioSeleccionado").html('');
                $("#contenedorInfoGeneralHorarioSeleccionado").html("");
                $("#mensajeTablaMatriculasActuales").html('');
                $("#contenedorTablaMatriculasActuales").html('');
            },
            uploadProgress: function(event,position,total,percentComplete){

            },
            success: function(data){ 
                if(data.validar == true)
                {               
                     $("#contenedorlistahorarios").html(data.div);
                     $("#contenedorInfoGeneralHorarioSeleccionado").html('');
                     $("#contenedorHorarioSeleccionado").html('');

                }else{
                     $("#contenedorlistahorarios").html('');
                }
                $("#mensajeContenedorHorario").html(data.mensaje);
            },
            complete: function(){
            },
            error: function(xhr, textStatus, errorThrown) {
                $("#contenedorlistahorarios").html('');
                if(xhr.status === 0){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">NO HAY CONEXIÓN A INTERNET. VERIFICA LA RED</div>');
                }else if(xhr.status == 404){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">ERROR [404]. PÁGINA NO ENCONTRADA</div>');
                }else if(xhr.status == 500){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">ERROR DEL SERVIDOR [500]</div>');
                }else if(errorThrown === 'parsererror'){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN JSON HA FALLADO </div>');
                }else if(errorThrown === 'timeout'){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">TIEMPO DE ESPERA TERMINADO</div>');
                }else if(errorThrown === 'abort'){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN AJAX FUE ABORTADA</div>');
                }else{
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>');
                }
            }
        }); 
    }
} 


function filtrarHorarioCursoSeleccionado(){
    var url = $("#rutaBase").text();
    var idConfCurso = $("#selectConfigurarCurso").val();
    if(idConfCurso == 0){
        $("#mensajeContenedorHorario").html('');
        $("#contenedorHorarioSeleccionado").html('');
    }else{
        $.ajax({
            url : url+'/matriculas/filtrardatoshorario',
            type: 'post',
            dataType: 'JSON',
            data: {id:idConfCurso},
            beforeSend: function(){
                cargandoMatriculas("#contenedorHorarioSeleccionado");
                cargandoMatriculas("#contenedorInfoGeneralHorarioSeleccionado");
                $("#mensajeContenedorHorario").html('');
                $("#mensajeContenedorHorario").html('');
            },
            uploadProgress: function(event,position,total,percentComplete){

            },
            success: function(data){ 
                if(data.validar == true)
                {
                  $("#contenedorInfoGeneralHorarioSeleccionado").html(data.datosGenerales);
                     $("#contenedorHorarioSeleccionado").html(data.tabla);
                }else{
                     $("#contenedorHorarioSeleccionado").html('');
                }
                console.log(data)
                $("#mensajeContenedorHorario").html(data.mensaje);
            },
            complete: function(){
            },
            error: function(xhr, textStatus, errorThrown) {
                $("#contenedorHorarioSeleccionado").html('');
                if(xhr.status === 0){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">NO HAY CONEXIÓN A INTERNET. VERIFICA LA RED</div>');
                }else if(xhr.status == 404){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">ERROR [404]. PÁGINA NO ENCONTRADA</div>');
                }else if(xhr.status == 500){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">ERROR DEL SERVIDOR [500]</div>');
                }else if(errorThrown === 'parsererror'){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN JSON HA FALLADO </div>');
                }else if(errorThrown === 'timeout'){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">TIEMPO DE ESPERA TERMINADO</div>');
                }else if(errorThrown === 'abort'){
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN AJAX FUE ABORTADA</div>');
                }else{
                    $("#mensajeContenedorHorario").html('<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>');
                }
            }
        }); 
    }
} 

function validarIngresoMatricula(f){
    var _validar = false;
    if(confirm("¿ESTAS SEGURO DE REGISTRAR ESTA MATRÍCULA?")){
        _validar = true;
    }
    return _validar;
}

function limpiarFormIngresoMatriculas()
{
    $('#formIngresoUsuario').each(function () {
        this.reset();
    });
    $("#contenedorDatosMatricula").html('');
    $("#contenedorHorarioSeleccionado").html('');
    $("#contenedorlistahorarios").html('');
    
    setTimeout(function() {$("#mensajeFormIngresoMatricula").html('');},1500);
}

$(function(){
    $("#formIngresoMatricula").ajaxForm({
        beforeSend: function(){
            $("#mensajeFormIngresoMatricula").html('');
            $("#btnGuardarMatricula").button('loading');
        },
        uploadProgress: function(event,position,total,percentComplete){

        },
        success: function(data){
            if(data.validar==true){
                limpiarFormIngresoMatriculas();
            }
            $("#btnGuardarMatricula").button('reset');
            $("#mensajeFormIngresoMatricula").html(data.mensaje);
        },
        complete: function(){
        },
        error: function(xhr, textStatus, errorThrown) {
            $("#btnGuardarMatricula").button('reset');
            if(xhr.status === 0){
                $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">NO HAY CONEXIÓN A INTERNET. VERIFICA LA RED</div>');
            }else if(xhr.status == 404){
                $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">ERROR [404]. PÁGINA NO ENCONTRADA</div>');
            }else if(xhr.status == 500){
                $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">ERROR DEL SERVIDOR [500]</div>');
            }else if(errorThrown === 'parsererror'){
                $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN JSON HA FALLADO </div>');
            }else if(errorThrown === 'timeout'){
                $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">TIEMPO DE ESPERA TERMINADO</div>');
            }else if(errorThrown === 'abort'){
                $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">LA PETICIÓN AJAX FUE ABORTADA</div>');
            }else{
                $("#mensajeFormIngresoMatricula").html('<div class="alert alert-danger text-center" role="alert">OCURRIÓ UN ERROR INESPERADO</div>');
            }
        }
    });    
}); 

</script>
