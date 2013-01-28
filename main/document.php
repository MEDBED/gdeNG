<?PHP
session_start();
$page="document.php";
$script="scripts/update_document.php";
$titre="Gestion des documents";
$pageDescription="Visualisation, modification, ajout et supression de documents de l'entité";
$pageHelp="";
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../logout.php");
	exit;
}
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
include("../include/check_perms.php");
connectSQL();	
//entete_page($GLOBALS['params']['appli']['title_appli']." - ".$titre,'');
?>
<script type="text/javascript">

</script>
<style type="text/css">
.bar {
    height: 18px;
    background: green;
}
</style>
</head>
<body>
<link rel="stylesheet" href="content/plupload/css/plupload.queue.css" type="text/css" media="screen" />
<script type="text/javascript" src="content/plupload/js/browserplus-min.js"></script>
<script type="text/javascript" src="content/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="content/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<!--<script src="content/fileUpload/js/vendor/jquery.ui.widget.js"></script>
<script src="content/fileUpload/js/jquery.iframe-transport.js"></script>
<script src="content/fileUpload/js/jquery.fileupload.js"></script>
<script src="content/fileUpload/js/load-image.min.js"></script>
<script src="content/fileUpload/js/canvas-to-blob.min.js"></script>
<script src="content/fileUpload/js/jquery.fileupload-fp.js"></script>
<script>
$(function () {
    $('#fileupload').fileupload({
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result, function (index, file) {
                $('<p/>').text(file.name).appendTo(document.body);
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
        },
        add: function (e, data) {
        $(this).fileupload('process', data).done(function () {
            data.submit();
        });
    }
    });
});
</script>-->
<script>
/*jQuery(document).ready( function() {
     jQuery("a[rel^='prettyPhoto']").prettyPhoto({
        social_tools: false
    }); 
}); */
/*jQuery(document).ready( function() {
    // Setup html5 version
    $("#uploader").pluploadQueue({
        // General settings
        runtimes : 'html5,html4',
        url : 'content/plupload/upload.php',
        max_file_size : '10mb',
        chunk_size : '1mb',
        unique_names : true,
        filters : [
                {title : "Fichiers images", extensions : "jpg,gif,png,bmp"},
                {title : "Fichiers compressés", extensions : "zip,tar,gz,tar.gz"},
                {title : "Documents", extensions : "txt,doc,pdf,odt,rtf,log"},
                {title : "Tableaux", extensions : "xls,xlt,ods"}
        ],

        // Resize images on clientside if we can
        resize : {width : 1024, height : 768, quality : 100}
    });
    // Client side form validation
    $('#formAdd').submit(function(e) {
    var uploader = $('#uploader').pluploadQueue();
    // Validate number of uploaded files        
    if (uploader.total.queued==0){       
            if (document.getElementById('rattachement')){                   
                if (document.getElementById('rattachement').selectedIndex == 0){
                    alert('Vous devez choisir un formulaire de rattachement'); e.preventDefault();    
                    return; 
                }
            }
            if (uploader.total.uploaded == 0) {
                    // Files in queue upload them first                     
                if (uploader.files.length > 0){
                    // When all files are uploaded submit form                                      
                    uploader.bind('UploadProgress', function() {
                            if (uploader.total.uploaded == uploader.files.length)
                                    $('#formAdd').submit();
                            });
                uploader.start();                                                                 
            } else {
                alert('Vous devez ajouter au moins 1 fichier');  
                e.preventDefault();
            }
        }
    }else{alert('Vous devez envoyer les fichiers ajoutés'); e.preventDefault();}            
    }); 
});
*/
</script>
<div class="highslide-html-content" id="highslide-html<?php echo $page;?>">
	<div class="highslide-header">	
	</div>
	<div class="highslide-body">
		<?php echo $pageHelp;?>			
	</div>
    <div class="highslide-footer">
        <div>
            <span class="highslide-resize" title="Resize">
                <span></span>
            </span>
        </div>
    </div>
</div>

<div id="container2">
	<h1><?php  echo $titre;?></h1>
        <!--<h2><?php  echo $pageDescription;?></h2>	-->
        <div id="mess" style="display: none;"></div>
        <div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html<?php echo $page;?>',headingText: 'Aide',preserveContent: false } )"></a></div>
	<div class="content">
            <!--<input id="fileupload" type="file" style="height: 25px;" name="files[]" data-url="<?php echo $GLOBALS['params']['appli']['document_folder'];?>/" multiple>	
            <div id="progress">
                <div class="bar" style="width: 0%;"></div>
            </div>
            -->
            <!--<div id="add_doc">            
                <form method="post" action='<?php echo $script;?>' id="formAdd">                              
                <input type="hidden" name="id_source" value="">                                
                <input type="hidden" name="add" value="1">
                <table width="100%">    
                    <tr>
                    <td width=100px valign=middle><b>Fichier(s) <font color="#ff0000">*</font></b>
                        <img src="graphs/icons/bulle.png" 
                        onMouseOver="AffBulle('<?php echo addslashes($title_extension);?>')" onMouseOut="HideBulle()"></TD>
                    <td><div style="width: 100%;float: left; margin-right: 20px ;">
                    <div id="uploader" style="width: 450px; height: 240px;"></div>
                    </div>
                    </td>
                </tr>
                <tr>
                    <td title="Explication du contenu du ou des fichiers"><b>Description</b></td>
                    <td title="Explication du contenu du ou des fichiers"><textarea cols="62" rows="3" name="description"></textarea></td>
                </tr>
                <?php       
                echo ('                 
                <tr>
                    <td title="Formulaire auqel ce document sera rattaché"><b>Zone <font color="#ff0000">*</font></b></td>
                    <td>                                    
                        <SELECT NAME="id_zone" id="id_zone">
                        ');
                        $requete="SELECT DISTINCT detail,id from zone WHERE id!=:id ORDER BY detail;";
                        $prep=$db->prepare($requete);           		
                        $prep->bindParam(":id",$_SESSION['id_zone'],PDO::PARAM_INT);
                        $prep->execute();
                        while ($row = $prep->fetch(PDO::FETCH_ASSOC))
                        {
                            echo '<option value="'.$row[id].'">'.$row[detail].'</option>';
                        }
                        echo ('
                        </select>
                    </td>
                </tr>
                ');        
                ?>
                <tr>
                    <td><b>Accessible à</b></td>
                    <td>
                        <SELECT NAME="acces" id="acces">
                            <option value="2">A mon groupe</option>       
                            <option value="1">Moi uniquement</option>                                                                             
                            <option value="3">Tous sauf entité</option>
                            <option value="0" selected>Tout le monde </option>
                        </select>
                    </td>
                </tr>       
                <tr>
                    <td colspan=2>
                        <p id="validButton"><button type="submit" id="submitButton" name="valid" value="Valider" style="cursor: pointer;" class="buttonValid">Enregistrer</button></p>
                        </td>
                </tr>
            </table>
            </form>-->
            <div id="SelectedRowList"></div>	
            <div id="masterContainer" style="width: 100%;padding-top: 0px;">	
                    <div id="childContainer"></div>	
            </div>
             <script type="text/javascript">	                    
            $(document).ready(function () {	
                //Prepare jTable               
                $('#masterContainer').jtable({
                    title: '<a href="#" onclick="return hs.htmlExpand(this, { src: \'main/visionneuse.php?affAll=1\',objectType: \'iframe\', headingText: \'Visionneuse\',width: 900,preserveContent: false });"><img src="graphs/icons/visionneuse.png" alt="Visionneuse" title="Lancer la visionneuse"></a>&nbsp;Liste des documents ',	  
                    pageSize: <?php echo $_SESSION['pageSize'];?>,
                    sorting: true,	
                    paging: true,
                    defaultSorting: 'date DESC',						                                     
                    actions: {
                    <?php 
                    if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & LECTURE && 
                                    (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & LECTURE){
                            echo "listAction: 'scripts/update_document.php?action=list&source=doc',";
                    }
                    if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & MODIFICATION &&
                                    (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & MODIFICATION){
                            echo "updateAction: 'scripts/update_document.php?action=update&source=doc',";
                    }
                    if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & CREATION &&
                                    (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & CREATION){
                            echo "createAction: 'scripts/update_document.php?action=create&source=doc',";
                    }
                    if ((int)$_SESSION['PERMS']['zone'][$_SESSION['id_zone']] & SUPPRESSION &&
                                    (int)$_SESSION['PERMS']['entite'][ $_SESSION['ENTITE'][$_SESSION['id_entite']]['id_type'] ] & SUPPRESSION){
                            echo "deleteAction: 'scripts/update_document.php?action=delete&source=doc',";
                    }
                    ?>		                                            		                                            		                                           		                                            		                                            
                    },
                    fields: {                                                                                      
                        id_document: {	                                            					                                            
                            key: true,
                            create: false,
                            edit: false,
                            list: false                                          
                        },		
                        fic:{
                            title: '',
                            width: '1%',                                                   
                            type: 'file',
                            edit: false,
                            sorting: false,
                            display: function (data){                                                        
                                info = '<div><form method="post" action="scripts/download.php"><input type="hidden" name="file" value="'+data.record.fic+'"><input type="hidden" name="filename" value="'+data.record.ficName+'"><input type="submit" value="" style="cursor: pointer;" class="buttonDownload" title="Télécharger"></form>';
                                return info;
                            },
                            //inputClass: 'validate[custom[fileExtension]]'
                        } ,
                        voir: {
                            title: '',
                            width: '1%',
                            edit: false,
                            create: false,
                            sorting: false,
                            display: function(data){
                                if(checkExt(data.record.fic,'img')){
                                    return '<a class="titi" href="scripts/affImage.php?file='+data.record.fic+'" rel="prettyPhoto" alt="'+data.record.ficName+'" title="'+data.record.description+'"><img src="graphs/icons/voir.png" alt="'+data.record.ficName+'"></a>';
                                }
                            }
                        },
                        id_zone: {                                                       
                            title: 'Zone',							
                            options: 'main/liste/getZone.php',
                            inputClass: 'validate[required]'
                        },   
                        /*visionneuse: {
                            title: '',
                            width: '1%',
                            sorting: false,
                            edit: false,
                            create: false,
                            display: function(data){
                                return '<a href="#" onclick="return hs.htmlExpand(this, { src: \'main/visionneuse.php?source=materiel&id='+data.record.id_source+'\',objectType: \'iframe\', headingText: \'Visionneuse\',width: 900,preserveContent: false });"><img src="graphs/icons/visionneuse.png" alt="'+data.record.ficName+'"></a>';
                            }
                        },*/
                        ficName:{
                            title: 'Nom',
                            width: '10%',                                                                                                    
                            /*edit: false,
                            create: false,*/
                            //inputClass: 'validate[custom[fileExtension]]'
                        } ,
                        ext: {
                            title: 'Extension',
                            width: '5%',
                            edit: false,
                            create: false,
                            display: function (data){
                                f = data.record.fic;
                                return f.substring((Math.max(0, f.lastIndexOf(".")) || f.length) + 1);
                            }
                        },
                        description:{
                            title: 'Description',                                                                            
                            width: '30%',
                            type: 'textarea',
                        },  
                        utilisateur:{
                            title: 'Ajouté par',
                            width: '15%',
                            create: false,
                            edit : false,
                        } ,
                        acces: {
                            title: 'Accessible à',
                            width: '15%',                                                    
                            options: { '0':'Tout le monde','1': 'A mon groupe','2': 'Moi uniquement','3': 'Tous sauf entité' },			                                            			                                                                                               
                        }, 
                        dateFin: {
                            title: 'Fin de validité',
                            width: '10%',
                            type: 'date',
                            displayFormat: 'dd-mm-yy',
                            inputClass: 'validate[custom[datefr]]'
                            /*create: false,
                            edit: false*/
                        }, 
                        updateOnDoc: {
                            title: 'Modifié le',
                            width: '10%',
                            type: 'date',
                            displayFormat: 'dd-mm-yy',
                            inputClass: 'validate[custom[datefr]]',
                            create: false,
                            edit: false
                        }, 
                    }
                });
                //$('#masterContainer').jtable('load');
                $('#masterContainer').jtable('load','',function(){
                    jQuery("a[rel^='prettyPhoto']").prettyPhoto({
                        social_tools: false
                    });                                                
                });    
            });
            </script> 
        </div>
    </div>        
</div>
</body>
</html>