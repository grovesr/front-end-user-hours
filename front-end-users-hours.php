<?php
/*
Plugin Name: Front End Users Hours
Description: Add tables and shortcodes to extend the functionality of the Front End Users plugin.  Specifically we are adding the ability to track volunteer hours for each user.  This functionality plugin allows us to upgrade Front End Users without losing the hours tracking functionality that we are adding. Perhaps it goes without saying that you have to have the Front End Users plugin installed and activated
before this functionality plugin will work.

== Important Notes ==
TODO:
    1) enable the user to edit their hour entries in the Hours detail table
    2) enable automatic e-mails to ulstercorps when users have entered hours so we know when we need to verify the hours
    3) DONE: fix the confirmation and password e-mails so that the return address isn't ulsterc3@box7... but is more like ulstercorps@ulstercorps.org
    4) add user's previous hours activity names to the top of the events list pull-down for quick re-entry of activity hours

Prerequisite: https://wordpress.org/plugins/front-end-only-users/
This functionality plugin was developed with Front End Users v1.26
Version: 0.3
License: GPL
Author: Rob Groves
Author URI: yoururl
*/
global $wpdb, $EWD_FEUPHRS_db_version, $ewd_feup_user_hours_table_name, $feup_message;
$EWD_FEUPHRS_db_version = "0.3.0";
$ewd_feup_user_hours_table_name = $wpdb->prefix . "EWD_FEUP_User_Hours";

define( 'EWD_FEUPHRS_CD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EWD_FEUPHRS_CD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/* when plugin is activated*/
register_activation_hook(__FILE__,'Install_EWD_FEUPHRS');
/* included in file*/

/* When plugin is deactivated*/
register_deactivation_hook( __FILE__, 'Remove_EWD_FEUPHRS' );
/* included from this file */

if ( is_admin() ){
	add_action('admin_init', 'Add_EWD_FEUPHRS_Scripts');
}

function Add_EWD_FEUPHRS_Scripts() {
	if (isset($_GET['page']) && $_GET['page'] == 'EWD-FEUPHRS-options') {
		$url_one = plugins_url("front-end-user-hours/js/Admin.js");
		// wp_register_script('password-strength', $url_five, array('jquery'));
		wp_enqueue_script('PageSwitch', $url_one, array('jquery'));
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	}
}

// Process the forms posted by users from the front-end of the plugin
if (isset($_POST['ewd-feuphrs-action'])) {
	add_action('init', 'Process_EWD_FEUPHRS_Front_End_Forms');
}

function Remove_EWD_FEUPHRS() {
  	/* Deletes the database field */
	delete_option('EWD_FEUPHRS_db_version');
}

/* Creates the admin menu for the contests plugin */
if ( is_admin() ){
	add_action('admin_menu', 'EWD_FEUPHRS_Plugin_Menu');
	add_action('init', 'Update_EWD_FEUPHRS_Content');
}

function EWD_FEUPHRS_date_scripts() {
#	wp_enqueue_style(
#			'date-picker-style',
#			plugins_url( '/js/http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css' , __FILE__ ));
#	wp_enqueue_script(
#			'jquery-1.9.1',
#			'http://code.jquery.com/jquery-1.9.1.js');
#	wp_enqueue_script(
#			'jquery-ui.js',
#			'http://code.jquery.com/ui/1.11.0/jquery-ui.js');
	wp_enqueue_script(
			'date-picker-js',
			plugins_url( '/js/EWD_FEUPHRS_date_picker.js' , __FILE__ ));

}

add_action( 'wp_enqueue_scripts', 'EWD_FEUPHRS_date_scripts' );

/*
// Process the forms posted by users from the front-end of the plugin
if (isset($_POST['ewd-feuphrs-action'])) {
	add_action('init', 'Process_EWD_FEUPHRS_Front_End_Forms');
}
*/

/* Admin Page setup */
function EWD_FEUPHRS_Plugin_Menu() {
	add_menu_page('Front End User Hours Plugin', 'Front-End Users Hours', 'administrator','EWD-FEUPHRS-options', 'EWD_FEUPHRS_Output_Options',null , '51');
}

add_action('activated_plugin','save_feuphrs_error');
function save_feuphrs_error(){
	update_option('plugin_error',  ob_get_contents());
}
include "Functions/Events_Functions.php";
include "Functions/Install_EWD_FEUPHRS.php";
include "Functions/EWD_FEUPHRS_Output_Options.php";
include "Functions/Process_Front_End_Hours_Forms.php";
include "Functions/Update_Admin_Hours_Databases.php";
include "Functions/Update_EWD_FEUPHRS_Content.php";
include "Functions/Update_EWD_FEUPHRS_Tables.php";
include "Functions/EWD_FEUP_Hours_Querys.php";
include "Functions/PublicFunctionsHours.php";
include "Shortcodes/Insert_User_Hours_Add.php";
include "Shortcodes/Insert_User_Hours_Details.php";
include "Shortcodes/Insert_User_Hours_Summary.php";
include "Shortcodes/Insert_User_First_Last_If_Logged_In.php";


// Updates the UPCP database when required
if (get_option('EWD_FEUPHRS_DB_Version') != $EWD_FEUPHRS_db_version) {
	Update_EWD_FEUPHRS_Tables();
}
