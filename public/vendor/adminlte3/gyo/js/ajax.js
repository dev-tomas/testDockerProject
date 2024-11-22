function objetoAjax(){	var xmlhttp=false;try {xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");} catch (e) {try {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");} catch (E) {xmlhttp = false;}}if (!xmlhttp && typeof XMLHttpRequest!='undefined') {xmlhttp = new XMLHttpRequest();}return xmlhttp;}
function Mostrar(url2){
        $("#contenido").html("<br><br><br><br><br><br><br><br><center>Cargando...<br><img src='img/ajax-loader.gif'></center>");
            $.ajax({
                url: url2,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       			processData: false
            })
                .done(function(res){
                    $("#contenido").html("" + res);
                });

}
function dar_click(){
			var obj=document.getElementByid('show-hide-sidebar-toggle');
			obj.click();
}
function enviar_contrax(){
            var formData = new FormData(document.getElementById("f73"));
            $.ajax({
                url: "php/g_contra.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){  }, 2000);
                });	
}
function cambiar(capa) {
soy = document.getElementById(capa);
soy.style.display = (soy.style.display == "block") ? "none" : "block";
}
function mostrar_lalin(){
            $.ajax({
                url: "roku.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       			processData: false
            })
                .done(function(res){
                    $("#loli").html("" + res);
                });
}

function funct_datos(ctrl){
	this.document.f72.txtnombre.value = ctrl.attr("nombre");
	this.document.f72.txtapellido.value = ctrl.attr("apellido");
	this.document.f72.txtemail.value = ctrl.attr("correo");
	this.document.f72.txtcelular.value = ctrl.attr("telefono");
	this.document.f72.txtfecha.value = ctrl.attr("fecha");
}
function enviar_dp(){

            var formData = new FormData(document.getElementById("f72"));
            $.ajax({
                url: "php/g_datos_personales.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ this.document.location.href="men_base.php" }, 2000);
                });
}

function enviar2(ctrl, ac, banear){
	var url = 'src/g_usuario.php';
	var nombre = document.frmac.nombre.value;
	var rol = document.frmac.rol.value;
	var ryu=ctrl.attr("cojo");

	var req = (banear != undefined ? (banear == 4 ? 4 : 5) : (ac == 0 ? (id == "0" ? 1 : 2): 3));
	var	id=(req == 3 || req == 4 || req == 5 ? ac :this.document.frmac.Hidden_Id.value);
	var enviar = (banear != undefined ? (banear == 4 ? (confirm("Desea Banear a Este Usuario?")) : (confirm("Desea Activar a Este Usuario?"))) : (req == 3 ? (confirm("Desea Eliminar Este Usuario?")) : true));
	if(enviar)
	{		
		var correo="";
		if(ryu==1){
			correo = prompt("Ingrese correo de usuario: ", "");
			if(correo==""){
				alert("Debe Ingresar el correo electronico")
			}else{
		divResultado = document.getElementById('reps');
		ajax=objetoAjax();
		var URL=url+"?nombre="+nombre+"&rol="+rol+"&id="+id+"&req="+req+"&correo="+correo;
		
		ajax.open("GET", URL);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divResultado.innerHTML = ajax.responseText
				setTimeout(function(){ Mostrar('src/n_usuario.php'); }, 1000);
			}
		}
		ajax.send(null);
	}
		}else{
		divResultado = document.getElementById('reps');
		ajax=objetoAjax();
		var URL=url+"?nombre="+nombre+"&rol="+rol+"&id="+id+"&req="+req+"&correo="+correo;
		
		ajax.open("GET", URL);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divResultado.innerHTML = ajax.responseText
				setTimeout(function(){ Mostrar('src/n_usuario.php'); }, 1000);
			}
		}
		ajax.send(null);
		}
	}
}
function enviar_p(ctrl, ac, banear){
	var url = 'src/g_usuario.php';
	var nombre = document.frmac.nombre.value;
	var rol = document.frmac.rol.value;
	var req = (banear != undefined ? (banear == 4 ? 4 : 5) : (ac == 0 ? (id == "0" ? 1 : 2): 3));
	var	id=(req == 3 || req == 4 || req == 5 ? ac :this.document.frmac.Hidden_Id.value);
	var enviar = (banear != undefined ? (banear == 4 ? (confirm("Desea Banear la cuenta de este Paciente?")) : (confirm("Desea Activar la cuenta este Usuario?"))) : (req == 3 ? (confirm("Desea Eliminar Este Usuario?")) : true));
	if(enviar)
	{		
		divResultado = document.getElementById('reps');
		ajax=objetoAjax();
		var URL=url+"?nombre="+nombre+"&rol="+rol+"&id="+id+"&req="+req;
		
		ajax.open("GET", URL);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divResultado.innerHTML = ajax.responseText
				setTimeout(function(){ Mostrar('src/n_paciente.php'); }, 1000);
			}
		}
		ajax.send(null);
	}
}





function enviar6(ctrl, ac){
	var url = 'src/g_rol.php';


	var nombre=this.document.frmac.nombre.value;
	var Permisos = [];
    jQuery.each($('.chk_permiso'), function (key, value) {
    	if($(this).prop("checked"))
    	{
    		var item = {};
    		item.id =  $(this).attr("value");
    		Permisos.push(item);
    	}
    });
    var req = (ac == 0 ? (id == "0" ? 1 : 2): 3);
	var	id=(req == 3 ? ac :this.document.frmac.Hidden_Id.value);
	var enviar = (req == 3 ? (confirm("Desea Eliminar Este Plan?")) : true);
	parametros = {};
	parametros.nombre = nombre;
	parametros.permisos = Permisos;
	parametros.req = req;
	parametros.id = id;
	if(enviar)
	{
		divResultado = document.getElementById('reps');
		$.ajax({
            type: "POST",
            data: parametros,
            url: url,
            dataType: "html",
            success: function (data) {
               divResultado.innerHTML = data;
               setTimeout(function(){ Mostrar('src/permisologia.php'); }, 1000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });             
		// divResultado = document.getElementById('reps');
		// ajax=objetoAjax();
		// var URL= url;
		// ajax.open("POST", URL);
		// ajax.onreadystatechange=function() {
		// 	if (ajax.readyState==4) {
		// 		divResultado.innerHTML = ajax.responseText
		// 	}
		// }
		// ajax.send("nombre="+nombre);
	}
}
function Limpiar6(flag)
{
	if(flag)
	{
		$("#cantidad").val("");
		$("#porcentaje").val("");
		$("#porcentaje_2").val("");
		$("#porcentaje_3").val("");
		$("#nombre").val("");
		$("#Hidden_Id").val("0");
	 	jQuery.each($('.chk_permiso'), function (key, value) {
	    	$(this).prop("checked", false);
	    	
	    });	
	    $("#frm").css("display", "block");
	    $("#lista").css("display", "none");
	    $("#btn_atras").css("display", "block");
	    $("#btn_nuevo").css("display", "none");
	    $("#wrap").height($("#contentn").height());
	}
	else
	{
	    $("#frm").css("display", "none");
	    $("#lista").css("display", "block");
	    $("#btn_atras").css("display", "none");
	    $("#btn_nuevo").css("display", "block");
	}

}

/*----------------------------------------------------------------*/

function eliminacion(ctrl){
var msj="";
var msj2="";

msj="¿Esta seguro de Eliminar este registro?";	
msj2="Eliminar exitosamente";
	 alertify.confirm(msj, function(e){
	 	if(e){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="src/elimina.php?tabla="+ctrl.attr("tabla")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
							alertify.success(msj2);
								setTimeout(function(){ Mostrar("src/"+ctrl.attr("urle")+".php");select_cliente("src/cliente_select.php","clientes_select") }, 50);
				}
			}
			ajax.send(null)
	 	}
	 })
}
function eliminacion2(ctrl){
var msj="";
var msj2="";

msj="¿Esta seguro de Eliminar este registro?";	
msj2="Eliminar exitosamente";
	 alertify.confirm(msj, function(e){
	 	if(e){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="src/elimina.php?tabla="+ctrl.attr("tabla")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
							alertify.success(msj2);
								setTimeout(function(){ loader11() }, 50);
				}
			}
			ajax.send(null)
	 	}
	 })
}


function edicion(ctrl){
var msj="";
var msj2="";
var color="";
var tipo="";
msj="Habilitarlo";	
msj2="habilitado";
color="#B1DE7E";
tipo="info";
if(ctrl.attr("valor")==0){
msj="Eliminarlo";	
msj2="Eliminado";
color="#DD6B55";
tipo="warning";
}
swal({
  title: "Esta seguro?",
  text: "El registro seleccionado sera "+msj2+"!",
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: "Si, "+msj+"!",
  closeOnConfirm: false
},
function(){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php") }, 50);
				}
			}
			ajax.send(null)

  
	 		
  swal("Eliminado!", "Su registro seleccionado ha sido "+msj2+".", "success");
});




}

function edicion2(ctrl){
var msj="";
var msj2="";
var color="";
var tipo="";
msj="Habilitarlo";	
msj2="habilitado";
color="#B1DE7E";
tipo="info";
if(ctrl.attr("valor")==0){
msj="Inhabilitarlo";	
msj2="Inhabilitado";
color="#DD6B55";
tipo="warning";
}
swal({
  title: "Esta seguro?",
  text: "El registro seleccionado sera "+msj2+"!",
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: "Si, "+msj+"!",
  closeOnConfirm: false
},
function(){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php") }, 50);
				}
			}
			ajax.send(null)

  
	 		
  swal("Inhabilitado!", "Su registro seleccionado ha sido "+msj2+".", "success");
});





}
function edicion3(ctrl){

swal({
  title: "Esta seguro?",
  text: "El registro seleccionado sera eliminado!",
  type: "warning",
  showCancelButton: true,
  confirmButtonColor: "#DD6B55",
  confirmButtonText: "Si, eliminar!",
  closeOnConfirm: false
},
function(){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id2");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php") }, 50);
				}
			}
			ajax.send(null)
	 		
  swal("Inhabilitado!", "Su registro seleccionado ha sido eliminado.", "success");
});





}
function edicion4(ctrl){
var titulo="";
var texto="";
var tipo="";
var color="";
var frase1="";
var frase2="";
var frase3="";
if(ctrl.attr("valor")==0){
texto="El usuario seleccionado sera baneado!";
tipo="warning";
color="#DD6B55";
frase1="Si, Banear!";
frase2="Inhabilitado!";
frase3="Su registro seleccionado ha sido baneado.";
}else{
texto="El usuario seleccionado sera activado!";
tipo="success";
color="#63D144";
frase1="Si, Activar!";
frase2="Activado!";
frase3="Su registro seleccionado ha sido activado.";
}


swal({
  title: "Esta seguro?",
  text: texto,
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: frase1,
  closeOnConfirm: false
},
function(){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id2");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php") }, 50);
				}
			}
			ajax.send(null)
	 		
  swal(frase2, frase3, "success");
});





}
function edicion5(ctrl){
var msj="";
var msj2="";
var color="";
var tipo="";
msj="Entregado";	
msj2="Entregado";
color="#B1DE7E";
tipo="info";

swal({
  title: "Esta seguro?",
  text: "El registro seleccionado sera puesto en "+msj2+"!",
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: "Si, "+msj+"!",
  closeOnConfirm: false
},
function(){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elimxa.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id")+"&id_contenedor="+ctrl.attr("idcontenedor");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php") }, 50);
				}
			}
			ajax.send(null)
	 		
  swal("Realizado!", "Su registro seleccionado ha sido "+msj2+".", "success");
});





}
function edicion6(ctrl){
var msj="";
var msj2="";
var color="";
var tipo="";
msj="Habilitarlo";	
msj2="habilitado";
color="#B1DE7E";
tipo="info";
if(ctrl.attr("valor")==0){
msj="Eliminarlo";	
msj2="Eliminado";
color="#DD6B55";
tipo="warning";
}
swal({
  title: "Esta seguro?",
  text: "El registro seleccionado sera "+msj2+"!",
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: "Si, "+msj+"!",
  closeOnConfirm: false
},
function(){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
								setTimeout(function(){ fink(this.document.v19.id_cliente.value) }, 50);
				}
			}
			ajax.send(null)
	 		
  swal("Inhabilitado!", "Su registro seleccionado ha sido "+msj2+".", "success");
});





}
function elimin(ctrl){
msj="Inhabilitarlo";	
msj2="Inhabilitado";
color="#DD6B55";
tipo="warning";

swal({
  title: "Esta seguro?",
  text: "El registro seleccionado sera "+msj2+"!",
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: "Si, "+msj+"!",
  closeOnConfirm: false
},
function(){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php") }, 50);
				}
			}
			ajax.send(null)
	 		
  swal("Inhabilitado!", "Su registro seleccionado ha sido "+msj2+".", "success");
});





}


function eliminx(ctrl){
msj="Eliminarlo";	
msj2="Eliminado";
color="#DD6B55";
tipo="warning";

swal({
  title: "Esta seguro?",
  text: "El registro seleccionado sera "+msj2+"!",
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: "Si, "+msj+"!",
  closeOnConfirm: false
},
function(){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
								setTimeout(function(){ recarga_fact() }, 50);
				}
			}
			ajax.send(null)
	 		
  swal("Eliminado!", "Su registro seleccionado ha sido "+msj2+".", "success");
});

}

function recarga_fact(){
            $.ajax({
                url: "php/facturacion_frecuente.php",
                type: "get",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#tal_facturacion").html("" + res);
                });		
}


function checkedio(){
     var cont = this.document.formu_check.con.value;
	    jQuery.each($('.chk_permison'), function (key, value) {
    	if($(this).prop("checked"))
    	{

    		cont=cont+1;
    		alert(""+cont);
    	}else{
			cont=cont-1;    		
    	}
			$("#contado2").val(cont);
    });
}

function permisos(nombre, id){
	     $("#permisito").html("<b><i class='fa fa-lock'></i> Permisos de "+ nombre+"</b>");
            $.ajax({
                url: "php/permisos.php?id="+id,
                type: "get",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#permisos").html("" + res);
                });	
}
function cargo(nombre, nombre_rol, id){
	     $("#permisito").html("<b> <span class='label label-success'><i class='fa fa-user'></i> USUARIO: "+nombre.toUpperCase()+"</span> </b>");
            $.ajax({
                url: "php/cargos.php?id="+id,
                type: "get",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#cargos").html("" + res);
                });	
}

function envio_0(){
	            var formData = new FormData(document.getElementById("f1"));
            $.ajax({
                url: "php/anadirchofer.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/chofer.php") }, 50);
                });
}
function envio_1(){
	            var formData = new FormData(document.getElementById("main"));
            $.ajax({
                url: "php/anadircliente.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

                });
}
function envio_2(){
	            var formData = new FormData(document.getElementById("main"));
            $.ajax({
                url: "php/anadirplazo.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/plazos.php") }, 50);
                });
}

function envio_3(){
	            var formData = new FormData(document.getElementById("f58"));
            $.ajax({
                url: "php/anadirmunicipio.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/municipio.php") }, 50);
                });
}

function envio_4(){
	            var formData = new FormData(document.getElementById("f62"));
            $.ajax({
                url: "php/anadirvehiculo.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/vehiculo.php") }, 50);
                });
}
function envio_5(){
	            var formData = new FormData(document.getElementById("f60"));
            $.ajax({
                url: "php/anadirmodelo.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/modelo.php") }, 50);
                });
}
function envio_6(){
	if((this.document.f63.txttipo.value==0)&&(this.document.f63.txtdetalle.value=="")){
							swal({
							title: "No deje campos vacios",
							text: "Debe ingresar el tipo de contenedor",
							type:"error"
							},
							function(e){   
								if (e) {
									

								} 
							});
}else{
	            var formData = new FormData(document.getElementById("f63"));
            $.ajax({
                url: "php/anadircontenedor.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/faltantes.php") }, 50);
                });
}

}
function envio_7(){
	            var formData = new FormData(document.getElementById("u57"));
            $.ajax({
                url: "php/anadircargo.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/permisologia.php") }, 50);
                });
}
function envio_8(){
	            var formData = new FormData(document.getElementById("f61"));
            $.ajax({
                url: "php/anadirruta.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ fink(this.document.v19.id_cliente.value) }, 50);
                });
}
function envio_9(){
	            var formData = new FormData(document.getElementById("f64"));
            $.ajax({
                url: "php/anadirviaje.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/faltantes.php") }, 50);
                });
}
function envio_10(){
	            var formData = new FormData(document.getElementById("f75"));
            $.ajax({
                url: "php/anadirfrecuente.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ recarga_fact() }, 50);
                });
}

function envio_11(){
	            var formData = new FormData(document.getElementById("f83"));
            $.ajax({
                url: "php/anadircategoria.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/categoria.php") }, 50);
                });
}
function envio_12(){
	            var formData = new FormData(document.getElementById("f71"));
            $.ajax({
                url: "php/anadirfactura.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){Mostrar('php/facturacion.php'); carga_facturacion1(); carga_facturacion2(); totii(); }, 50);
                });
}
function envio_13(){
	            var formData = new FormData(document.getElementById("f171"));
            $.ajax({
                url: "php/anadirpago.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){Mostrar('php/pago_chofer.php'); totii2(); }, 50);
                });
}
function envio_14(){
	            var formData = new FormData(document.getElementById("fermi"));
            $.ajax({
                url: "php/anadirtarifa.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ fink(this.document.v19.id_cliente.value) }, 50);
                });
}
function envio_15(){
	            var formData = new FormData(document.getElementById("f56"));
            $.ajax({
                url: "php/anadirlicencia.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar('php/chofer.php'); }, 50);
                });
}
function envio_16(formulario, lugar, lugar2,div){
	            var formData = new FormData(document.getElementById(formulario));
            $.ajax({
                url: lugar,
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#"+div).html("" + res);
				setTimeout(function(){ Mostrar(lugar2); }, 50);
                });
}
function envio_17(formulario, lugar,div){
	            var formData = new FormData(document.getElementById(formulario));
            $.ajax({
                url: lugar,
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#"+div).html("" + res);

                });
}
function cambio_1(capa, capa2, capa3, capa4, capa5) {
soy = document.getElementById(capa);
soy.style.display = (soy.style.display == "block") ? "none" : "block";

soy2 = document.getElementById(capa2);
soy2.style.display = (soy2.style.display == "none") ? "block" : "none";

soy3 = document.getElementById(capa3);
soy3.style.display = (soy3.style.display == "block") ? "none" : "block";

soy4 = document.getElementById(capa4);
soy4.style.display = (soy4.style.display == "none") ? "block" : "none";

soy5 = document.getElementById(capa5);
soy5.style.display = (soy5.style.display == "block") ? "none" : "block";
}

function cambio_2(capa, capa2,capa3) {
soy = document.getElementById(capa);
soy.style.display = (soy.style.display == "block") ? "none" : "block";

soy2 = document.getElementById(capa2);
soy2.style.display = (soy2.style.display == "none") ? "block" : "none";

soy3 = document.getElementById(capa3);
soy3.style.display = (soy3.style.display == "block") ? "none" : "block";



}
function cambio_3(capa, capa2, capa3) {
soy = document.getElementById(capa);
soy.style.display = (soy.style.display == "block") ? "none" : "block";

soy2 = document.getElementById(capa2);
soy2.style.display = (soy2.style.display == "none") ? "block" : "none";

soy3 = document.getElementById(capa3);
soy3.style.display = (soy3.style.display == "block") ? "none" : "block";

}
function cambio_4(capa,	 capa2,capa3) {
soy = document.getElementById(capa);
soy.style.display = (soy.style.display == "block") ? "none" : "block";

soy2 = document.getElementById(capa2);
soy2.style.display = (soy2.style.display == "none") ? "block" : "none";

soy3 = document.getElementById(capa3);
soy3.style.display = (soy3.style.display == "block") ? "none" : "block";



}
function cambio_5(capa, capa6, capa8, capa2, capa7, capa9, capa3,  capa4, capa5) {
soy = document.getElementById(capa);
soy.style.display = (soy.style.display == "block") ? "none" : "block";

soy2 = document.getElementById(capa2);
soy2.style.display = (soy2.style.display == "none") ? "block" : "none";

soy3 = document.getElementById(capa3);
soy3.style.display = (soy3.style.display == "block") ? "none" : "block";

soy4 = document.getElementById(capa4);
soy4.style.display = (soy4.style.display == "none") ? "block" : "none";

soy5 = document.getElementById(capa5);
soy5.style.display = (soy5.style.display == "block") ? "none" : "block";

soy6 = document.getElementById(capa6);
soy6.style.display = (soy6.style.display == "block") ? "none" : "block";

soy7 = document.getElementById(capa7);
soy7.style.display = (soy7.style.display == "none") ? "block" : "none";

soy8 = document.getElementById(capa8);
soy8.style.display = (soy8.style.display == "block") ? "none" : "block";

soy9 = document.getElementById(capa9);
soy9.style.display = (soy9.style.display == "none") ? "block" : "none";

}


function edito_1(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_1.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/permisologia.php") }, 50);
                });
}
function edito_2(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_2.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/cliente.php") }, 50);
                });
}
function edito_3(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_3.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/marca.php") }, 50);
                });
}
function edito_4(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_4.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/n_usuario1.php") }, 50);
                });
}
function edito_5(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_5.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/modelo.php") }, 50);
                });
}
function edito_6(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_6.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/configuracion.php") }, 50);
                });
}
function edito_7(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_7.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/configuracion.php") }, 50);
                });
}
function edito_8(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_8.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/configuracion.php") }, 50);
                });
}
function edito_9(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_9.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/configuracion.php") }, 50);
                });
}
function edito_10(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_10.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/configuracion.php") }, 50);
                });
}

function edito_11(){

              var formData = new FormData(document.getElementById("main2"));

            $.ajax({
                url: "php/edito_12.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/skin.php") }, 50);
                  });

}
function edito_12(campo, campoid){
campo = document.getElementById(campo);
            $.ajax({
                url: "php/edito_13.php?campo="+campo.value+"&campoid="+campoid,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/configuracion.php") }, 50);
                });
}

function f_check(ctrl){
var codpermiso=ctrl.attr("codpermiso");
var codrol=ctrl.attr("codrol");
var nombre=ctrl.attr("id");
var valor=ctrl.attr("value");

	$.ajax({
                url: "php/per_check.php?valor="+valor+"&cod_rol="+codrol+"&cod_permiso="+codpermiso,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

                });
            if(valor==0){    
				$("#"+nombre).val("1");
			}else{
				$("#"+nombre).val("0");
			}
}
function f_check2(ctrl){

var codusuario=ctrl.attr("codusuario");
var nombre=ctrl.attr("id");

var valor=ctrl.attr("value");
	     $("#permisito").html("<b><i class='fa fa-user'></i> Usuario: <font color='green'>"+ ctrl.attr("nombreusuario")+"</font><br><i class='fa fa-sitemap'></i> Cargo: <font color='green'>"+ctrl.attr("nombrerol")+"</font> </b>");
	$.ajax({
                url: "php/per_check2.php?valor="+valor+"&cod_usuario="+codusuario,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

                });

}

function toqueton(ctrl){

	this.document.f64.txtcontenedor.value=ctrl.attr("contenedor");
	this.document.f64.id_contenedor.value=ctrl.attr("id");
	this.document.f64.tipo.value=ctrl.attr("tipo");	
	this.document.f64.txtcliente.value="";	
}
function toqueton1(ctrl){

	this.document.f64.id_vehiculo.value=ctrl.attr("id");
	this.document.f64.txtvehiculo.value=ctrl.attr("placa");
}
function toqueton2(ctrl){

	this.document.f64.id_chofer.value=ctrl.attr("id");
	this.document.f64.txtchofer.value=ctrl.attr("nombre");
}
function toqueton3(ctrl){
var valor1=this.document.f71.id_cliente.value
var valor2=ctrl.attr("id");

            $.ajax({
                url: "php/contenedores_temp.php?id="+valor2,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       			processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
                    setTimeout(function(){ carga_facturacion1(); carga_facturacion2(); totii() }, 50);
                });

}
function toqueton4(ctrl){
var valor1=this.document.f171.id_choferx.value
var valor2=ctrl.attr("id");

            $.ajax({
                url: "php/choferes_temnp.php?id="+valor2,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       			processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
                    setTimeout(function(){ chofer_facturacion1(); totii2() }, 50);
                });

}
function toqueton5(ctrl){

	this.document.fermi.id.value=ctrl.attr("id");
	this.document.fermi.txtprecio.value=ctrl.attr("precio");
}

function toketon1(ctrl){

	this.document.f71.txtcargo.value=ctrl.attr("valor");

}
function toketon2(ctrl){
	this.document.f71.id_cliente.value=ctrl.attr("id");
	this.document.f71.txtcliente.value=ctrl.attr("nombre");
	this.document.f71.txtcliente.disabled=true;	

}
function toketon3(ctrl){
	this.document.f171.id_choferx.value=ctrl.attr("id");
	this.document.f171.txtchofer.value=ctrl.attr("nombre");
	this.document.f171.txtchofer.disabled=true;	

}



function funct_6(ctrl){

this.document.e2.id_chofer.value = ctrl.attr("id");
$("#nacionalidade").val(ctrl.attr("nacionalidad"));
$("#txtcedulae").val(ctrl.attr("cedula"));	
$("#idtxtnombree").val(ctrl.attr("nombre"));
$("#idtxtapellidoe").val(ctrl.attr("apellido"));
$("#idtxtcelulare").val(ctrl.attr("celular"));
$("#idtxtfechae").val(ctrl.attr("fecha"));
$("#idtxtdireccione").val(ctrl.attr("direccion"));

}
function funct_7(ctrl){

this.document.e3.id_vehiculo.value = ctrl.attr("id");
this.document.e3.txtplaca.value=ctrl.attr("placa");
this.document.e3.txtchasis.value=ctrl.attr("chasis");	
this.document.e3.txttamano.value=ctrl.attr("tamano");
this.document.e3.txtnumchasis.value=ctrl.attr("numcha");

this.document.e3.txtcolor.value=ctrl.attr("color");
this.document.e3.txtano.value=ctrl.attr("ano");
this.document.e3.txtnummotor.value=ctrl.attr("nummot");
this.document.e3.txtpanapass.value=ctrl.attr("panapass");
this.document.e3.txtfass.value=ctrl.attr("fass");
}
function funct_8(ctrl){
	     $("#licencianombre").html(ctrl.attr("nombre"));
this.document.f56.id.value = ctrl.attr("id");
this.document.f56.txttipo.value=ctrl.attr("tipo");
this.document.f56.txtfecha.value=ctrl.attr("fecha");

}

function validacion_usuarios(ctrl){
var titulo="";
var texto="";
var tipo="";
var color="";
var frase1="";
var frase2="";
var frase3="";
if(ctrl.attr("valor")==0){
texto="El usuario seleccionado sera rechazado!";
tipo="warning";
color="#DD6B55";
frase1="Si, rechazar!";
frase2="Rechazado!";
frase3="Su registro seleccionado ha sido rechazado.";
}else{
texto="El usuario seleccionado sera aceptado!";
tipo="success";
color="#63D144";
frase1="Si, Activar!";
frase2="Activado!";
frase3="Su registro seleccionado ha sido aceptado.";
}


swal({
  title: "Esta seguro?",
  text: texto,
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: frase1,
  closeOnConfirm: false
},
function(){
									setTimeout(function(){ $("#myModal2").modal("hide") }, 100);
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id2");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText

								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php") }, 500);

				}
			}
			ajax.send(null)
	 		
  swal(frase2, frase3, "success");
});





}
function load_json(){ 
var msj1="Recargado";
var msj2="";
var color="";
var tipo="";
msj2="";
color="#6A6C6D";
tipo="success";
swal({
  title: "Recargar registro de la API?",
  text: "Los registros se actulizaran!",
  type: tipo,
  showCancelButton: true,
  confirmButtonColor: color,
  confirmButtonText: "Si, recargar!",
  closeOnConfirm: false
},
function(){
            $.ajax({
                url: "php/recargar.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
                                setTimeout(function(){ Mostrar("php/subasta.php") }, 50);
                });
  swal(msj1+"!", "Los registros se han actualizado.", "success");
});   
}
function eliminar_temp_compra(id){
            $.ajax({
                url: "php/eliminar_temporal_compra.php?id="+id,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#tabla_compra").html("" + res);

                });	
}
function eliminar_temp_remision(id){
            $.ajax({
                url: "php/eliminar_temporal_remision.php?id="+id,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#div_remision").html("" + res);

                });	
}
function eliminar_temp_transferencia(id){
            $.ajax({
                url: "php/eliminar_temporal_transferencia.php?id="+id,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#tabla_compra").html("" + res);

                });	
}