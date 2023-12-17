<?
$string=$_REQUEST['data'];
$act = $_REQUEST['act'];
$answer = array();
switch($act) {
	case "urlencode":
		$answer=array(
			"source"=>$string,
			"output"=>urlencode($string),
		);
		break;
}

echo  json_encode($answer);

?>