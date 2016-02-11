<?php 

/**
 * Exporter for the newmediakassel.com website – Uses the json API as input.
 *
 * @author  Jörn Röder <me@joernroeder.de>
 * 
 */

// ---

$apiPath = 'http://wwwwwwwww.newmediakassel.com/api/v2/Project';

function storeUrlToFilesystem($url, $localFile) {
	try {
		$data = file_get_contents($url);
		$handle = fopen($localFile, "w");
		fwrite($handle, $data);
		fclose($handle);
		return true;	
	} catch (Exception $e) {
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	return false;
}

// http://stackoverflow.com/a/2955878/520544
function slugify($text) { 
  // replace non letter or digits by -
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  if (empty($text))
  {
    return 'n-a';
  }

  return $text;
}

$json = json_decode(file_get_contents($apiPath . '.json'));


foreach ($json as $post) {
	if (!$post->IsPublished) {
		continue;
	}
	

	$title = $post->Title;
	$date  = $post->Date;
	$slug = slugify($title);
	$content = $post->Text;
	
	$authors = array();

	foreach ($post->Persons as $person) {
		$authors[] = $person->FirstName . ' ' . $person->Surname;
	}

	$authors = join($authors, ', ');

	// date parsing
	list($year, $month) = explode('-', $date);

	// cleanup vimeo player
	$content = str_replace('?color=c9ff23', '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=c9ff23', $content);
	$content = str_replace('?portrait=0', '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0', $content);

	$string = <<<HEREDOC
# $title

Date: {$year}/{$month}/01

Authors: $authors

---
---

$content

HEREDOC;

	echo "\n" . $title . "\n";

	$folderPath = './' . $year . $month . '-' . $slug;

	// create project folder
	mkdir($folderPath);

	// search in content for images, download them to the new project folder and add the markdown equivalent to the content
	$string = preg_replace_callback('/\[img (\d+)\]/i', function ($matches) {
		global $folderPath, $post;

		$imageId = $matches[1];
		$url = '';

		foreach ($post->Images as $img) {
			if ($img->ID != $imageId) {
				continue;
			}

			$url = $img->Urls->_1680->Url;
			break;
		}

		echo "-\t" . $url ."\n";


		/*preg_match('/src="(.*?)"/', $matches[0], $imgMatches);

		$url = $imgMatches[1];
		$url = str_replace('www.neuemedienkassel.de', 'old.newmediakassel.com', $url);

		*/
		// not from the uploads directory. returning old content
		if (strpos(strtolower($url), '/uploads/') === false) {
			return $matches[0];
		}

		$imgName = basename($url);
		$imgName = split('-', $imgName);
		array_shift($imgName);

		$imgName = join('-', $imgName);

		storeUrlToFilesystem($url, $folderPath . '/' . $imgName);
		
		return '![](' . $imgName . ')';
	}, $string);
	
	// write markdown file
	file_put_contents($folderPath . '/' . $slug . '.md', $string);
}

echo "\n\nDONE!!!";


