<?php

namespace joernroeder;

class NavigationLoader extends ContentLoader {

	protected $folderMap = array();

	protected $links = array();

	protected $navigationItems = array();
	
	private $navigation = array();

	public function __construct($config) {
		parent::__construct($config);

		$cacheKey = $this->getCacheKey();		

		// check cache
		if ($this->isCacheEnabled() && ($cache = $this->loadCache(-1, $cacheKey))) {
			$this->folderMap = (array) $cache->data;
			return;
		}
		
		foreach (new \DirectoryIterator($this->folder) as $file) {
			if ($file->isDot()) continue;

			if ($file->isDir()) {
				$dirName = $file->getFilename();
				
				$indexGiven = preg_match('/(\d+)\-/', $dirName, $matches);
				if ($indexGiven) {
					$index = (int) $matches[1];

					// index already exists. finding the next free slot ...
					while (isset($this->folderMap[$index])) {
						$index++;
					}

					$this->folderMap[$index] = $this->createFolder($dirName, str_replace($matches[0], '', $dirName));
					sort($this->folderMap);
				}
				else {
					$this->folderMap[] = $this->createFolder($dirName, $dirName);
				}
			}
		}

		sort($this->folderMap);

		if ($this->config->get('navigation.sortreverse')) {
			krsort($this->folderMap);
		}

		if ($this->isCacheEnabled()) {
			$this->saveToCache($this->folderMap, (object) array(
				'lastModified'	=> -1,
				'sha1Hash'		=> $cacheKey
			));
		}
		
	}

	protected function createFolder($name, $url) {
		return (object) array(
			'name'	=> $name,
			'url'	=> $url
		);
	}

	/**
	 * iterate through folder and create sha1 hash of names and last modified dates
	 */
	protected function getCacheKey() {
		$folders = array();

		foreach (new \DirectoryIterator($this->folder) as $file) {
			if ($file->isDot()) continue;

			if ($file->isDir()) {
				$folders[] = $file->getFilename() . '-' . $file->getMTime();
			}
		}

		return sha1(join(':', $folders));
	}

	public function loadNavigation() {
		foreach ($this->folderMap as $folder) {
			$folder = (object) $folder;

			$loader = new PageLoader($this->config, $folder->url, $folder->name);
			$project = $loader->load();
			
			if (!$project) {
				continue;
			}
				
			$title = $project->get('MenuTitle') ? $project->get('MenuTitle') : $project->get('Title');
			$url = $this->config->pathFor('project', strtolower($folder->url));

			$link = array(
				'Title' => $title,
				'Url' => $url
			);

			$project->addData($link);

			if (!$project->get('HideInNavigation')) {
				$this->links[] = $link;
			}

			$this->navigationItems[] = $project;
		}
	}
	
	public function getNavigation() {
		return $this->navigation;
	}
	
	public function setNavigation($navigation) {
		$this->navigation = $navigation;
	}

	public function getFolderName($urlSegment) {
		foreach ($this->folderMap as $folder) {
			if ($urlSegment == $folder->url) {
				return $folder->name;
			}
		}

		return false;

	}

	public function toJSON() {
		return json_encode($this->getLinks());
	}

	public function getLinks($currentUrl = null) {
		// add current url state
		for ($i = 0; $i < sizeof($this->links); $i++) {
			if ($currentUrl == $this->links[$i]['Url']) {
				$this->links[$i]['IsActive'] = true;
				break;
			}
		}

		return $this->links;
	}

	public function getNavigationItems() {
		return $this->navigationItems;
	}

	protected function getCacheFileName() {
		return 'navigation';
	}
}