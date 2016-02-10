{% extends "index.php" %}

{% block title %}{{ Title }} »{% endblock %}

{% block detail %}
	<div {%  if Width %} style="width:{{ Width }}px"{% endif %}>
		<header>
			<h1>{{ Title }}</h1>
			<p>
				<time datetime="{{ DateTime }}">{{ Date }}</time>
				
				{% if Type and Type|lower != "project" %}
					// {{ Type }}
				{% endif %}

				{% if Authors %}
					// 
					{% for Author in Authors %}
						{% if false == loop.first %} &middot; {% endif %}
						{% if Author.Url %}
							<a href="{{ Author.Url }}" rel="external">{{ Author.Title }}</a>
						{% else %}
							{{ Author }}
						{% endif %}
					{% endfor %}
				{% endif %}

				{% if For %}
					for 
					{% for f in For %}
						{% if false == loop.first %}, {% endif %}
							{% if f.Url %}
								<a href="{{ f.Url }}" rel="external">{{ f.Title }}</a>
							{% else %}
								{{ f }}
							{% endif %}
					{% endfor %}
				{% endif %}

				{% block meta %}{% endblock %}
			</p>
		</header>

		<section>
			{{ Content|raw }}
		</section>
	</div>
{% endblock %}

{% block js %}
<script src="/js/fitvids.min.js" type="text/javascript"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) { 
		fitvids('#main');
 		document.getElementById('main').scrollIntoView();
	});
</script>
{% endblock %}

{% block json %}
	<script id="project-data" type="application/json">{{ JSON }}</script>
{% endblock %}