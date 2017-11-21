<?php

require __DIR__ . '/../vendor/autoload.php';

use joernroeder\Pocomd\Config;
use joernroeder\Pocomd\Page;
use joernroeder\Pocomd\NavigationLoader;

/**
 * The AppConfig class acts as a middleware between the content loaders and the routing framework (http://flightphp.com/)
 */
class MyAppConfig extends Config {

	public static $defaults = array(
		'debug'						=> true,
		'logErrors'					=> true,
		'showErrors'				=> true,
		'cache.enabled'				=> true,
		'cache.path'				=> '../cache/',
		'content.path'				=> '../content/',
		'assets.path'				=> 'assets/',
		'navigation.sortreverse'	=> true,
		'template.data'				=> array(
			'MaxWidth'	=> '800px'
		)
	);

	public function get($key) {
		if ($val = parent::get($key)) {
			return $val;
		}

		return Flight::has($key) ? Flight::get($key) : null;
	}

	public function set($key, $value) {
		return Flight::set($key, $value);
	}

	public function pathFor($key, $urlSegements) {
		$urlSegements = !is_array($urlSegements) ? array($urlSegements) : $urlSegements;

		return '/' . join('/', $urlSegements);
	}

	public function render(Page $page) {
		return Flight::view()->display($page->getTemplate(), $page->getTemplateData());
	}

	public function notFound() {
		return Flight::notFound();
	}

	public function updateNavigationItem(&$navItem, $page) {
		$additionalKeys = array('Type', 'Date', 'ShowInTicker');

		foreach ($additionalKeys as $key) {
			if ($val = $page->get($key)) {
				$navItem[$key] = $val;
			}
		}
	}

  	public function updateTemplateData(&$templateData) {
		// Iterate through the navigation items and add <ticker> items to the template.
		$this->addTickerItems($templateData);
		$this->updateNavigationStructure($templateData);
		$this->addLogos($templateData);
	}

	private function addToTemplateData(&$templateData, $key, $value) {
		$templateData = array_merge(
				$templateData,
				array(
					$key => $value
				)
		);
	}

	private function addTickerItems(&$templateData) {
		$nav = $templateData['Navigation'];
		$tickerItems = array();

		foreach ($nav as $item) {
			if(isset($item['ShowInTicker']) && $item['ShowInTicker']) {
				$tickerItems[] = $item;
			}
		}

		$this->addToTemplateData($templateData, 'TickerItems', $tickerItems);
	}

	private function addLogos(&$templateData) {
		$dir = $this->get('assets.path') . 'logos';
		$logos = array();

		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file === '.' or $file === '..') continue;

        			$logos[] = '/' . $dir . '/' . $file;
				}

        		closedir($dh);
			}
		}

		$this->addToTemplateData($templateData, 'Logos', $logos);
	}

	private function updateNavigationStructure(&$templateData) {

	}

	// override factories

	public function createNavigationLoader($context) {
		return new MyNavigationLoader($context);
	}
}

// ----------------------------------------

class MyNavigationLoader extends NavigationLoader {

	// adds a year seperator to the navigation list
	public function getLinks($currentUrl = null) {
		$links = parent::getLinks($currentUrl);
		$currentDate = null;
		$items = array();

		$currentYear = null;

		foreach ($links as $link) {
			//print_r($link);
			$date = isset($link['Date']) ? $link['Date'] : null;

			// check if the link has a new date.
			// add it to the items list an reset the date.
			if ($date && $currentDate != $date) {
				if ($currentYear && (!empty($currentYear['Typed']) || !empty($currentYear['Project']))) {
					// append previous year
					$items[] = $currentYear['Year'];

					$types = [];
					foreach ($currentYear['Typed'] as $typed) {
						if (!in_array($typed['Type'], $types)) {
							if (is_array($typed['Type'])) {
								foreach ($typed['Type'] as $t) {
									if (!in_array($t, $types)) {
										$types[] = $t;
									}
								}
							}
							else {
								$types[] = $typed['Type'];
							}
						}
					}

					if (!empty($currentYear['Typed'])) {
						$items[] = array(
							'Title' => join(' // ', $types),
							'IsSubHeadline' => true,
							'Type' => 'subheadline'
						);

						foreach ($currentYear['Typed'] as $typed) {
							$items[] = $typed;
						}
					}

					if (!empty($currentYear['Project'])) {
						$items[] = array(
							'Title' => 'Projects',
							'IsSubHeadline' => true,
							'Type' => 'subheadline'
						);

						foreach ($currentYear['Project'] as $project) {
							$items[] = $project;
						}
					}
				}

				$currentYear = array(
					'Year' => array(
						'Title' => $date,
						'IsDate' => true
					),
					'Typed' => array(),
					'Project' => array()
				);

				$currentDate = $date;
			}

			// push link to items array
			$typed = isset($link['Type']) && $link['Type'] ? 'Typed' : 'Project';
			$currentYear[$typed][] = $link;
		}

		return $items;
	}
}

/**
 * Apply AppConfig and global settings to Flight.
 */
Flight::set('config', new MyAppConfig());
Flight::set('flight.log_errors', Flight::get('config')->get('logErrors'));

if (Flight::get('config')->get('showErrors')) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

/**
 * Initiate Twig, and register to Flight
 */
$twigLoader = new Twig_Loader_Filesystem('../templates');
$twigConfig = array(
	'cache'	=>	Flight::get('config')->get('cache.enabled') ? Flight::get('config')->get('cache.path') . 'twig/' : false,
	'debug'	=>	Flight::get('config')->get('debug'),
);
Flight::register('view', 'Twig_Environment', array($twigLoader, $twigConfig), function($twig) {
	$twig->addExtension(new Twig_Extension_Debug()); // Add the debug extension

	$currentUrl = sprintf(
		"%s://%s%s",
		isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
		$_SERVER['SERVER_NAME'],
		$_SERVER['REQUEST_URI']
	);

	$twig->addGlobal('CurrentUrl', $currentUrl);
});

/**
 * Add navigation hook on start, get all links and store them in config -> 'app.navigation'
 */
Flight::before('start', function(&$params, &$output) {
	if (Flight::has('navigation.loader')) return;

	Flight::get('config')->initNavigation(Flight::request()->url);
});


// ! --- ROUTE: Index ---------------------------

Flight::route('/', function() {
	return Flight::view()->display('index.php', Flight::get('config')->getTemplateData());
});

// ! --- ROUTE: About ---------------------------

Flight::route('/we', function() {
	return Flight::get('config')->renderPage('about');
});


// ! --- ROUTE: Sitemap -------------------------

/**
 * Publishes all routes in the /sitemap.xml
 */
Flight::route('/sitemap.xml', function () {
	$items = Flight::get('navigation.loader')->getNavigationItems();
	$sitemap = array();

	foreach ($items as $item) {
		$sitemap[] = array(
			'Url' => $item->get('Url'),
			'DateTime' => $item->get('DateTime')
		);
	}

	Flight::response()->header('Content-Type', 'application/xml');
	Flight::view()->display('sitemap.php', array(
		'Sitemap' => $sitemap
	));
});


// ! --- ROUTE: Project -------------------------

Flight::route('/@name', function ($name) {
	return Flight::get('config')->renderPage($name);
});


// ! --- ROUTE: 404 - Not Found -----------------

Flight::map('notFound', function() {
	Flight::view()->display('404.php', Flight::get('config')->getTemplateData());
	Flight::stop(404);
});


// ! --- Kick things off! -----------------------

Flight::start();
