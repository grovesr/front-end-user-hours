<?php
function Process_EWD_FEUPHRS_Front_End_Forms() {

		global $user_message;
        if (isset($_POST['ewd-feuphrs-action'])) {
		    switch ($_POST['ewd-feuphrs-action']) {
		        case "hours":
										$user_message['Medssage_Type'] = 'Update';
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
		$lastid = $wpdb->insert_id;
		$data['Hour_ID'] = $lastid;
		$feup_success = true;
		send_unverified_hours_email($user, $data);
		return __("Hours entered", 'EWD_FEUP');
	} else {
		return __('Error inserting hours, please try again', 'EWD_FEUP');
	}
}

function send_unverified_hours_email($user, $data) {
	global $feuphrs_admin;
	// Use FE Users settings
	$Admin_Email = get_option("EWD_FEUP_Admin_Email");
	$verify_url = admin_url('admin.php?page=EWD-FEUPHRS-options&' .
													'Action=EWD_FEUPHRS_VerifyHours&' .
													'DisplayPage=User&User_ID=' .
													$user['User_ID'] . '&Hour_ID=' .
													$data['Hour_ID']);
	//send email to admin
	$subject = "Front End User Hours Need To Be Verified on ".get_bloginfo('name');
	$headers = array();
	$headers[] = "From:$Admin_Email";
	$headers[] = "Cc:$feuphrs_admin";
	$message =  "Front End Users Hours user '" . $user['Username'] . "' has entered '" ;
	$message .= $data['Hours'] . "' volunteer hours for event '" . $data['Event_Name'] . "'. ";
	$message .= "These hours need to be verified in the admin section.\n\n";
	$message .= "Click on the link below to go to the admin section and verify ";
	$message .= " the hours.\n\n";
	$message .= $verify_url;
	$mail_success = wp_mail($Admin_Email, $subject, $message, $headers);
}
