<?php

namespace joernroeder;

class Page {

	/**
	 * holds the data
	 *
	 * @var stdclass
	 */
	protected $data;

	/**
	 * holds the meta data lastModified etc.
	 *
	 * @var [type]
	 */
	protected $meta;

	/**
	 * optional template name
	 *
	 * @var string
	 */
	protected $template;

	public static $defaultTemplate = 'page'; // todo change it to a basic page template

	public function __construct($config, $data = null, $meta = null, $template = null) {
		$this->config = $config;
		$this->data = (object) $data;
		$this->meta = $meta;

		$this->template = isset($data->Template) && !$template ? $data->Template : $template;
	}

	/**
	 * returns the project data
	 *
	 * @return array project data
	 */
	public function getData() {
		return (object) $this->data ? $this->data : array();
	}

	// todo: lowercase keys
	public function setData($data) {
		$this->data = $data;
	}

	public function addData($data) {
		$this->setData((object) array_merge(
			(array) $this->getData(),
			(array) $data)
		);
	}

	// todo: lowercase key lookup
	public function get($key) {
		if (!$this->data) return null;

		return property_exists($this->data, $key) ? $this->data->$key : null;
	}

	/**
	 * returns the data as json string
	 *
	 * @return string
	 */
	public function toJSON() {
		return json_encode($this->getData());
	}

	public function getTemplateData() {
		$data = array_merge(
			(array) $this->config->getTemplateData(),
			(array) $this->getData()/*,
			array('JSON' => $this->toJSON())*/
		);

		return $data;		
	}

	/**
	 * returns the template name
	 * 
	 * @return string
	 */
	public function getTemplate() {
		return $this->template ? $this->template . '.php' : static::$defaultTemplate . '.php';
	}

	/*public function render() {
		//print_r();
		return $render($this->getTemplate(), $this->getTemplateData());
	}*/

}