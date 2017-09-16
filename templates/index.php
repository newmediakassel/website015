<!DOCTYPE html>
<html>
<head>
	{% block head %}
		<meta charset="utf-8">
		
		<title>{% block title %}{% endblock %} New Media Kassel</title>

		{% if Description %}
			<meta name="description" content="{{ Description }}">
		{% else %}
			<meta name="description" content="The principle of academic freedom is designed to make sure that powers outside the university, including government and corporations, are not able to control the curriculum or intervene in extra-mural speech.">
		{% endif %}

		<!-- JEREMIAH JOHNSON | NSA/SEO Generator | nsa-seo.com | 2013 -->
		<meta name="keywords" content="{% if Keywords %}{% for Keyword in Keywords %}{{ Keyword }}, {% endfor %}{% endif %}angelina jolie law mi, barack obama blacklist black-ops, coldplay tactics key, coldplay camouflage camouflage, lady gaga secret key, gwyneth paltrow data mosaic, kanye west error conspiracy, leonardo dicaprio jet infosec, justin timberlake sigdev cbot, kristen stewart data newton, david beckham mobile maple, tom cruise surveillance usaf, kim kardashian mailbomb bronze, steven spielberg field national, kristen stewart mosaic firefly, usher merlin halcon, lebron james bird dog sig, usher majic maser, angelina jolie xi radio, beyonce blacklist information, madonna hackers fox, louis c.k. factor supra, kurt cobain sigdev defense, beyonce lock death, taylor swift norad milsatcom, selena gomez encryption stanford, kanye west face ground, usher icbm defcon, usher usaf wire, gwyneth paltrow clone tbranch, barack obama supercomputer infowar, heidi klum pi halo, lady gaga mobile arpa, oprah cypher propaganda, miley cyrus mj-12 lf, david beckham objective missile, mila kunis insert infrasound, selena gomez umbrella humint, justin bieber ground information, britney spears secops indigo, barack obama halo radint, lil wayne agcy signature, lebron james algorithm enigma, kristen stewart door sigdev, jennifer aniston anonymous uhf, lebron james mole law, usher gsg-9 airforce, miley cyrus majic filter, kate middleton vaccine white, britney spears surveillance lablink, will smith mil secret, drake data usaf, jessica alba sigdev transfer, mila kunis sp4 sp4, jennifer aniston jet mailbomb, kanye west psyops warfare, drake device cold, drake navelexsyssecengcen amu, oprah vaccine aec, michael jackson wire group, jessica alba ld groom, usher irbm infowar, david beckham code cdc, louis c.k. e-bomb lethal, britney spears aldergrove pgp, lebron james radint device, justin timberlake bnd noise, drake defcon hyper, kanye west sp4 transfer, kim kardashian jupiter sam, ashton kutcher blocks scan, steven spielberg shape oil, heidi klum encryption special, justin bieber sniper anonymous, miley cyrus shf assassination, beyonce norad retinal, taylor swift bronze bluebird, tom cruise data government, rihanna wire infosec, michael jackson privacy gsg-9, beyonce collat propaganda, jerry seinfeld bi face, mila kunis spies proton, brad pitt spies white, ben affleck area51 umbrella, oprah trinity spies, angelina jolie integrate classified, gwyneth paltrow fbi privacy, drake error bnd, ben affleck anonymous stp, taylor swift screening sequence, mila kunis psyops encryption, justin bieber infosec objective, jerry seinfeld error anti, leonardo dicaprio oil guard, angelina jolie integrate mechanics, gwyneth paltrow guard bugs bunny, justin timberlake bi trinity" />
		
		<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
		<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json">
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#262626">
		<meta name="msapplication-TileImage" content="/mstile-144x144.png">
		<meta name="theme-color" content="#262626">

		{# if Keywords %}
			<meta name="keywords" content="{% for Keyword in Keywords %}{{ Keyword }}{% if false == loop.last %}, {% endif %}{% endfor %}">
		{% endif #}
	{% endblock %}

	<style type="text/css">
		#main {
			width: 90%;
			{% if MaxWidth %}max-width: {{ MaxWidth }};{% endif %}
			
			text-align: center;
			margin: -10rem auto 10rem;
			padding-top: 10rem;
		}

		#main img,
		#main iframe {
			max-width: 100%;
		}

		#main header a {
                        text-decoration: none;
                }

                #main header a:hover,
                #main header a:focus {
                        text-decoration: underline;
                }

		#main header h1 {
			margin-bottom: 0;
		}

		#main header p {
			font-family: monospace;
		}
		
		#main table {
			margin-left: auto;
			margin-right: auto;
		}

		#main td {
			padding: 0.1em 0.5em;
		}
		
		#main > div {
			margin: 0 auto;
		}
		
		#main ul,
		#main ol {
			display: inline-block;
		}
		
		#main ul li,
		#main ol li {
			text-align: left;
		}
		
		#main h2 + ul,
		#main h2 + ol,
		#main h3 + ul,
		#main h2 + ol {
			margin-top: 0;
		}
		
	</style>

	{% block jsonld %}{% endblock %}
</head>
<body>
	{{ include('includes/navigation.php') }}

	<main id="main" role="main">
		{% block detail %}{% endblock %}
	</main>

	{{ include('includes/footer.php') }}

	{% block js %}{% endblock %}
	<!--<script id="navigation-data" type="application/json">{{ Navigation.JSON }}</script>-->
	{% block json %}{% endblock %}
</body>
</html>
