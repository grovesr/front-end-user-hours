<?php 
		if (isset($_GET['Page'])) {

			$Page = $_GET['Page'];

		} else {

			$Page = 1;

		}


		global $ewd_feup_user_hours_table_name;


		if (isset($_GET['User_ID'])) {
			$User_ID = $_GET['User_ID'];
		} else {
			$User_ID='0';
		}



		$Sql = "SELECT * FROM $ewd_feup_user_hours_table_name WHERE User_ID=".$User_ID.' ';



		if (isset($_GET['OrderBy'])) {$Sql .= "ORDER BY " . $_GET['OrderBy'] . " " . $_GET['Order'] . " ";}

		else {$Sql .= "ORDER BY User_ID ";}

		$Sql .= "LIMIT " . ($Page - 1)*20 . ",20";



				$myrows = $wpdb->get_results($Sql);

				$num_rows = $wpdb->num_rows; 

				$Number_of_Pages = ceil($num_rows/20);

				$Current_Page_With_Order_By = "admin.php?page=EWD-FEUPHRS-options&DisplayPage=UserHours&User_ID=" . $User_ID;

				if (isset($_GET['OrderBy'])) {$Current_Page_With_Order_By .= "&OrderBy=" .$_GET['OrderBy'] . "&Order=" . $_GET['Order'];}
				$HoursCount = $wpdb->num_rows;




		function printTableHeadings() { ?>

		<tr>

			<th scope='col' id='cb' class='manage-column column-cb check-column'  style="">

					<input type="checkbox" />

			</th>



			<th scope='col' class='manage-column column-cb check-column'>

				<span>Event</span>

			</th>



			<th scope='col' class='manage-column column-cb check-column'>

				<span>Hours</span>

			</th>



			<th scope='col' class='manage-column column-cb check-column'>

				<span>Verified</span>

			</th>

		</tr>



		<?php }

?>

<!-- The details of a specific product for editing, based on the product ID -->

		
		<?php $Username = $wpdb->get_row($wpdb->prepare("SELECT Username FROM $ewd_feup_user_table_name WHERE User_ID ='%d'", $_GET['User_ID'])); ?>



<a href="admin.php?page=EWD-FEUPHRS-options&DisplayPage=Dashboard" class="NoUnderline">&#171; <?php _e("Back", 'EWD_FEUP') ?></a>


	<div id="col-right">

	<div class="col-wrap">

		<form action="admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_MassEditHours&DisplayPage=UserHours&User_ID=<?php echo $User_ID?>" method="post">  

		<h2><?php _e("User Hours for ".$Username->Username, 'EWD_FEUP') ?></h2>



			<div class="tablenav top">

				<div class="alignleft actions">
					<select name='action' autocomplete='off'>
			  			<option value='-1' selected='selected'><?php _e("Bulk Actions", 'EWD_FEUP') ?></option>
			  			<option value='verify' ><?php _e("Verify", 'EWD_FEUP') ?></option>
						<option value='delete'><?php _e("Delete", 'EWD_FEUP') ?></option>
					</select>
					<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php _e('Apply', 'EWD_FEUP') ?>"  />
				</div>
				<div class='tablenav-pages <?php if ($Number_of_Pages <= 1) {echo "one-page";} ?>'>
						<span class="displaying-num"><?php echo $HoursCount; ?> <?php _e("items", 'EWD_FEUP') ?></span>
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

					<thead><?php printTableHeadings() ?></thead>



					<tfoot><?php printTableHeadings() ?></tfoot>



				<tbody id="the-list" class='list:tag'>

					

					 <?php

						if ($myrows) { 

				  			foreach ($myrows as $row) {

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

								if (!$row->Verified) {

									echo "<span class='verify'>";

									echo "<a class='verify-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_VerifyHours&DisplayPage=UserHours&Hour_ID=" . $row->Field_ID ."&User_ID=".$User_ID."'>" . __("Verify", 'EWD_FEUP') . "</a>";

									echo "</span> | ";

								}

								echo "<span class='delete'>";

								echo "<a class='delete-tag' href='admin.php?page=EWD-FEUPHRS-options&Action=EWD_FEUPHRS_DeleteHours&DisplayPage=UserHours&Hour_ID=" . $row->Field_ID ."&User_ID=".$User_ID."'>" . __("Delete", 'EWD_FEUP') . "</a>";

								echo "</span>";

								echo "</div>";

								echo "<div class='hidden' id='inline_" . $row->Field_ID ."'></div>";

								echo "</td>";



								echo "<td class='name column-name'>{$row->Hours}</td>";

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

			</table>



			<div class="tablenav bottom">

					<div class='tablenav-pages <?php if ($Number_of_Pages <= 1) {echo "one-page";} ?>'>
							<span class="displaying-num"><?php echo $HoursCount; ?> <?php _e("items", 'EWD_FEUP') ?></span>
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

	</div>

	</div>