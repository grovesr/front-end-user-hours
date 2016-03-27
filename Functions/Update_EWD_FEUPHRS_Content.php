<?php
/* This file is the action handler. The appropriate function is then called based 
*  on the action that's been selected by the user. The functions themselves are all
* stored either in Prepare_Data_For_Insertion.php or Update_Admin_Databases.php */

function Update_EWD_FEUPHRS_Content() {
global $feup_message;
unset($feup_message);
if (isset($_GET['Action'])) {
	$feup_message['Message_Type']='Update';
				switch ($_GET['Action']) {
						case 'EWD_FEUPHRS_AddHours':
							$feup_message['Message'] = Add_EWD_FEUP_User_Hours(array(
								'User_ID' => $_POST['User_ID'],
								'Hours' => $_POST['ewd-feup-hours'],
								'Event_Name' => $_POST['ewd-feup-event'],
								'Verified' => 1,
								'Event_ID' => empty($_POST['ewd-feup-event-id']) ? null : $_POST['ewd-feup-event-id']
							));
						break;
						case 'EWD_FEUPHRS_VerifyHours':
							$feup_message['Message'] = Verify_EWD_FEUPHRS_Hours($_GET['Hour_ID']);
						break;
						case 'EWD_FEUPHRS_DeleteHours':
							$feup_message['Message'] = Delete_EWD_FEUPHRS_Hours($_GET['Hour_ID']);
						break;
						case 'EWD_FEUPHRS_DeleteAllUserHours':
							$feup_message['Message'] = Delete_EWD_FEUPHRS_AllUserHours($_GET['User_ID']);
							break;
						case 'EWD_FEUPHRS_MassEditHours':
							if (isset($_POST['Hours_Bulk']) & isset($_POST['action'])) {
								if ($_POST['action'] == 'verify') {
									$feup_message['Message'] = Mass_Verify_EWD_FEUPHRS_Hours($_POST['Hours_Bulk']);
									echo $feup_message['Message'];
								} else if ($_POST['action'] == 'delete') {
									$feup_message['Message'] = Mass_Delete_EWD_FEUPHRS_Hours($_POST['Hours_Bulk']);
								}
							}
						case 'EWD_FEUPHRS_MassEditUserHours':
							if (isset($_POST['Users_Bulk']) & isset($_POST['action'])) {
								if ($_POST['action'] == 'verify') {
									$feup_message['Message'] = Mass_Verify_EWD_FEUPHRS_User_Hours($_POST['Users_Bulk']);
								} else if ($_POST['action'] == 'delete') {
									$feup_message['Message'] = Mass_Delete_EWD_FEUPHRS_User_Hours($_POST['Users_Bulk']);
								}
								}
						break;
						default:
								//unset($feup_message);
								break;
				}
		}
}

