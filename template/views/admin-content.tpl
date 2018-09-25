{% include 'static/admin/header.tpl' with { 
	title: 'Контент'
} %}
<div class='block-title'>Добавление контента</div>
{{ action('AdminContent@add')|raw }}
<hr class='big' />
<div class='block-title'>Список контента</div>
<table id="content"></table>
<script type='text/javascript'>
	$('#content').contentble({
		type: 'content',
		data: {{ content|raw }},
		columns: {
			title: [ "Название", 300 ]
		},
		tools: [ 'delete', 'edit' ]
	});
</script>
{% include 'static/admin/footer.tpl' %}