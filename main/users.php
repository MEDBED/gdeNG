<?php 
if(!isset($_COOKIE["ID_UTILISATEUR"]) && $_SESSION['isAdmin']==1){
	header("Location: ../logout.php");
	exit;
}
session_start();
$page="users.php";
$script="scripts/update_user.php";
$titre="Gestion des utilisateurs";
$pageDescription="Modification et gestion des permissions";
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
	     	Nom: <input type="text" name="Fnom" id="Fnom" size=5/>
	        Prénom: <input type="text" name="Fprenom" id="Fprenom" size=5 style="height: 14px;font-size: small;"/>	        
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
					title: 'Liste des utilisateurs',
					paging: true,						
					/*selecting: true,
					selectingCheckboxes: true,		
					multiselect: true,
					selectOnRowClick: false,*/																		
					pageSize: <?php echo $_SESSION['pageSize'];?>,
					sorting: true,
					defaultSorting: 'nom ASC',
					actions: {						
						listAction: '<?php echo $script;?>?action=list&type=u',
						createAction: '<?php echo $script;?>?action=create&type=u',
						updateAction: '<?php echo $script;?>?action=update&type=u',
						deleteAction: '<?php echo $script;?>?action=delete&type=u'
					},
					fields: {
						id_user: {
							key: true,
							create: false,
							edit: false,
							list: false
						},
						perms_edit: {
							title: 'Zone',
							width: '1%',
							listClass: 'jtableTdCenter',	
							edit: false,
							create: false,
							sorting: false,	
							setOnTextClick: false,					
							//options: 'main/liste/getModele.php',
							display: function (studentData) {				
		                        //Create an image that will be used to open child table
		                        var $img = new Array();
		                        $img = $('<img src="graphs/icons/clef.png" title="Mofifier les permissions" />')	                      
		                        //Open child table when user clicks the image
		                        $img.click(function () {	                        	                 
		                            $('#masterContainer').jtable('openChildTable',	    	                            
		                                    $img.closest('tr'),
		                                    {
		                                        title: 'Permissions de ' + studentData.record.prenom + ' ' + studentData.record.nom,	
												paging: true,	
												sorting: true,					
												defaultSorting: 'detail ASC',
												pageSize: <?php echo $_SESSION['pageSize'];?>,												
		                                        actions: {
		                                            listAction: 'scripts/update_perms.php?action=list&type=u&id_user='+studentData.record.id_user+'&source2=zone',		                                            
		                                            updateAction: 'scripts/update_perms.php?action=update&type=u&id_user='+studentData.record.id_user+'&source2=zone',
		                                            //changeAction: 'scripts/update_modele.php?action=change&id_materiel=' + studentData.record.id_materiel
		                                        },
		                                        fields: {  		                                        			                                        	
		                        					id_perm: {
		                                        		key: true,  	                                        		                                        	                      				
		                        						create: false,
		                        						edit: false,
		                        						list: false
		                        					}  , 
		                        					id_user: {		                                        		                                        		                                        	                      			
		                        						create: false,		                        												                    							
		                        						list: false,
		                        						type: 'hidden',
		                        					}  ,  
		                        					source2: {		                                        		  	                                        		                                        	                      			
		                        						create: false,		                        						
		                        						list: false,
		                        						type: 'hidden',
		                        					}  ,                                   
		                        					id_source: {		                                        		                                        		                                        	                      			
		                        						create: false,		                        						
		                        						list: false,
		                        						type: 'hidden',
		                        					}  ,    
		                                            detail: {
														title: 'Zone',
			                                            edit: false,
			                    						create: false,			                                            
		                                            },	 		                                                                                     	                                         
		                                            lecture: {
		                                            	setOnTextClick: false,	
														sorting: false,														
		                                                title: 'Lecture',
		                                                width: '10%',
		                                                type: 'checkbox',
		                                                values: { '0':'', '1':'X'},
		                                                listClass: 'jtableTdCenter',
		                                                formText: ' '
		                                            }, 
		                                            modification: {
		                                            	setOnTextClick: false,	
														sorting: false,														
		                                                title: 'Moficiation',
		                                                width: '10%',
		                                                type: 'checkbox',
		                                                values: { '0':'', '1':'X'},
		                                                listClass: 'jtableTdCenter',
		                                                formText: ' '
		                                            },
		                                            creation: {
		                                            	setOnTextClick: false,
														sorting: false,																												
		                                                title: 'Création',
		                                                width: '10%',
		                                                type: 'checkbox',
		                                                values: { '0':'', '1':'X'},
		                                                listClass: 'jtableTdCenter',
		                                                formText: ' '
		                                            }, 
		                                            suppression: {
		                                            	setOnTextClick: false,	
														sorting: false,														
		                                                title: 'Suppression',
		                                                width: '10%',
		                                                type: 'checkbox',
		                                                values: { '0':'', '1':'X'},
		                                                listClass: 'jtableTdCenter',
		                                                formText: ' '		                                                
		                                            },                                                                                 
		                                        }
		                                    }, function (data) { //opened handler		                                   
		                                        data.childTable.jtable('load');		                                                                          		                                    	
		                                    });	 	                                               
		                        });	                       
		                        //Return image to show on the person row	  	                                                    	                                      
		                        return $img;	                        
	                    	}
						},	
						perms_entite: {
							title: 'Entité',
							width: '1%',	
							listClass: 'jtableTdCenter',	
							edit: false,
							create: false,
							sorting: false,	
							setOnTextClick: false,					
							//options: 'main/liste/getModele.php',
							display: function (studentData) {				
		                        //Create an image that will be used to open child table
		                        var $img = new Array();
		                        $img = $('<img src="graphs/icons/clef.png" title="Mofifier les permissions" />')	                      
		                        //Open child table when user clicks the image
		                        $img.click(function () {	                        	                 
		                            $('#masterContainer').jtable('openChildTable',	    	                            
		                                    $img.closest('tr'),
		                                    {
		                                        title: 'Permissions de ' + studentData.record.prenom + ' ' + studentData.record.nom,	
												paging: true,	
												sorting: true,					
												defaultSorting: 'detail ASC',
												pageSize: <?php echo $_SESSION['pageSize'];?>,												
		                                        actions: {
		                                            listAction: 'scripts/update_perms.php?action=list&type=u&id_user='+studentData.record.id_user+'&source2=entite',		                                            
		                                            updateAction: 'scripts/update_perms.php?action=update&type=u&id_user='+studentData.record.id_user+'&source2=entite',
		                                            //changeAction: 'scripts/update_modele.php?action=change&id_materiel=' + studentData.record.id_materiel
		                                        },
		                                        fields: {  		                                        			                                        	
		                        					id_perm: {
		                                        		key: true,  	                                        		                                        	                      				
		                        						create: false,
		                        						edit: false,
		                        						list: false
		                        					}  , 
		                        					id_user: {		                                        		                                        		                                        	                      			
		                        						create: false,		                        												                    							
		                        						list: false,
		                        						type: 'hidden',
		                        					}  ,  
		                        					source2: {		                                        		  	                                        		                                        	                      			
		                        						create: false,		                        						
		                        						list: false,
		                        						type: 'hidden',
		                        					}  ,                                   
		                        					id_source: {		                                        		                                        		                                        	                      			
		                        						create: false,		                        						
		                        						list: false,
		                        						type: 'hidden',
		                        					}  ,    
		                                            detail: {
														title: 'Zone',
			                                            edit: false,
			                    						create: false,			                                            
		                                            },	 		                                                                                     	                                         
		                                            lecture: {
		                                            	setOnTextClick: false,	
														sorting: false,														
		                                                title: 'Lecture',
		                                                width: '10%',
		                                                type: 'checkbox',
		                                                values: { '0':'', '1':'X'},
		                                                listClass: 'jtableTdCenter',
		                                                formText: ' '
		                                            }, 
		                                            modification: {
		                                            	setOnTextClick: false,	
														sorting: false,														
		                                                title: 'Moficiation',
		                                                width: '10%',
		                                                type: 'checkbox',
		                                                values: { '0':'', '1':'X'},
		                                                listClass: 'jtableTdCenter',
		                                                formText: ' '
		                                            },
		                                            creation: {
		                                            	setOnTextClick: false,
														sorting: false,																												
		                                                title: 'Création',
		                                                width: '10%',
		                                                type: 'checkbox',
		                                                values: { '0':'', '1':'X'},
		                                                listClass: 'jtableTdCenter',
		                                                formText: ' '
		                                            }, 
		                                            suppression: {
		                                            	setOnTextClick: false,	
														sorting: false,														
		                                                title: 'Suppression',
		                                                width: '10%',
		                                                type: 'checkbox',
		                                                values: { '0':'', '1':'X'},
		                                                listClass: 'jtableTdCenter',
		                                                formText: ' '		                                                
		                                            },                                                                                 
		                                        }
		                                    }, function (data) { //opened handler		                                   
		                                        data.childTable.jtable('load');		                                                                          		                                    	
		                                    });	 	                                               
		                        });	                       
		                        //Return image to show on the person row	  	                                                    	                                      
		                        return $img;	                        
	                    	}
						},	
						nom: {							
							title: 'Nom',
							inputClass: 'validate[required]'														
						},
						prenom: {							
							title: 'Prénom',														
							inputClass: 'validate[required]'
						},
						login: {							
							title: 'Login',							
							inputClass: 'validate[required]',
						},	
						ldap: {						
							title: 'Connexion LDAP',
							type: 'checkbox',							
							values: { '0':'Non', '1':'Oui'},							
						},	
						actif: {						
							title: 'Compte actif',
							type: 'checkbox',							
							values: { '0':'Non', '1':'Oui'},												
						},	
						date_inscription: {		
							create: false,
							edit: false,										
							title: 'Inscrit le',
							type: 'date',							
						},
						password: {
							list: false,
							create: false,
							edit: true,
							title: 'Mot de passe',
							type: 'password'
						},	
						passwordOrg: {
							list: false,
							create: false,
							edit: true,					
							type: 'hidden'
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
		                Fnom: $('#Fnom').val(),
		                Fprenom: $('#Fprenom').val(),		                	               
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