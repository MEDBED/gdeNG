<?php 
if(!isset($_COOKIE["ID_UTILISATEUR"]) && $_SESSION['isAdmin']==1){
	header("Location: ../logout.php");
	exit;
}
session_start();
$page="serviceIP.php";
$script="scripts/update_serviceIP.php";
$titre="Gestion des services IP";
$pageDescription="Création ou modification des services accessibles par IP";
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
include_once("../include/textes.php");
connectSQL();
?>
<html>
  <head>	
  </head>
  <body>
  <div class="highslide-html-content" id="highslide-html2">
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
		<h2><?php  echo $pageDescription;?></h2>	
		<div id="mess" style="display: none;"></div>	
		<div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html2',headingText: 'Aide',preserveContent: false } )"></a></div>
	<div class="content">		
		<div class="filtering">
	    <form>       
	     	Editeur: <input type="text" name="Fediteur" id="Fediteur" size=5/>
	        Nom: <input type="text" name="Fnom" id="Fnom" size=5 style="height: 14px;font-size: small;"/>	        
	        <button type="submit" id="LoadRecordsButton" class="buttonValid">Filtrer</button>
	    </form>
		</div>
		<div id="SelectedRowList"></div>	
		<div id="masterContainer" style="width: 100%;padding-top: 0px;">	
			<div id="childContainer"></div>	
		</div>	
		<script type="text/javascript">
	
			$(document).ready(function () {
	
			    //Prepare jTable
				$('#masterContainer').jtable({
					title: 'Liste des services IP',
					paging: true,																												
					pageSize: <?php echo $_SESSION['pageSize'];?>,
					sorting: true,					
					actions: {						
						listAction: '<?php echo $script;?>?action=list',
						createAction: '<?php echo $script;?>?action=create&type=u',
						updateAction: '<?php echo $script;?>?action=update',
						deleteAction: '<?php echo $script;?>?action=delete&type=u'
					},
					fields: {
                                             id: {
                                                key: true,
                                                create: false,
                                                edit: false,
                                                list: false
                                            },
                                            id_type: {							
                                                title: '',
                                                width: '1%',							
                                                options: 'main/liste/getType.php?source=materiel',
                                                inputClass: 'validate[required]',
                                                display: function (studentData) {				
                                                    //Create an image that will be used to open child table
                                                    var $img99 = new Array();                                        
                                                    var icone=studentData.record.type;
                                                    if (icone){
                                                        icone = AccentToNoAccent(icone);                                                    
                                                        var exist = test_fichier(icone.toLowerCase(),'materiel');    
                                                        //alert(exist);
                                                        if (exist != 0){
                                                            $img99 = $('<img src="graphs/icons/materiel/' + exist + '" title="' + studentData.record.type + '" />');
                                                            return $img99;	
                                                        }else{
                                                            return studentData.record.type_materiel;
                                                        }	
                                                    }else{return studentData.record.type_materiel;}                                                        
                                                }
                                            },
                                            type: {							
                                                title: 'Type',
                                                width: '10%',							
                                                edit: false,
                                                create: false,                                                
                                            },
                                            port: {                                            
                                                title: 'Port',
                                                nputClass: 'validate[required,custom[integer]]'
                                            },
                                            protocol: {							
                                                title: 'Protocole',														
                                                inputClass: 'validate[required]'
                                            },  
                                            detail: {                                           
                                               title: 'Descriptif',
                                               inputClass: 'validate[required]'
                                            }
						                                              
					}, 
	                selectionChanged: function () {
	                    //Get all selected rows
	                    var $selectedRows = $('#masterContainer').jtable('selectedRows');
	     
	                    $('#SelectedRowList').empty();
	                    if ($selectedRows.length > 0) {
	                        //Show selected rows
	                        $selectedRows.each(function () {
	                            var record = $(this).data('record');
	                            $('#SelectedRowList').append();//'<b>id_materiel</b>: ' + record.id_materiel 
	                        });
	                    } else {
	                        //No rows selected
	                        $('#SelectedRowList').append();
	                    }
	                },
	              //Initialize validation logic when a form is created
	                formCreated: function (event, data) {                	
	                    data.form.validationEngine();
	                },
	                //Validate form when it is being submitted
	                formSubmitting: function (event, data) {
	                    return data.form.validationEngine('validate');
	                },
	                //Dispose validation logic when form is closed
	                formClosed: function (event, data) {
	                    data.form.validationEngine('hide');
	                    data.form.validationEngine('detach');
	                }
				});
				//Re-load records when user click 'load records' button.
		        $('#LoadRecordsButton').click(function (e) {
		            e.preventDefault();
		            $('#masterContainer').jtable('load', {
		                Fediteur: $('#Fediteur').val(),
		                Fnom: $('#Fnom').val(),		                	               
		                //cityId: $('#cityId').val()
		            });
		        });
		 
		        //Load all records when page is first shown
		        $('#LoadRecordsButton').click();
				//Load person list from server
				//$('#masterContainer').jtable('load');
		      //Delete selected students
		        $('#UpdateButton').button().click(function () {
		            var $selectedRows = $('#masterContainer').jtable('selectedRows');
		            $('#masterContainer').jtable('deleteRows', $selectedRows);
		        });
	
			});
	
		</script>			
 	</div>
 	</div> 
  </body>
</html>