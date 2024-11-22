function objetoAjax(){	var xmlhttp=false;try {xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");} catch (e) {try {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");} catch (E) {xmlhttp = false;}}if (!xmlhttp && typeof XMLHttpRequest!='undefined') {xmlhttp = new XMLHttpRequest();}return xmlhttp;}
function Mostrar(url2){
            alert(url2);

divResultado = document.getElementById('contenido');
	ajax=objetoAjax();
	var URL=url2;
	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}
	ajax.send(null)
/*

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
                
/*            $.ajax({
                url: url2,
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       			processData: false
            })
                .done(function(res){
                    $("#contenido").html("" + res);
                });*/
/*
	*/
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

function Mostrar_fal(url){
	divResultado = document.getElementById('contenido');
	ajax=objetoAjax();
	var URL=url;
	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
				setTimeout(function(){ 
				 loader5('Seleccione cliente');
				 loader6('Seleccione tipo chofer') }, 50);
		}
	}
	ajax.send(null)
}

 function Mostrar_n(url){
	 
	divResultado = document.getElementById('lalin');
	ajax=objetoAjax();
	var URL=url;
	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText

		}
	}
	ajax.send(null)
}


function enviar(){

            var formData = new FormData(document.getElementById("f10"));
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
				alertify.alert("El sistema se actualizara en unos segundos presione OK y espere", function (e) {
				    if (e) {
				        history.go(-1);
				    }
				});				
				setTimeout(function(){ this.document.location.href="vistacliente.php" }, 5000);
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





function funcion(url,fic){
	

}
function editar2(ctrl)
{
	$("#nombre").val(ctrl.attr("nombre"));
	$("#rol").val(ctrl.attr("rol"));
	$("#Hidden_Id").val(ctrl.attr("id"));
}
function Limpiar2()
{
	$("#nombre").val("");
	$("#rol").val(1);
	$("#Hidden_Id").val("0");

}
function enviar3(url){
	    

		
	divResultado = document.getElementById('reps');
	ajax=objetoAjax();
	var URL=url;

	ajax.open("GET", URL);

	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}
	ajax.send(null);
	
}

function editar4(ctrl)
{
	$("#nombre").val(ctrl.attr("nombre"));
	$("#tipo").val(ctrl.attr("tipo"));
	$("#Hidden_Id").val(ctrl.attr("id"));
}
function Limpiar4()
{
	$("#nombre").val("");
	$("#tipo").val("");
	$("#Hidden_Id").val("0");
}

function editar5(ctrl)
{
	$("#nombre").val(ctrl.attr("nombre"));
	$("#Hidden_Id").val(ctrl.attr("id"));
}
function Limpiar5()
{
	$("#nombre").val("");
	$("#Hidden_Id").val("0");
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
function editar6(ctrl,max)
{
	Limpiar6(true);
	$("#nombre").val(ctrl.attr("nombre"));
	$("#porcentaje").val(ctrl.attr("porcentaje"));
	$("#porcentaje_2").val(ctrl.attr("porcentajep"));
	$("#porcentaje_3").val(ctrl.attr("porcentajea"));
	$("#cantidad").val(max);	
	$("#Hidden_Id").val(ctrl.attr("id"));
	var permisos = JSON.parse(ctrl.attr("permisos").replace(/\'/g, '"'));
	jQuery.each(permisos, function (key, value) {
		$(value.id_chek).prop("checked", (value.permitido == 1))
	});
	$("#wrap").height($("#content").height()+80);
}
function editar_cate(ctrl)
{
	Limpiar6(true);
	$("#nombre").val(ctrl.attr("nombre"));
	$("#Hidden_Id").val(ctrl.attr("id"));
	var permisos = JSON.parse(ctrl.attr("permisos").replace(/\'/g, '"'));
	jQuery.each(permisos, function (key, value) {
		$(value.id_chek).prop("checked", (value.permitido == 1))
	});
	$("#wrap").height($("#content").height()+80);
}

/*----------------------------------------------------------------*/
function Mostrar1(){
	divResultado = document.getElementById('pantalla');
	ajax=objetoAjax();
	var URL="pagina1.html";
	ajax.open("GET", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}
	ajax.send(null)
}




function iniciar(){		
	alert("Ingrese Los Datos del Producto en la Casillas Respectivas");
	this.document.frmproducto.txtcodigo.focus();
}
function validar(){
	var precio=this.document.frmproducto.txtprecio.value;	
	var costo=this.document.frmproducto.txtcosto.value;	
	var cantidad=this.document.frmproducto.txtcantidad.value;	
	if (isNaN(precio)){
		alert("Solo Debe Introducir Cantidades en Numero, Ejemplo: 100.50 � 150");
		this.document.frmproducto.txtprecio.value="";
		this.document.frmproducto.txtprecio.focus();
	}
	if (isNaN(costo)){
		alert("Solo Debe Introducir Cantidades en Numero, Ejemplo: 100.50 � 150");
		this.document.frmproducto.txtcosto.value="";
		this.document.frmproducto.txtcosto.focus();
	}
	if (isNaN(cantidad)){
		alert("Solo Debe Introducir Cantidades en Numero, Ejemplo: 1 � 5");
		this.document.frmproducto.txtcantidad.value="";
		this.document.frmproducto.txtcantidad.focus();
	}
}
  

var numeros="0123456789";
var letras="abcdefghyjklmnñopqrstuvwxyz";
var letras_mayusculas="ABCDEFGHYJKLMNÑOPQRSTUVWXYZ";

function tiene_numeros(texto){
   for(i=0; i<texto.length; i++){
      if (numeros.indexOf(texto.charAt(i),0)!=-1){
         return 1;
      }
   }
   return 0;
} 

function tiene_letras(texto){
   texto = texto.toLowerCase();
   for(i=0; i<texto.length; i++){
      if (letras.indexOf(texto.charAt(i),0)!=-1){
         return 1;
      }
   }
   return 0;
} 

function tiene_minusculas(texto){
   for(i=0; i<texto.length; i++){
      if (letras.indexOf(texto.charAt(i),0)!=-1){
         return 1;
      }
   }
   return 0;
} 

function tiene_mayusculas(texto){
   for(i=0; i<texto.length; i++){
      if (letras_mayusculas.indexOf(texto.charAt(i),0)!=-1){
         return 1;
      }
   }
   return 0;
} 

function seguridad_clave(clave){
	var seguridad = 0;
	if (clave.length!=0){
		if (tiene_numeros(clave) && tiene_letras(clave)){
			seguridad += 30;
		}
		if (tiene_minusculas(clave) && tiene_mayusculas(clave)){
			seguridad += 30;
		}
		if (clave.length >= 4 && clave.length <= 5){
			seguridad += 10;
		}else{
			if (clave.length >= 6 && clave.length <= 8){
				seguridad += 30;
			}else{
				if (clave.length > 8){
					seguridad += 40;
				}
			}
		}
	}
	return seguridad				
}	

function muestra_seguridad_clave(clave,formulario){
	seguridad=seguridad_clave(clave);
	formulario.seguridad.value=seguridad + "%";
	document.getElementById('segur').style.background='#CCCCCC';
}


function enviar_cate(ctrl, ac){
	var url = 'src/g_cate.php';

	var nombre=this.document.frmac.nombre.value;
	var Permisos = [];

    var req = (ac == 0 ? (id == "0" ? 1 : 2): 3);
	var	id=(req == 3 ? ac :this.document.frmac.Hidden_Id.value);
	var enviar = (req == 3 ? (confirm("Desea Eliminar Esta Categoria?")) : true);
	parametros = {};
	parametros.nombre = nombre;
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
               setTimeout(function(){ Mostrar('src/categorias.php'); }, 1000);
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



function editar_ser(ctrl)
{
	$("#descrip").val(ctrl.attr("descrip"));
	$("#nombre").val(ctrl.attr("nombre"));
	$("#rol").val(ctrl.attr("rol"));
	$("#Hidden_Id").val(ctrl.attr("id"));
	$("#precio").val(ctrl.attr("precio"));
}

function env_formu(){
			codigo=this.document.formuploadajax.cod.value;
			usuario=this.document.formuploadajax.cod_u.value;
            var formData = new FormData(document.getElementById("formuploadajax"));
            formData.append("dato", "valor");
            //formData.append(f.attr("name"), $(this)[0].files[0]);
            $.ajax({
                url: "php/guarda_foto.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("Respuesta: " + res);
				setTimeout(function(){ buscar_ultim(codigo, usuario); }, 1000);      
					
                });
}
function env_formu2(){
			codigo=this.document.formuploadajax.cod.value;
			usuario=this.document.formuploadajax.cod_u.value;
            var formData = new FormData(document.getElementById("formuploadajax"));
            formData.append("dato", "valor");
            //formData.append(f.attr("name"), $(this)[0].files[0]);
            $.ajax({
                url: "guarda_foto.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("Respuesta: " + res);
				setTimeout(function(){ buscar_ultim2(codigo, usuario); }, 1000);      
					
                });
}


function contenidocito(){
	url="";
if(this.document.formu.password.value==this.document.formu.password2.value){
	url="boton.php";
	
}else{
	url="boton2.php";
}

	divResultado = document.getElementById('chiamo');
	ajax=objetoAjax();

	var URL=url;

	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
							setTimeout(function(){ contenidocito2(); }, 500);
		}
	}
	ajax.send(null)	
}


function contenidocito2(){

if(this.document.formu.password.value==this.document.formu.password2.value){
	url="1.php";
	
}else{
	url="2.php";
}
if((this.document.formu.password.value=="")&&(this.document.formu.password2.value=="")){
	url="3.php";
}

	divResultado = document.getElementById('carlo');
	ajax=objetoAjax();

	var URL=url;

	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}
	ajax.send(null)	
}


function ShowImagePreview( files )
{
    if( !( window.File && window.FileReader && window.FileList && window.Blob ) )
    {
      alert('The File APIs are not fully supported in this browser.');
      return false;
    }

    if( typeof FileReader === "undefined" )
    {
        alert( "Filereader undefined!" );
        return false;
    }

    var file = files[0];

    if( !( /image/i ).test( file.type ) )
    {
        alert( "File is not an image." );
        return false;
    }

    reader = new FileReader();
    reader.onload = function(event) 
            { var img = new Image; 
              img.onload = UpdatePreviewCanvas; 
              img.src = event.target.result;  }
    reader.readAsDataURL( file );
}

function UpdatePreviewCanvas()
{
    var img = this;
    var canvas = document.getElementById( 'previewcanvas' );

    if( typeof canvas === "undefined" 
        || typeof canvas.getContext === "undefined" )
        return;

    var context = canvas.getContext( '2d' );

    var world = new Object();
    world.width = canvas.offsetWidth;
    world.height = canvas.offsetHeight;

    canvas.width = world.width;
    canvas.height = world.height;

    if( typeof img === "undefined" )
        return;

    var WidthDif = img.width - world.width;
    var HeightDif = img.height - world.height;

    var Scale = 0.0;
    if( WidthDif > HeightDif )
    {
        Scale = world.width / img.width;
    }
    else
    {
        Scale = world.height / img.height;
    }
    if( Scale > 1 )
        Scale = 1;

    var UseWidth = Math.floor( img.width * Scale );
    var UseHeight = Math.floor( img.height * Scale );

    var x = Math.floor( ( world.width - UseWidth ) / 2 );
    var y = Math.floor( ( world.height - UseHeight ) / 2 );

    context.drawImage( img, x, y, UseWidth, UseHeight );  
}
 function Mostrar_cuco(ctrl){

	url="php/facturas_histo.php?cod_factura="+ctrl.attr("id")+"&tyu=1";
	divResultado = document.getElementById('contenido');
	ajax=objetoAjax();
	var URL=url;
	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText

		}
	}
	ajax.send(null)
}
function loader(str)
{
var xmlhttp;
 
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("POST","proc3.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);
}

function loader2(str)
{
var xmlhttp;
 
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("myDiv2").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("POST","proc4.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);
}
function loader3(str)
{
var xmlhttp;
 
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("myDiv3").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("POST","proc3.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);
}
function edit_form_1(){
            var formData = new FormData(document.getElementById("edit1"));
            $.ajax({
                url: "php/editarservicio.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/servicio.php") }, 50);      
					
                });
}

function e_form1(){
            var formData = new FormData(document.getElementById("f2"));
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
				setTimeout(function(){ Mostrar("php/chofer_list.php") }, 50);      
					
                });
}
function e_form2(){

            var formData = new FormData(document.getElementById("f9"));
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
				setTimeout(function(){ Mostrar("php/asegura.php"); select_cliente("php/cliente_select.php","clientes_select") }, 50);      
					
                });
$("#idtxtcliente").val("");
}
function e_form3(){

            var formData = new FormData(document.getElementById("f7"));
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

				setTimeout(function(){ Mostrar("php/municipio_list.php") }, 50);      
                });
                $("#txtmunicipio").val("");
}
function marcon_1(){
	divResultado = document.getElementById('marcon1');
	ajax=objetoAjax();
	var URL="php/marcon_1.php";
	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText;
		}
	}
	ajax.send(null);
}
function marcon_2(){
	divResultado = document.getElementById('marcon2');
	ajax=objetoAjax();
	var URL="php/marcon_2.php";
	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText;
		}
	}
	ajax.send(null);
}
function marcon_3(){
	divResultado = document.getElementById('marcon3');
	ajax=objetoAjax();
	var URL="php/marcon_3.php";
	ajax.open("POST", URL);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText;
		}
	}
	ajax.send(null);
}
function e_form4(){

            var formData = new FormData(document.getElementById("f5"));
            $.ajax({
                url: "php/anadirmarca.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

				setTimeout(function(){ Mostrar("php/marca.php") }, 50);

                });
				setTimeout(function(){ marcon_1(); }, 500);
				setTimeout(function(){ marcon_2(); }, 600);
				setTimeout(function(){ marcon_3(); }, 650);
                $("#txtmarca").val(0);
}
function e_form5(){

            var formData = new FormData(document.getElementById("f6"));
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
                $("#idtxtmodelo").val("");
				setTimeout(function(){ Mostrar("php/modelo.php") }, 50);
                });
}
function e_form6(){

            var formData = new FormData(document.getElementById("f3"));
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
                $("#idtxtmodelo").val("");

				setTimeout(function(){ Mostrar("php/vehiculo.php"); limp_7() }, 50);
                });
$("#txtfass").val("");
$("#txtpanapass").val("");

}
function e_form7(){

            var formData = new FormData(document.getElementById("f8"));
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

				setTimeout(function(){ Mostrar("php/ruta.php") }, 50);
                });
                $("#txtdesde").val("");
                $("#txthasta").val("");
}
function caton_1(){
            $.ajax({
                url: "php/caton_1.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#caton").html("" + res);
                });	
}
function caton_2(){
            $.ajax({
                url: "php/caton_2.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#caton_2").html("" + res);
                });	
}
function e_form8(){

            var formData = new FormData(document.getElementById("f30"));
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

				setTimeout(function(){ Mostrar("php/categoria.php"); caton_1(); caton_2()
				 }, 50);
                });

}
function e_form9(){

            var formData = new FormData(document.getElementById("f31"));
            $.ajax({
                url: "php/anadirservicio.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

				setTimeout(function(){ Mostrar("php/servicio.php") }, 50);
                });
}
function e_form10(){

            var formData = new FormData(document.getElementById("f32"));
            $.ajax({
                url: "php/anadirrepuesto.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

				setTimeout(function(){ Mostrar("php/repuesto.php") }, 50);
                });
}
function e_form11(){

            var formData = new FormData(document.getElementById("f33"));
            $.ajax({
                url: "php/ingresarrepuesto.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

				setTimeout(function(){ Mostrar("php/repuesto.php") }, 50);
                });
}
function e_form12(){

            var formData = new FormData(document.getElementById("f34"));
            $.ajax({
                url: "php/actualizarrepuesto.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

				setTimeout(function(){ Mostrar("php/repuesto.php") }, 50);
                });
}
function e_form13(){

            var formData = new FormData(document.getElementById("f35"));
            $.ajax({
                url: "php/anadirmantenimiento.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

				setTimeout(function(){ Mostrar("php/mantenimiento_a.php") }, 50);
                });
}
function e_form14(){
this.document.f35.txtfecha.value = "dd-mm-AAAA";
this.document.f35.id_vehiculo.value = this.document.f36.id_vehiculo.value;
this.document.f35.txtserial.value = this.document.f36.txtserial.value;
this.document.f35.id_servicio.value = this.document.f36.txtservicio.value;
this.document.f35.txtservicio.value = "Servicio Seleccionado.!";
if((this.document.f36.id_vehiculo.value=="")||(this.document.f36.txtservicio.value=="Seleccione Servicio")||(this.document.f36.txtnombremecanico.value=="")||(this.document.f36.txtcostomecanico.value=="")){
							alertify.alert("No debe dejar campos vacios", function (e) {
							    if (e) {
							    							    }
							});
}else{

            var formData = new FormData(document.getElementById("f36"));
            $.ajax({
                url: "php/anadirmantenimiento2.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

				setTimeout(function(){ Mostrar("php/mantenimiento_b.php") }, 50);
                });
}
}
function e_form_asignar(){

            var formData = new FormData(document.getElementById("f21"));
            $.ajax({
                url: "php/asigna.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);

				setTimeout(function(){ Mostrar("php/asignacion.php");
				 loader5('Seleccione cliente');
				 loader6('Seleccione tipo chofer');
				 }, 50);
                });
                $("#txtdesde").val("");
                $("#txthasta").val("");
}
function reporte_1(){

            var formData = new FormData(document.getElementById("f22"));
            $.ajax({
                url: "php/reporteOrdenesSemanal.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#contenido").html("" + res);

                });
}

function e_form_vehiculo2(){
     $("#vehinom").html("Vehiculos de "+ this.document.f18.nom_contratante.value);
     this.document.f17.id_contratante.value= this.document.f18.id_contratante.value;
     this.document.f17.nom_contratante.value=this.document.f18.nom_contratante.value;

            var formData = new FormData(document.getElementById("f18"));
            $.ajax({
                url: "php/anadirvehiculo2.php",
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
			divResultado = document.getElementById('vehicom');
			ajax=objetoAjax();
			var URL="php/vehicom.php?id="+this.document.f18.id_contratante.value;
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
				}
			}
			ajax.send(null)

}

function e_form_rutas(){
            var formData = new FormData(document.getElementById("f15"));
            $.ajax({
                url: "php/anadirruta_cliente.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ select_cliente("php/cliente_select.php","clientes_select") }, 50);
                });
			divResultado = document.getElementById('rutacli');
			ajax=objetoAjax();
			var URL="php/rutacli.php?id="+this.document.f15.id_cliente.value;
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText

				}
			}
			ajax.send(null)

}
function e_form_rutas2(){
            var formData = new FormData(document.getElementById("f15_2"));
            $.ajax({
                url: "php/anadirruta_cliente.php",
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
			divResultado = document.getElementById('rutacli');
			ajax=objetoAjax();
			var URL="php/rutacli.php?id="+this.document.f15_2.id_cliente.value;
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
				setTimeout(function(){ select_cliente("php/cliente_select.php","clientes_select") }, 50);
				}
			}
			ajax.send(null)

}
function e_form_faltantes(){
if(this.document.f13.txtcliente.value=="0"){
				alertify.error("Debe seleccionar un cliente");
}else{
            var formData = new FormData(document.getElementById("f13"));
            $.ajax({
                url: "php/guardarExcel.php",
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
                this.document.f13.txtarchivo.value="";

}
}

function funct_datos(ctrl){
	this.document.f10.txtnombre.value = ctrl.attr("nombre");
	this.document.f10.txtapellido.value = ctrl.attr("apellido");
	this.document.f10.txtcorreo.value = ctrl.attr("correo");
	this.document.f10.txtcelular.value = ctrl.attr("telefono");
	this.document.f10.txtfecha.value = ctrl.attr("fecha");
}


function funct_1(ctrl){
	this.document.e9.id_asegura.value = ctrl.attr("id");
	$("#idtxtaseguradorae").val(ctrl.attr("nombre"));
}
function funct_2(ctrl){
	this.document.e7.id_municipio.value = ctrl.attr("id");
	this.document.e7.idtxtmunicipioe.value=ctrl.attr("nombre");
}
function funct_3(ctrl){
	this.document.e5.id_marca.value = ctrl.attr("id");
	this.document.e5.idtxtmarcae.value=ctrl.attr("nombre");

}
function funct_4(ctrl){
	this.document.e6.id_modelo.value = ctrl.attr("id");
	$("#idtxtmodeloe").val(ctrl.attr("nombre"));
}
function funct_5(ctrl){
	this.document.e8.id_ruta.value = ctrl.attr("id");
	$("#idtxtrutae").val(ctrl.attr("nombre"));
$("#idtxtprecioe").val(ctrl.attr("precio"));	
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

	$("#id_choferx").val(ctrl.attr("id"));

	$("#idtxtchoferli").val(ctrl.attr("nombre"));
$("#idtxttipoli").val(ctrl.attr("tipo"));	
$("#idtxtfechav").val(ctrl.attr("fecha"));
}
function funct_9(ctrl){
	this.document.f16.id_contratante.value = ctrl.attr("idpo");
	$("#txtcontra").val(ctrl.attr("nombre"));
	$("#idtxtcelularcho").val(ctrl.attr("telefono"));	
	$("#idtxtpoliza").val(ctrl.attr("poliza"));
	$("#txtgrupo").val(ctrl.attr("grupo"));
    $("#txtcorreocontra").val(ctrl.attr("correo"));	
}
function funct_10(ctrl){
     $("#vehinom").html("Vehiculos de "+ ctrl.attr("nombre"));
     this.document.f17.id_contratante.value=ctrl.attr("id");
     this.document.f17.nom_contratante.value=ctrl.attr("nombre");

			divResultado = document.getElementById('vehicom');
			ajax=objetoAjax();
			var URL="php/vehicom.php?id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
				}
			}
			ajax.send(null)

}

function funct_11(){
this.document.f18.id_contratante.value=this.document.f17.id_contratante.value;
this.document.f18.nom_contratante.value=this.document.f17.nom_contratante.value;
$("#idtxtplaca").val("");
$("#idtxtchasis").val("");	
$("#idtxttamano").val("");
$("#idtxtnumchasis").val("");
$("#idtxtcolor").val("#000");
$("#idtxtano").val("");
$("#idtxtnummotor").val("");
}
function funct_12(ctrl){

this.document.e3_v.id_vehiculo.value = ctrl.attr("id");
$("#idtxtplacae").val(ctrl.attr("placa"));
$("#idtxtchasise").val(ctrl.attr("chasis"));	
$("#idtxttamanoe").val(ctrl.attr("tamano"));
$("#idtxtnumchasise").val(ctrl.attr("numcha"));

$("#idtxtcolore").val(ctrl.attr("color"));
$("#idtxtanoe").val(ctrl.attr("ano"));
$("#idtxtnummotore").val(ctrl.attr("nummot"));

}

function funct_13(ctrl){
     $("#cliecon").html("Tarifas de "+ ctrl.attr("nombre"));
     this.document.f19.id_cliente.value=ctrl.attr("id");
     this.document.f19.nom_cliente.value=ctrl.attr("nombre");
				setTimeout(function(){ fink(ctrl.attr("id")) }, 50);

}
function fink(val){
			divResultado = document.getElementById('rutacli');
			ajax=objetoAjax();
			var URL="php/rutacli.php?id="+val;
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
				}
			}
			ajax.send(null)
}

function funct_14(){
this.document.f15.id_cliente.value = this.document.f19.id_cliente.value;
this.document.f15.txtcliente.value = this.document.f19.nom_cliente.value;
}
function funct_15(ctrl){
this.document.f15_2.id_cliente.value = this.document.f19.id_cliente.value;
this.document.f15_2.id_ruta.value = ctrl.attr("id");
this.document.f15_2.txtcliente.value = this.document.f19.nom_cliente.value;
this.document.f15_2.txt20pies.value = ctrl.attr("piesa");
this.document.f15_2.txt40pies.value = ctrl.attr("piesb");
this.document.f15_2.txt20piesRF.value = ctrl.attr("piesc");
this.document.f15_2.txt40piesRF.value = ctrl.attr("piesd");
$("#idtxtruta3").val(ctrl.attr("ruta"));
}
function funct_16(ctrl){
this.document.f30.id_categoria.value = ctrl.attr("codigo");
this.document.f30.txtcategoria.value = ctrl.attr("nombre");
}
function funct_17(ctrl){
this.document.edit1.id_servicio.value = ctrl.attr("codigo");
this.document.edit1.txtservicio.value = ctrl.attr("nombre");
}
function funct_18(ctrl){
this.document.f32.id_repuesto.value = ctrl.attr("codigo");
this.document.f32.txtrepuesto.value = ctrl.attr("nombre");
}
function funct_19(ctrl){
this.document.f33.id_repuesto.value = ctrl.attr("codigo");
this.document.f33.txtrepuesto.value = ctrl.attr("nombre");
this.document.f33.txtcantidad.value = "";
}
function funct_20(ctrl){
this.document.f34.id_repuesto.value = ctrl.attr("codigo");
this.document.f34.txtrepuesto.value = ctrl.attr("nombre");
this.document.f34.txtprecio.value = ctrl.attr("precio");
}
function funct_21(ctrl){
this.document.f35.txtfecha.value = ctrl.attr("fecha");;
this.document.f35.id_vehiculo.value = ctrl.attr("idvehiculo");
this.document.f35.txtserial.value = ctrl.attr("vehiculo");
this.document.f35.id_servicio.value = ctrl.attr("idservicio");
this.document.f35.txtservicio.value = ctrl.attr("servicio");
}
function funct_22(ctrl){
this.document.f51.orden.value = ctrl.attr("orden");;
this.document.f51.txtorden.value = ctrl.attr("textorden");
this.document.f51.id_orden.value = ctrl.attr("idorden");
}
function funct_23(ctrl){
this.document.f52.id_viaje.value = ctrl.attr("id");
this.document.f52.txtcontenedor.value = ctrl.attr("contenedor");
this.document.f52.orden.value = ctrl.attr("orden");
}
function funct_24(ctrl){
this.document.f53.id_viaje.value = ctrl.attr("id");
this.document.f53.txtcontenedor.value = ctrl.attr("contenedor");
this.document.f53.txtembarque.value=ctrl.attr("orden");
}
function inabi_func(ctrl){

this.document.ina1.txtnombre.value = ctrl.attr("nombre");
this.document.ina1.id.value = ctrl.attr("id");
this.document.ina1.tipo.value = "chofer";
this.document.ina1.txtmotivo.value="";
}
function inabi_func_2(ctrl){

this.document.ina2.txtserial.value = ctrl.attr("nombre");
this.document.ina2.id.value = ctrl.attr("id");
this.document.ina2.tipo.value = "vehiculo";
this.document.ina2.txtmotivo.value="";
}
function inabi_func_3(ctrl){

this.document.ina3.txtnombre.value = ctrl.attr("nombre");
this.document.ina3.id.value = ctrl.attr("id");
this.document.ina3.tipo.value = "cliente";
this.document.ina3.txtmotivo.value="";
}


function desabilitacion_1(ctrl){
	$("#nombrecito").html("<i><b style='color:#FFF'>"+ctrl.attr("nombre")+"</b></i>");
            $.ajax({
                url: "php/desa_1.php?id="+ctrl.attr("id"),
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       			processData: false
            })
                .done(function(res){
                    $("#peloerabo").html("" + res);
                });	
}
function desabilitacion_2(ctrl){
	$("#nombrecito2").html("<i><b style='color:#FFF'>"+ctrl.attr("nombre")+"</b></i>");
            $.ajax({
                url: "php/desa_2.php?id="+ctrl.attr("id"),
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       			processData: false
            })
                .done(function(res){
                    $("#peloerabo3").html("" + res);
                });	
}

function desabilitacion_3(ctrl){
	$("#clientecito").html("<i><b style='color:#FFF'>"+ctrl.attr("nombre")+"</b></i>");
            $.ajax({
                url: "php/desa_3.php?id="+ctrl.attr("id"),
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
       			processData: false
            })
                .done(function(res){
                    $("#peloerabo2").html("" + res);
                });	
}
function inabi_1(){

            var formData = new FormData(document.getElementById("ina1"));
            $.ajax({
                url: "php/inabi_1.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/chofer_list.php") }, 50);
                });
}
function inabi_2(){

            var formData = new FormData(document.getElementById("ina2"));
            $.ajax({
                url: "php/inabi_2.php",
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
function inabi_3(){

            var formData = new FormData(document.getElementById("ina3"));
            $.ajax({
                url: "php/inabi_3.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/asegura.php") }, 50);
                });
}


function m_form(){

            var formData = new FormData(document.getElementById("e9"));
            $.ajax({
                url: "php/editaraseguradora.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
                $("#idtxtaseguradorae").val("");
				setTimeout(function(){ Mostrar("php/asegura.php") }, 50);
                });
}
function m_form2(){

            var formData = new FormData(document.getElementById("e7"));
            $.ajax({
                url: "php/editarmunicipio.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
                $("#idtxtmunicipioe").val("");
				setTimeout(function(){ Mostrar("php/municipio_list.php") }, 50);
                });
}
function m_form3(){

            var formData = new FormData(document.getElementById("e5"));
            $.ajax({
                url: "php/editarmarca.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
                $("#idtxtmunicipioe").val("");
				setTimeout(function(){ Mostrar("php/marca.php") }, 50);
                });
}
function m_form4(){

            var formData = new FormData(document.getElementById("e6"));
            $.ajax({
                url: "php/editarmodelo.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
                $("#idtxtmunicipioe").val("");
				setTimeout(function(){ Mostrar("php/modelo.php") }, 50);
                });
}
function m_form5(){

            var formData = new FormData(document.getElementById("e8"));
            $.ajax({
                url: "php/editarruta.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
                $("#idtxtmunicipioe").val("");
				setTimeout(function(){ Mostrar("php/ruta.php") }, 50);
                });
}
function m_form_licencia(){

            var formData = new FormData(document.getElementById("f100"));
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
				setTimeout(function(){ Mostrar("php/chofer_list.php") }, 50);
                });
}

function m_form_cierre(){

            var formData = new FormData(document.getElementById("f51"));
            $.ajax({
                url: "php/guarda_cierre.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/cierres.php") }, 50);
                });
}
function m_form_contratante(){

            var formData = new FormData(document.getElementById("f16"));
            $.ajax({
                url: "php/anadircontratante.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/chofer_list2.php") }, 50);
                });
}

function enviar_contrax(){
            var formData = new FormData(document.getElementById("contrax"));
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

                $("#idtxtmunicipioe").val("");

                });	
                this.document.f11.txtcontra.value="";
                this.document.f11.txtnueva1.value="";
                this.document.f11.txtnueva2.value="";
}
function m_form_chofer(){

if((this.document.e2.txtnombre.value=="")||(this.document.e2.txtcedula2.value=="")||(this.document.e2.txtapellido.value=="")||(this.document.e2.txtdireccion.value=="")||(this.document.e2.txtcelular.value=="")||(this.document.e2.txtfecha.value=="")){

							alertify.alert("No debe dejar campos vacios", function (e) {
							    if (e) {
							    							    }
							});

}else{
            var formData = new FormData(document.getElementById("e2"));
            $.ajax({
                url: "php/editarchofer.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/chofer_list.php") }, 50);
                });
}
}
function m_form_vehiculo(){

if((this.document.e3.txtplaca.value=="")||(this.document.e3.txtchasis.value=="")||(this.document.e3.txtnumchasis.value=="")||(this.document.e3.txtmunicipio.value=="Seleccione")||(this.document.e3.txtano.value=="")){

							alertify.alert("No debe dejar campos vacios, Y debe seleccionar Municipio", function (e) {
							    if (e) {
							    							    }
							});

}else{
            var formData = new FormData(document.getElementById("e3"));
            $.ajax({
                url: "php/editarvehiculo.php",
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
}
function m_form_vehiculo2(){

if((this.document.e3_v.txtplaca.value=="")||(this.document.e3_v.txtchasis.value=="")||(this.document.e3_v.txtnumchasis.value=="")||(this.document.e3_v.txtmunicipio.value=="Seleccione")||(this.document.e3_v.txtano.value=="")){

							alertify.alert("No debe dejar campos vacios, Y debe seleccionar Municipio", function (e) {
							    if (e) {
							    							    }
							});

}else{
var valor=this.document.f17.id_contratante.value;
            var formData = new FormData(document.getElementById("e3_v"));
            $.ajax({
                url: "php/editarvehiculo2.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
								setTimeout(function(){ veitono(valor) }, 10);
                });
}
}
function veitono(valor){
			divResultado = document.getElementById('vehicom');
			ajax=objetoAjax();
			var URL="php/vehicom.php?id="+valor;
			ajax.open("POST", URL);

			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
				}
			}
			ajax.send(null)
}

function eliminacion(ctrl){
var msj="";
var msj2="";

msj="¿Esta seguro de Eliminar este registro?";	
msj2="Eliminar exitosamente";
	 alertify.confirm(msj, function(e){
	 	if(e){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elimina.php?tabla="+ctrl.attr("tabla")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
							alertify.success(msj2);
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php");select_cliente("php/cliente_select.php","clientes_select") }, 50);
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
			var URL="php/elimina.php?tabla="+ctrl.attr("tabla")+"&id="+ctrl.attr("id");
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
if(ctrl.attr("valor")==1){
msj="¿Esta seguro de habilitar este registro?";
msj2="Habilitado exitosamente";
}else{
if(ctrl.attr("valor")==2){
msj="¿Esta seguro de habilitar este Vehiculo?";	
msj2="Inhabilitado exitosamente";
}else{
msj="¿Esta seguro de inhabilitar este registro?";	
msj2="Inhabilitado exitosamente";
}
}
	 alertify.confirm(msj, function(e){
	 	if(e){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
							alertify.success(msj2);
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php"); select_cliente("php/cliente_select.php","clientes_select");}, 50);
				}
			}
			ajax.send(null)
				setTimeout(function(){ marcon_1(); }, 500);
				setTimeout(function(){ marcon_2(); }, 600);
				setTimeout(function(){ marcon_3(); }, 650);
				setTimeout(function(){ caton_1(); }, 700);
				setTimeout(function(){ caton_2(); }, 750);
	 	}
	 })
}

function edicion2(ctrl){
var msj="";
var msj2="";
if(ctrl.attr("valor")==1){
msj="¿Esta seguro de habilitar este chofer?";
msj2="Habilitado exitosamente";
}else{
msj="¿Esta seguro de Inhabilitar este registro?";	
msj2="Inhabilitado exitosamente";
}
	 alertify.confirm(msj, function(e){
	 	if(e){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
							alertify.success(msj2);
								setTimeout(function(){ Mostrar("php/"+ctrl.attr("urle")+".php") }, 50);
				}
			}
			ajax.send(null)
	 	}
	 })
}

function edicion3(ctrl){
var valor=this.document.f17.id_contratante.value;
var msj="";
var msj2="";
if(ctrl.attr("valor")==1){
msj="¿Esta seguro de habilitar este ?";
msj2="Habilitado exitosamente";
}else{
msj="¿Esta seguro de inhabilitar este vehiculo?";	
msj2="Inhabilitado exitosamente";
}
	 alertify.confirm(msj, function(e){
	 	if(e){
			divResultado = document.getElementById('mensaje');
			ajax=objetoAjax();
			var URL="php/elim.php?tabla="+ctrl.attr("tabla")+"&valor="+ctrl.attr("valor")+"&campo_id="+ctrl.attr("campoid")+"&id="+ctrl.attr("id");
			ajax.open("POST", URL);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
							alertify.success(msj2);
								setTimeout(function(){ veitono(valor) }, 10);
				}
			}
			ajax.send(null)
	 	}
	 })
}



function enviar_contra_2(url){
	clave = document.frmac.clave.value;
	rclave = document.frmac.rclave.value;
	name = document.frmac.u_nam.value;
if((clave=="")&&(rclave=="")){
				alertify.alert("Disculpe, Debe llenar los campos ", function (e) {
				    if (e) {

				    }
				});		
}else{
if(clave==name){
				alertify.alert("Disculpe, No use el mismo nombre de usuario como contraseña", function (e) {
				    if (e) {

				    }
				});		
}else{
if(clave==rclave){	

	divResultado = document.getElementById('reps');
	ajax=objetoAjax();
	var URL=url+"?clave="+clave+"&rclave="+rclave;

	ajax.open("GET", URL);

	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {

			divResultado.innerHTML = ajax.responseText;
							alertify.success("Modificacion exitosa");
							
				setTimeout(function(){ this.document.location.href="../vistacliente.php" }, 1000);
		}
	}
	ajax.send(null);


	}else{
				alertify.alert("Disculpe, Las contraseñas no coinciden", function (e) {
				    if (e) {

				    }
				});		
	}
}
}
}
function toque(str){

		$("#idtxtpedido").val(str.attr("id"));
}

function toque2(str){

		$("#idtxtvehiculo").val(str.attr("id"));
}

function toque3(str){

		$("#idtxtvehiculo2").val(str.attr("id"));
}
function toque4(str){
	this.document.f35.txtserial.value=str.attr("id");
	this.document.f35.id_vehiculo.value=str.attr("id_vehiculo");
}
function toque5(str){
	this.document.f35.txtservicio.value=str.attr("id");
	this.document.f35.id_servicio.value=str.attr("id_servicio");
}
function toque6(str){
	this.document.f36.id_vehiculo.value=str.attr("id");
	this.document.f36.txtserial.value=str.attr("placa");
}
function toque7(str){
	this.document.f36.id_repuesto.value=str.attr("id");
	this.document.f36.txtdescripcion.value=str.attr("placa");
}
function toque8(str){
	this.document.f50.txtorden.value=str.attr("orden");

	this.document.f50.txtidorden.value=str.attr("id");
}
function toque9(str){
	this.document.f42.txtconductor.value=str.attr("conduc");
	this.document.f42.txtidconductor.value=str.attr("id");
	this.document.f42.txttipoconductor.value=str.attr("tipo");	
}
function toque10(str){
	this.document.r51.txtorden.value=str.attr("orden");

	this.document.r51.txtidorden.value=str.attr("id");
}
function toque11(str){
	this.document.f51.txtorden.value=str.attr("orden");
	this.document.f51.orden.value=str.attr("ordenn");
	this.document.f51.id_orden.value=str.attr("id");
}
function toque12(str){
	this.document.f53.txtembarque.value=str.attr("orden");
	this.document.f53.orden.value=str.attr("ordenn");
	this.document.f53.id_orden.value=str.attr("id");
}
function loader5(str)
{

            $.ajax({
                url: "proc5.php?q="+str,
                type: "get",
                dataType: "html",

                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#myDiv5").html("" + res);
                });
/*
var xmlhttp;
 
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("myDiv5").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("POST","proc5.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);*/
}

function loader6(str)
{
            $.ajax({
                url: "proc6.php?q="+str,
                type: "get",
                dataType: "html",

                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#myDiv6").html("" + res);
                });
/*
var xmlhttp;
 
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("myDiv6").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("POST","proc6.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);
*/
}


function loader7(str)
{

            $.ajax({
                url: "proc7.php?q="+str,
                type: "get",
                dataType: "html",

                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#myDiv7").html("" + res);
                });

/*
var xmlhttp;
 
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("myDiv7").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("POST","proc7.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);
*/
}
function loader8(str)
{
var xmlhttp;
 
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("myDiv8").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("POST","proc8.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);
}

function loader9(str)
{
var xmlhttp;
 
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
{
document.getElementById("myDiv9").innerHTML=xmlhttp.responseText;
}
}
xmlhttp.open("POST","proc9.php",true);
xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xmlhttp.send("q="+str);
}

function loader10(str)
{

var urlx= "proc11.php";
//var descripcion = str;
var descripcion = this.document.f36.txtdescripcion.value;
var cantidad = this.document.f36.txtcantidad.value;
//var codigo = str3;
var codigo = this.document.f36.id_repuesto.value;
var status = str;

if (status==0) {

                urlx= "proc11.php";



}else{

	if (descripcion=="") {
		
				alertify.alert("Disculpe, debe seleccionar un repuesto", function (e) {
				    if (e) {
				        
				    }
				});
				

	}else if (cantidad==""){

		alertify.alert("Disculpe, debe ingresar una cantidad", function (e) {
				    if (e) {
				        
				    }
				});

	}else{

		document.f36.txtdescripcion.value = "";
		document.f36.txtcantidad.value = "";
		document.f36.txtcodigo.value = "";


                urlx= "proc10.php?cantidad="+cantidad+"&descripcion="+descripcion+"&codigo="+codigo;



/*
		var xmlhttp;
		 
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("myDiv10").innerHTML=xmlhttp.responseText;
		}
		}
		xmlhttp.open("POST","proc10.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("cantidad="+cantidad+"&descripcion="+descripcion+"&codigo="+codigo);
		*/

		}
$.ajax({
                url: urlx,
                type: "get",
                dataType: "html",

                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res2){
                    $("#myDivx10").html("" + res2);
                });


	}

}

function loader11(){
$.ajax({
                url: "proc11.php",
                type: "get",
                dataType: "html",

                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res2){
                    $("#myDivx10").html("" + res2);
                });

}
function loader12(ctr){
this.document.f36.txttipo.value="TIPO MANTENIMIENTO";
this.document.f36.id_vehiculo.value=ctr.attr("idvehiculo");
this.document.f36.txtserial.value=ctr.attr("idservicio");
this.document.f36.txtnombremecanico.value="";
this.document.f36.txtcostomecanico.value="";
$.ajax({
                url: "proc11.php?came=1",
                type: "get",
                dataType: "html",

                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res2){
                    $("#myDivx10").html("" + res2);
								setTimeout(function(){ caton_2();loader9('Seleccione') }, 50);                                        
                });

}
function select_cliente(urlx,div)
{
            $.ajax({
                url: "cliente_select.php",

                type: "get",
                dataType: "html",

                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#clientes_select").html("" + res);
                });
}
function limp_1(){
	$("#idtxtcliente").val("");
}
function limp_2(){
	$("#idtxtclientelist").val(0);
	$("#idtxtarchivo").val("");
}
function limp_3(){
	$("#nacionalidad").val("");
	$("#txtcedula").val("");
	$("#txtnombre").val("");
	$("#txtapellido").val("");
	$("#txtdireccion").val("");
	$("#txtcelular").val("");
	$("#txtfecha").val("");	
}
function limp_4(){
	$("#nacionalidad").val("");
	$("#txtcedula").val("");
	$("#txtnombre").val("");
	$("#txtapellido").val("");
	$("#txtdireccion").val("");
	$("#txtcelular").val("");
	$("#txtfecha").val("");	
}
function limp_5(){
	$("#txtmunicipio").val("");
}
function limp_6(){
	$("#txtmarca").val("");
}
function limp_7(){
	$("#txtplaca").val("");
	$("#txtchasis").val("");
	$("#txttamano").val("");
	$("#txtnumchasis").val("");
	$("#txtmunicipio").val(0);
	$("#txtcolor").val("#000000");
	$("#txtnummotor").val("");
	$("#txtplaca").val("");
	$("#txtmarca").val(0);
	$("#txtmarca").val(0);
	$("#txtano").val("");
loader('Seleccione');loader2('Seleccione');
}
function limp_8(){
	$("#txtdesde").val("");
	$("#txthasta").val("");
}
function elim_check(){
	msj="¿Esta seguro de Eliminar este registro?";	
	 alertify.confirm(msj, function(e){
	 	if(e){
        var formData = new FormData(document.getElementById("fomu_check"));
            $.ajax({
                url: "php/elim_check.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#contenido").html("" + res);
								setTimeout(function(){ Mostrar("php/faltantes.php") }, 50);                    
                });	
            }
        })
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
function vehicc(){

	            $.ajax({
                url: "vehicom_2.php",
                type: "get",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#vehicom_2").html("" + res);
                });
}
function vehiculom(){

	            $.ajax({
                url: "vehiculom.php",
                type: "get",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#vehiculom").html("" + res);
                });
}
function servicc(){
				var id = this.document.f35.id_vehiculo.value;
	            if(id==""){
						alertify.alert("Disculpe, debe seleccionar un vehiculo primero", function (e) {
				    		if (e) {
				        
				    		}
						});
	            }else{
	            $.ajax({
                url: "serviciom.php?id="+id,
                type: "get",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#serviciom").html("" + res);
                });
            }
}
function servicc2(){
	            $.ajax({
                url: "serviciom2.php",
                type: "get",
                dataType: "html",
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#serviciom2").html("" + res);
                });

}
function finfol(str){
     $("#inavi").html("<b>Total costo:</b> "+ str.attr("total")+" $");
		this.document.finfo.txttipo.value=str.attr("tipo");
		this.document.finfo.txtfecha.value=str.attr("fecha");
		this.document.finfo.txtnombremecanico.value=str.attr("mecanico");
		this.document.finfo.txtcostomecanico.value=str.attr("manobra");
		this.document.finfo.txtserial.value=str.attr("placa");		
		this.document.finfo.txtservicio.value=str.attr("servicio");
            $.ajax({
                url: "proinfo.php?id="+str.attr("id"),
                type: "get",
                dataType: "html",

                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res2){
                    $("#myDivx11").html("" + res2);
                });
}
function ondencit(){
		        $.ajax({
                url: "php/ordencita.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
		        processData: false
            })
                .done(function(res){
                    $("#ondencita").html("" + res);
                });
}
function ondencit2(){
		        $.ajax({
                url: "php/orden_reporte.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
		        processData: false
            })
                .done(function(res){
                    $("#ondencita").html("" + res);
                });
}
function ondencit3(){
		        $.ajax({
                url: "php/onden.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
		        processData: false
            })
                .done(function(res){
                    $("#onden2").html("" + res);
                });
}
function ondencit4(){
		        $.ajax({
                url: "php/onden2.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
		        processData: false
            })
                .done(function(res){
                    $("#onden3").html("" + res);
                });
}
function chofercitox(){
		        $.ajax({
                url: "php/chofercitos.php",
                type: "post",
                dataType: "html",
                cache: false,
                contentType: false,
		        processData: false
            })
                .done(function(res){
                    $("#chofercito").html("" + res);
                });
}
function guarda_incidencia(){
            var formData = new FormData(document.getElementById("f52"));
            $.ajax({
                url: "php/guardarIncidencias.php",
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

function guarda_reasignar(){
            var formData = new FormData(document.getElementById("f53"));
            $.ajax({
                url: "php/guarda_reasignar.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                    $("#mensaje").html("" + res);
				setTimeout(function(){ Mostrar("php/cierres.php") }, 50);
                });
}