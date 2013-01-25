<?php 
if(!isset($_COOKIE["ID_UTILISATEUR"]) && $_SESSION['isAdmin']==1){
	header("Location: ../logout.php");
	exit;
}
session_start();
$page="users.php";
$script="scripts/update_group.php";
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
	     	Nom: <input type="text" name="Falias" id="Falias" size=5/>	        	       
	        <button type="submit" id="LoadRecordsButton" class="buttonValid">Filtrer</button>
	    </form>
		</div>
		<div id="SelectedRowList"></div>	
		<div id="masterContainer" style="width: 100%;padding-top: 0px;">	
			<div id="childContainer">
				<!--  <p style="margin:0; padding: 0;padding-top: 5px;">Pour la sélection : <button id="UpdateButton">Supprimer</button></p>-->
			</div>	
		</div>	
		<script type="text/javascript">
	
			$(document).ready(function () {
	
			    //Prepare jTable
				$('#masterContainer').jtable({
					title: 'Liste des groupes',
					paging: true,	
					sorting: true,					
					defaultSorting: 'alias ASC',
					pageSize: <?php echo $_SESSION['pageSize'];?>,											
					actions: {						
						listAction: '<?php echo $script;?>?action=list&type=g',
						createAction: '<?php echo $script;?>?action=create&type=g',
						updateAction: '<?php echo $script;?>?action=update&type=g',
						deleteAction: '<?php echo $script;?>?action=delete&type=g'
					},
					fields: {
						id_groupe: {
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
							create: true,
							sorting: false,											
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
		                                        title: 'Permissions du groupe ' + studentData.record.alias,	  
                                                        paging: true,	
                                                        sorting: true,					
                                                        defaultSorting: 'detail ASC',
                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,												
		                                        actions: {
		                                            listAction: 'scripts/update_perms.php?action=list&type=g&id_user='+studentData.record.id_groupe+'&source2=zone',		                                            
		                                            updateAction: 'scripts/update_perms.php?action=update&type=g&id_user='+studentData.record.id_groupe+'&source2=zone',
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
                                    create: true,
                                    sorting: false,											
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
		                                        title: 'Permissions du groupe ' + studentData.record.alias,	  
                                                        paging: true,	
                                                        sorting: true,					
                                                        defaultSorting: 'detail ASC',
                                                        pageSize: <?php echo $_SESSION['pageSize'];?>,												
		                                        actions: {
		                                            listAction: 'scripts/update_perms.php?action=list&type=g&id_user='+studentData.record.id_groupe+'&source2=entite',		                                            
		                                            updateAction: 'scripts/update_perms.php?action=update&type=g&id_user='+studentData.record.id_groupe+'&source2=entite',		                                           
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
                                                                title: 'Entité',
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
                            user_add: {
                                    title: 'Utilisateurs',
                                    width: '1%',
                                    listClass: 'jtableTdRight',							
                                    sorting: false,
                                    paging: false,
                                    edit: true,
                                    create: false,
                                    selecting: true,
                                    selectingCheckboxes: true,		
                                    multiselect: true,
                                    selectOnRowClick: false,								
                                    //options: 'main/liste/getModele.php',
                                    display: function (studentData) {				
		                        //Create an image that will be used to open child table
		                        var $img = new Array();
		                        $img = $('<img src="graphs/icons/add.png" title="Ajouter des utilisateurs au groupe" />')	                      
		                        //Open child table when user clicks the image
		                        $img.click(function () {	                        	                 
		                            $('#masterContainer').jtable('openChildTable',	    	                            
										$img.closest('tr'),
										{
											title: 'Permissions du groupe ' + studentData.record.alias,	  
											sorting: true,
											paging: true,
											defaultSorting: 'nom ASC',
											actions: {
												listAction: 'scripts/update_group.php?action=listUser&type=a&id_group='+studentData.record.id_groupe,		                																							
											},
											fields: {  		                                        			                                        			                        					
												id_user: {
													key: true,
													create: false,		                        												                    							
													list: true,
													edit: true,													
													type: 'hidden',													
												},
												user_add: {
	                        						title: '',
	                        						width: '1%',
	                        						edit: false,
	                        						create: false,
	                        						sorting: false,							                        						
	                        						display: function (modeleData) {				
	                        	                        //Create an image that will be used to open child table
	                        	                        var $img2 = new Array();
	                        	                        $img2 = $('<img src="graphs/icons/add.png" title="Ajouter cet utilisateur au groupe" />')	                      
	                        	                        //Open child table when user clicks the image
	                        	                        $img2.click(function () {	                        	                 
	                        	                            $('#masterContainer').jtable('updateRecord', {	    	                            
                        	                                    record: {		                        	                                   
	                        	                                    id_groupe: studentData.record.id_groupe,
	                        	                                    id_user: modeleData.record.id_user,		                        	                                    
                        	                                    },	                        	                                    
                        	                                    url: 'scripts/update_group.php?action=addUser',                        	                                                  	                                    	                       	                        	                                    	                                                      	                                                     	                                     	                              
	                        	                            });
	                        	                                                      
	                        	                        });	                       
	                        	                        //Return image to show on the person row	  	                        	                                                    	                                      
	                        	                        return $img2;	                        	                        
	                                            	}   
	                                            },
												nom: {	
													width: '50%',						
													title: 'Nom',		                    																			
												},
												prenom: {		
													width: '50%',					
													title: 'Prénom',														
												}	                        						                        					 		                                                                                     	                                         		                                                                                                                          
											}, 											
										},
										function (data) { //opened handler		                                   
											data.childTable.jtable('load');																																
										});	 											
		                        });	   		                        					
		                        //Return image to show on the person row		                          	                                                    	                                     
		                        return $img;		                        														
	                    	}							
						},		
						user_remove: {
							title: '',
							width: '1%',							
							sorting: false,
							paging: false,
							edit: true,
							create: false,
							selecting: true,
							selectingCheckboxes: true,		
							multiselect: true,
							selectOnRowClick: false,								
							//options: 'main/liste/getModele.php',
							display: function (studentData) {				
		                        //Create an image that will be used to open child table
		                        var $img = new Array();
		                        $img = $('<img src="graphs/icons/remove.png" title="Retirer des utilisateurs du groupe" />')	                      
		                        //Open child table when user clicks the image
		                        $img.click(function () {	                        	                 
		                            $('#masterContainer').jtable('openChildTable',	    	                            
										$img.closest('tr'),
										{
											title: 'Permissions du groupe ' + studentData.record.alias,	  
											sorting: true,
											paging: true,
											defaultSorting: 'nom ASC',
											actions: {
												listAction: 'scripts/update_group.php?action=listUser&type=r&id_group='+studentData.record.id_groupe,		                																							
											},
											fields: {  		                                        			                                        			                        					
												id_user: {
													key: true,
													create: false,		                        												                    							
													list: true,
													edit: true,													
													type: 'hidden',													
												},
												user_add: {
	                        						title: '',
	                        						width: '1%',
	                        						edit: false,
	                        						create: false,
	                        						sorting: false,							                        						
	                        						display: function (modeleData) {				
	                        	                        //Create an image that will be used to open child table
	                        	                        var $img2 = new Array();
	                        	                        $img2 = $('<img src="graphs/icons/remove.png" title="Retirer cet utilisateur au groupe" />')	                      
	                        	                        //Open child table when user clicks the image
	                        	                        $img2.click(function () {	                        	                 
	                        	                            $('#masterContainer').jtable('updateRecord', {	    	                            
                        	                                    record: {		                        	                                   
	                        	                                    id_groupe: studentData.record.id_groupe,
	                        	                                    id_user: modeleData.record.id_user,		                        	                                    
                        	                                    },	                        	                                    
                        	                                    url: 'scripts/update_group.php?action=removeUser',                        	                                                  	                                    	                       	                        	                                    	                                                      	                                                     	                                     	                              
	                        	                            });
	                        	                                                      
	                        	                        });	                       
	                        	                        //Return image to show on the person row	  	                        	                                                    	                                      
	                        	                        return $img2;	                        	                        
	                                            	}   
	                                            },
												nom: {	
													width: '50%',			
													title: 'Nom',		                    																			
												},
												prenom: {
													width: '50%',							
													title: 'Prénom',														
												}	                        						                        					 		                                                                                     	                                         		                                                                                                                          
											}, 											
										},
										function (data) { //opened handler		                                   
											data.childTable.jtable('load');																																
										});	 											
		                        });	   		                        					
		                        //Return image to show on the person row		                          	                                                    	                                     
		                        return $img;		                        														
	                    	}							
						},					                                   		                   		                                                                          		                                    											
						alias: {							
							title: 'Nom du groupe',
							width: '99%',
							inputClass: 'validate[required]'														
						},																													
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
		                Falias: $('#Falias').val(),		             		                	              
		                //cityId: $('#cityId').val()
		            });
		        });
		 
		        //Load all records when page is first shown
		        $('#LoadRecordsButton').click();
				//Load person list from server
				//$('#masterContainer').jtable('load');		
				//Delete selected							
	
			});
	
		</script>			
 	</div>
 	</div> 
  </body>
</html>