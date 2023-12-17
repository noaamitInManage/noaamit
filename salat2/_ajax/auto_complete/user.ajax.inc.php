<?
$tb_name = 'tb_users';
header('Content-type: text/plain; charset=utf-8');
include($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011

$action = $_REQUEST['action'];

$q = $Db->make_escape(trim($_REQUEST['q']));

switch ($action) {

    case "getId":
        $q = $Db->make_escape(trim($_REQUEST['title']));
        $query = "SELECT `id`,`email` FROM `{$tb_name}` WHERE `email` LIKE '{$q}'";
        $result = $Db->query($query);
        $row = $Db->get_stream($result);
        echo $row['id'];
        break;

    case "getItems":
        if (strlen(trim($q)) == 0) {
            $query = "SELECT id,email FROM `{$tb_name}`";
            $result = $Db->query($query);

            while ($row = $Db->get_stream($result)) {
                echo $row['email'] . "\n";
            }
        } else {
            $query = "SELECT id,email FROM `{$tb_name}` WHERE `email` LIKE '{$q}%'";
            $result = $Db->query($query);
            while ($row = $Db->get_stream($result)) {
                echo $row['email'] . "\n";
            }
        }
        break;

    case 'get_user_tags':
        $user_id = $_REQUEST['user_id'];
        if (strlen(trim($q)) == 0) {
            $query = "
              SELECT `id` ,`title` FROM `tb_tags`
                WHERE `id` NOT IN(
                      SELECT `tag_id` FROM `tb_users__tags`
                        WHERE `user_id` = {$user_id}
                       )
                       LIMIT $salat_view_list_limit
            ";
            $result = $Db->query($query);
            while ($line = $Db->get_stream($result)) {
                $title = stripslashes($line['title']);
                echo "{$line['id']}). {$title}" . "\n";
            }
        } else {
            $query = "SELECT `id`, `title`
                        FROM `tb_tags`
                           WHERE `title` LIKE '%{$q}%'
                              AND `id` NOT IN(
                                  SELECT `tag_id` FROM `tb_users__tags`
                                    WHERE `user_id` = {$user_id}
                                   )
                                 ORDER BY `title` ASC
                                 LIMIT $salat_view_list_limit";
            $result = $Db->query($query);
            while ($line = $Db->get_stream($result)) {
                $title = stripslashes($line['title']);
                echo "{$line['id']}). {$title}" . "\n";
            }
        }
        break;

}


?>