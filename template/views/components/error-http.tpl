<!DOCTYPE html>
<html lang='ru'>
<head>
	{% include 'components/header-min.tpl' with {
		title: error
	} %}
</head>
<body>
	<span>{{ error }}</span>
</body>
</html>