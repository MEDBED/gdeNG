<?php 
if(!isset($_COOKIE["ID_UTILISATEUR"]) && $_SESSION['isAdmin']==1){
	header("Location: ../logout.php");
	exit;
}
session_start();
$page="alertes.php";
$script="scripts/update_alertes.php";
$titre="Gestion des alertes logiciels";
$pageDescription="Modification et gestion des alertes logiciels";
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
					title: 'Liste des logiciels',
					paging: true,						
					/*selecting: true,
					selectingCheckboxes: true,		
					multiselect: true,
					selectOnRowClick: false,*/																		
					pageSize: <?php echo $_SESSION['pageSize'];?>,
					sorting: true,					
					actions: {						
						listAction: '<?php echo $script;?>?action=list&a=<?echo $_GET[a];?>',
						//createAction: '<?php echo $script;?>?action=create&type=u',
						updateAction: '<?php echo $script;?>?action=update&a=<?echo $_GET[a];?>',
						//deleteAction: '<?php echo $script;?>?action=delete&type=u'
					},
					fields: {													
						editeur: {							
							title: 'Editeur',
							//inputClass: 'validate[required]'														
						},
                                                all_editeur: {
                                                    list: false,
                                                    title: 'Appliquer la modification à tout l\'éditeur ?',
                                                    type: 'checkbox',							
                                                    values: { '0':'', '1':'Oui'},
                                                },
						nom: {							
                                                    title: 'Nom',														
                                                    //inputClass: 'validate[required]'
						},  
                                                 all_nom: {
                                                    list: false,
                                                    title: 'Appliquer la modification à ce nom (+ l\'editeur) de logiciel ?',
                                                    type: 'checkbox',							
                                                    values: { '0':'', '1':'Oui'},
                                                 },
						version: {							
							title: 'Version',							
							//inputClass: 'validate[required]',
						},
                                                id_type: {							
                                                    title: 'Catégorie',
                                                    width: '1%',							
                                                    options: 'main/liste/getType.php?source=soft',
                                                    inputClass: 'validate[required]',
                                                 },
						alerte_adm: {						
                                                    title: 'Alerte A',
                                                    width: '2%',
                                                    listClass: 'jtableTdCenter',
                                                    type: 'radiobutton',
                                                    //list: false,
                                                    options: {  '1':'Installation requise',
                                                                '2':'Installation Inutile',
                                                                '3':'Installation gênant le bon fonctionnement du poste',
                                                                '4':'Installation dangereuse pour le poste voir tout le réseau informatique',
                                                                '5':'Aucune alerte'
                                                    },
                                                    display: function (data){                                                                                                                
                                                        if (data.record.alerte_adm==1){
                                                            info = '<div title="Aucun risque, contrôle de l\'installation" style="background-color: #339933; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.alerte_adm==2){
                                                            info = '<div title="Risque modéré, installation inutile" style="background-color: #FFC629; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.alerte_adm==3){
                                                            info = '<div title="Risque important, installation gênant le bon fonctionnement du poste" style="background-color: #FF8F29; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.alerte_adm==4){
                                                            info = '<div title="Risque majeur, installation perturbant le poste voir tout le réseau informatique" style="background-color: #FF0000; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.alerte_adm==5){
                                                            info = '<div title="Aucune alerte" style="background-color: #7d7d7d; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else{
                                                            info=data.record.alerte_adm;
                                                        }
                                                        return info;
                                                    },
                                                   inputClass: 'validate[required]',
						},	
						alerte_peda: {						
                                                    title: 'Alerte P',
                                                    type: 'radiobutton',
                                                    width: '2%',
                                                    listClass: 'jtableTdCenter',
                                                    options: {'1':'Installation requise', '2':'Installation Inutile','3':'Installation gênant le bon fonctionnement du poste','4':'Installation dangereuse pour le poste voir tout le réseau informatique','5':'Aucune alerte'},												
                                                    display: function (data){                                                                                                                
                                                        if (data.record.alerte_peda==1){
                                                            info = '<div title="Aucun risque, contrôle de l\'installation" style="background-color: #339933; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.alerte_peda==2){
                                                            info = '<div title="Risque modéré, installation inutile" style="background-color: #FFC629; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.alerte_peda==3){
                                                            info = '<div title="Risque important, installation gênant le bon fonctionnement du poste" style="background-color: #FF8F29; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.alerte_peda==4){
                                                            info = '<div title="Risque majeur, installation perturbant le poste voir tout le réseau informatique" style="background-color: #FF0000; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else  if (data.record.alerte_peda==5){
                                                            info = '<div title="Aucune alerte" style="background-color: #7d7d7d; width: 10px; height:10px;display: inline-block ;">&nbsp;</div>&nbsp;';
                                                        }else{
                                                            info=data.record.alerte_peda;
                                                        }
                                                        return info;
                                                    },
                                                    inputClass: 'validate[required]',
						}, 
                                                description: {
                                                    title: 'Description',
                                                    list: false,
                                                     type: 'textarea'
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