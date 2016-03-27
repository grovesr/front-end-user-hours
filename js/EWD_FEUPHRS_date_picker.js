
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