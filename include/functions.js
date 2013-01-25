function test_fichier(nom_fichier,src)
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
   	 	  data: {nom_fic: nom_fichier,src: src},
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
    if (str){
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
}
function dateDiff(d1, d2)
{
  var d1y = d1.getFullYear();
  var d1m = d1.getMonth();
  var d1d = d1.getDate();
  var d2y = d2.getFullYear();
  var d2m = d2.getMonth();
  var d2d = d2.getDate();
  var diff = d2y - d1y;
  if(d2m > d1m) diff--;
  else
  {
    if(d2m == d1m)
    {
      if(d2d > d1d) diff--;
    }
  }
  return diff;
}
function dayDiff(d1, d2)
{
  d1 = d1.getTime() / 86400000;
  d2 = d2.getTime() / 86400000;
  return new Number(d2 - d1).toFixed(0);
}
function diffdate(d1,d2){
    var WNbJours = d2.getTime() - d1.getTime();
    return Math.ceil(WNbJours/(1000*60*60*24));
}
Array.prototype.has = function(value) {
    var i;
    for (var i = 0, loopCnt = this.length; i < loopCnt; i++) {
        if (this === value) {
            return true;
        }
    }
    return false;
};
function checkExt(file,type) {//use in a form event or ina input
       //var value=file.value;
       if (type=='img'){
            if( file.match(/\.(jpg)|(gif)|(png)|(bmp)|(tiff)|(jpeg)$/) ){//here your extensions
		return true;	//actions like focus, not validate...
            }else {//right extension
		return false;
            }
       }else{return false;}
}