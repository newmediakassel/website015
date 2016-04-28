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
	// http://stackoverflow.com/a/13147238/520544
	function externalLinks() {
		for(var c = document.getElementsByTagName("a"), a = 0;a < c.length;a++) {
			var b = c[a];
			b.getAttribute("href") && b.hostname !== location.hostname && (b.target = "_blank");
		}
	};

	document.addEventListener("DOMContentLoaded", function(event) {
		fitvids('#main');
 		document.getElementById('main').scrollIntoView();
 		externalLinks();
	});
</script>
{% endblock %}

{% block jsonld %}
	<script type='application/ld+json'>
		{
			"@context": "http://schema.org/",
			"@type": "CreativeWork", 
			{% if DateTime %}
			"dateCreated": "{{ DateTime }}",
			{% endif %}
			"dateModified": {{ LastModified }},
			{% if Authors %}
			"url": "{{ CurrentUrl }}",
			"author": [
				{% for Author in Authors %}
					{
						"@context": "http://schema.org/",
						"@type": "Person",
						{% if Author.Url %}
						"name": "{{ Author.Title }}",
						"url": "{{ Author.Url }}"
						{% else %}
						"name": "{{ Author }}",
						{% endif %}
					}
					{% if false == loop.last %},{% endif %}
				{% endfor %}
			],
			{% endif %}
			"name": "{{ Title }}",
			"description": "{{ Description }}"
		}
	</script>
{% endblock %}
