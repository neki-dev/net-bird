'use strict';

var CSRF_TOKEN = null;
var ajaxCallbacks = [];

$(document).ready(function() {

	CSRF_TOKEN = $('meta[name=csrf-token]').attr('content');

	$(this).on('submit', 'form[data-ajax]', function(e) {

		var self = $(this);
		var data = serializeForm(self);
		var callback = ajaxCallbacks[data['_action']];

		hideFormMessage(self, true);

		$.ajax({
			type: 'POST',
			url: '/application/app-post.php',
			data: data,
			dataType: 'json',
			cache: false,
			success: function(e) {
				if(!callback) {
					console.debug(e['result']);
				} else {
					if(e['status'] === undefined) {
						callback(false, 'Undefined status', self);
					} else {
						callback(e['status'], e['result'], self);
					}
				}
			},
			error: function(e) {
				if(!callback) {
					console.error(e.responseText);
				} else {
					callback(false, e.responseText, self);
				}
			}
		});

		e.preventDefault();
		
	});

});

var addAjaxCallback = function(key, callback) {

	ajaxCallbacks[key] = callback;

};

var hideFormMessage = function(form, opacity = false) {

	var statuses = [ 'success', 'error' ];
	for(var key in statuses) {
		var self = form.children('.form-' + statuses[key]);
		if(opacity) {
			self.css('opacity', 0.5);
		} else {
			self.remove();
		}
	}

};

var showFormMessage = function(form, type, message) {

	form.prepend("<div class='form-" + type + "'>" + message + "</div>");

};

var poster = function(key, data, callback, params) {

	data = data || {};
	params = params || {};

	if(!CSRF_TOKEN) {
		console.error('Отсутствует токен CSRF');
		return false;
	}

	data['_csrf_token'] = CSRF_TOKEN;

	var post = {
		type: 'POST',
		url: '/application/actions/custom/' + key + '.php',
		data: data,
		dataType: 'json',
		cache: false,
		success: function(e) {
			if(!callback) return;
			if(e['status'] === undefined) {
				callback(false, 'Undefined status');
			} else {
				callback(e['status'], e['result']);
			}
		},
		error: function(e) {
			if(!callback) return;
			callback(false, e.responseText);
		}
	};

	for(var param in params) {
		post[param] = params[param];
	}

	$.ajax(post);

	return true;

};

var serializeForm = function(form) {

	form = form.serializeArray();

	var data = {};
	for (var i = 0; i < form.length; i++){
		data[form[i]['name']] = form[i]['value'];
	}

	return data;

};