<meta charset='utf-8' />
<meta name='viewport' content='width=device-width, initial-scale=1.0' />
<title>{{ _settings.sitename }} - {{ title }}</title>
<meta name='robots' content='index, follow' />
<meta name='keywords' content='{{ keywords }}' />
<meta name='description' content='{{ description }}' />
<meta property='og:title' content='{{ _settings.sitename }} - {{ title }}' />
<meta property='og:type' content='website' />
<meta property='og:image' content='/template/favicon/mstile.png' />
<meta property='og:description' content='{{ description }}' />
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
<meta name='apple-mobile-web-app-title' content='RFG' />
<meta name='msapplication-config' content='/template/favicon/browserconfig.xml' />
<meta name='theme-color' content='{{ theme ?? "#1f6d87" }}' />
<meta name="csrf-token" content='{{ csrf_token() }}' />