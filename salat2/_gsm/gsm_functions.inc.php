<?php
// General Constants -------------------------
define("DEFAULT_PRIORITY", "0.5");
define("DEFAULT_FREQUENCY","daily");
// General Functions -------------------------

function create_xml_header(){
	print "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	print "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";
}

function create_entity($p_loc,$p_lastmod,$p_changefreq,$p_priority){
	//$loc = htmlentities($p_loc,ENT_COMPAT);	
	$loc=$p_loc;
	$loc = str_replace("'","&apos;",$loc);
	print "\t<url>\n";
	print "\t\t<loc>" . $loc . "</loc>\n";	
	$lastmod = ($p_lastmod != "" ? $p_lastmod : date('Y-m-d'));
	print "\t\t<lastmod>". $lastmod ."</lastmod>\n";		
	$changefreq = 	($p_changefreq !="" ? $p_changefreq : DEFAULT_FREQUENCY);
	print "\t\t<changefreq>" . $changefreq  . "</changefreq>\n";
	$priority = ($p_priority != "" ? $p_priority : DEFAULT_PRIORITY);	
	print "\t\t<priority>". $priority ."</priority>\n";
	print "\t</url>\n";			
}


function create_xml_footer(){
	print "</urlset>\n";
}

function build_static_pages_entities(){
	GLOBAL $static_pages;
	
	foreach  ($static_pages as $key => $i){
		$loc = $i['loc'];
		$lastmod = $i['lastmod'];
		$changefreq = $i['changefreq'];
		$priority = $i['priority'];
		create_entity($loc,$lastmod,$changefreq,$priority);
	}
}

?>