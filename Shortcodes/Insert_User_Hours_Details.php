<?php 

/* Show the user hours formatted for the  */

function User_Hours_Details($atts) {

	$Custom_CSS = get_option("EWD_FEUP_Custom_CSS");

	$UserCookie = CheckLoginCookie();



	$defaults = array();

	

	// Get the attributes passed by the shortcode, and store them in new variables for processing

	extract( shortcode_atts( $defaults, $atts));



	$user_hours = Get_Users_Hours_Details($UserCookie['Username']);

	

	$ret = '

	<table>

		<tr><th>Hours</th><th>Activity</th><th>Begin Date</th><th>End Date</th><th>Verified</th></tr>

		<tbody>';



	foreach ($user_hours as $row) {
		$startDate=date_create($row->Hours_Start_Date);
		
		$stopDate=date_create($row->Hours_Stop_Date);

		$ret .= "<tr><td>{$row->Hours}</td><td>";



		if ($row->Event_ID) {

			$ret .= '<a href="' . EWD_FEUPHRS_Get_Event_Link($row->Event_ID) . '">';

		}



		$ret .= $row->Event_Name;



		if ($row->Event_ID) {

			$ret .= '</a>';

		}

		$ret .= "</td><td>".date_format($startDate,'m/d/Y');
		$ret .= "</td><td>".date_format($stopDate,'m/d/Y');

		$ret .= "</td><td>" . ($row->Verified ? 'Yes' : 'No') . '</td></tr>';

	}



	$ret .= '</tbody></table>';

	

	return $ret;

}

add_shortcode("user-hours-details", "User_Hours_Details");

