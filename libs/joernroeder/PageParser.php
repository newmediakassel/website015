<?php
namespace joernroeder;

//use \Michelf\Markdown as Markdown;
use \Michelf\MarkdownExtra as Markdown;
use Gregwar\Image\Image;

class PageParser {
	
	protected $path;

	public function __construct($path) {
		$this->path = $path;
	}

	public function getTitle(&$data) {
		// copied regular expression from markdown.php:793 
		$found = preg_match('{
				^(\#{1})	# $1 = string of #\'s
				[ ]*
				(.+?)		# $2 = Header text
				[ ]*
				\#*			# optional closing #\'s (not counted)
				\n+
			}xm', $data, $matches);

		if ($found) {
			$data = str_replace($matches[0], '', $data);
			return $matches[2];
		}
		else {
			return false;
		}
	}

	public function getMetas(&$data) {
		$metas = array();
		$datas = preg_split('{
			^(-{3})	# at least 3 dashes
			\n+
			(-{3})
			\n+
		}xm', $data);

		if (sizeof($datas) == 1) {
			return $metas;
		}

		$data = $datas[1];

		$metaPairs = preg_split('/\n+/', $datas[0]);

		if ($metaPairs && !empty($metaPairs)) {
			// extract loop
			foreach ($metaPairs as $pair) {
				$pairData = preg_split('/\:\s+?/', $pair);
				
				if (sizeof($pairData) == 2) {
					$metas[$pairData[0]] = $this->commaToArray($pairData[1]);
				}

			}
		}

		return $this->updateMetas($metas);
	}

	protected function commaToArray($data) {
		$list = preg_split('/(\s+)?,(\s+)?/', $data);

		if (!empty($list)) {
			foreach ($list as $i => $item) {
				$list[$i] = $this->extractLinks($list[$i]);
			}

			$data = $list;
		}

		if (sizeof($data) == 1 && !is_array($data[0])) {
			$data = $data[0];
		}
		
		return $data;
	}

	protected function extractLinks($data) {
		$links = array();

		preg_match_all('/\[(.*?)\]\((\S*)\)/', $data, $matches);

		if (!empty($matches) && !empty($matches[0])) {
			$data = array(
				'Title' => $matches[1][0],
				'Url' => $matches[2][0]
			);
		}

		return $data;
	}

	protected function processImages(&$data, $width, $title = null) {

		$found = preg_match_all('{
			(				# wrap whole match in $1
			  !\[(\w+)?\]
			  \(			# literal paren
				(
					\S*	# src url = $3
				)
			  \)
			)
			}xm', $data, $matches);

		if (!$found) return false;

		for ($i = 0; $i < $found; $i++) {
			$tag = $matches[0][$i];
			$additionalClasses = $matches[2][$i];
			$imageFile = $matches[3][$i];

			$imagePath = $this->path . '/' . $imageFile;
			$url = '';

			if (file_exists($imagePath)) {
				// @todo check if it's an image (jpg, gif, png)
				$contentType = explode('/', mime_content_type($imagePath));

				if ($contentType[0] == 'image' && in_array($contentType[1], Image::$types)) {
					$image = Image::open($imagePath); //->setName($title);
					
					if ($title) {
						$image->setPrettyName($title);
					}

					try {
						$url = '![](/' . $image->cropResize($width, PHP_INT_MAX)->guess(100) . ')';
					}
					catch (Exception $e) {
						echo $image;
						echo $e->getMessage();
					}
				}
				// @todo we're going to copy the file to the public assets directory
				else {
					$url = '![](/' . $imageFile . ')';
				}
			}
			else {
				$url = "<strong style=\"color:red;display:block\">Couldn't process $imageFile</strong>";
			}

			if ($additionalClasses == 'screenshot') {
				$url = '<span class="screen_header"></span>' . $url . '<span class="screen_footer"></span>';
			}

			$data = str_replace($tag, $url, $data);
		}
	}

	protected function getMeta($metas, $key) {
		return isset($metas[$key]) ? $metas[$key] : false;
	}

	protected function parseDate($date, &$metas) {
		$date = strtotime($date);
			
		if (date('Y', $date) !== 1970) {
			$metas['Date'] = date('Y', $date);
			$metas['DateTime'] = date('c', $date);
		}
	}

	protected function updateMetas($metas) {
		if ($date = $this->getMeta($metas, 'Date')) {
			$this->parseDate($date, $metas);
		}

		return $metas;
	}

	protected function getDescription($content) {
		$maxLength = 160;

		$content = trim(strip_tags($content));

		if( strlen( $content ) > $maxLength ) {
			$cut_content = substr( $content, 0, $maxLength ); //cut at 200 chars
			$last_space = strrpos( $cut_content, " " ); //find the position of the last space in the 200 chars text
			$short_content = substr( $cut_content, 0, $last_space ); //cut again at the last space
			$end_content = $short_content."..."; // add three dots
			
			$content = $end_content;
		}

		return $content;
	}

	protected function getWidth($metas = null) {
		$width = 1024;
					
		if ($metas) {
			$width = (int) $this->getMeta($metas, 'Width');
		}

		return $width;
	}

	public function parse($data, $additionalData = array()) {
		$title = $this->getTitle($data);
		$metas = $this->getMetas($data);

		$keywords = (array) $this->getMeta($metas, 'Keywords');
		$this->processImages($data, $this->getWidth($metas), join('-', array_merge(array($title), $keywords)));

		$content = Markdown::defaultTransform($data);

		return array_merge($metas, array(
			'Title'			=> $title,
			'Content'		=> $content,
			'Description'	=> $this->getDescription($content)
		), $additionalData);
	}
}