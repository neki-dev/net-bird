<select name='{{ name }}'{{ data.class }}{{ data.disabled ? ' disabled' : '' }}>
	<option selected disabled>{{ data.desc }}</option>
	{% for option in data.value %}
		{% if option[2] %}
			<option value='{{ option[0] }}' selected>{{ option[1] }}</option>
		{% else %}
			<option value='{{ option[0] }}'>{{ option[1] }}</option>
		{% endif %}
	{% endfor %}
</select>