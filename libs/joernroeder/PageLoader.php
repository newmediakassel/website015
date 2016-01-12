<?php

namespace joernroeder;

class PageLoader extends ContentLoader {

	/**
	 * project name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * the content extractor
	 *
	 * @var \joernroeder\ProjectParser
	 */
	protected $parser;

	public function __construct($config, $name, $folderName = null) {
		parent::__construct($config);
		
		$this->name = $name;

		$folderName = $folderName ? $folderName : $name;
		$this->folder = $this->path . '/' . strtolower($folderName);

		$this->parser = $this->createParser($this->folder);
	}

	public function getPageFile() {
		return $this->folder . '/' . $this->name . '.md';
	}

	public function load() {
		$projectFile = $this->getPageFile();

		if (!file_exists($projectFile)) {
			return false;
		}

		if ($this->isCacheEnabled() && ($cache = $this->loadCache(filemtime($projectFile)))) {
			return $this->createObject($cache->data, $cache->meta);
		}
		
		$file = $this->loadFile($projectFile);
		$data = $this->parser->parse($file->data);

		if ($this->isCacheEnabled()) {
			$this->saveToCache($data, $file->meta);
		}

		// todo pass meta to object
		return $this->createObject($data, $file->meta);	
	}

	protected function createParser($folder) {
		return new PageParser($folder);
	}

	protected function createObject($data, $meta) {
		return new Page($this->config, $data, $meta);
	}

	protected function getCacheFileName() {
		return $this->name;
	}
}