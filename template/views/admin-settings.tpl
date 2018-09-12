{% include 'static/admin/header.tpl' with { 
	title: 'Настройки'
} %}
{{ action('AdminSettings@edit', {
	default: defaultSettings
})|raw }}
{% include 'static/admin/footer.tpl' %}
