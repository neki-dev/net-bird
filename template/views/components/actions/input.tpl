{% if data.comment %}
<div class='input-title'>{{ data.comment }}</div>
{% endif %}
<input 
	type='{{ data.subtype ?? "text" }}' 
	value='{{ data.value }}' 
	name='{{ name }}' 
	placeholder='{{ data.desc }}'
	{{ data.class }}
	{{ data.disabled ? ' disabled' : '' }}
	{{ data.range|raw }} 
/>