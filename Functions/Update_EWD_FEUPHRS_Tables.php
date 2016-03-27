<?php
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
function Update_EWD_FEUPHRS_Tables() {
		/* Add in the required globals to be able to create the tables */
  	global $wpdb;
   	global $EWD_FEUPHRS_db_version;
	global $ewd_feup_user_hours_table_name;
   	/* Create the user hours table */
   	$sql="CREATE TABLE $ewd_feup_user_hours_table_name (
	  	Field_ID int(11) NOT NULL AUTO_INCREMENT,
	  	User_ID mediumint(9) NOT NULL,
	  	Hours int(11) NOT NULL,
	  	Verified boolean NOT NULL,
	  	Event_ID int(11) NULL,
	  	Event_Name varchar(255),
	  	UNIQUE KEY id (Field_ID)
	    )	
		DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
	dbDelta($sql);
	update_option("EWD_FEUPHRS_db_version", $EWD_FEUPHRS_db_version);
}