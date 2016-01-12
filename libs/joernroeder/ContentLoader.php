<?php

namespace joernroeder;

abstract class ContentLoader {

	protected $config = null;

	/**
	 * project folder path
	 *
	 * @var string
	 */
	protected $folder;

	public static $cacheFolder = 'content';
	public static $cachePrefix = 'cache';

	public function __construct($config) {
		$this->config = $config;

		$this->path = realpath($this->config->get('content.path'));
		$this->folder = $this->path;
	}

	/**
	 * loads a file and converts it to utf-8 charset
	 * 
	 * {@link http://stackoverflow.com/questions/2236668/file-get-contents-breaks-up-utf-8-characters}
	 *
	 *  todo return file meta data (last modified)
	 *  
	 * @param  string $file name of the file to load
	 *
	 * @return string       array(content => file content, meta => array(lastmodified ->))
	 */
	protected function loadFile($file) {
		$content = file_get_contents($file);
		
		return (object) array(
			'data'	=> mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)),
			'meta'	=> (object) array(
				'lastModified' => filemtime($file),
				'sha1Hash' => sha1($content)
			)
		);
	}

	// todo: add last modified timestamp to cache file
	protected function getCacheFilePath() {
		return $this->config->get('cache.path') . '/' . static::$cacheFolder . '/' . static::$cachePrefix . '-' . $this->getCacheFileName();
	}

	// todo check if loaded cache file is still valid
	protected function loadCache($lastModified = -1, $cacheValidationToken = '') {
		$cacheFile = $this->getCacheFilePath();
		
		if (file_exists($cacheFile)) {
			$file = $this->loadFile($cacheFile);

			// check if original content file was modified
			if ($lastModified !== -1 && $lastModified > $file->meta->lastModified) {
				return false;
			}

			// check sha1 values
			if ($cacheValidationToken && $cacheValidationToken != $file->meta->sha1Hash) {
				return false;
			}
			
			return json_decode($file->data);
		}

		return false;
	}

	// todo add last modified timestamp to cache file
	protected function saveToCache($data, $meta) {
		$cacheFile = $this->getCacheFilePath();
		$cache = array(
			'data' => $data,
			'meta' => $meta
		);

		file_put_contents($cacheFile, json_encode($cache, 0));
	}

	protected function isCacheEnabled() {
		return $this->config->get('cache.enabled');
	}

	protected function isCacheValid() {

	}

	protected abstract function getCacheFileName();

}