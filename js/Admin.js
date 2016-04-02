function ShowTab(TabName) {
		jQuery(".OptionTab").each(function() {
				jQuery(this).addClass("HiddenTab");
				jQuery(this).removeClass("ActiveTab");
		});
		jQuery("#"+TabName).removeClass("HiddenTab");
		jQuery("#"+TabName).addClass("ActiveTab");

		jQuery(".nav-tab").each(function() {
				jQuery(this).removeClass("nav-tab-active");
		});
		jQuery("#"+TabName+"_Menu").addClass("nav-tab-active");
}
jQuery(document).ready(function() {
	jQuery('.datepicker').datepicker();
	var currentDate = new Date();
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();
    thisDate= month + "/" + day + "/" + year;
    jQuery( '#ewd-feup-begin-date' ).val( thisDate );
    jQuery( '#ewd-feup-end-date' ).val( thisDate );
});
