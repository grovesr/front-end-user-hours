<?php
	if (isset($_GET['Page'])) {
		$Page = $_GET['Page'];
	} else {
		$Page = 1;
	}
	if (isset($_GET['Action'])) {
		$Action = $_GET['Action'];
	} else {
		$Action = NULL;
	}
	global $ewd_feup_user_hours_table_name;
	if (isset($_GET['User_ID'])) {
		$User_ID = $_GET['User_ID'];
	} else {
		$User_ID='0';
	}
	$Sql = "SELECT * FROM $ewd_feup_user_hours_table_name WHERE User_ID=".$User_ID.' ';
	if (isset($_GET['OrderBy'])) {
		$Sql .= "ORDER BY " . $_GET['OrderBy'] . " " . $_GET['Order'] . " ";
	}	else {
		$Sql .= "ORDER BY Hours_Start_Date DESC ";
	}
	$Sql .= "LIMIT " . ($Page - 1)*20 . ",20";
	$myrows = $wpdb->get_results($Sql);
	$Sql =  "SELECT COUNT(Hours) FROM $ewd_feup_user_hours_table_name ";
	$Sql .= "where User_ID=$User_ID";
	$Number_of_Pages = ceil($wpdb->get_var($Sql)/20);
	$Current_Page_With_Order_By = "admin.php?page=EWD-FEUPHRS-options&DisplayPage=User&User_ID=" . $User_ID;
	if (isset($_GET['OrderBy'])) {$Current_Page_With_Order_By .= "&OrderBy=" .$_GET['OrderBy'] . "&Order=" . $_GET['Order'];}
	$HoursCount = $wpdb->num_rows;
	function printTableHeadings() { ?>
	<tr>
		<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">
				<input type="checkbox" />
		</th>
		<th scope='col' class='manage-column'>
			<span>Event</span>
		</th>
		<th scope='col' class='manage-column'>
			<span>Hours</span>
		</th>
		<th scope='col' class='manage-column'>
			<span>Begin</span>
		</th>
		<th scope='col' class='manage-column'>
			<span>End</span>
		</th>
		<th scope='col' class='manage-column'>
			<span>Verified</span>
		</th>
	</tr>
	<?php }
?>

<!-- The details of a specific product for editing, based on the product ID -->
	<?php $Username = $wpdb->get_row($wpdb->prepare("SELECT Username FROM $ewd_feup_user_table_name WHERE User_ID ='%d'", $_GET['User_ID'])); ?>
	<div id="col-right">
	<div class="col-wrap">
		<form action="admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_MassEditHours&DisplayPage=User&User_ID=<?php echo $User_ID?>" method="post">
		<h2><?php _e("User Hours for ".$Username->Username, 'EWD_FEUP') ?></h2>
			<?php  table_nav($Number_of_Pages, $Page, $Current_Page_With_Order_By, TRUE);?>
			<table class="wp-list-table widefat fixed tags sorttable" cellspacing="0">
				<thead><?php printTableHeadings() ?></thead>
				<tbody id="the-list" class='list:tag'>
					 <?php
						if ($myrows) {
				  			foreach ($myrows as $row) {
								$startDate=date_create($row->Hours_Start_Date);
								$stopDate=date_create($row->Hours_Stop_Date);
								echo "<tr id='Row" . $row->Field_ID ."'>";
								echo "<th scope='row' class='check-column'>";
								echo "<input type='checkbox' name='Hours_Bulk[]' value='" . $row->Field_ID ."' />";
								echo "</th>";
								echo "<td class='name column-name'>";
								if ($row->Event_ID) {
									echo '<a href="' . EWD_FEUPHRS_Get_Event_Link($row->Event_ID) . '">';
								}
								echo $row->Event_Name;
								if ($row->Event_ID) {
									echo '</a>';
								}
								echo "<div class='row-actions'>";
								echo "<span class='delete'>";
								echo "<a class='delete-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_DeleteHours&DisplayPage=User&Hour_ID=" . $row->Field_ID ."&User_ID=".$User_ID."'>" . __("Delete", 'EWD_FEUP') . "</a>";
								echo "</span>&nbsp;|&nbsp;";
								if (!$row->Verified) {
									echo "<span class='verify'>";
									echo "<a class='verify-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_VerifyHours&DisplayPage=User&Hour_ID=" . $row->Field_ID ."&User_ID=".$User_ID."'>" . __("Verify", 'EWD_FEUP') . "</a>";
									echo "</span>&nbsp;|&nbsp;";
								} else {
									echo "<span class='un-verify'>";
									echo "<a class='un-verify-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_UnVerifyHours&DisplayPage=User&Hour_ID=" . $row->Field_ID ."&User_ID=".$User_ID."'>" . __("Un-Verify", 'EWD_FEUP') . "</a>";
									echo "</span>";
								}
								echo "</div>";
								echo "<div class='hidden' id='inline_" . $row->Field_ID ."'></div>";
								echo "</td>";
								echo "<td class='name column-name'>{$row->Hours}</td>";
								echo "<td>".date_format($startDate,'m/d/Y')."</td>";
								echo "<td>".date_format($stopDate,'m/d/Y')."</td>";
								echo "<td class='name column-name'>";
								if ($row->Verified) {
									echo 'Yes';
								} else {
									echo 'No';
								}
								echo "</td>";
								echo "</tr>";
							}
						}
					?>
				</tbody>
				<tfoot><?php printTableHeadings() ?></tfoot>
			</table>
			<?php  table_nav($Number_of_Pages, $Page, $Current_Page_With_Order_By);?>
			</form>
		</div>
	</div>
	<div id="col-left">
		<div class="col-wrap">
			<div class="form-wrap">
				<h3><?php _e("Add User Hours", 'EWD_FEUP') ?></h3>
				<div class="wrap">
					<form id="EWD_FEUPHRS_AddUserHours" method="post" action="admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_AdminAddUserHours&DisplayPage=User&User_ID=<?php echo $User_ID?>" class="validate" enctype="multipart/form-data">
						<?php wp_nonce_field(); ?>
						<div class="form-field form-field-required">
								<input type="hidden" name="ewd-feuphrs-userid" value="<?php echo $User_ID; ?>">
								<input type="hidden" name="ewd-feuphrs-event-id" value="0">
								<label for='ewd-feuphrs-hours' class='ewd-feup-field-label'><?php _e('Hours', 'EWD_FEUP');?>: </label>
								<input id="ewd-feuphrs-hours" type='text' class='ewd-feup-text-input' name='ewd-feuphrs-hours'>
								<div class="pure-control-group">
									<label for='ewd-feuphrs-event-name' class='ewd-feup-field-label'><?php _e('Event', 'EWD_FEUP');?>: </label>
									<input id="ewd-feuphrs-event-name" type="text" class="ewp-feup-text-input ewd-feup-event-selector" name="ewd-feup-event" placeholder="Event"/>
								</div>
								<div class="pure-control-group">
									<label for='ewd-feuphrs-begin' class='ewd-feup-field-label'><?php _e('Begin', 'EWD_FEUP');?>: </label>
									<input type="text" class="ewp-feup-text-input datepicker" id="ewd-feup-begin-date" name="ewd-feuphrs-begin" placeholder="Begin"/>
								</div>
								<div class="pure-control-group">
									<label for='ewd-feuphrs-end' class='ewd-feup-field-label'><?php _e('End', 'EWD_FEUP');?>: </label>
									<input type="text" class="ewp-feup-text-input datepicker" id="ewd-feup-end-date" name="ewd-feuphrs-end" placeholder="End"/>
								</div>
						</div>
						<p class="submit"><input type="submit" name="User_Hours_Add" id="User_Hours_Add" class="button-primary" value="<?php _e('Add User Hours', 'EWD_FEUP') ?>"  /></p>
					</form>
					<?php echo EWD_FEUPHRS_Event_Selector();?>
				</div>
			</div>
		</div>
	</div>
