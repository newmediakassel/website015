{% extends "page.php" %}

{% block title %}About »{% endblock %}

{% block detail %}
	<h1>{{ Title }}</h1>
	
	{{ Content|raw }}
{% endblock %}