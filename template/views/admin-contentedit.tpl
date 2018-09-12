{% include 'static/admin/header.tpl' with { 
	title: 'Контент'
} %}
<div class='block-title'>Редактирование контента</div>
{{ action('AdminContent@edit', {
	default: defaultContent
})|raw }}
{% include 'static/admin/footer.tpl' %}