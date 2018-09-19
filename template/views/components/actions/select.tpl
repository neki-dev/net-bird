<select {{ tags|raw }}>
	<option selected disabled>{{ data.placeholder }}</option>
	{% for option in value %}
		{% if option[2] %}
			<option value='{{ option[0] }}' selected>{{ option[1] }}</option>
		{% else %}
			<option value='{{ option[0] }}'>{{ option[1] }}</option>
		{% endif %}
	{% endfor %}
</select>