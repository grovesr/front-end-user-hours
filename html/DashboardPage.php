<!-- List of the users with logged hours -->
<?php
	global $ewd_feup_user_table_name, $ewd_feup_user_hours_table_name, $wpdb;
	if (isset($_GET['Page'])) {
		$Page = $_GET['Page'];
	}
	else {
		$Page = 1;
	}
	$Sql=EWD_FEUP_Query_User_Hours_Page($_GET,
																			$ewd_feup_user_table_name,
																			$ewd_feup_user_hours_table_name);
	$userRows = $wpdb->get_results($Sql);
	$num_rows = $wpdb->num_rows;
	$Sql=EWD_FEUP_Query_User_Hours_Count($ewd_feup_user_hours_table_name);
	$Number_of_Pages = ceil($wpdb->get_var($Sql)/20);
	$userVerifiedHours=array();
	$userUnverifiedHours=array();
	if ($userRows) {
		foreach ($userRows as $User) {
			$Sql=EWD_FEUP_Sum_Verified_Hours($ewd_feup_user_hours_table_name,$User->User_ID);
			$verifiedHours=$wpdb->get_row($Sql);
			if (trim($verifiedHours->Hours) !== '') {
				$hours=trim($verifiedHours->Hours);
			} else {
				$hours='0';
			}
			$userVerifiedHours[strval($User->User_ID)]=$hours;
			$Sql=EWD_FEUP_Sum_Unverified_Hours($ewd_feup_user_hours_table_name,$User->User_ID);
			$unverifiedHours=$wpdb->get_row($Sql);
			if (trim($unverifiedHours->Hours) !== '') {
				$hours=trim($unverifiedHours->Hours);
			} else {
				$hours='0';
			}
			$userUnverifiedHours[strval($User->User_ID)]=$hours;
		}
	};
	$Current_Page_With_Order_By = "admin.php?page=EWD-FEUPHRS-options&DisplayPage=Dashboard";
	if (isset($_GET['OrderBy'])) {
		$Current_Page_With_Order_By .= "&OrderBy=" .$_GET['OrderBy'] . "&Order=" . $_GET['Order'];
	}
function dashboard_header($Page)
{?>
	<tr>
		<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
			<input type="checkbox" />
		</th>
		<th scope='col' id='event-name' class='manage-column column-name sortable desc'  style="">
			<?php if (!isset($_GET['OrderBy']) or
								(isset($_GET['OrderBy']) and $_GET['OrderBy'] == "Username" and $_GET['Order'] == "ASC")) {
							echo "<a href='admin.php?page=EWD-FEUPHRS-options&DisplayPage=Dashboard&OrderBy=Username&Order=DESC&Page=$Page'>";
						} else {
							echo "<a href='admin.php?page=EWD-FEUPHRS-options&DisplayPage=Dashboard&OrderBy=Username&Order=ASC&Page=$Page'>";
						} ?>
				<span><?php _e("Username", 'EWD_FEUP') ?></span>
				<span class="sorting-indicator"></span>
			</a>
		</th>
		<th scope='col' id='hours' class='manage-column column-type sortable desc'  style="">
				<span><?php _e("Verified Hours", 'EWD_FEUP') ?></span>
		</th>
		<th scope='col' id='hours' class='manage-column column-type sortable desc'  style="">
				<span><?php _e("Unverified Hours", 'EWD_FEUP') ?></span>
		</th>
	</tr>
	<?php
}?>

<div id="col-right">
<div class="col-wrap">
<?php echo get_option('plugin_error'); ?>
<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>
<form action="admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_MassEditUserHours" method="post">
<?php  table_nav($Number_of_Pages, $Page, $Current_Page_With_Order_By, TRUE);?>
<table class="wp-list-table widefat fixed tags sorttable" cellspacing="0">
	<thead>
		<?php dashboard_header($Page); ?>
	</thead>
	<tfoot>
		<?php dashboard_header($Page); ?>
	</tfoot>
	<tbody id="the-list" class='list:tag'>
		<?php
			if ($userRows) {
	  			foreach ($userRows as $User) {
					echo "<tr id='User" . $User->Username ."'>";
					echo "<th scope='row' class='check-column'>";
					echo "<input type='checkbox' name='Users_Bulk[]' value='" . $User->User_ID ."' />";
					echo "</th>";
					echo "<td class='name column-name'>";
					echo "<strong>";
					echo "<a class='row-title' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_User_Hours&DisplayPage=User&User_ID=" . $User->User_ID ."' title='Edit " . $User->Username . " Hours'>" . $User->Username . "</a></strong>";
					echo "<br />";
					echo "<div class='row-actions'>";
					echo "<span class='delete'>";
					echo "<a class='delete-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_DeleteAllUserHours&DisplayPage=Dashboard&User_ID=" . $User->User_ID ."'>" . __("Delete All", 'EWD_FEUP') . "</a>";
		 			echo "</span>";
					echo "&nbsp;(<span class='verify'>";
					echo "<a class='verify-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_VerifyAllUserHours&DisplayPage=Dashboard&User_ID=" . $User->User_ID ."'>" . __("Verify", 'EWD_FEUP') . "</a>";
		 			echo "</span>";
					echo "&nbsp;|&nbsp;<span class='un-verify'>";
					echo "<a class='un-verify-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_UnVerifyAllUserHours&DisplayPage=Dashboard&User_ID=" . $User->User_ID ."'>" . __("Un-Verify", 'EWD_FEUP') . "</a>";
		 			echo ")</span>";
					echo "</div>";
					echo "<div class='hidden' id='inline_" . $User->User_ID ."'>";
					echo "<div class='name'>" . $User->Username . "</div>";
					echo "</div>";
					echo "</td>";
					echo "<td class='hours column-hours'>" . $userVerifiedHours[strval($User->User_ID)] . "</td>";
					echo "<td class='hours column-hours'>" . $userUnverifiedHours[strval($User->User_ID)] . "</td>";
					echo "</tr>";
				}
			}
		?>
	</tbody>
</table>
<div class="tablenav bottom">
	<?php table_nav($Number_of_Pages, $Page, $Current_Page_With_Order_By);?>
	<br class="clear" />
</div>
</form>

<br class="clear" />
</div>
</div>
<div id="col-left">
	<div class="col-wrap">
		<div class="form-wrap">
			<h3><?php _e("Export Users Hours Spreadsheet", 'EWD_FEUP') ?></h3>
			<div class="wrap">
				<form id="EWD_FEUPHRS_ExportUsersHoursSpreadsheet" method="post" action="admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_ExportUsersHoursSpreadsheet&DisplayPage=Dashboard" class="validate" enctype="multipart/form-data">
					<?php wp_nonce_field(); ?>
					<div class="form-field form-required">
							<p><?php _e("Export a spreadsheet of all user's hours entries.", 'EWD_FEUP') ?></p>
					</div>
					<p class="submit"><input type="submit" name="User_Hours_Export" id="User_Hours_Export" class="button-primary" value="<?php _e('Export Users Hours', 'EWD_FEUP') ?>"  /></p>
				</form>
			</div>
			<hr>
			<h3><?php _e("Create Users Hours Spreadsheet Template", 'EWD_FEUP') ?></h3>
			<div class="wrap">
				<form id="EWD_FEUPHRS_ExportUsersHoursSpreadsheetTemplate" method="post" action="admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_ExportUsersHoursSpreadsheetTemplate&DisplayPage=Dashboard" class="validate" enctype="multipart/form-data">
					<?php wp_nonce_field(); ?>
					<div class="form-field form-required">
							<p><?php _e("Create a spreadsheet template to use when importing users hours in bulk.", 'EWD_FEUP') ?></p>
					</div>
					<p class="submit"><input type="submit" name="User_Hours_Template" id="User_Hours_Template" class="button-primary" value="<?php _e('Create Template', 'EWD_FEUP') ?>"  /></p>
				</form>
			</div>
			<hr>
			<h3><?php _e("Add Users Hours from Spreadsheet", 'EWD_FEUP') ?></h3>
			<div class="wrap">
				<form id="addtag" method="post" action="admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_AddUsersHoursFromSpreadsheet&DisplayPage=Dashboard" class="validate" enctype="multipart/form-data">
					<?php wp_nonce_field(); ?>
					<div class="form-field form-required">
							<input name="Users_Hours_Spreadsheet" id="Users_Hours_Spreadsheet" type="file" value=""/>
							<p><?php _e("The spreadsheet containing all of the users hours you wish to add. Use the option above to create template with all the necessary columns.", 'EWD_FEUP') ?></p>
					</div>
					<p class="submit"><input type="submit" name="Hours_Import" id="submit" class="button-primary" value="<?php _e('Add Users Hours', 'EWD_FEUP') ?>"  /></p>
				</form>
			</div>
		</div>
	</div>
</div>
