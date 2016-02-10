<?php 

/**
 * Wordpress Exporter for the old.newmediakassel.com website – Uses a MySQL-Dump as input.
 *
 * @author  Jörn Röder <me@joernroeder.de>
 * 
 */

// ---

$sqlDumpPath = 'query_result.xml';

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

$xml = simplexml_load_string(file_get_contents($sqlDumpPath));

if (!$xml || !$root = $xml->custom) {
	echo "no root element <custom> found.";
	exit;
}

$rows = $root->row;

foreach ($rows as $post) {
	$fieldsMap = array();
	$index = 0;

	foreach ($post as $field) {
		$fieldsMap[$index] = strtolower((string) $field->attributes()->name);
		$index++;
	}

	$title = (string) $post->field[array_search('post_title', $fieldsMap)];
	$date  = (string) $post->field[array_search('post_date', $fieldsMap)];
	$slug = (string) $post->field[array_search('post_name', $fieldsMap)];
	$content = (string) $post->field[array_search('post_content', $fieldsMap)];
	$authors = (string) $post->field[array_search('authors', $fieldsMap)];
	$tags = strToLower((string) $post->field[array_search('tags', $fieldsMap)]);

	// date parsing
	list($year, $month) = explode('-', $date);

	// keywords for search bots
	$keywords = $tags ? 'Keywords: ' . $tags : '';

	// cleanup vimeo player
	$content = str_replace('?color=c9ff23', '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=c9ff23', $content);
	$content = str_replace('?portrait=0', '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0', $content);

	// Exclude Tweets
	if ($authors == 'Tweet @nmkhk') {
		break;
	}

	$string = <<<HEREDOC
# $title

Date: {$year}/{$month}/01

Authors: $authors

$keywords

---
---

$content

HEREDOC;

	$folderPath = './' . $year . $month . '-' . $slug;

	// create project folder
	mkdir($folderPath);

	// search in content for images, download them to the new project folder and add the markdown equivalent to the content
	$string = preg_replace_callback('/(<img) (.*)(\\\?>)/i', function ($matches) {
		global $folderPath;

		preg_match('/src="(.*?)"/', $matches[0], $imgMatches);

		$url = $imgMatches[1];
		$url = str_replace('www.neuemedienkassel.de', 'old.newmediakassel.com', $url);

		// not from the uploads directory. returning old content
		if (strpos($url, '/uploads/') === false) {
			return $imgMatches[0];
		}

		$imgName = basename($url);

		storeUrlToFilesystem($url, $folderPath . '/' . $imgName);

		return '![](' . $imgName . ')';
	}, $string);
	
	// write markdown file
	file_put_contents($folderPath . '/' . $slug . '.md', $string);
}

echo "\n\nDONE!!!";


