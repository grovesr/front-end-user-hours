<?php
function Get_User_Hours_Summary($Username) {

	global $wpdb, $ewd_feup_user_hours_table_name, $ewd_feup_user_table_name;
	$Sql="SELECT User_ID from $ewd_feup_user_table_name WHERE Username = '$Username' LIMIT 1";
	$User=$wpdb->get_row($Sql);
	$User_ID=$User->User_ID;
	$user_hours = $wpdb->get_results($wpdb->prepare("SELECT * FROM $ewd_feup_user_hours_table_name WHERE User_ID='%s'", $User_ID));



	$verifiedHours = 0;

	$unverifiedHours = 0;


	foreach ($user_hours as $row) {

		if ($row->Verified) {

			$verifiedHours += $row->Hours;

		} else {

			$unverifiedHours += $row->Hours;

		}

	}



	return array($verifiedHours, $unverifiedHours);

}



function Get_Users_Hours_Details($Username) {

	global $wpdb, $ewd_feup_user_hours_table_name, $ewd_feup_user_table_name;
	$Sql="SELECT User_ID from $ewd_feup_user_table_name WHERE Username = '$Username' LIMIT 1";
	$User=$wpdb->get_row($Sql);
	$User_ID=$User->User_ID;
	return $wpdb->get_results($wpdb->prepare("SELECT * FROM $ewd_feup_user_hours_table_name WHERE User_ID='%s' ORDER BY Hours_Stop_Date DESC", $User_ID));

}
