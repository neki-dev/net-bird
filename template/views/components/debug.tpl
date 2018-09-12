<link rel='stylesheet' href='/template/css/system/debug.parse.css' type='text/css' />
<input type='checkbox' class='debug-toggle' checked />
<div class='debug-print'>
	{% for message in messages %}
		<span class='debug-parse '>{{ message|raw }}</span>
	{% endfor %}
</div>