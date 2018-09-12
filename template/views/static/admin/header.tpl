<!DOCTYPE html>
<html lang='ru'>
<head>
	{% include 'components/header.tpl' %}
</head>
<body>
	<div class='wrapper'>
		<div class='tool'>
			<div class='title'>
				Панель администратора /
				<a href='/' class='back'>на главную</a>
				<a href='/admin/logout'>выйти</a>
			</div>
			<ul>
				<li><a href='/admin/settings'>Настройки</a></li>
				<li><a href='/admin/content'>Контент</a></li>
			</ul>
		</div>
		<div class='content'>
			<span class='title'>{{ title }}</span>