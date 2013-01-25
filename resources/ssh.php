<?php
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../logout.php");
	exit;
}
session_start();
include_once("../header.inc.php");
include_once("../include/functions.php");
$titre='SSH Client';
entete_page($GLOBALS['params']['appli']['title_appli']." - ".$titre,'');
?>
<script type="text/javascript" src="../content/jquery-1.8.1.min.js"></script>

 <script type="text/javascript">
            
        </script>
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
    </head>
    <body>          
        <div style="width: 60em; height: 30em;">
            
        </div>
       <div id="gateone"></div>   
       <script type="text/javascript" src="../content/GateOne/gateone/static/gateone.js"></script> 
        <script type="text/javascript">
        jQuery(document).ready(function(){
           GateOne.init({
            url: "https://10.71.111.155:4443/",
            autoConnectURL: '10.71.21.10'
            //fillContainer: true,
            //embedded: true               
           });
        });
      // window.onload = GateOne.init({goURL: 'https://10.71.111.155:4443'});
       /* window.onload = function() {
            GateOne.prefs.url = 'https://10.71.111.155:4443';
            GateOne.prefs.fillContainer = true;
            GateOne.prefs.embedded = true;
            GateOne.prefs.autoConnectURL = 'ssh://10.71.21.10:22';
 
            GateOne.init();
        };*/
        </script>
    </body>        
</html>