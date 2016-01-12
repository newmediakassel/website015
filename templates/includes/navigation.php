<header id="top">
	<style>
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
	</style>
	<center>
		<pre>



NMMMMM MMMMMMMMMMMMMMM  MMMMMMMMMMMMMMMMy:    
NMMMMM MMMMMMMMMMMMMMM  MMMMMMMMMMMMMMMMMMy:  
NMMMMM MMNNNNNNNMMMMMM  MMMMMMMMMMMMMMMMMMMMy:
NMMMMM          MMMMMM  MMMMMM +dMMMMM  MMMMMM
NMMMMM          MMMMMM  MMMMMM  .+dMMM  MMMMMM
NMMMMM          MMMMMM  MMMMMM    .+MM  MMMMMM
/dMMMM          MMMMMM  MMMMMM      ./  MMMMMM
  /dMM          MMMMMM  MMMMMM          MMMMMM
    /d          MMMMMM  MMMMMM          MMMMMM

            ~ ~ New Media Kassel ~ ~            


		</pre>
		<nav>
			<ul>
				<!-- Todo: add active link state -->
				<li><a href="/" rel="home">Home</a></li>
				<li><a href="/we">About the Class</a></li>
			</ul>

			<ul>
				{% block navigation %}
				
					{% for Link in Navigation %}
						<li><a href="{{ Link.Url }}" rel="bookmark"{% if Link.IsActive %} class="active"{% endif %}>{{ Link.Title }}</a></li>
					{% endfor %}

				{% endblock %}
			</ul>

			<ul>
				<li><a href="http://newsletter.newmediakassel.com">Subscribe to our Newsletter</a></li>
				<li><a href="http://calendar.newmediakassel.com">See our Events in the Calendar</a></li>
				<li><a href="https://twitter.com/nmkhk">Follow us on Twitter</a></li>
			</ul>
		</nav>

		<pre>


~ ~ * ~ x o x ~ * ~ ~


		</pre>
	</center>
</header>