'use strict';

var fondInit = function(table, params) {

	var $window = $(window);

	table = $('#' + table);

	var data = JSON.parse(params.data),
		dynamic = false;

	if(data.length == 0) {
		table.html("<i>Список пуст...</i>");
		return;
	}

	table.append("<thead><tr></tr></thead><tbody></tbody>");

	// title

	table.find('thead tr').append("<td style='width:20px;'>#</td>");
	Object.keys(params.columns).map(function(key, index) {
		var value = params.columns[key];
		table.find('thead tr').append("<td style='width:" + value[1] + "px;'>" + value[0] + "</td>");
	});
	if(params.tools) {
		table.find('thead tr').append("<td></td>");
	}

	// data

	for(var i = 1; i <= data.length; ++i) {
		var td = "<td>" + i + "</td>";
		Object.keys(params.columns).map(function(key, index) {
			if(params.columns[key][2] === true) {
				dynamic = true;
				td += "<td class='dynamic' data-field='" + key + "'>" + data[i - 1][key] + "</td>";
			} else {
				td += "<td data-field='" + key + "'>" + data[i - 1][key] + "</td>";
			}
		});
		if(params.tools) {
			var tools = "";
			for(var k = 0; k < params.tools.length; ++k) {
				if(params.tools[k] == "edit") {
					tools += "<a href='" + location.href + "/" + data[i - 1].id + "' class='tool-edit'>Редактировать</a>";
				} else if(params.tools[k] == "open") {
					tools += "<a href='" + location.href.replace("admin/", "") + "/" + data[i - 1].id + "' target='_blank' class='tool-open'>Открыть</a>";
				} else if(params.tools[k] == "delete") {
					tools += "<a href='#' class='tool-delete'>Удалить</a>";
				}
			}
			td += "<td>" + tools + "</td>";
		}
		table.find('tbody').append("<tr data-id='" + data[i - 1].id + "'>" + td + "</tr>");
	}

	// hint

	if(dynamic) {
		table.before("<div class='help'>* Для редактирования нажмите на поле которое собираетесь изменить. После чего нажмите Enter для сохранения нового значения.</div>");
	}

	// edit dynamic rows

	var editActive = null;

	$window.keypress(function(e) {

		if(editActive && e.keyCode == 13) {
			
			var value = editActive.children('input').val();
			
			runPost.send(params.type + 'Edit', {
				id: editActive.parent().attr('data-id'),
				field: editActive.attr('data-field'),
				value: value
			}, function(success, result) {
				if(success) {
					editActive.html(value);
					editActive = null;
					alert('Изменения сохранены');
				} else if(result) {
					alert(result);
				}
			});

			return false;
		}

	});

	table.on('click', 'tbody td.dynamic', function() {
		
		if(editActive) {
			return;
		}

		var self = $(this),
			value = self.html();

		editActive = self.html('<input type="text" value="' + value + '" placeholder="' + value + '" />');
		editActive.children('input').focus();

	});

	// delete rows

	table.on('click', 'tbody a.tool-delete', function() {
				
		if(confirm('Вы действительно хотите это удалить?')) {

			var self = $(this).parent().parent();
			
			runPost.send('itemDelete', { 
				type: params.type,
				id: self.attr('data-id')
			}, function(success, result) {
				if(success) {
					self.remove();
				} else if(result) {
					alert(result);
				}
			});

		}

		return false;

	});

}