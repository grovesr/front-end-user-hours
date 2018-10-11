<?php
/* This file is the action handler. The appropriate function is then called based
*  on the action that's been selected by the user. The functions themselves are all
* stored either in Prepare_Data_For_Insertion.php or Update_Admin_Databases.php */

function Update_EWD_FEUPHRS_Content() {
	global $feup_message;
	if (isset($_GET['Action'])) {
		switch ($_GET['Action']) {
		case 'EWD_FEUPHRS_AdminAddUserHours':
			if(isset($_POST['User_Hours_Add']) and
				isset($_POST['ewd-feuphrs-userid']) and !empty($_POST['ewd-feuphrs-userid']) and
				isset($_POST['ewd-feuphrs-hours']) and !empty($_POST['ewd-feuphrs-hours']) and
				isset($_POST['ewd-feup-event']) and !empty($_POST['ewd-feup-event']) and
				isset($_POST['ewd-feuphrs-begin']) and !empty($_POST['ewd-feuphrs-begin']) and
				isset($_POST['ewd-feuphrs-end']) and !empty($_POST['ewd-feuphrs-end']))
			{
					$feup_message = admin_enter_hours();
			}
			break;
		case 'EWD_FEUPHRS_VerifyHours':
			$feup_message['Message_Type']='Update';
			$feup_message['Message'] = Verify_EWD_FEUPHRS_Hours($_GET['Hour_ID']);
			break;
		case 'EWD_FEUPHRS_UnVerifyHours':
			$feup_message['Message_Type']='Update';
			$feup_message['Message'] = UnVerify_EWD_FEUPHRS_Hours($_GET['Hour_ID']);
			break;
		case 'EWD_FEUPHRS_DeleteHours':
		$feup_message['Message_Type']='Update';
			$feup_message['Message'] = Delete_EWD_FEUPHRS_Hours($_GET['Hour_ID']);
			break;
		case 'EWD_FEUPHRS_VerifyAllUserHours':
		  $feup_message['Message_Type']='Update';
			$feup_message['Message'] = Verify_EWD_FEUPHRS_AllUserHours($_GET['User_ID']);
			break;
			case 'EWD_FEUPHRS_UnVerifyAllUserHours':
			  $feup_message['Message_Type']='Update';
				$feup_message['Message'] = UnVerify_EWD_FEUPHRS_AllUserHours($_GET['User_ID']);
				break;
		case 'EWD_FEUPHRS_DeleteAllUserHours':
		  $feup_message['Message_Type']='Update';
			$feup_message['Message'] = Delete_EWD_FEUPHRS_AllUserHours($_GET['User_ID']);
			break;
		case 'EWD_FEUPHRS_MassEditHours':
			if (isset($_POST['Hours_Bulk']) & isset($_POST['action'])) {
				if ($_POST['action'] == 'verify') {
					$feup_message['Message_Type']='Update';
					$feup_message['Message'] = Mass_Verify_EWD_FEUPHRS_Hours($_POST['Hours_Bulk']);
				} else if ($_POST['action'] == 'delete') {
					$feup_message['Message_Type']='Update';
					$feup_message['Message'] = Mass_Delete_EWD_FEUPHRS_Hours($_POST['Hours_Bulk']);
				}
			}
			break;
		case 'EWD_FEUPHRS_MassEditUserHours':
			if (isset($_POST['Users_Bulk']) & isset($_POST['action'])) {
				if ($_POST['action'] == 'verify') {
					$feup_message['Message_Type']='Update';
					$feup_message['Message'] = Mass_Verify_EWD_FEUPHRS_User_Hours($_POST['Users_Bulk']);
				} else if ($_POST['action'] == 'delete') {
					$feup_message['Message_Type']='Update';
					$feup_message['Message'] = Mass_Delete_EWD_FEUPHRS_User_Hours($_POST['Users_Bulk']);
				}
			}
			break;
		case 'EWD_FEUPHRS_AddUsersHoursFromSpreadsheet':
			if (isset($_POST['Hours_Import'])) {
				$feup_message = EWD_FEUPHRS_Import_Users_Hours_From_Excel();
			}
		case 'EWD_FEUPHRS_AddUsersFromSpreadsheet':
		    if (isset($_POST['Users_Import'])) {
		        $feup_message = EWD_FEUPHRS_Import_Users_From_Excel();
		    }
		case 'EWD_FEUPHRS_ExportUsersHoursSpreadsheetTemplate':
			if (isset($_POST['User_Hours_Template'])) {
				$feup_message['Message_Type']='Update';
				$feup_message['Message'] = EWD_FEUPHRS_Export_Users_Template_To_Excel();
			}
			break;
			case 'EWD_FEUPHRS_ExportUsersHoursSpreadsheet':
				if (isset($_POST['User_Hours_Export'])) {
					$feup_message['Message_Type']='Update';
					$feup_message['Message'] = EWD_FEUPHRS_Export_Users_Hours_To_Excel();
				}
				break;
		default:
				unset($feup_message);
				break;
		}
	}
}
