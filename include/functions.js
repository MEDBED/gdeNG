function test_fichier(nom_fichier)
{     
     //donnees="nom_fic="+nom_fichier;
     //alert(donnees);
	nom_fichier = jQuery.base64.encode(nom_fichier);
     /*return jQuery.ajax({
    	 type: 'POST',
    	 url : 'include/existe.php',
    	 data: {nom_fic: nom_fichier},      	 
     });*/
	jQuery.ajax({
		  type: 'POST',
   	 	  url : 'include/existe.php',
   	 	  data: {nom_fic: nom_fichier},
	      async: false,  
	      success:function(data) {
	         result = data; 
	      }
	   });
	   return result;

}
//Supression des accents dans uen chaîne
function replaceAll(str, search, repl) {
	 while (str.indexOf(search) != -1)
	  str = str.replace(search, repl);
	 return str;
	}
function AccentToNoAccent(str) {
	 var norm = new Array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë',
	'Ì','Í','Î','Ï', 'Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý',
	'Þ','ß', 'à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î',
	'ï','ð','ñ', 'ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ý','þ','ÿ');
	var spec = new Array('A','A','A','A','A','A','A','C','E','E','E','E',
	'I','I','I','I', 'D','N','O','O','O','0','O','O','U','U','U','U','Y',
	'b','s', 'a','a','a','a','a','a','a','c','e','e','e','e','i','i','i',
	'i','d','n', 'o','o','o','o','o','o','u','u','u','u','y','y','b','y');
	 for (var i = 0; i < spec.length; i++)
	  str = replaceAll(str, norm[i], spec[i]);
	 return str;
	 }