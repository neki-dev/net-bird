'use strict';

$(document).ready(function() {

	var modal = $('#modal');

	$('#modal-open').click(function() {
		modal.show();
		return false;
	});

	$('#modal-overlay, #modal-close').click(function() {
		modal.hide();
	});

});