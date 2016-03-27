<?php
function Add_EWD_FEUPHRS_User_Hours($hours) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->insert($ewd_feup_user_hours_table_name, $hours);
	$insert = __("Hours have been added successfully edited.", 'EWD_FEUP');
	return $insert;
}

function Verify_EWD_FEUPHRS_Hours($Field_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->update($ewd_feup_user_hours_table_name, array('Verified' => 1),array( 'Field_ID' => $Field_ID));
	$update = __("Hours have been successfully verified.", 'EWD_FEUP');
	return $update;
}

function Mass_Verify_EWD_FEUPHRS_Hours($ids) {
		if (is_array($ids)) {
				foreach ($ids as $Field) {
						if ($Field != "") {
								Verify_EWD_FEUPHRS_Hours($Field);
						}
				}
		}
		return __("Hours have been successfully verified.", 'EWD_FEUP');
}

function Mass_Verify_EWD_FEUPHRS_User_Hours($ids) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	if (is_array($ids)) {
		foreach ($ids as $User_ID) {
			if ($User_ID != "") {
				$hoursRows=$wpdb->get_results("SELECT Field_ID from $ewd_feup_user_hours_table_name WHERE User_ID = $User_ID");
				foreach ($hoursRows as $hourRow) {
					Verify_EWD_FEUPHRS_Hours($hourRow->Field_ID);
				}
			}
		}
	}
	return __("Hours have been successfully verified.", 'EWD_FEUP');
}

function Mass_Delete_EWD_FEUPHRS_User_Hours($ids) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	if (is_array($ids)) {
		foreach ($ids as $User_ID) {
			if ($User_ID != "") {
				$hoursRows=$wpdb->get_results("SELECT Field_ID from $ewd_feup_user_hours_table_name WHERE User_ID = $User_ID");
				foreach ($hoursRows as $hourRow) {
					Delete_EWD_FEUPHRS_Hours($hourRow->Field_ID);
				}
			}
		}
	}
	return __("Hours have been successfully verified.", 'EWD_FEUP');
}

function Delete_EWD_FEUPHRS_Hours($Field_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->delete($ewd_feup_user_hours_table_name, array( 'Field_ID' => $Field_ID));
	$update = __("Hours have been successfully verified.", 'EWD_FEUP');
	return $update;
}

function Delete_EWD_FEUPHRS_AllUserHours($User_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->delete($ewd_feup_user_hours_table_name, array( 'User_ID' => $User_ID));
	$update = __("Hours have been successfully verified.", 'EWD_FEUP');
	return $update;
}

function Mass_Delete_EWD_FEUPHRS_Hours($ids) {
		if (is_array($ids)) {
				foreach ($ids as $Field) {
						if ($Field != "") {
								Delete_EWD_FEUPHRS_Hours($Field);
						}
				}
		}
		return __("Hours have been successfully verified.", 'EWD_FEUP');
}
