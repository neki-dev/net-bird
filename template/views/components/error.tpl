<!DOCTYPE html>
<html lang='ru'>
<head>
	{% include 'components/header-min.tpl' with {
		title: errno
	} %}
</head>
<body class='dark'>
	<div class='frame'>
		<div class='padding'>
			<div class='file'>{{ file }} :: {{ line.error }}</div>
			<div class='message'>
				<h3>{{ errno }}</h3>
				<span>{{ message }}</span>
			</div>
			{% if(developed) %}
				<div class='code'>
					<pre id='code'><div class='lines'>{% for i in range(line.start, line.end) %}{% if(i == line.error) %}<span class='active'>{{ i }}</span>{% else %}<span>{{ i }}</span>{% endif %}{% endfor %}</div><code class='php'>{{ code }}</code></pre>
				</div>
			{% endif %}
			<div class='tabs-toggle'>
				<span class='active' data-tab='args'>ARGUMENTS</span>
				<span data-tab='backtrace'>BACKTRACE</span>
				<span data-tab='session'>$_SESSION</span>
				<span data-tab='get'>$_GET</span>
				<span data-tab='post'>$_POST</span>
			</div>
			<div class='tabs-container'>
				<div class='active' class='item' data-tab='args'><span class="debug-parse">{{ presets.args|raw }}</span></div>
				<div class='item' data-tab='backtrace'><span class="debug-parse">{{ presets.backtrace|raw }}</span></div>
				<div class='item' data-tab='session'><span class="debug-parse">{{ presets.session|raw }}</span></div>
				<div class='item' data-tab='get'><span class="debug-parse">{{ presets.get|raw }}</span></div>
				<div class='item' data-tab='post'><span class="debug-parse">{{ presets.post|raw }}</span></div>
			</div>
		</div>
	</div>
	<script type='text/javascript'>
		hljs.initHighlightingOnLoad();
		$('.tabs-toggle > span').click(function() {
			$('.tabs-toggle > span').removeClass('active');
			$(this).addClass('active');
			$('.tabs-container > div').removeClass('active');
			$('.tabs-container > div[data-tab=' + $(this).data('tab') + ']').addClass('active');
		});
	</script>
</body>
</html>