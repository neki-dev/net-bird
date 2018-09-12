<!DOCTYPE html>
<html lang='ru'>
<head>
	{% include 'components/header.tpl' with { 
		title: 'Авторизация'
	} %}
</head>
<body class='smooth'>
	<div class='admin-login'>
		<h2>Панель управления</h2><br/>
		{{ action('AdminAuth@login')|raw }}
	</div>
</body>
</html>