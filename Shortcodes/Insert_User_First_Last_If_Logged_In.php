<?php
/* Providea means of indicating that a user is logged in that is invisible if
the user is not logged in */
function Insert_User_First_Last_If_Logged_In($atts) {
		// Include the required global variables, and create a few new ones
		global $wpdb;
		global $ewd_feup_user_table_name, $ewd_feup_levels_table_name, $ewd_feup_user_fields_table_name;

		$UserCookie = CheckLoginCookie();
		$ReturnString = "";
		if ($UserCookie) {
			$User = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ewd_feup_user_table_name WHERE Username='%s'", $UserCookie['Username']));
			$FieldRows = $wpdb->get_results($wpdb->prepare("SELECT Field_Value FROM $ewd_feup_user_fields_table_name WHERE User_ID='%s' AND (Field_Name = 'First Name' OR Field_Name = 'Last Name')",$User->User_ID));
			$FullName = "";
			foreach ($FieldRows as $row) {
				$FullName .= $row->Field_Value.' ';
			}
			$ReturnString = "<strong>Active Volunteer:</strong> $FullName";
		}
		return $ReturnString;
}
add_shortcode("user-first-last-if-logged-in", "Insert_User_First_Last_If_Logged_In");
