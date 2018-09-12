<div class='pags-list'>
{% for page in pags %}
	{% if(page.type == 'link') %}
		<a href='{{ page.link }}'>{{ page.name }}</a>
	{% elseif(page.type == 'span') %}
		<span class='active'>{{ page.name }}</span>
	{% elseif(page.type == 'text') %}
		<span class='delims'>{{ page.name }}</span>
	{% endif %}
{% endfor %}
</div>