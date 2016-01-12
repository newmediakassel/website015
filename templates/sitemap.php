<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{% for Item in Sitemap %}
	<url>
		<loc>{{ BaseUrl }}{{ Item.Url }}</loc>
	</url>
{% endfor %}
</urlset>