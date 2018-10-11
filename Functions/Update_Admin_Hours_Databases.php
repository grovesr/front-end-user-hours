<?php
function admin_enter_hours() {
	if (!is_numeric($_POST['ewd-feuphrs-hours']) ||
		$_POST['ewd-feuphrs-hours'] <= 0) {
		return array('Message_Type' => 'Error',
								 'Message' => 'Hours must be a positive number');
	}
	if (empty($_POST['ewd-feup-event'])) {
		return array('Message_Type' => 'Error',
								 'Message' => 'An event must be specified');
	}
	if (empty($_POST['ewd-feuphrs-end']) || empty($_POST['ewd-feuphrs-begin'])) {
		return array('Message_Type' => 'Error',
								 'Message' => 'Begin and end dates must be specified');
	}
	$beginDate=strtotime($_POST['ewd-feuphrs-begin']);
	$endDate=strtotime($_POST['ewd-feuphrs-end']);
	if ($beginDate >  $endDate) {
		return array('Message_Type' => 'Error',
								 'Message' => 'End date must be less than or equal to start date');
	}
	$insert = array(
		'User_ID' => esc_sql($_POST['ewd-feuphrs-userid']),
		'Hours' => esc_sql($_POST['ewd-feuphrs-hours']),
		'Event_Name' => esc_sql($_POST['ewd-feup-event']),
		'Hours_Start_Date' => Date('Y-m-d', $beginDate),
		'Hours_Stop_Date' => Date('Y-m-d', $endDate),
		'Verified' => 1,
		'Event_ID' => empty($_POST['ewd-feup-event-id']) ? null : esc_sql($_POST['ewd-feup-event-id']));
	return Add_EWD_FEUPHRS_User_Hours($insert);
}

function Add_EWD_FEUPHRS_User_Hours($hours) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->show_errors();
	$wpdb->insert($ewd_feup_user_hours_table_name, $hours);
	return array('Message_Type' => 'Update',
								'Message' => "Hours have been added successfully edited.");
}

function Verify_EWD_FEUPHRS_Hours($Field_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->update($ewd_feup_user_hours_table_name, array('Verified' => 1),array( 'Field_ID' => $Field_ID));
	$update = __("Hours have been successfully verified.", 'EWD_FEUP');
	return $update;
}

function UnVerify_EWD_FEUPHRS_Hours($Field_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->update($ewd_feup_user_hours_table_name, array('Verified' => 0),array( 'Field_ID' => $Field_ID));
	$update = __("Hours have been successfully un-verified.", 'EWD_FEUP');
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
	return __("Hours have been successfully deleted.", 'EWD_FEUP');
}

function Delete_EWD_FEUPHRS_Hours($Field_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->delete($ewd_feup_user_hours_table_name, array( 'Field_ID' => $Field_ID));
	$update = __("Hours have been successfully deleted.", 'EWD_FEUP');
	return $update;
}

function Verify_EWD_FEUPHRS_AllUserHours($User_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$Sql =  "UPDATE $ewd_feup_user_hours_table_name SET Verified=1 WHERE User_ID=$User_ID";
	$wpdb->query($Sql);
	$update = __("Hours have been successfully verified.", 'EWD_FEUP');
	return $update;
}

function UnVerify_EWD_FEUPHRS_AllUserHours($User_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$Sql =  "UPDATE $ewd_feup_user_hours_table_name SET Verified=0 WHERE User_ID=$User_ID";
	$wpdb->query($Sql);
	$update = __("Hours have been successfully un-verified.", 'EWD_FEUP');
	return $update;
}

function Delete_EWD_FEUPHRS_AllUserHours($User_ID) {
	global $wpdb;
	global $ewd_feup_user_hours_table_name;
	$wpdb->delete($ewd_feup_user_hours_table_name, array( 'User_ID' => $User_ID));
	$update = __("Hours have been successfully deleted.", 'EWD_FEUP');
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
		return __("Hours have been successfully deleted.", 'EWD_FEUP');
}

function Add_FEUPHRS_Users_Hours_From_Spreadsheet($Excel_File_Name) {
	global $wpdb;
	global $ewd_feup_user_table_name;
	global $ewd_feup_user_hours_table_name;
	$events_table = 'wp_em_events';

	if (!wp_verify_nonce($_POST['_wpnonce'])) {
		$update = __('There has been a validation error.');
		$user_update = array("Message_Type" => "Error", "Message" => $update);
		return $user_update;
	}

	$Excel_URL = '../wp-content/plugins/front-end-user-hours/user-sheets/' . $Excel_File_Name;

	// Uses the PHPExcel class to simplify the file parsing process
	include_once('../wp-content/plugins/front-end-only-users/PHPExcel/Classes/PHPExcel.php');

	// Build the workbook object out of the uploaded spredsheet
	$inputFileType = PHPExcel_IOFactory::identify($Excel_URL);
  $objReader = PHPExcel_IOFactory::createReader($inputFileType);
	$objWorkBook = $objReader->load($Excel_URL);

	// Create a worksheet object out of the product sheet in the workbook
	$hoursIndex = $objWorkBook->getIndex($objWorkBook->getSheetByName('Hours Import'));
	if($hoursIndex >= 0) {
		$objWorkBook->setActiveSheetIndex($hoursIndex);
	} else {
		$update =  __('Spreadsheet missing "Hours Import" tab.');
		$user_update = array("Message_Type" => "Error", "Message" => $update);
		return $user_update;
	}
	$sheet = $objWorkBook->getActiveSheet();

	//List of fields that can be accepted via upload
	$Allowed_Fields = array ("Username" => "Username",
							 "Event Name" => "Event_Name",
							 "Start Date" => "Hours_Start_Date",
							 "End Date" => "Hours_Stop_Date",
							 "Hours" => "Hours");

	// Get column names
	$highestColumn = $sheet->getHighestDataColumn(1);
	$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	for ($column = 0; $column < $highestColumnIndex; $column++) {
		$Titles[$column] = trim($sheet->getCellByColumnAndRow($column, 1)->getValue());
	}
	if(count($Titles) < count($Allowed_Fields)) {
		// missing one or more columns
		$requiredColumns = implode(', ', array_keys($Allowed_Fields));
		$update = __("You are missing one or more required columns. Make sure you have these columns ($requiredColumns).", 'EWD_FEUP');
		$user_update = array("Message_Type" => "Error", "Message" => $update);
		return $user_update;
	}
	// Make sure all columns are acceptable based on the acceptable fields above
	foreach ($Titles as $key => $Title) {
		if ($Title != "" and !array_key_exists($Title, $Allowed_Fields)) {
			$update =  __("You have a column which is not recognized: ", 'EWD_FEUP') .
								$Title . __(". </br>Please start with the template spreadsheet.", 'EWD_FEUP');
			$user_update = array("Message_Type" => "Error", "Message" => $update);
			return $user_update;
		}
		if ($Title == "") {
			$update = __("You have a blank column that has been edited.</br>Please delete that column and re-upload your spreadsheet.", 'EWD_FEUP');
			$user_update = array("Message_Type" => "Error", "Message" => $update);
			return $user_update;
		}
	}
	// Put the spreadsheet data into a multi-dimensional array to facilitate processing
	$highestRow = $sheet->getHighestRow();
	for ($row = 2; $row <= $highestRow; $row++) {
		for ($column = 0; $column < $highestColumnIndex; $column++) {
			$Data[$row][$column] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
		}
	}
	$sql = "SELECT LOWER($events_table.event_name) AS event_name, $events_table.event_id, $events_table.event_start_date
	FROM $events_table ";
	$sql .= "INNER JOIN ";
	$sql .= "(SELECT LOWER(event_name) AS event_name, MAX(event_start_date) AS maxdate FROM wp_em_events GROUP BY LOWER(event_name)) maxd
	ON ";
	$sql .= "($events_table.event_name=maxd.event_name AND $events_table.event_start_date=maxd.maxdate)";
	$eventsFromDb = $wpdb->get_results($sql);
	$Events = array();
	// Create an array of the events currently in the Events Manager database,
	// with Event_name as the key and Level_ID as the value
	foreach ($eventsFromDb as $event) {
		$Events[trim($event->event_name)] = $event->event_id;
	}
	// index to get certain info out of $Data since we don't know the column order
	$usernameIndex = array_search('Username', $Titles);
	$hoursIndex = array_search('Hours', $Titles);
	$eventIndex = array_search('Event Name', $Titles);
	$startDateIndex = array_search('Start Date', $Titles);
	$stopDateIndex = array_search('End Date', $Titles);
	// $wpdb->show_errors();
	foreach ($Data as $User) {
		if ($User[$hoursIndex] == "0" or empty($User[$hoursIndex])) {
			// skip this entry if the hours are 0 or empty
			continue;
		}
		// Create an array of the values that are being inserted for each user
		if(array_key_exists(strtolower(trim($User[$eventIndex])), $Events)) {
			$eventId = "'".$Events[strtolower(trim($User[$eventIndex]))]."'";
		} else {
			$eventId = 'NULL';
		}
		$username = trim($User[$usernameIndex]);
		$sql = "SELECT DISTINCT User_ID from $ewd_feup_user_table_name WHERE Username = '$username'";
		$userid = $wpdb->get_var($sql);
		if($userid == "") {
		    // user doesn't exist skip
		    continue;
		}
		$startDate = date_create_from_format('m-d-Y', trim($User[$startDateIndex]));
		if ($startDate === FALSE) {
			$update = __(trim($User[$startDateIndex]) . "Inavlid date format for Start Date in row for user '$username'.  Should be in mm-dd-yyyy format.", 'EWD_FEUP');
			$user_update = array("Message_Type" => "Error", "Message" => $update);
			return $user_update;
		}
		$endDate = date_create_from_format('m-d-Y', trim($User[$stopDateIndex]));
		if ($endDate === FALSE) {
			// use the start date if no end date specified, or it is incorrectly formatted
			$endDate = $startDate;
		}
		$User[$startDateIndex] = $startDate->format('Y-m-d');
		$User[$stopDateIndex] = $endDate->format('Y-m-d');
		$values = [];
		$colNames = [];
		foreach ($User as $colIndex => $value) {
			if ($Titles[$colIndex] != 'Username') {
				$values[$colIndex] = esc_sql($value);
				$colNames[$colIndex] = $Allowed_Fields[$Titles[$colIndex]];
			}
		}
		$colNames[] = "User_ID";
		$values[] = $userid;
		$colNamesString = implode(",", $colNames);
		$valuesString = implode("','", $values);
		$sql =  "INSERT INTO $ewd_feup_user_hours_table_name ";
		$sql .= "(" . $colNamesString . ", Event_ID, Verified) ";
		$sql .= "VALUES ('" . $valuesString . "', $eventId, '1')";
		$wpdb->query($sql);
		unset($values);
		unset($valuesString);
	}
	$update = __("Hours added successfully.", 'EWD_FEUP');
	$user_update = array("Message_Type" => "Update", "Message" => $update);
	return $user_update;
}

function Add_FEUPHRS_Users_From_Spreadsheet($Excel_File_Name) {
    // This function replaces Add_FEUP_Users_From_Spreadsheet from the FEUP plugin
    // This version is a bit more robust and doesn't add users more than once
    global $wpdb;
    global $ewd_feup_user_table_name;
    global $ewd_feup_user_fields_table_name;
    global $ewd_feup_levels_table_name;
    global $ewd_feup_fields_table_name;
    global $EWD_FEUP_Full_Version;
    
    $Sign_Up_Email = get_option("EWD_FEUP_Sign_Up_Email");
    $Use_Crypt = get_option("EWD_FEUP_Use_Crypt");
    $Username_Is_Email = get_option("EWD_FEUP_Username_Is_Email");
    $Create_WordPress_Users = get_option("EWD_FEUP_Create_WordPress_Users");
    
    if (!wp_verify_nonce($_POST['_wpnonce'])) {return __("There has been a validation error.", 'front-end-only-users');}
    
    $Excel_URL = '../wp-content/plugins/front-end-user-hours/user-sheets/' . $Excel_File_Name;
    
    // Uses the PHPExcel class to simplify the file parsing process
    include_once('../wp-content/plugins/front-end-only-users/PHPExcel/Classes/PHPExcel.php');
    
    // Build the workbook object out of the uploaded spredsheet
    $inputFileType = PHPExcel_IOFactory::identify($Excel_URL);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objWorkBook = $objReader->load($Excel_URL);
    
    // Create a worksheet object out of the product sheet in the workbook
    $sheet = $objWorkBook->getActiveSheet();
    
    //List of fields that can be accepted via upload
    $Allowed_Fields = array ("Username" => "Username", "Password" => "User_Password", "Level" => "Level_Name", "Email Confirmed" => "User_Email_Confirmed", "Admin Approved" => "User_Admin_Approved");
    $Custom_Fields_From_DB = $wpdb->get_results("SELECT Field_ID, Field_Name, Field_Options, Field_Type FROM $ewd_feup_fields_table_name");
    if (is_array($Custom_Fields_From_DB)) {
        foreach ($Custom_Fields_From_DB as $Custom_Field_From_DB) {
            $Allowable_Custom_Fields[$Custom_Field_From_DB->Field_Name] = $Custom_Field_From_DB->Field_Name;
            $Field_IDs[$Custom_Field_From_DB->Field_Name] = $Custom_Field_From_DB->Field_ID;
        }
    }
    
    // Get column names
    $highestColumn = $sheet->getHighestDataColumn(1);
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    for ($column = 0; $column < $highestColumnIndex; $column++) {
        $Titles[$column] = trim($sheet->getCellByColumnAndRow($column, 1)->getValue());
    }
    
    // Make sure all columns are acceptable based on the acceptable fields above
    foreach ($Titles as $key => $Title) {
        if ($Title != "" and !array_key_exists($Title, $Allowed_Fields) and !array_key_exists($Title, $Allowable_Custom_Fields)) {
            $Error = __("You have a column which is not recognized: ", 'front-end-only-users') . $Title . __(". <br>Please make sure that the column names match the user field labels exactly.", 'front-end-only-users');
            $user_update = array("Message_Type" => "Error", "Message" => $Error);
            return $user_update;
        }
        if ($Title == "") {
            $Error = __("You have a blank column that has been edited.<br>Please delete that column and re-upload your spreadsheet.", 'front-end-only-users');
            $user_update = array("Message_Type" => "Error", "Message" => $Error);
            return $user_update;
        }
        if (is_array($Allowable_Custom_Fields)) {
            if (array_key_exists($Title, $Allowable_Custom_Fields)) {
                $Custom_Fields[$key] = $Title;
                unset($Titles[$key]);
            }
        }
    }
    if (!is_array($Custom_Fields)) {$Custom_Fields = array();}
    
    // Put the spreadsheet data into a multi-dimensional array to facilitate processing
    $highestRow = $sheet->getHighestRow();
    for ($row = 2; $row <= $highestRow; $row++) {
        for ($column = 0; $column < $highestColumnIndex; $column++) {
            $Data[$row][$column] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
        }
    }
    
    // Create an array of the levels currently in the FEUP database,
    // with Level_Name as the key and Level_ID as the value
    $Levels_From_DB = $wpdb->get_results("SELECT * FROM $ewd_feup_levels_table_name");
    foreach ($Levels_From_DB as $Level) {
        $Levels[$Level->Level_Name] = $Level->Level_ID;
    }
    
    // Creates an array of the field names which are going to be inserted into the database
    // and then turns that array into a string so that it can be used in the query
    for ($column = 0; $column < $highestColumnIndex; $column++) {
        if ($Allowed_Fields[$Titles[$column]] != "Level_Name" and !array_key_exists($column, $Custom_Fields)) {$Fields[] = $Allowed_Fields[$Titles[$column]];}
        if ($Allowed_Fields[$Titles[$column]] == "Level_Name") {$Level_Column = $column; $Fields[] = "Level_ID";}
        if ($Allowed_Fields[$Titles[$column]] == "User_Password") {$Password_Column = $column;}
        if ($Allowed_Fields[$Titles[$column]] == "Username") {$Username_Column = $column;}
    }
    $FieldsString = implode(",", $Fields);
    
    $ShowStatus = "Show";
    $Today = date("Y-m-d H:i:s");
    $wpdb->show_errors();
    
    // Create the query to insert the users one at a time into the database and then run it
    foreach ($Data as $User) {
        
        // Create an array of the values that are being inserted for each user
        foreach ($User as $Col_Index => $Value) {
            if ((!isset($Password_Column) or $Password_Column != $Col_Index) and (!isset($Level_Column) or $Level_Column != $Col_Index) and !array_key_exists($Col_Index, $Custom_Fields)) {$Values[] = esc_sql($Value);}
            if (isset($Level_Column) and $Level_Column == $Col_Index) {
                $Values[] = $Levels[$Value];
            }
            if (isset($Password_Column) and $Password_Column == $Col_Index) {
                if($Use_Crypt == "Yes") {
                    $Values[] = Generate_Password($Value);
                } else {
                    $Values[] = sha1(md5($Value.$Salt));
                }
                $Password = $Value;
            }
            if (isset($Username_Column) and $Username_Column == $Col_Index) {
                $Username = $Value;
            }
            if (array_key_exists($Col_Index, $Custom_Fields)) {
                $Custom_Fields_To_Insert[$Custom_Fields[$Col_Index]] = $Value;
            }
        }
        $User_Exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) from $ewd_feup_user_table_name WHERE Username = '".esc_sql($Username)."'")
            );
        if ($User_Exists == "0") {
            $ValuesString = implode("','", $Values);
            $wpdb->query(
                $wpdb->prepare("INSERT INTO $ewd_feup_user_table_name (" . $FieldsString . ", User_Date_Created) VALUES ('" . $ValuesString . "','%s')", $Today)
                );
            
            $User_ID = $wpdb->insert_id;
            if ($Sign_Up_Email == "Yes") {EWD_FEUP_Send_Email(array(), array(), $User_ID);}
            
            if ($Create_WordPress_Users == "Yes" and $Username_Is_Email == "Yes") {$WP_ID = EWD_FEUP_Add_WP_User($User_ID, array('Username' => $Username, 'Password' => $Password));}
            
            if (is_array($Custom_Fields_To_Insert)) {
                foreach ($Custom_Fields_To_Insert as $Field => $Value) {
                    $Trimmed_Field = trim($Field);
                    $Field_ID = $Field_IDs[$Trimmed_Field];
                    $wpdb->query($wpdb->prepare("INSERT INTO $ewd_feup_user_fields_table_name (Field_ID, User_ID, Field_Name, Field_Value, User_Field_Date_Created) VALUES (%d, %d, %s, %s, %s)", $Field_ID, $User_ID, $Trimmed_Field, $Value, $Today));
                }
            }
            unset($ValuesString);
            unset($Custom_Fields_To_Insert);
        }
        unset($Values);
        unset($User_ID);
        
    }
    
    $message = __("Users added successfully.", 'front-end-only-users');
    $user_update = array("Message_Type" => "Update", "Message" => $message);
    return $user_update;
}
