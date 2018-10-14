<?php
/* Add any update or error notices to the top of the admin page */
function EWD_FEUPHRS_Error_Notices(){
    global $feuphrs_message;
		if (isset($feuphrs_message)) {
			if (is_array($feuphrs_message) and $feuphrs_message['Message_Type'] == "Update") {echo "<div class='notice notice-success is-dismissable'><p>" . $feuphrs_message['Message'] . "</p></div>";}
			if (is_array($feuphrs_message) and $feuphrs_message['Message_Type'] == "Error") {echo "<div class='notice notice-error is-dismissable'><p>" . $feuphrs_message['Message'] . "</p></div>";}
		} 
}

?>