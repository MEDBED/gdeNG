<?PHP
session_start();
$page="querybuilder.php";
$script="scripts/querybuilder.php";
$titre="Générateur de requêtes";
$pageDescription="";
$pageHelp="";
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../index.php");
	exit;
}
include_once("../header.inc.php");
include_once("../include/functions.php");
include_once("../include/protect_var.php");
//entete_page($GLOBALS['params']['appli']['title_appli']." - ".$titre,'');
?>
<script type="text/javascript" src="../include/functions.js"></script>
<script type="text/javascript" src="../content/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="../content/queryBuilder/queryBuilderForm.js">
<script type="text/javascript" src="../content/queryBuilder/queryBuilder.js">
<script type="text/javascript" src="../content/queryBuilder/queryBuilderFormWindow.js">
<script type="text/javascript">

Ext.onReady(function(){ 
                                         
        var sampleGridColumns= [{
                header: 'Id',
                width:90,
                dataIndex: 'id'
        },{
                header: 'Title', 
                dataIndex: 'title',
                sortable: true,
                width:150
        },{
                header: 'Alias', 
                dataIndex: 'alias',
                sortable: true,
                width:130
        },{
                header: 'Created',
                width:80,
                dataIndex: 'created_date'
        },{
                header: 'Modified',
                width:80,
                sortable: true,
                dataIndex: 'modified',
        },{
                header: 'Published', 
                dataIndex: 'state',
                sortable: true,
                renderer:function(v){if(v==1){return 'Published'}else{return '<span style="color:red">UnPublished</span>'}}
        }];
        
        var sampleGridReader = new Ext.data.JsonReader({
                        totalProperty: 'total',
                        successProperty: 'success',
                        idProperty: 'id',
                        root: 'data'
                },[
                        {name: 'id'},
                        {name: 'title'},
                        {name: 'created'},
                        {name: 'state'},
                        {name: 'alias'},
                        {name: 'created_date'},
                        {name: 'modified'}
        ]);
                
        
        // Typical Store collecting the Proxy, Reader and Writer together.
        var sampleGridStore = new Ext.data.Store({
                reader          : sampleGridReader,
                autoLoad        : true, 
                url                     : 'scripts/querybuilder.php',
        });
        
        // create the Grid
        var grid = new Ext.grid.GridPanel({
                tbar:[{ 
                        text: 'Query Builder', 
                        icon: 'content/queryBuilder/images/icons/querybuilder.png',
                        handler: function(){
                                queryBuilder.show();
                        }, 
                        scope: this
                }],
                store: sampleGridStore,
                renderTo:'queryBuilderGridExample',
                columns: sampleGridColumns,     
                stripeRows: true,
                height: 350,
                title: 'Grid with QueryBuilder'
        });
        
        var queryBuilder = new Ext.ux.QueryBuilder({
                title                                   : 'Query Builder',
                border                                  : true,
                width                                   : 1000,
                y_                                              : 200,
                height                                  : 200,
                grid                                    : grid,
                filePath                                : scriptUrl+'ux/queryBuilder/', 
                treeDataUrl                             : 'scripts/querybuilder.php?task=queryBuilder&subtask=treeData&mainquery=contents',
                fieldStoreUrl                   : 'scripts/querybuilder.php?task=queryBuilder&subtask=fields&mainquery=contents',
                multipleValuesStoreUrl  : 'scripts/querybuilder.php?task=queryBuilder&subtask=multipleValues',
                querySaveUrl                    : 'scripts/querybuilder.php?task=queryBuilder&subtask=saveQuery&mainquery=contents',
                countRecordUrl                  : 'scripts/querybuilder.php?task=queryBuilder&subtask=countRecord&mainquery=contents',
                parentNodesComboStoreUrl: 'scripts/querybuilder.php?task=queryBuilder&subtask=parentNodes'
        });
        
        queryBuilder.on('run', function(filterObject){          
                this.grid.store.baseParams = {};                
                this.grid.store.baseParams['filterQuery'] = filterObject.filter;                
                this.grid.store.reload();
        });
});

</script>
</head>
<body>
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
		<h2><?php  echo $pageDescription;?></h2>	
		<div id="mess" style="display: none;"></div>
		<div id="help" title="Aide"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html<?php echo $page;?>',headingText: 'Aide',preserveContent: false } )"></a></div>
	<div class="content">
		<?php 
		connectSQL();
		$req="SELECT * FROM user WHERE id=$_COOKIE[ID_UTILISATEUR];";
		$res=@mysql_fetch_array(@mysql_query($req)); 		
		?>		
		<div class="formLeft">
			<table>
			<tr><td></td><td></td></tr>		
			</table>
		</div>
		<div class="formRight">
			<table>
			<tr><td></td><td></td></tr>		
			</table>
		</div>				
	</div>
</div>
</body>
</html>