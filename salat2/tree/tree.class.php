<?php

// ------------------------------------------------------------------------------------------------------------ |
// BUILD TREE LEVEL NODE - CALLED FROM THE TOP
// ------------------------------------------------------------------------------------------------------------ |
function BuildNode($tree,$parentid,$catid,$is_next,$lastfill=''){
	GLOBAL $_LANG;
	$children = $tree["{$catid}"]; // get children array
	$is_father = (COUNT($children)>0)?1:0; // is this node has children
	$currentcat = $tree["{$parentid}"]["{$catid}"];
	$icon = (($currentcat['icon']!='')?$currentcat['icon']:'folder_close.gif');
	// print current node
	print "<div>";
	print $lastfill; // output backwards fills (vert\blank)
	if ($is_next==1){
		if ($is_father==1){ print "<img id='sign_".$catid."' src='tree/icons_".$_LANG['salat_dir']."/p_node_r.gif' border='0' align='absmiddle' style='cursor:hand;' onclick=\"OpenCloseNode('$catid')\" height=22 width=16><img id='folder_".$catid."' src='tree/icons_".$_LANG['salat_dir']."/".$icon."' border='0' align='absmiddle' style='cursor:hand;' onclick=\"OpenCloseNode('$catid')\" height=20 width=20>";
		}else{ print "<img id='sign_".$catid."' src='tree/icons_".$_LANG['salat_dir']."/node_r.gif' border='0' align='absmiddle' height=22 width=16>"; }
	}else{
		if ($is_father==1){ print "<img id='sign_".$catid."' src='tree/icons_".$_LANG['salat_dir']."/pb_node_r.gif' border='0' align='absmiddle' style='cursor:hand;' onclick=\"OpenCloseNode('$catid')\" height=22 width=16><img id='folder_".$catid."' src='tree/icons_".$_LANG['salat_dir']."/".$icon."' border='0' align='absmiddle' style='cursor:hand;' onclick=\"OpenCloseNode('$catid')\" height=20 width=20>";
		}else{ print "<img id='sign_".$catid."' src='tree/icons_".$_LANG['salat_dir']."/b_node_r.gif' border='0' align='absmiddle' height=22 width=16>"; } }
	// output input if any specified
	if ($currentcat['input']['type']=='checkbox' || $currentcat['input']['type']=='radio') print "<input type='".$currentcat['input']['type']."' value='yes' ".$currentcat['input']['status']." onclick=''>";
	// output node title and link
	print "<span id='node_".$catid."' class='spannode' ".(($currentcat['style']!='')?'style=\''.$currentcat['style'].'\'':'')." title='".$currentcat['alt']."' onclick=\"NodeSelect('".$catid."')\" onmouseover=\"nodeMOver('".$catid."','spannodehover')\" onmouseout=\"nodeMOut('".$catid."','spannode')\">";
	if ($is_father==0) print "<img src='tree/icons_".$_LANG['salat_dir']."/".$icon."' border=0 align=absmiddle height=20 width=20>";
	print htmlspecialchars(stripslashes($currentcat['title']));
	print "&nbsp;</span>";
	print "</div>";
	// loop current node childs - if any
	if ($is_father==1){
		$loopcount = COUNT($children);
		$loopindex = 0;
		print "<div id='child_".$catid."' style='display:none;'>";
		foreach ($children as $childid => $childdetails){
			$loopindex++;
			BuildNode($tree,$catid,$childid,($loopcount>$loopindex?1:0),$lastfill.($is_next==1?'<img src=\'tree/icons_'.$_LANG['salat_dir'].'/vert.gif\' border=0 align=absmiddle>':'<img src=\'tree/icons_'.$_LANG['salat_dir'].'/blank.gif\' border=0 align=absmiddle>'));
		}
		print "</div>";
	}
}


// ------------------------------------------------------------------------------------------------------------ |
// BUILD TOP LEVEL NODES
// ------------------------------------------------------------------------------------------------------------ |
function BuildTree($tree,$perms,$treetitle,$treetitlestyle,$treetitleclass,$treelogo,$treefile){
	GLOBAL $_LANG;

	// check for user permission

	if (is_array($perms)){ $tree = TreeCheckPermissions($tree,$perms); }
	else{ print "<div style='color:gray;' align=center>".$_LANG['salat_menu_nop']."</div>"; return (false); }
	// output tree title (text\image)
	if (is_file($treelogo)) print "<span style='".$treetitlestyle."' class='".$treetitleclass."'><img src='".$treelogo."' border=0 align=absmiddle title='".$treetitle."'></span><br>";
	elseif ($treetitle!='') print "<span style='".$treetitlestyle."' class='".$treetitleclass."'>".$treetitle."</span><br>";
	// loop all the parents
	$loopcount = COUNT($tree['0']);
	$loopindex = 0;
	foreach ($tree['0'] as $catid => $catdetails){
		$loopindex++;
		// check inner-tree permissions
		if ((substr($catid,0,1)=="p")){
			if (in_array(substr($catid,1),$perms[$perms['-1'][0]])){
				// build current node
				BuildNode($tree,'0',$catid,($loopcount>$loopindex?1:0),'');
			}
		}else{
			// build current node
			BuildNode($tree,'0',$catid,($loopcount>$loopindex?1:0),'');
		}
	}
}

// ------------------------------------------------------------------------------------------------------------ |
// RETURNS PROCESSES FROM TREE WHICH EXISTS ALSO IN PERMS
// ------------------------------------------------------------------------------------------------------------ |
function TreeCheckPermissions($tree,$perms){
	$tmpArr = Array();
	if (is_array($perms['-1'])){ // if permission to view whole tree (like: shop_categories)
		$tmpArr = $tree;
	}else{
		foreach ($perms as $key => $row)
			foreach ($row as $index => $val)
				if (isset($tree[$key][$val]))
					$tmpArr[$key][$val] = $tree[$key][$val];
	}
	return ($tmpArr);
}


?>