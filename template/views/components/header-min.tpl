<meta charset='utf-8' />
<meta name='viewport' content='width=device-width, initial-scale=1.0' />
<title>{{ title }}</title>
{% for link in css_remote %}
	<link rel='stylesheet' href='{{ link }}' type='text/css' />
{% endfor %}
{% set css = _assets.css|merge((css ?: [])) %}
{% if _config.combine.enabled %}
	<link rel='stylesheet' href='/template/combine/{{ css|join(",") }}.css?{{ _config.developed ? random() : "" }}' type='text/css' />
{% else %}
	{% for link in css %}
		<link rel='stylesheet' href='/template/css/{{ link }}.css?{{ _config.developed ? random() : "" }}' type='text/css' />
	{% endfor %}
{% endif %}
{% for link in js_remote %}
	<script type='text/javascript' src='{{ link }}'></script>
{% endfor %}
{% set js = _assets.js|merge((js ?: [])) %}
{% if _config.combine.enabled %}
	<script type='text/javascript' src='/template/combine/{{ js|join(",") }}.js?{{ _config.developed ? random() : "" }}'></script>
{% else %}
	{% for link in js %}
		<script type='text/javascript' src='/template/js/{{ link }}.js?{{ _config.developed ? random() : "" }}'></script>
	{% endfor %}
{% endif %}
<link rel='shortcut icon' href='/template/favicon/favicon.ico' />