<?php

try
{
	//Open database connection
	$con = mysql_connect("localhost","gdeNG","gde");
	mysql_select_db("gdeNG", $con);

	//Getting records (listAction)
	if($_GET["action"] == "list")
	{
		//Get records from database
		$result = mysql_query("SELECT * FROM materiel;");
		
		//Add all records to an array
		$rows = array();
		while($row = mysql_fetch_array($result))
		{
		    $rows[] = $row;		    
		}
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Records'] = $rows;
		print json_encode($jTableResult);
	}
	//Creating a new record (createAction)
	else if($_GET["action"] == "create")
	{
		//Insert record into database
		$result = mysql_query("INSERT INTO materiel(id_modele,nom,sn,systeme,systeme_version,date_installe) 
				VALUES('" . $_POST["id_modele"] . "','" . $_POST["nom"] . "','" . $_POST["sn"] . "','" . $_POST["systeme"] . "','" . $_POST["systeme_version"] . "','" . $_POST["date_installe"]."')");
		
		//Get last inserted record (to return to jTable)
		$result = mysql_query("SELECT * FROM materiel WHERE id = LAST_INSERT_ID();");
		$row = mysql_fetch_array($result);

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $row;
		print json_encode($jTableResult);
	}
	//Updating a record (updateAction)
	else if($_GET["action"] == "update")
	{
		//Update record in database
		$result = mysql_query("UPDATE materiel SET id_modele = '" . $_POST["id_modele"] . "', nom = '" . $_POST["nom"] . "', sn = '" . $_POST["sn"] . "', systeme = '" . $_POST["systeme"] . "', systeme_version = '" . $_POST["systeme_version"] . "', date_installe = '" . $_POST["date_installe"] . "' 
				WHERE id = " . $_POST["id"] . ";");

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}
	//Deleting a record (deleteAction)
	else if($_GET["action"] == "delete")
	{
		//Delete from database
		$result = mysql_query("DELETE FROM materiel WHERE id = " . $_POST["id"] . ";");

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}

	//Close database connection
	mysql_close($con);

}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = $ex->getMessage();
	print json_encode($jTableResult);
}
	
?>