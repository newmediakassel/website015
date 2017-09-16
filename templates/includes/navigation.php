<header id="top">
	<style>
		a {
			text-decoration: underline;
			color: purple;
		}

		a:hover {
			text-decoration: none;
		}

		a:visited {
			color: blue;
		}

		header a {
			text-decoration: none;
		}

		header a:hover,
		header a:focus {
			text-decoration: underline;
		}

		#top nav ul {
			list-style: none;
			padding: 0;
		}

		#top nav ul li {
			padding-bottom: 0.25rem;
		}

		#top nav ul a {
			text-decoration: none;
		}

		#top nav ul a:hover,
		#top nav ul a:focus {
			text-decoration: underline;
		}

		#top nav ul a.active {
			font-weight: bold;
			text-decoration: underline;
		}

		/* indicate class related items */
		#top nav ul li.workshop,
		#top nav ul li.exhibition,
		#top nav ul li.talk,
		#top nav ul li.lecture {
			font-style: italic;
		}

		#top nav ul li abbr {
			cursor: help;
			font-style: normal;
			font-family: monospace;
		}

		/* year separator */
		#top nav ul li .date {
			display: inline-block;
			margin-top: 1em;
			font-family: monospace;
		}

		/* ticker */
		marquee {
			font-family: monospace;
			width: 50%;
			min-width: 500px;
			margin: 1rem 0;
		}

		marquee ul,
		marquee li {
			display: inline;
			list-style: none;
		}

		marquee a {
			font-weight: bold;
		}
	</style>
	<center>
		<h1 style="padding: 3rem 0">
			<a href="/">
				<img src="{{ random(Logos) }}" alt="New Media Kassel" >
			</a>
		</h1>

		<nav>
			<ul>
				<!-- Todo: add active link state -->
				<li><a href="/" rel="home">Home</a></li>
				<li><a href="/we">About the Class</a></li>
			</ul>

			<ul>
				<li><a href="http://newsletter.newmediakassel.com" target="_blank">Subscribe to our Newsletter</a></li>
				<li><a href="http://calendar.newmediakassel.com" target="_blank">See our Events in the Calendar</a></li>
				<li><a href="https://twitter.com/nmkhk" target="_blank">Follow us on Twitter</a></li>
				<li><a href="https://www.facebook.com/newmediakassel/" target="_blank">Like us on Facebook</a></li>
				<li><a href="https://instagram.com/newmedia_kassel" target="_blank">Follow us on Instagram</a></li>
			</ul>

			<ul>
				{% block navigation %}
					{% if TickerItems %}
						<li>
							<marquee onmouseover="this.stop();" onmouseout="this.start();">
								+ + + <strong>CURRENT / UPCOMING EVENTS</strong> +++ <ul>
									{% for Link in TickerItems %}
										<li>
											{% if Link.Url %}
												<a href="{{ Link.Url }}" rel="bookmark" class="{% if Link.IsActive %}active{% endif %}">
													{{ Link.Title|trim }}
												</a>
											{% else %}
												{{ Link.Title|trim }}
											{% endif %}

										{% if not loop.last %} +++ {% endif %}
										</li>
									{% endfor %}
								</ul> + + +
							</marquee>
						</li>
					{% endif %}

					{% for Link in Navigation %}
						<li{% if Link.Type %} class="{% if Link.Type is iterable %}{% for t in Link.Type %}{{ t|lower|e('html_attr') }}{% endfor %}{% else %}{{ t|lower|e('html_attr') }}{% endif %}"{% endif %}>
							{% if Link.IsDate %}
								~ <strong class="date" id="{{ Link.Title }}">{{ Link.Title }}</strong> ~
							{% elseif Link.Url %}
								<a href="{{ Link.Url }}" rel="bookmark" class="{% if Link.IsActive %}active{% endif %}">
									{{ Link.Title|trim }}
								</a>

								{% if Link.Type is iterable %}
									[
									{% for t in Link.Type|sort %}
										<abbr title="{{ t }}">{{ t|first|upper }}</abbr>
									{% endfor %}
									]
								{% else %}
									{% if Link.Type and Link.Type|lower != "project" %}
										<abbr title="{{ Link.Type }}">
											[{{ Link.Type|first|upper }}]
										</abbr>
									{% endif %}
								{% endif %}
							{% else %}
								{{ Link.Title|trim }}
							{% endif %}
						</li>
					{% endfor %}

				{% endblock %}
			</ul>
		</nav>

		<pre>


~ ~ * ~ x <a href="#top" class="to-top">&#8593;</a> x ~ * ~ ~


		</pre>
	</center>
</header>
