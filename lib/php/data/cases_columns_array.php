<?php

//Function to query the db and return the correct columns for the cases table html.

function columns_array($dbh)
	
	{
		
		$get_columns = $dbh->prepare('SELECT * from cm_columns');
		$get_columns->execute();
		$result = $get_columns->fetchAll(PDO::FETCH_ASSOC);
		
		// Hard code last name col before first name col
		$tmp = $result[2];
		$result[2] = $result[3];
		$result[3] = $tmp;
		
		return $result;
		
	}	

