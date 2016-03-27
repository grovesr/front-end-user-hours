<!-- List of the users with logged hours -->
<div id="col-right">
<div class="col-wrap">
<?php echo get_option('plugin_error'); ?>
<?php wp_nonce_field(); ?>
<?php wp_referer_field(); ?>

<?php 
	global $ewd_feup_user_table_name, $ewd_feup_user_hours_table_name, $wpdb;
	if (isset($_GET['Page'])) {
		$Page = $_GET['Page'];
	}
	else {$Page = 1;
	}
	$Sql=EWD_FEUP_Query_User_Hours_Page($_GET,$ewd_feup_user_table_name, $ewd_feup_user_hours_table_name);
	$userRows = $wpdb->get_results($Sql);
	$num_rows = $wpdb->num_rows;
	$Number_of_Pages = ceil($wpdb->num_rows/20);
	$Sql=EWD_FEUP_Query_User_Hours_All($_GET,$ewd_feup_user_table_name, $ewd_feup_user_hours_table_name);
	$userRowsAll = $wpdb->get_results($Sql);
	$userVerifiedHours=array();
	$userUnverifiedHours=array();
	if ($userRows) {
		foreach ($userRowsAll as $User) {
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
	if (isset($_GET['OrderBy'])) {$Current_Page_With_Order_By .= "&OrderBy=" .$_GET['OrderBy'] . "&Order=" . $_GET['Order'];}?>
<form action="admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_MassEditUserHours" method="post">    
<div class="tablenav top">
	<div class="alignleft actions">
		<select name='action'>

				<option value='' selected='selected'><?php _e("Bulk Actions", 'EWD_FEUP') ?></option>

				<option value='verify'><?php _e("Verify", 'EWD_FEUP') ?></option>

				<option value='delete'><?php _e("Delete", 'EWD_FEUP') ?></option>

		</select>
		<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php _e('Apply', 'EWD_FEUP') ?>"  />
	</div>
	<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
		<span class="displaying-num"><?php echo $wpdb->num_rows; ?> <?php _e("items", 'EWD_FEUP') ?></span>
		<span class='pagination-links'>
			<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='Go to the first page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=1'>&laquo;</a>
			<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='Go to the previous page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page-1;?>'>&lsaquo;</a>
			<span class="paging-input"><?php echo $Page; ?> <?php _e("of", 'EWD_FEUP') ?> <span class='total-pages'><?php echo $Number_of_Pages; ?></span></span>
			<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='Go to the next page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page+1;?>'>&rsaquo;</a>
			<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='Go to the last page' href='<?php echo $Current_Page_With_Order_By . "&Page=" . $Number_of_Pages; ?>'>&raquo;</a>
		</span>
	</div>
</div>

<table class="wp-list-table widefat fixed tags sorttable" cellspacing="0">
	<thead>
		<tr>
			<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
				<input type="checkbox" /></th><th scope='col' id='event-name' class='manage-column column-name sortable desc'  style="">
				<?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Username" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=EWD-FEUPHRS-options&DisplayPage=Dashboard&OrderBy=Username&Order=DESC'>";}
				else {echo "<a href='admin.php?page=EWD-FEUPHRS-options&DisplayPage=Dashboard&OrderBy=Username&Order=ASC'>";} ?>
					<span><?php _e("Username", 'EWD_FEUP') ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='hours' class='manage-column column-type sortable desc'  style="">
					<span><?php _e("Verified Hours", 'EWD_FEUP') ?></span>
				</a>
			</th>
			<th scope='col' id='hours' class='manage-column column-type sortable desc'  style="">
					<span><?php _e("Unverified Hours", 'EWD_FEUP') ?></span>
				</a>
			</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
				<input type="checkbox" /></th><th scope='col' id='field-name' class='manage-column column-name sortable desc'  style="">
				<?php if (isset($_GET['OrderBy']) && $_GET['OrderBy'] == "Username" and $_GET['Order'] == "ASC") { echo "<a href='admin.php?page=EWD-FEUPHRS-options&DisplayPage=Dashboard&OrderBy=Hours&Order=DESC'>";}
				else {echo "<a href='admin.php?page=EWD-FEUPHRS-options&DisplayPage=Dashboard&OrderBy=Username&Order=ASC'>";} ?>
					<span><?php _e("Username", 'EWD_FEUP') ?></span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope='col' id='hours' class='manage-column column-type sortable desc'  style="">
					<span><?php _e("Verified Hours", 'EWD_FEUP') ?></span>
				</a>
			</th>
			<th scope='col' id='hours' class='manage-column column-type sortable desc'  style="">
					<span><?php _e("Unverified Hours", 'EWD_FEUP') ?></span>
				</a>
			</th>
		</tr>
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
					echo "<a class='row-title' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_User_Hours&User_ID=" . $User->User_ID ."' title='Edit " . $User->Username . " Hours'>" . $User->Username . "</a></strong>";
					echo "<br />";
					echo "<div class='row-actions'>";
					echo "<span class='delete'>";
					echo "<a class='delete-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_DeleteAllUserHours&DisplayPage=Dashboard&User_ID=" . $User->User_ID ."'>" . __("Delete", 'EWD_FEUP') . "</a>";
		 			echo "</span>";
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
	<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
		<span class="displaying-num"><?php echo $wpdb->num_rows; ?> <?php _e("items", 'EWD_FEUP') ?></span>
		<span class='pagination-links'>
			<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='Go to the first page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=1'>&laquo;</a>
			<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='Go to the previous page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page-1;?>'>&lsaquo;</a>
			<span class="paging-input"><?php echo $Page; ?> <?php _e("of", 'EWD_FEUP') ?> <span class='total-pages'><?php echo $Number_of_Pages; ?></span></span>
			<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='Go to the next page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page+1;?>'>&rsaquo;</a>
			<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='Go to the last page' href='<?php echo $Current_Page_With_Order_By . "&Page=" . $Number_of_Pages; ?>'>&raquo;</a>
		</span>
	</div>
	<br class="clear" />
</div>
</form>

<br class="clear" />

</div>
</div>
