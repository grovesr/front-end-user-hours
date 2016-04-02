<?php
function EWD_FEUP_Query_User_Hours_Page($get,$user_table_name,$user_hours_table_name) {
	if (isset($get['Page'])) {
		$Page = $get['Page'];
	}
	else {$Page = 1;
	}
	if (!isset($get['OrderBy'])) {
		$get['OrderBy'] = null;
	}
	$Sql =  "SELECT distinct $user_table_name.User_ID, $user_table_name.Username ";
	$Sql .= "FROM $user_hours_table_name join $user_table_name on (";
	$Sql .= "$user_hours_table_name.User_Id = $user_table_name.User_Id) ";
	if (isset($get['OrderBy']) and isset($get['Order']) and $get['DisplayPage'] == "Dashboard") {
		$Sql .= "ORDER BY " . $get['OrderBy']  ." ". $get['Order'] . " ";
	}
	else {$Sql .= "ORDER BY  $user_table_name.Username";
	}
	$Sql .= " LIMIT " . ($Page - 1)*20 . ",20";
	return $Sql;
};

function EWD_FEUP_Query_User_Hours_Count($user_hours_table_name) {
	$Sql = "SELECT COUNT(DISTINCT User_ID) FROM $user_hours_table_name ";
	return $Sql;
};

function EWD_FEUP_Sum_Verified_Hours($user_hours_table_name,$UserID) {
	$Sql = "SELECT sum(Hours) as Hours FROM $user_hours_table_name where User_ID=$UserID and Verified = 1";
	return $Sql;
};

function EWD_FEUP_Sum_Unverified_Hours($user_hours_table_name,$UserID) {
	$Sql = "SELECT sum(Hours) as Hours FROM $user_hours_table_name where User_ID=$UserID and Verified = 0";
	return $Sql;
};

function EWD_FEUP_Query_Users_Hours($user_table_name,$user_hours_table_name) {
	$Sql =  "SELECT $user_table_name.User_ID, $user_table_name.Username, ";
	$Sql .= "$user_hours_table_name.Event_ID, $user_hours_table_name.Event_Name, ";
	$Sql .= "$user_hours_table_name.Hours_Start_Date, $user_hours_table_name.Hours_Stop_Date, ";
	$Sql .= "$user_hours_table_name.Hours, $user_hours_table_name.Verified ";
	$Sql .= "FROM $user_hours_table_name join $user_table_name on (";
	$Sql .= "$user_hours_table_name.User_Id = $user_table_name.User_Id) ";
	$Sql .= "ORDER BY $user_table_name.Username, $user_hours_table_name.Hours_Stop_Date DESC";
	return $Sql;
};
