<?php 

/* Show the user hours formatted for the  */

function User_Hours_Add($atts) {

	global $feup_success, $user_message;



	$Custom_CSS = get_option("EWD_FEUP_Custom_CSS");

	$UserCookie = CheckLoginCookie();



	$defaults = array(

		'redirect' => '#'

	);

	

	// Get the attributes passed by the shortcode, and store them in new variables for processing

	extract( shortcode_atts( $defaults, $atts, 'user-hours-add'));



	if ($feup_success && $redirect != '#') {

		FEUPRedirect($redirect);

	}



	if ($user_message) {

		$message = $user_message['Message'];

	} else {
		$message="";
	}



	$hours = __('Hours', 'EWD_FEUP');

	$event = __('Event', 'EWD_FEUP');
	$beginDatePh=__('Begin','EWD_FEUP');
	$endDatePh=__('End','EWD_FEUP');
	$beginDate=date('m-d-Y');
	$endDate=date('m-d-Y');
	$submit_text = __('Submit', 'EWD_FEUP');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');


	$ret = <<<EOT

		$message

		<form action="" method="post" class="pure-form pure-form-aligned">

			<input type="hidden" name="ewd-feup-action" value="hours" />

			<div class="pure-control-group">

				<label class="ewd-feup-field-label">$hours</label>

				<input type="text" class="ewp-feup-text-input" maxlength=3 name="ewd-feup-hours" placeholder="$hours" />

			</div>

			<div class="pure-control-group">

				<label class="ewd-feup-field-label">$event</label>

				<input type="text" class="ewp-feup-text-input ewd-feup-event-selector" name="ewd-feup-event" placeholder="$event"/>

			</div>
			<div class="pure-control-group">

				<label class="ewd-feup-field-label">$beginDatePh</label>

				<input type="text" class="ewp-feup-text-input datepicker" id="ewd-feup-begin-date" name="ewd-feup-begin-date" placeholder="$beginDatePh"/>

			</div>
			<div class="pure-control-group">

				<label class="ewd-feup-field-label">$endDatePh</label>

				<input type="text" class="ewp-feup-text-input datepicker" id="ewd-feup-end-date"  name="ewd-feup-end-date" placeholder="$endDatePh"/>

			</div>
			<div class="pure-control-group">

				<label class="ewd-feup-field-label"></label>

				<input type='submit' class='ewd-feup-submit pure-button pure-button-primary' name='Hours_Submit' value="$submit_text">

			</div>

		</form>

EOT;


	$ret .= EWD_FEUPHRS_Event_Selector();

	

	return $ret;

}

add_shortcode("user-hours-add", "User_Hours_Add");

