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

function EWD_FEUPHRS_Export_Users_Hours_To_Excel() {
	global $wpdb;
	global $ewd_feup_user_table_name, $ewd_feup_user_hours_table_name;
	include_once('../wp-content/plugins/front-end-only-users/PHPExcel/Classes/PHPExcel.php');
	// Instantiate a new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$sheet = new PHPExcel_Worksheet($objPHPExcel, 'User Hours');
	$objPHPExcel->addSheet($sheet, 0);
	$defaultIndex = $objPHPExcel->getIndex($objPHPExcel->getSheetByName('Worksheet'));
	if($defaultIndex >= 0) {
		$objPHPExcel->removeSheetByIndex($defaultIndex);
	}
	// Print out the regular order field labels
	$sheet->setCellValue("A1", "Username");
	$sheet->setCellValue("B1", "Event");
	$sheet->setCellValue("C1", "Event ID");
	$sheet->setCellValue("D1", "Start Date");
	$sheet->setCellValue("E1", "End Date");
	$sheet->setCellValue("F1", "Hours");
	$sheet->setCellValue("G1", "Verified");



	//start while loop to get data
	$Sql = EWD_FEUP_Query_Users_Hours($ewd_feup_user_table_name, $ewd_feup_user_hours_table_name);
	$Users = $wpdb->get_results($Sql);
	$rowCount = 2;
	foreach ($Users as $User)
	{
    	$sheet->setCellValue("A" . $rowCount, $User->Username);
		$sheet->setCellValue("B" . $rowCount, $User->Event_Name);
		$sheet->setCellValue("C" . $rowCount, $User->Event_ID);
		$sheet->setCellValue("D" . $rowCount, $User->Hours_Start_Date);
		$sheet->setCellValue("E" . $rowCount, $User->Hours_Stop_Date);
		$sheet->setCellValue("F" . $rowCount, $User->Hours);
		$sheet->setCellValue("G" . $rowCount, $User->Verified);
    	$rowCount++;
	}

	// Redirect output to a client�s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="FEUP_User_Hours_Export.xls"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	return __('exported users hours', 'EWD_FEUP');
}

function EWD_FEUPHRS_Export_Users_Template_To_Excel() {
	global $wpdb;
	global $ewd_feup_user_table_name;

	include_once('../wp-content/plugins/front-end-only-users/PHPExcel/Classes/PHPExcel.php');

	// Instantiate a new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$instructionInxdex = 0;
	$hoursIndex = 1;
	Add_Help_To_Import_Spreadsheet($objPHPExcel, $instructionIndex);
	$importSheet = new PHPExcel_Worksheet($objPHPExcel, 'Hours Import');
	$objPHPExcel->addSheet($importSheet, $hoursIndex);
	$defaultIndex = $objPHPExcel->getIndex($objPHPExcel->getSheetByName('Worksheet'));
	if($defaultIndex >= 0) {
		$objPHPExcel->removeSheetByIndex($defaultIndex);
	}
	// Print out the regular order field labels
	$importSheet->setCellValue("A1", "Username");
	$importSheet->setCellValue("B1", "Event");
	$importSheet->setCellValue("C1", "Start Date");
	$importSheet->setCellValue("D1", "End Date");
	$importSheet->setCellValue("E1", "Hours");
	$importSheet->setCellValue("F1", "Event");
	$importSheet->setCellValue("G1", "Start Date");
	$importSheet->setCellValue("H1", "End Date");
	$importSheet->setCellValue("I1", "Hours");

	//start while loop to get data
	$rowCount = 2;
	$Users = $wpdb->get_results("SELECT * FROM $ewd_feup_user_table_name");
	$Today = date("m-d-Y");
	foreach ($Users as $User)
	{
    	$importSheet->setCellValue("A" . $rowCount, $User->Username);
			$importSheet->setCellValue("C" . $rowCount, $Today);
			$importSheet->setCellValue("D" . $rowCount, $Today);
			$importSheet->setCellValue("E" . $rowCount, 0);
    	$rowCount++;
	}

	$objPHPExcel->setActiveSheetIndexByName('Instructions');
	$objPHPExcel->getActiveSheet()->setSelectedCell("A3");
	// Redirect output to a client�s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="FEUP_User_Hours_Template.xls"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	return __('exported template', 'EWD_FEUP');
}

function Create_Import_Instructions() {
	$str = <<<EOD
	Hours are entered in the 'Hours Import' tab.
	1) Delete all user rows except for the user rows that are to have hours imported.
		a) This is just for clarity.  If you leave unused rows and leave the hours
		   entries at 0, they will be ignored on import.
	2) If you want to import hours for multiple different events/dates for a single user,
	   copy that user's row the appropriate number of times.
    2a) It is possible to import multiple events per row by adding more Event, Start, End, Hours columns.
        See the Hours Import tab. This is useful if you have a small number of events that all users may
        have participated in.
	3) Enter the event name in each row.
		a) If you use the exact name of an event recorded in Events Manager, a link
		   will be created between the hours entered and the event.
	4) Enter the start date and possibly end date in each row.
		a) Dates should be in yyyy-mm-dd format (i.e. 2016-03-26).
		b) It's OK to have the same start and end dates.
		c) If you enter only the start date, the end date will be set to the start date.
	5) Enter the number of hours for each event/date in each row.
EOD;
return $str;
}

function Add_Help_To_Import_Spreadsheet($objPHPExcel = NULL, $instructionIndex = 0) {
	$helpSheet = new PHPExcel_Worksheet($objPHPExcel, 'Instructions');
	$objPHPExcel->addSheet($helpSheet, $instructionIndex);
	$instructions = Create_Import_Instructions();
	$helpSheet
		->setCellValue("A1", "Instructions for using the FEUP_Users_Hours_Template to import hours:");
	$helpSheet
		->getCell("A1")
		->getStyle()
		->getFont()
		->applyFromArray(
			array(
				'bold' => TRUE
			)
		);
	$helpSheet
		->getRowDimension('1')
		->setRowHeight(50);
	$helpSheet
		->getStyle("A1")
		->getAlignment()
		->setVertical('center');
	$helpSheet
		->setCellValue("A2", $instructions);
	$helpSheet
		->getStyle("A2")
		->applyFromArray(array("font" => array( "bold" => FALSE)));
	$helpSheet
		->getColumnDimension('A')
		->setAutoSize(TRUE);
	$helpSheet
		->getRowDimension('2')
		->setRowHeight(600);
	$helpSheet
		->getStyle("A2")
		->getAlignment()
		->setVertical('top');
	$helpSheet
		->getStyle("A2")
		->getAlignment()
		->setWrapText(TRUE);
}

function EWD_FEUPHRS_Import_Users_Hours_From_Excel() {
	global $wpdb;
	global $ewd_feup_user_table_name;
	include_once('../wp-content/plugins/front-end-only-users/PHPExcel/Classes/PHPExcel.php');
	// Instantiate a new PHPExcel object
	if (!is_user_logged_in()) {exit();}
	/* Test if there is an error with the uploaded spreadsheet and return that error if there is */
	if (!empty($_FILES['User_Hours_Spreadsheet']['error']) &
			$_FILES['User_Hours_Spreadsheet']['error'] != 0 ) {
		switch($_FILES['User_Hours_Spreadsheet']['error'])	{
			case '1':
				$error = __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'EWD_FEUP');
				break;
			case '2':
				$error = __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'EWD_FEUP');
				break;
			case '3':
				$error = __('The uploaded file was only partially uploaded', 'EWD_FEUP');
				break;
			case '4':
				$error = __('No file was uploaded.', 'EWD_FEUP');
				break;

			case '6':
				$error = __('Missing a temporary folder', 'EWD_FEUP');
				break;
			case '7':
				$error = __('Failed to write file to disk', 'EWD_FEUP');
				break;
			case '8':
				$error = __('File upload stopped by extension', 'EWD_FEUP');
				break;
			case '999':
			default:
				$error = __('No error code avaiable', 'EWD_FEUP');
		}
	}	elseif (empty($_FILES['Users_Hours_Spreadsheet']['tmp_name']) ||
						$_FILES['Users_Hours_Spreadsheet']['tmp_name'] == 'none') {
			/* Make sure that the file exists */
				$error = __('No file was uploaded here..', 'EWD_FEUP');
	}
	/* Check that it is a .xls or .xlsx file */
	if(!preg_match("/\.(xls.?)$/", $_FILES['Users_Hours_Spreadsheet']['name']) and
	 !preg_match("/\.(csv.?)$/", $_FILES['Users_Hours_Spreadsheet']['name'])) {
		$error = __('File must be .csv, .xls or .xlsx', 'EWD_FEUP');
	} else {
		/* Move the file and store the URL to pass it onwards*/
		//for security reason, we force to remove all uploaded file
		$target_path = ABSPATH . 'wp-content/plugins/front-end-user-hours/user-sheets/';
		$target_path = $target_path . basename( $_FILES['Users_Hours_Spreadsheet']['name']);
		if (!move_uploaded_file($_FILES['Users_Hours_Spreadsheet']['tmp_name'], $target_path)) {
		//if (!$upload = wp_upload_bits($_FILES["Item_Image"]["name"], null, file_get_contents($_FILES["Item_Image"]["tmp_name"]))) {
 			  $error .= "There was an error uploading the file, please try again!";
		}
		else {
 				$Excel_File_Name = basename( $_FILES['Users_Hours_Spreadsheet']['name']);
		}
	}

	/* Pass the data to the appropriate function in Update_Admin_Databases.php to create the users */
	if (!isset($error)) {
			$user_update = Add_FEUPHRS_Users_Hours_From_Spreadsheet($Excel_File_Name);
			return $user_update;
	}
	else {
			$output_error = array("Message_Type" => "Error", "Message" => $error);
			return $output_error;
	}
}

function EWD_FEUPHRS_Import_Users_From_Excel() {
    global $wpdb;
    global $ewd_feup_user_table_name;
    include_once('../wp-content/plugins/front-end-only-users/PHPExcel/Classes/PHPExcel.php');
    // Instantiate a new PHPExcel object
    if (!is_user_logged_in()) {exit();}
    /* Test if there is an error with the uploaded spreadsheet and return that error if there is */
    if (!empty($_FILES['Users_Spreadsheet']['error']) &
        $_FILES['Users_Spreadsheet']['error'] != 0 ) {
            switch($_FILES['Users_Spreadsheet']['error'])	{
                case '1':
                    $error = __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'EWD_FEUP');
                    break;
                case '2':
                    $error = __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'EWD_FEUP');
                    break;
                case '3':
                    $error = __('The uploaded file was only partially uploaded', 'EWD_FEUP');
                    break;
                case '4':
                    $error = __('No file was uploaded.', 'EWD_FEUP');
                    break;
                    
                case '6':
                    $error = __('Missing a temporary folder', 'EWD_FEUP');
                    break;
                case '7':
                    $error = __('Failed to write file to disk', 'EWD_FEUP');
                    break;
                case '8':
                    $error = __('File upload stopped by extension', 'EWD_FEUP');
                    break;
                case '999':
                default:
                    $error = __('No error code avaiable', 'EWD_FEUP');
            }
        }	elseif (empty($_FILES['Users_Spreadsheet']['tmp_name']) ||
            $_FILES['Users_Spreadsheet']['tmp_name'] == 'none') {
                /* Make sure that the file exists */
                $error = __('No file was uploaded here..', 'EWD_FEUP');
        }
        /* Check that it is a .xls or .xlsx file */
        if(!preg_match("/\.(xls.?)$/", $_FILES['Users_Spreadsheet']['name']) and
            !preg_match("/\.(csv.?)$/", $_FILES['Users_Spreadsheet']['name'])) {
                $error = __('File must be .csv, .xls or .xlsx', 'EWD_FEUP');
            } else {
                /* Move the file and store the URL to pass it onwards*/
                $msg .= $_FILES['Users_Spreadsheet']['name'];
                //for security reason, we force to remove all uploaded file
                $target_path = ABSPATH . 'wp-content/plugins/front-end-user-hours/user-sheets/';
                $target_path = $target_path . basename( $_FILES['Users_Spreadsheet']['name']);
                if (!move_uploaded_file($_FILES['Users_Spreadsheet']['tmp_name'], $target_path)) {
                    //if (!$upload = wp_upload_bits($_FILES["Item_Image"]["name"], null, file_get_contents($_FILES["Item_Image"]["tmp_name"]))) {
                    $error .= "There was an error uploading the file, please try again!";
                }
                else {
                    $Excel_File_Name = basename( $_FILES['Users_Spreadsheet']['name']);
                }
            }
            
            /* Pass the data to the appropriate function in Update_Admin_Databases.php to create the users */
            if (!isset($error)) {
                $user_update = Add_FEUPHRS_Users_From_Spreadsheet($Excel_File_Name);
                return $user_update;
            }
            else {
                $output_error = array("Message_Type" => "Error", "Message" => $error);
                return $output_error;
            }
}
