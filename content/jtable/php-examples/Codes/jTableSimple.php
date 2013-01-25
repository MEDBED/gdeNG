<html>
  <head>

    <link href="themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
	<link href="scripts/jtable/themes/standard/blue/jtable_blue.css" rel="stylesheet" type="text/css" />
	
	<script src="scripts/jquery-1.6.4.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
    <script src="scripts/jtable/jquery.jtable.js" type="text/javascript"></script>
	
  </head>
  <body>
	<div id="PeopleTableContainer" style="width: 600px;"></div>
	<script type="text/javascript">

		$(document).ready(function () {

		    //Prepare jTable
			$('#PeopleTableContainer').jtable({
				title: 'Liste des matériels',
				actions: {
					listAction: 'PersonActions.php?action=list',
					createAction: 'PersonActions.php?action=create',
					updateAction: 'PersonActions.php?action=update',
					deleteAction: 'PersonActions.php?action=delete'
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false
					},
					nom: {
						title: 'Nom',
						width: '20%'
					},
					systeme: {
						title: 'SE',
						width: '20%'
					},
					systeme_version: {
						title: 'Version',
						width: '20%'
					},
					sn: {
						title: 'SN',
						width: '20%'
					},
					date_installe: {
						title: 'Installé le',
						width: '10%',
						type: 'date',
						/*create: false,
						edit: false*/
					}
				}
			});

			//Load person list from server
			$('#PeopleTableContainer').jtable('load');

		});

	</script>
 
  </body>
</html>
