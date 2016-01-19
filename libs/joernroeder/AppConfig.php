<?php

namespace joernroeder;

abstract class AppConfig {

	protected static $config = array();

	public abstract function set($key, $value);
	public abstract function pathFor($key, $urlSegements);
	public abstract function render(\joernroeder\Page $page);
	public abstract function notFound();

	public function get($key) {
		if (isset(static::$config[$key])) {
			return static::$config[$key];
		}

		return null;
	}

	public function getNavigation() {
		return array(
			'Navigation' => $this->get('app.navigation')
		);
	}

	protected function setNavigation($navigation) {
		$this->set('app.navigation', $navigation);
	}

	public function initNavigation($currentUrl) {
		$nav = new \joernroeder\NavigationLoader($this);
		$nav->loadNavigation();
		$links = $nav->getLinks($currentUrl);

		// store navigation loader instance
		$this->set('navigation.loader', $nav);

		// save links for template
		$this->setNavigation($links);
	}

	public function getFolderName($name) {
		// todo: construct navigation loader inside config
		$folderName = $this->get('navigation.loader')->getFolderName($name);
	
		// couldn't resolve folder path for the given name.
		if (!$folderName) {
			return $this->notFound();
		}

		return $folderName;
	}

	public function renderPage($name) {
		$folderName = $this->getFolderName($name);

		$pageLoader = new \joernroeder\PageLoader($this, $name, $folderName);
		$page = $pageLoader->load();

		// couldn't load page -> returning 404
		if (!$page) {
			return $this->notFound();
		}

		return $this->render($page);
	}
	
	public function getTemplateData() {
		return array_merge(
			$this->getNavigation(),
			$this->get('template.data')
		);
	}

	// add 
	public function updateNavigationItem(&$navItem, $page) {
	}

}