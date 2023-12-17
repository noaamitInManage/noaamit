<?
$act=$_REQUEST['act'];
$tb_name='tb_category';

$parent_id=trim($Db->make_escape($_REQUEST['parent_id']));

$answer=array("err"=>"");

function get_parent_items($parent_id){
    $categoryGroupArr=array();
    include ($_SERVER['DOCUMENT_ROOT'].'/_static/categoryGroup.inc.php');//$categoryGroupArr
    $category=$categoryGroupArr[$parent_id];

    $items=array();
    foreach($category['items'] AS $key=>$value){
        if(count($value['items'])){
            foreach($value['items'] AS $k=>$v){
                $items[$k]=array(
                    "id"=>$k,
                    "title"=>' '.$v['title'].' '
                );

            }
        }else{//sec level
            $items[$key]=array(
                "id"=>$key,
                "title"=>'-- '.$value['title'].' --'
            );
        }
    }
    //die('<hr /><pre>' . print_r($items, true) . '</pre><hr />');
    return $items;
}

switch ($act){
   case 'getSons':
         $q="SELECT id,title FROM `{$tb_name}` WHERE `parent_id`='{$parent_id}'";
         $r=$Db->query($q) or die($q);
         
         while($row = $Db->get_stream($r)) {
            $html.=<<<HTML
            <option value="{$row['id']}">-- {$row['title']}-- </option>
         
HTML;
        
         }
 $answer['html']=$html;
 include($_SERVER['DOCUMENT_ROOT'].'/_static/category/category-'. $parent_id.'.inc.php'); //$categoryArr
 $answer['parent_category_name'] = $categoryArr['title'];

      break;
  case 'composeSons': // category.php module
      $parent_id=$_REQUEST['parent_id'];
      $categories_child_items=get_parent_items($parent_id);

      $html='<div class="category_item">
                <p>
                    <select name="ex_catgories[]">'
                        .BuildCombo_V3($categories_child_items,"id","title",0).
                    '</select>
                </p>
             </div>';

        $answer['html']=$html;

        break;
      
      
   default:
      
      break;
   
}

header('Content-Type: text/html; charset=UTF-8');
echo json_encode($answer);
?>