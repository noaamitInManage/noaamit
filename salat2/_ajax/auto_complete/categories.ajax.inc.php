<?
$answerArr=array();
$tb_name='tb_categories_lang';
$act = $_REQUEST['action'];
$q = $_REQUEST['q'];

switch (strtolower($act)){
    case 'get_all_categories':
        $query="SELECT obj_id as id, title FROM `{$tb_name}` WHERE title LIKE '{$q}%' ";

        $res=$Db->query($query);

        while($row = $Db->get_stream($res)) {
            echo $row['id'] . '.' . $row['title']. "\n";;
        }
        break;
}
exit();