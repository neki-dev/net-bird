<meta charset='utf-8' />
<meta name='viewport' content='width=device-width, initial-scale=1.0' />
<title>{{ title }}</title>
{% for font in _assets.fonts %}
	<link rel='stylesheet' href='https://fonts.googleapis.com/css?family={{ font }}:300,300i,400,400i,700,700i&amp;subset=cyrillic' type='text/css' />
{% endfor %}
{% for link in css_remote %}
	<link rel='stylesheet' href='{{ link }}' type='text/css' />
{% endfor %}
{% set css = _assets.css|merge((css ?: [])) %}
{% if css|length > 0 %}
	<link rel='stylesheet' href='/template/combine.css={{ css|join(",") }}{{ udev() }}' type='text/css' />
{% endif %}
{% for link in js_remote %}
	<script type='text/javascript' src='{{ link }}'></script>
{% endfor %}
{% set js = _assets.js|merge((js ?: [])) %}
{% if js|length > 0 %}
	<script type='text/javascript' src='/template/combine.js={{ js|join(",") }}{{ udev() }}'></script>
{% endif %}
<link rel='shortcut icon' href='/template/favicon/favicon.ico?' />