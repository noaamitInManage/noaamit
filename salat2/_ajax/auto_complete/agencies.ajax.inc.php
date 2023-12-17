<?
$tb_name='tb_users';
header('Content-type: text/plain; charset=utf-8');


$action=$_REQUEST['action'];

$q = $Db->make_escape(trim($_REQUEST['q']));

switch ($action){

case "getItems":
	$sql = "SELECT `id`,`agency_name`,`email` FROM `tb_agents` WHERE
		`agency_name` LIKE '%{$q}%' OR `email` LIKE '%{$q}%'";  
	$res = $Db->query($sql);
	while($item = $Db->get_stream($res)){
		echo $item['email'] .' - '.$item['agency_name'] .'|'.  $item['id']."\n";
	}
	break;
}

?>
