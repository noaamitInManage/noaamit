<script language="javascript">

	/* pre-load tree images */
	var imgPlusTop = new Image(); imgPlusTop.src = "tree/p_node_r.gif";
	var imgPlusBottom = new Image(); imgPlusBottom.src = "tree/pb_node_r.gif";
	var imgMinusTop = new Image(); imgMinusTop.src = "tree/m_node_r.gif";
	var imgMinusBottom = new Image(); imgMinusBottom.src = "tree/mb_node_r.gif";
	var imgFolderOpen = new Image(); imgFolderOpen.src = "tree/folderopen.gif";
	var imgFolderClose = new Image(); imgFolderClose.src = "tree/folder_close.gif";
	var lastNodeID = "";
	
	/* select node by key not by id */
	function NodeByKey(nameKey, mode){ // mode: click,change
		if (treeKeysArr[''+nameKey]) 
			if (mode=='click') NodeClicked(treeKeysArr[''+nameKey]); // do CLICK
			else OpenCloseNode(treeKeysArr[''+nameKey]); // do OPEN\CLOSE
	}
	
	/* activate link */
	function NodeClicked(leafKey){
		if (lastNodeID!='') document.getElementById('node_'+lastNodeID).className = "spanunselect";
		document.getElementById('node_'+leafKey).className = "spanselect";
		lastNodeID = leafKey;
	}
	
	/* node clicked */
	function NodeSelect(leafKey){
		NodeClicked(leafKey);
		// emulate click
		if (treeLinksArr[leafKey+'-type']=="javascript"){
			eval(treeLinksArr[leafKey+'-link']);
		}else if (treeLinksArr[leafKey+'-type']=="html"){
			if (treeLinksArr[leafKey+'-target']=="internal"){
				document.location = treeLinksArr[leafKey+'-link'];
			}else if (treeLinksArr[leafKey+'-target']=="external"){
				window.open(treeLinksArr[leafKey+'-link']);
			}else{
				eval("top."+treeLinksArr[leafKey+'-target']+".document.location = '"+treeLinksArr[leafKey+'-link']+"'");
			}
		}
	}
	
	/* mouse over node */
	function nodeMOver(leafKey,clsname){
		if (lastNodeID!=leafKey) document.getElementById('node_'+leafKey).className = clsname;
	}
	
	/* mouse out of node */
	function nodeMOut(leafKey,clsname){
		if (lastNodeID!=leafKey) document.getElementById('node_'+leafKey).className = clsname;
	}
	
	/* open and close node - folder and sign images */
	function OpenCloseNode(leafKey){
		if (document.getElementById('child_'+leafKey)){
			if (document.getElementById('child_'+leafKey).style.display == "block"){
				CloseLeaf(leafKey);
			}else{
				OpenLeaf(leafKey);
			}
		}
	}
	
	/* open node - folder and sign images */
	function OpenLeaf(leafKey){
		if (document.getElementById('child_'+leafKey)){
			//document.getElementById('folder_'+leafKey).src = imgFolderOpen.src;
			if (document.getElementById('sign_'+leafKey).src==imgPlusTop.src) document.getElementById('sign_'+leafKey).src = imgMinusTop.src;
			else document.getElementById('sign_'+leafKey).src = imgMinusBottom.src;
			document.getElementById('child_'+leafKey).style.display = "block";
		}
	}
	
	/* close node - folder and sign images */
	function CloseLeaf(leafKey){
		if (document.getElementById('child_'+leafKey)){
			if (document.getElementById('sign_'+leafKey).src==imgMinusTop.src) document.getElementById('sign_'+leafKey).src = imgPlusTop.src;
			else document.getElementById('sign_'+leafKey).src = imgPlusBottom.src;
			//document.getElementById('folder_'+leafKey).src = imgFolderClose.src;
			document.getElementById('child_'+leafKey).style.display = "none";
		}
	}
	
	function OpenTree(){
		//
	}
	
	function CloseTree(){
		//
	}
</script>