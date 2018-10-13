<?php
/* Add any update or error notices to the top of the admin page */
function EWD_FEUPHRS_Error_Notices(){
    global $feuphrs_message;
		if (isset($feuphrs_message)) {
			if (is_array($feuphrs_message) and $feuphrs_message['Message_Type'] == "Update") {echo "<div class='updated'><p>" . $feuphrs_message['Message'] . "</p></div>";}
			if (is_array($feuphrs_message) and $feuphrs_message['Message_Type'] == "Error") {echo "<div class='error'><p>" . $feuphrs_message['Message'] . "</p></div>";}
		} 
}

?>