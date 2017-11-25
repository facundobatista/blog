// Select a printer friendly css version
// Change link accordly to enable or disable printer-friendly style

var locate = window.location.search;

function argv(str) {
    point = str.lastIndexOf("=");
    return(str.substring(point+1,str.length-1));
}

$( document ).ready(switch_printer_friendly ( (argv(locate) != "printer_friendly")) );

function switch_printer_friendly ( disable_printer_friendly ) {
   var i, a;

   for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
     if(a.getAttribute("rel").indexOf("style") != -1
        && a.getAttribute("title")) {
       if(a.getAttribute("title") == "printer-friendly") {
           a.disabled = disable_printer_friendly;

           // Change link to enable/disable printer friendly
           x = document.getElementById("printer_friendly_link");
           if (disable_printer_friendly) {
              x.setAttribute('onclick','switch_printer_friendly( false ); return false;');
              x.innerHTML = "Imprimir";
           } else {
              x.setAttribute('onclick','switch_printer_friendly( true ); return false;');
              x.innerHTML = "Vista normal";
           }

       }
     }
   }
}
