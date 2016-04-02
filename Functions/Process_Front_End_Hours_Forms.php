<?php
function Process_EWD_FEUPHRS_Front_End_Forms() {

		global $user_message;
        if (isset($_POST['ewd-feuphrs-action'])) {
		    switch ($_POST['ewd-feuphrs-action']) {
		        case "hours":
                    $user_message['Message'] = Enter_Hours();
                    break;
            }
		}

}

function Enter_Hours() {
	global $wpdb, $feup_success;
	global $ewd_feup_user_hours_table_name;
	global $ewd_feup_user_table_name;
	$user = CheckLoginCookie();
	if (!$user) {
		return __("Error: Not logged in", 'EWD_FEUP');
	}
	if (!is_numeric($_POST['ewd-feup-hours']) ||
		$_POST['ewd-feup-hours'] <= 0) {
		return __('Hours must be a positive number', 'EWD_FEUP');
	}
	if (empty($_POST['ewd-feup-event'])) {
		return __('An event must be specified', 'EWD_FEUP');
	}

	if (empty($_POST['ewd-feup-end-date']) || empty($_POST['ewd-feup-begin-date'])) {
		return __('Begin and end dates must be specified', 'EWD_FEUP');
	}

	$beginDate=strtotime($_POST['ewd-feup-begin-date']);
	$endDate=strtotime($_POST['ewd-feup-end-date']);

	if ($beginDate >  $endDate) {
		return __('End date must be less than or equal to start date', 'EWD_FEUP');
	}

	if ($user) {
		$userId=$wpdb->get_row($wpdb->prepare("SELECT User_ID from $ewd_feup_user_table_name where Username = %s",$user['Username']));
	}
	$user['User_ID']=$userId->User_ID;
	$data = array(
		'User_ID' => $user['User_ID'],
		'Hours' => $_POST['ewd-feup-hours'],
		'Verified' => 0,
		'Event_Name' => $_POST['ewd-feup-event'],
		'Hours_Start_Date' => Date('Y-m-d',$beginDate),
		'Hours_Stop_Date' => Date('Y-m-d',$endDate)
	);
	if (!empty($_POST['ewd-feup-event-id'])) {
		$data['Event_ID'] = $_POST['ewd-feup-event-id'];
	}
	if ($wpdb->insert($ewd_feup_user_hours_table_name, $data)) {
		$feup_success = true;
		return __("Hours entered", 'EWD_FEUP');
	} else {
		return __('Error inserting hours, please try again', 'EWD_FEUP');
	}
}
