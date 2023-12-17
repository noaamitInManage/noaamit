<?

header('Content-type: text/plain; charset=utf-8');
include($_project_server_path . $_includes_path . 'site.array.inc.php'); // 15/04/2011

$action = $_REQUEST['action'];

$q = $Db->make_escape(trim($_REQUEST['q']));

switch ($action) {

    case 'get_company_industries':
        $company_id = $_REQUEST['company_id'];
        if (strlen(trim($q)) == 0) {
            $query = "
              SELECT `id` ,`title` FROM `tb_industries`
                WHERE `id` NOT IN(
                      SELECT `industry_id` FROM `tb_companies__industries`
                        WHERE `company_id` = {$company_id}
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
                        FROM `tb_industries`
                           WHERE `title` LIKE '%{$q}%'
                              AND `id` NOT IN(
                                  SELECT `industry_id` FROM `tb_companies__industries`
                                    WHERE `company_id` = {$company_id}
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

    case 'get_room_companies':
        $company_id = $_REQUEST['company_id'];
        if (strlen(trim($q)) == 0) {
            $query = "
              SELECT `id` ,`name`, `friendly_name` FROM `tb_companies`
                WHERE `id` NOT IN(
                      SELECT `company_id` FROM `tb_meeting_rooms__limited_to_companies`
                        WHERE `company_id` = {$company_id}
                       )
            ";
            $result = $Db->query($query);
            while ($line = $Db->get_stream($result)) {
                $title = stripslashes($line['friendly_name'] ?: $line['name']);
                echo "{$line['id']}). {$title}" . "\n";
            }
        } else {
            $query = "SELECT `id`, `name`, `friendly_name`
                        FROM `tb_companies`
                           WHERE `name` LIKE '%{$q}%'
                              AND `id` NOT IN(
                                  SELECT `company_id` FROM `tb_meeting_rooms__limited_to_companies`
                                    WHERE `company_id` = {$company_id}
                                   )
                                 ORDER BY `name` ASC";
            $result = $Db->query($query);
            while ($line = $Db->get_stream($result)) {
                $title = stripslashes($line['friendly_name'] ?: $line['name']);
                echo "{$line['id']}). {$title}" . "\n";
            }
        }

        break;
}


?>