'use strict';

$(document).ready(function() {

	var uploader		= $('.input-file'),
		presetFile		= uploader.children('input[type=text]').val(),
		fileUpload		= uploader.children('a.upload'),
		fileDelete		= uploader.children('a.delete'),
		fileName		= uploader.children('.status'),
		fileData		= uploader.children('input[type=text]'),
		fileSubject		= uploader.children('input[type=file]');

	var activeFile = null;

	if(presetFile && presetFile.length) {
		presetFileLoad(presetFile);
	} else {
		fileUpload.show();
		fileDelete.hide();
	}

	fileSubject.change(function(file) {

		var form = new FormData();

		form.append('file', $(this).prop('files')[0]);
		form.append('setsize', fileSubject.attr('data-size'));
		form.append('_csrf_token', CSRF_TOKEN);

		poster('fileUpload', form, function(success, result) {
			if(success) {
				fileData.attr('value', result.file);
				presetFileLoad(result.file, result.name);
			} else if(result) {
				alert(result);
			}
		}, {
   			processData: false,
    		contentType: false
		});

	});

	fileUpload.click(function() {

		fileSubject.click();

		return false;

	});

	fileDelete.click(function() {

		poster('fileDelete', {
			file: activeFile,
		}, function(success, result) {
			if(success) {
				fileUpload.show();
				fileDelete.hide();
				fileName.html('Файл не загружен');
				fileData.removeAttr('value');
				activeFile = null;
			} else if(result) {
				alert(result);
			}
		});

		return false;

	});

	function presetFileLoad(file, name) {

		fileUpload.hide();
		fileDelete.show();

		fileName.html(name);

		activeFile = file;

	}

});