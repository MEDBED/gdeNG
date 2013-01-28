$(document).ready(function (){
    // TREE MENU
    //$('#treeMenu ul li:has("div")').find('span:first').not('.imageMenu').addClass('closed');    
    $('#treeMenu ul li:has("div")').find('div').not('.imageMenu').hide();	
    $('#treeMenu li:has("div")').find('a:first').click (function (){ 
        $(this).parent('li').find('span.nv1:first').toggleClass('opened');
        $(this).parent('li').find('div:first').not('.imageMenu').slideToggle();	
        
     }); 
    $('#treeMenu li:has("div")').find('span.nv1:first').click (function (){ 
        $(this).parent('li').find('span.nv1:first').toggleClass('opened');
        $(this).parent('li').find('div:first').not('.imageMenu').slideToggle();	
        
     });
    $( "#treeMenu ul li ul li" ).click(function() {        
        //$(this).prev(".itemContainer").toggleClass("hide"),
        $("#treeMenu li span.nv2").not(this).prev("#treeMenu li").removeClass("isSelected");
        $("#treeMenu li span.nv2").not(this).removeClass("isSelected");
        $(this).find('span.nv2:first').toggleClass("isSelected");
        return false;
    });
});