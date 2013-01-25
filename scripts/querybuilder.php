<?php
 error_reporting(E_ALL&~E_NOTICE);
    include('db.php');
    
    /***
    *helper function
    ***************/
    function RequestGetVar($key, $default=''){
        if(isset($_REQUEST[$key])){
            return $_REQUEST[$key];
        }
        return $default;
    }
    
    /***********************************
    *Query builder task are handled under this function
    ************************************************************/
    function queryBuilder($subtask, $mainquery){
        $userId                        = 123;//dummy user id, you can put here user id from session
        
        switch( $subtask ){            
            case 'treeData':
                $node                = RequestGetVar( 'node', 'Root' );
                $result                = getQueryBuilderTreeData( $node, $mainquery );
            break;
            case 'fields':
                $result                = new stdClass();
                $result->fields        = getQueryBuilderFields( $mainquery );
                $result->total        = count( $result->fields );
            break;
            case 'multipleValues':
                $multipleValues        = RequestGetVar( 'multipleValues', '' );
                $table                = RequestGetVar( 'table', '' );
                $result                = new stdClass();
                $result->data        = getQueryBuilderMultipleValues( $multipleValues, $table );
                $result->total        = count( $result->data );
            break;
            case 'parentNodes':
                $result                    = new stdClass();                    
                $publicQueries             = new stdClass( );
                $publicQueries->text     = 'Public';
                $publicQueries->id         = 'Public';
                if($userId){//if logged in user
                    $privateQueries         = new stdClass( );
                    $privateQueries->text     = 'Private';
                    $privateQueries->id     = 'Private';
                    $result->data             = array( $publicQueries, $privateQueries );
                }else{
                    $result->data             = array( $publicQueries );
                }
                $result->total        = count( $result->data );
            break;
            case 'countRecord':
                $filter                = RequestGetVar( 'filter', '' );
                $result                = getQueryBuilderRecord( $filter, $mainquery );
                $result                = count( $result );
            break;
            case 'saveQuery':
                include('tableQuery.php');
                $table                = new TableQuery('com_extensiondemo_querybuilder_queries', 'id');
                $post                = $_REQUEST;                
                $post['createdBy']  = $userId;
                $result                = new stdClass();
                if( $table->bind($post) ){
                    if ( $table->store(false) ) {
                        $result->feedback    = 'Details Saved' ;
                        $result->id         = (int) $table->get('id');    
                        $result->success      = true;            
                    } else {
                        $result->feedback    = $table->getError();
                        $result->success      = false;
                    }
                }else{
                    $result->success          = false;
                    $result->feedback         = $table->getError();
                }        
            break;            
        }        
        
        return json_encode($result);
        
    }
    
    /*************************************
    * Return the private and public queries list
    ****************************************************************/
    function getQueryBuilderTreeData( $parent = 'Root' , $mainquery='contents' ){
        
        $userId    = 123;//dummy user id, you can put here user id from session
        
        if( $parent == 'Root' ){
            $publicQueries             = new stdClass( );
            $publicQueries->text     = 'Public';
            $publicQueries->id         = 'Public';
            $publicQueries->cls        = 'folder';
            if($userId){//if logged in user
                $privateQueries         = new stdClass( );
                $privateQueries->text     = 'Private';
                $privateQueries->id     = 'Private';
                $privateQueries->cls    = 'folder';
                return array( $publicQueries, $privateQueries );
            }
            
            return array( $publicQueries );
        }else{
            $sql     = "Select title as text, 'true' as leaf, id, json from com_extensiondemo_querybuilder_queries where parent = '{$parent}' AND mainquery='{$mainquery}' ";
            if( $parent=='Private' ){
                $sql .= " AND createdBy = '{$userId}' ";
            }
            
            return loadObjectlist( $sql );
        }        
    }
    
    /*************************************
    * Return the Query Builder Fields
    ****************************************************************/
    function getQueryBuilderFields( $mainquery = 'contents'  ){        
        $sql     = "Select * from com_extensiondemo_querybuilder_fields WHERE mainquery='{$mainquery}' and published=1";        
        return loadObjectlist($sql);
    }
    
    /*************************************
    * Return the 'multiple value' field data
    ****************************************************************/
    function getQueryBuilderMultipleValues( $query , $table=''){        
        return loadObjectlist( $query );
    }
    
    
    
    /****
    *Return the table to be used for builing query
    **********/
    function getTables($filterQuery){
        $tables            = array ('com_extensiondemo_content c');
        if( $filterQuery !='' ){
            $filterQuery              = json_decode($filterQuery);
            foreach($filterQuery->tables as $table){
                $table = trim($table);
                if( $table!=='com_extensiondemo_content' && $table!=='c' && !in_array($table, $tables)){
                    $tables[] = $table;
                }
            }
        }
        return implode(',', $tables);        
    }
    
    /*************************************
    * Return resultset returned from the query built upon given filter
    ****************************************************************/
    function getQueryBuilderRecord( $filter, $mainquery='contents' ){        
        $filterQuery              = json_decode($filter);
        $whereCondition              = $filterQuery->where;        
        $tables                    = getTables($filter);        
        
        $query = "SELECT * FROM  ".$tables." WHERE 1=1 AND (". $whereCondition .")";            
        
        return loadObjectlist( $query );        
    }
    
    /**
     * Gets Atricals from the com_extensiondemo_content table by filtering them accroding to $filter option
     *
     *
     * @access public
     * @return records found
     */
    function contents( $filter ="" ){        
        $tables         = getTables($filter);
        $whereCondition    = '1=1';
        if( $filter !='' ){
            $filterQuery              = json_decode($filter);
            $whereCondition              = $filterQuery->where;
            $additionalFields        = $filterQuery->additionalFields;
            $newFields                = array();
            foreach($additionalFields as $additionalField){
                $newFields[]        = $additionalField->fieldIndex;
            }
            $newFields                = implode(',',$newFields);
            if($newFields){
                $newFields        = ', '.$newFields;
            }
        }    
        
        $sql = "SELECT c.* $newFields , DATE_FORMAT(created, '%b %e, %Y') as created_date, DATE_FORMAT(modified, '%b %e, %Y') as modified FROM ".$tables." WHERE 1=1 AND (". $whereCondition .")";            
                
        return loadObjectlist($sql);
    }
    
    $task                = $_REQUEST['task'];
    if($task=='queryBuilder'){
        $subtask                = $_REQUEST['subtask'];
        $mainquery                = $_REQUEST['mainquery'];
        echo queryBuilder($subtask, $mainquery);        
    }else{    
        $filterQuery        = $_REQUEST['filterQuery'];
        $rows                = contents($filterQuery);
        $result             = new stdClass();
        $result->data        = $rows;
        $result->total        = count($rows);
        $result->success    = true;
        echo $json             =  json_encode($result);    
    }

?>
