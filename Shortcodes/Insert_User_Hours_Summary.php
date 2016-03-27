<?php 

/* Show the user hours formatted for the  */

function User_Hours_Summary($atts) {

	$Custom_CSS = get_option("EWD_FEUP_Custom_CSS");

	$UserCookie = CheckLoginCookie();


	$defaults = array();

	

	// Get the attributes passed by the shortcode, and store them in new variables for processing

	extract( shortcode_atts( $defaults, $atts));



	list($verifiedHours, $unverifiedHours) = Get_User_Hours_Summary($UserCookie['Username']);



	$ret = "Verified hours: $verifiedHours <br>Unverified hours: $unverifiedHours";

	

	return $ret;

}

add_shortcode("user-hours-summary", "User_Hours_Summary");

