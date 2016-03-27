<?php

/* Creates the admin page, and fills it in based on whether the user is looking at

*  the overview page or an individual item is being edited */

function EWD_FEUPHRS_Output_Options() {

		global $wpdb, $error, $Full_Version;

		global $ewd_feup_user_table_name, $ewd_feup_user_fields_table_name, $ewd_feup_levels_table_name, $ewd_feup_fields_table_name;

		

		if (isset($_GET['DisplayPage'])) {
			  $Display_Page = $_GET['DisplayPage'];
		}
		else {
			$Display_Page = null;
		}

		if (!isset($_GET['Action'])) {
			$_GET['Action'] = null;
		}

		include( plugin_dir_path( __FILE__ ) . '../html/AdminHeader.php');

		if ($_GET['Action'] == 'EWD_FEUPHRS_User_Hours' | 
				($_GET['Action'] == 'EWD_FEUPHRS_MassEditHours' and $Display_Page == 'UserHours') |
				$_GET['Action'] == 'EWD_FEUPHRS_AddHours'|
				$_GET['Action'] == 'EWD_FEUPHRS_VerifyHours' |
				$_GET['Action'] == 'EWD_FEUPHRS_DeleteHours'|
				$_GET['Action'] == 'EWD_FEUPHRS_DeleteAllUserHours')  {include( plugin_dir_path( __FILE__ ) . '../html/UserHours.php');}

		else {include( plugin_dir_path( __FILE__ ) . '../html/MainScreen.php');}

		include( plugin_dir_path( __FILE__ ) . '../html/AdminFooter.php');

}
