<?php
/* FILENAME: smart-404.php
 *
 * Copying and distribution of this file, with or without modification,
 * are permitted in any medium without royalty provided the copyright
 * notice and this notice are preserved including information about me
 * and my site http://www.thejohnnyoshow.com/coding-corner.html :-)
 * This file is offered as-is, without any warranty.
 *
 * This software is free to use and alter as you need, however please don't
 * sell it, and please if possible direct others to my site if they want a
 * copy (http://www.thejohnnyoshow.com) Please like and share my videos :-)
 *
 * How to use this software...
 * edit the values in the section below to match your site requirements
 *
 * Add the error document setting to the .htaccess file
 * 
 * example .htaccess file
   ErrorDocument 404 /thejohnnyoshow/smart-404.php
 * 
 * 
 * 
 */

// ------------ Configure below this line ----------------

$sitemap = "../sitemap.xml";
$defaultURL = 'http://www.thejohnnyoshow.com';
$defaultExtention= 'html';

$badurl = $_SERVER ['REQUEST_URI'];

$extention = pathinfo ( parse_url ( $badurl, PHP_URL_PATH ), PATHINFO_EXTENSION );

if (!$extention)
	$extention=$defaultExtention;

$badurl = str_replace ( '.' . $extention, '', $badurl );

$searchfor = explode ( ',', preg_replace ( '/\s+/', ',', preg_replace ( '/[^a-z0-9]/i', ' ', $badurl ) ) );

$DomDocument = new DOMDocument ();
$DomDocument->preserveWhiteSpace = false;
$DomDocument->load ( $sitemap );
$DomNodeList = $DomDocument->getElementsByTagName ( 'loc' );
$bestmatch=$defaultURL;

foreach ( $DomNodeList as $url ) {
	$check = $url->nodeValue;
	$checkextention = str_replace ( '_', '', pathinfo ( parse_url ( $check, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	
	if (strtolower ( $checkextention ) == strtolower ( $extention )) {
		
		$last = findMatch ( $searchfor, $check );
		if ($last > $highest) {
			$highest = $last;
			$bestmatch = $check;
		}
	}
}

header ( 'Location: ' . trim ( $bestmatch ), 404 );
function findMatch($searchfor, $in) {
	for($i = 0; $i < count ( $searchfor ); $i ++) {
		
		if (strlen ( $searchfor [$i] ) > 1){ 
			$count=substr_count ( $in, $searchfor [$i] );
			if($count>5)
				$count=5;
			$matches+=(1-($count/10))*$count;
		}
	}
	return $matches;
}
?>