<?php

//define("_PAGING_NumOfItems"					,9);	// number of rows in page
define("_PAGING_NumOfLinks"					,3);	// number of links in page (before and after current pagenum)
define("_PAGING_Defualt_Template"			,'<a href="?pagenum={PAGENUM}">{CONTENT}</a>');
define("_PAGING_Next"						,"Next >");
define("_PAGING_Prev"						,"< Back");
define("_PAGING_First"						,"|< First");
define("_PAGING_Last"						,"Last >|");

/*
$max_forced_rows - set "fake" limit on the acctual paging limit
*/
function getSQLPagingArr($query,$pagenum=0,$pages=0,$tmpl=_PAGING_Defualt_Template,$first='',$last='',$nextPH='',$prevPH='',$next=_PAGING_Next,$prev=_PAGING_Prev,$limit=_PAGING_NumOfItems,$force_total_counting = false,$num_of_links = _PAGING_NumOfLinks,$max_forced_rows=0 ){
	GLOBAL $mdlName,$mdlID,$objID,$pagingStr;
	$Db= Database::getInstance();

	if(!function_exists('_make_paging_link')){
		function  _make_paging_link($tmpl,$pagenum,$pages,$content){
			return (str_replace(array('{PAGENUM}','{PAGES}','{CONTENT}'),array($pagenum,$pages,$content),$tmpl));
		}
	}
	// set RESULT
	if($pagenum<=0) $pagenum = $_REQUEST[_PAGING_Defualt_REQUEST_Pagenum];
	if($pages<=0) $pages = $_REQUEST[_PAGING_Defualt_REQUEST_Pages];
	if ($pagenum<=0){ $pagenum = 1; $firstPageLink = 1; $pages = 0; }
	if ($pagenum > _PAGING_NumOfLinks) $firstPageLink = $pagenum - _PAGING_NumOfLinks;
	else $firstPageLink = 1;
	// calc total pages, if needed
	$pages = $_SESSION['paging'][$mdlID."-".$objID."-".$pagingStr];//getPaging();
	if($force_total_counting){
		$pages = 0;
	}
	if ($pages<=0){
		/*slice out the clause - from the last `FROM` before the first `JOIN` until the end... 	
		$position_of_first_join = (strpos(strtolower($query),'join'));
		$query_clouse = substr($query,strrpos(substr(strtolower($query),0,$position_of_first_join),"from"));*/
		$tmp_query = strtolower(trim($query));
		$selectCounter=1;
		$selectPos=0;
		$fromPos=0;
		$offsetPos=1;
		while ($selectCounter > 0){
			$selectPos = strpos($tmp_query, 'select ', $offsetPos+1);
			$fromPos = strpos($tmp_query, 'from ', $offsetPos+1);
			if($selectPos===false){
				$offsetPos=$fromPos;
				$fromPos = strpos($tmp_query, 'from ', $fromPos+1);
				if($fromPos!==false){
					$offsetPos=$fromPos;
				}
				break;
			}
			if($selectPos < $fromPos){
				$selectCounter++;
				$offsetPos=$selectPos;
			}else{
				$selectCounter--;
				$offsetPos=$fromPos;
			}
		}
		$query_clouse = substr($query, $offsetPos);
		// exception of UNION
		if (strpos(strtolower($query),' union ') > 0){
			$query_clouse = substr($query,strpos(strtolower($query),"from"));
		}
		$query_paging = "SELECT COUNT(*) as `Rows` ".$query_clouse;
		if(stristr($query_paging,"order by")){
			$query_paging = substr($query_paging,0,stripos($query_paging,"order by"));
		}
		$result_paging = $Db->query($query_paging);
		//print "mysqlNumRows:".mysql_num_rows($result_paging)."<br/>";
		//print "mysql_query:".$query_paging."<br/>";
		if ($max_forced_rows==0){
			if(strpos(strtolower($query_paging),"group by")===false) $rows = mysqli_result($result_paging,0,0);
			else $rows = mysql_num_rows($result_paging);
		}else{
			$rows = $max_forced_rows;
		}
		if($rows > $limit) $pages = ceil($rows/$limit);
		else $pages = 1;
		$_SESSION['paging'][$mdlID."-".$objID."-".$pagingStr] = $pages;//
		//setPaging($pages);
		if ($pagenum > $pages) $pagenum = $pages; 
		$lastPageLink = 0;
	}
	if ($pages > _PAGING_NumOfLinks && ($pagenum+_PAGING_NumOfLinks) < $pages) $lastPageLink = $pagenum + _PAGING_NumOfLinks;
	else $lastPageLink = $pages;
	if ($pagenum > 0 && $pagenum <= $pages) $start_at_page = ($pagenum-1)*$limit;
	else $start_at_page = 0;
	$query_limit = " LIMIT ".$start_at_page.",".$limit;
	$query_limited = $query.' '.$query_limit;
	$result = $Db->query($query_limited) or db_showError(__FILE__,__LINE__,$query_limited);
	
	// set PAGING
	$paging = "";
	if($pages>1){
		// first page link
		if ($first!=''&&$pagenum>1) $paging .= _make_paging_link($tmpl,1,$pages,$first).' ';
		// prev page link
		if($pagenum>1) $paging .= _make_paging_link($tmpl,$pagenum-1,$pages,$prev).' ';
		// place holder instead of prev page link
		elseif ($prevPH!='') $paging .= $prevPH;//_make_paging_link($tmpl,$pagenum-1,$pages,$nextPH).' ';
		
		if (_PAGING_Format == 2){
			// fix MIDDLE links to always show _PAGING_NumOfLinks*2+1 links and the CUR link in the middle
			$firstPageLink = $pagenum - _PAGING_NumOfLinks;
			$lastPageLink = $pagenum + _PAGING_NumOfLinks;
			if ($firstPageLink<1){
				$firstPageLink = 1;
				$lastPageLink = $firstPageLink + 2 * _PAGING_NumOfLinks;
			}
			if ($lastPageLink>$pages){
				$lastPageLink = $pages;
				$firstPageLink = max($lastPageLink - 2 * _PAGING_NumOfLinks , 1);
			}
		}else{
			// show LEFT MORE(...) link
			if($pagenum > _PAGING_NumOfLinks+1 && $first=='') $paging .= ' '._make_paging_link($tmpl,1,$pages,1).' ... ';
		}
		
		// show MIDDLE links
		for ($i=$firstPageLink ; $i<=$lastPageLink ; $i++){
			if($i==$pagenum) $paging .= " <b>{$i}</b>";
			else $paging .= ' '._make_paging_link($tmpl,$i,$pages,$i).' ';
		}
		
		if (_PAGING_Format == 1){
			// show RIGHT MORE(...) link
			if($pages > 0 && $pagenum < $pages-_PAGING_NumOfLinks && $last=='') $paging .= ' ... '._make_paging_link($tmpl,$pages,$pages,$pages).' ';
		}
		
		// show NEXT link
		if($pagenum < $pages) $paging .= ' '._make_paging_link($tmpl,$pagenum+1,$pages,$next);
		// place holder instead of next page link
		elseif ($nextPH!='') $paging .= ' '.$nextPH.' ';
		// fix LAST link, if TRUE set to number of pages
		if ($last===true) $last = $pages;
		// show LAST link
		if ($last!=''&&$pagenum < $pages) $paging .= ' '._make_paging_link($tmpl,$pages,$pages,$last).' ';
	}
	//print "pages:".$pages;
	// return RESULT and PAGING
	return (array(
		'result' => $result,
		'total_rows' => $rows,
		'paging' => $paging,
		'pages' => $pages,
		'query' => $query_limited,
	));
	
}

?>