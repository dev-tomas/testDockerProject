function soloNombres(e){
                 key = e.keyCode || e.which;
                 tecla = String.fromCharCode(key).toLowerCase();
                 letras = "abcdefghijklmnopqrstuvwxyzñ ";
                 especiales = [];

                 tecla_especial = false
                 for(var i in especiales){
                     if(key == especiales[i]){
                  tecla_especial = true;
                  break;
                            } 
                 }
                 
                        if(letras.indexOf(tecla)==-1 && !tecla_especial)
                     return false;
                     }

                     function soloNumeros(e){
        var key = window.Event ? e.which : e.keyCode
        return (key >= 48 && key <= 57)
      }

      function soloPrecio(e){
        key = e.keyCode || e.which;
                 tecla = String.fromCharCode(key).toLowerCase();
                 letras = ".0123456789";
                 especiales = [];

                 tecla_especial = false
                 for(var i in especiales){
                     if(key == especiales[i]){
                  tecla_especial = true;
                  break;
                            } 
                 }

                                  
                        if(letras.indexOf(tecla)==-1 && !tecla_especial)
                     return false;
      }

      function soloPeso(e){
        key = e.keyCode || e.which;
                 tecla = String.fromCharCode(key).toLowerCase();
                 letras = ".0123456789";
                 especiales = [];

                 tecla_especial = false
                 for(var i in especiales){
                     if(key == especiales[i]){
                  tecla_especial = true;
                  break;
                            } 
                 }

                                  
                        if(letras.indexOf(tecla)==-1 && !tecla_especial)
                     return false;
      }

      function soloLetrasNumeros(e){
                 key = e.keyCode || e.which;
                 tecla = String.fromCharCode(key).toLowerCase();
                 letras = "abcdefghijklmnopqrstuvwxyz1234567890 ";
                 especiales = [];

                 tecla_especial = false
                 for(var i in especiales){
                     if(key == especiales[i]){
                  tecla_especial = true;
                  break;
                            } 
                 }
                 
                        if(letras.indexOf(tecla)==-1 && !tecla_especial)
                     return false;
                     }
                     


                     function soloseriales(e){
                 key = e.keyCode || e.which;
                 tecla = String.fromCharCode(key).toLowerCase();
                 letras = "abcdefghijklmnopqrstuvwxyz1234567890";
                 especiales = [];

                 tecla_especial = false
                 for(var i in especiales){
                     if(key == especiales[i]){
                  tecla_especial = true;
                  break;
                            } 
                 }
                 
                        if(letras.indexOf(tecla)==-1 && !tecla_especial)
                     return false;
                     }

                     function soloCedula(e){
                 key = e.keyCode || e.which;
                 tecla = String.fromCharCode(key).toLowerCase();
                 letras = "1234567890-";
                 especiales = [];

                 tecla_especial = false
                 for(var i in especiales){
                     if(key == especiales[i]){
                  tecla_especial = true;
                  break;
                            } 
                 }
                 
                        if(letras.indexOf(tecla)==-1 && !tecla_especial)
                     return false;
                     }

function temperatura(e){
                 key = e.keyCode || e.which;
                 tecla = String.fromCharCode(key).toLowerCase();
                 letras = "0123456789-";
                 especiales = [];

                 tecla_especial = false
                 for(var i in especiales){
                     if(key == especiales[i]){
                  tecla_especial = true;
                  break;
                            } 
                 }
                 
                        if(letras.indexOf(tecla)==-1 && !tecla_especial)
                     return false;
                     }
