<meta charset='utf-8' />
<meta name='viewport' content='width=device-width, initial-scale=1.0' />
<title>{{ _settings.sitename }} - {{ title }}</title>
<meta name='robots' content='index, follow' />
<meta name='keywords' content='{{ keywords }}' />
<meta name='description' content='{{ description }}' />
<meta property='og:title' content='{{ _settings.sitename }} - {{ title }}' />
<meta property='og:type' content='website' />
<meta property='og:image' content='/template/favicon/miniature.png' />
<meta property='og:description' content='{{ description }}' />
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
<meta name='apple-mobile-web-app-title' content='RFG' />
<meta name='msapplication-config' content='/template/favicon/browserconfig.xml' />
<meta name='theme-color' content='{{ theme ?? "#222222" }}' />
<meta name="csrf-token" content='{{ csrf_token() }}' />