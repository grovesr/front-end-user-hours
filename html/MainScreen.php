<?php function table_nav($Number_of_Pages, $Page, $Current_Page, $top = FALSE) {
	if($top) {?>
	<div class="tablenav top">
		<div class="alignleft actions">
			<select name='action'>

					<option value='' selected='selected'><?php _e("Bulk Actions", 'EWD_FEUP') ?></option>

					<option value='verify'><?php _e("Verify", 'EWD_FEUP') ?></option>

					<option value='delete'><?php _e("Delete", 'EWD_FEUP') ?></option>

			</select>
			<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php _e('Apply', 'EWD_FEUP') ?>"  />
		</div>
	<?php
} else {?>
	<div class="tablenav bottom">
<?php
}
	?>
		<div class="tablenav-pages <?php if ($Number_of_Pages == 1) {echo 'one-page';} ?>"
			<span class="pagination-links">
				<a class="first-page" <?php if ($Page == 1) {echo 'style="pointer-events:none;"';} ?> title="Go to the first page" href="<?php echo $Current_Page; ?>&Page=1">&laquo;</a>
				<a class="prev-page"  <?php if ($Page <= 1) {echo 'style="pointer-events:none;"';} ?> title="Go to the previous page" href="<?php echo $Current_Page; ?>&Page=<?php if ($Page <= 1) {echo 1;} else {echo $Page - 1;}?>">&lsaquo;</a>
				<span class="paging-input"><?php echo $Page; ?> <?php _e("of", 'EWD_FEUP') ?> <span class="total-pages"><?php echo $Number_of_Pages; ?></span>
				<a class="next-page" <?php if ($Page >= $Number_of_Pages) {echo 'style="pointer-events:none;"';} ?> title="Go to the next page" href="<?php echo $Current_Page; ?>&Page=<?php if ($Page >= $Number_of_Pages) {echo $Number_of_Pages;} else {echo $Page + 1;}?>">&rsaquo;</a>
				<a class="last-page" <?php if ($Page == $Number_of_Pages) {echo 'style="pointer-events:none;"';} ?> title="Go to the last page" href="<?php echo $Current_Page . "&Page=$Number_of_Pages";?>">&raquo;</a>
			</span>
		</div>
	</div>
<?php
}?>


		<div class="EWD_FEUP_Menu">
				 <h2 class="nav-tab-wrapper">
						<?php
							if ($Display_Page == '' or $Display_Page == 'Dashboard') {
								$active = 'nav-tab-active';
							}
							echo "<a id=\"Dashboard_Menu\" class=\"MenuTab nav-tab $active\" onclick=\"ShowTab('Dashboard');\">Dashboard</a>";
							unset($active);
						?>
						<?php
							if ($User_ID) {
								if ($Display_Page == 'User') {
									$active = 'nav-tab-active';
								}
								echo "<a id=\"User_Menu\" class=\"MenuTab nav-tab $active\" onclick=\"ShowTab('User');\">User</a>";
							}
						?>
				 </h2>
		</div>



		<div class="clear"></div>



		<!-- Add the individual pages to the admin area, and create the active tab based on the selected page -->

		<div class="OptionTab <?php if ($Display_Page == "" or $Display_Page == 'Dashboard') {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="Dashboard">
				<?php include( plugin_dir_path( __FILE__ ) . 'DashboardPage.php'); ?>
		</div>
		<div class="OptionTab <?php if ( $Display_Page == 'User' and $User_ID) {echo 'ActiveTab';} else {echo 'HiddenTab';} ?>" id="User">
				<?php include( plugin_dir_path( __FILE__ ) . 'UserHours.php'); ?>
		</div>
