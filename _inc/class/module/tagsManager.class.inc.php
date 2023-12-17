<?php

class tagsManager extends BaseManager
{
    private static $table = 'tb_tags';

    public $id = 0;

    public $title = '';
    public $featured = 0;
    public $is_linkedin_tag = 0;

    public $last_update = 0;


    function __construct($item_id)
    {
        parent::__construct();

        $tagArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/tags/' . get_item_dir($item_id) . '/tag-' . $item_id . '.inc.php'); // $tagArr

        foreach ($tagArr AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }


    //-------------------------------------------------------------------------------------------------------------------//

    function __destruct()
    {

    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function __set($var, $val)
    {
        $this->$var = $val;
    }


    //-------------------------------------------------------------------------------------------------------------------//


    public function __get($var)
    {
        return $this->$var;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function update_static_files($inner_id = '')
    {
        $UpdateStaticFiles = new tagsUpdateStaticFiles();
        $UpdateStaticFiles->updateStatics($inner_id);
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function all($type = '')
    {
        $file_name = 'tags';
        if ($type == 'featured') {
            $file_name = 'featured_tags';
        }

        $tagsArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $file_name . '.inc.php'); // $tagsArr

        return $tagsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_gdArr()
    {
        $tagsArr = self::all('featured');

        $i = 1;
        foreach ($tagsArr as &$tagArr) {
            $tagArr['order_num'] = $i;
            $i++;
        }

        return array_values($tagsArr);
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function add($title, $from_linkedin = 1)
    {
        $Db = Database::getInstance();

        if ($title == '') {
            return false;
        }

        $title = $Db->make_escape(strtolower(trim($title)));
        $sql = "
            SELECT `id`, `title` FROM `" . self::$table . "` WHERE `title` = '" . $title ."'
        ";
        $result = $Db->query($sql);

        if ($result->num_rows) {
            $tagArr = $Db->get_stream($result);
            $Db->query("UPDATE `tb_tags` SET `count` = `count` + 1 WHERE `id` = " . $tagArr['id']);

            return false;
        }

        $db_fieldsArr = array(
            'title' => $title,
            'featured' => 0,
            'is_linkedin_tag' => $from_linkedin,
            'priority' => 0,
            'last_update' => time(),
        );

        $tag_id = $Db->insert(self::$table, $db_fieldsArr);
        self::update_static_files($tag_id);

        return true;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function search($q, $excluded = '')
    {
        $tagsArr = array();

        if (strlen($q) < configManager::$tags_search_ac_min_characters) {
            return $tagsArr;
        }

        $Db = Database::getInstance();
        $User = User::getInstance();

        $q = $Db->make_escape($q);

        $excluded_str = $excluded != '' ? ' AND `id` NOT IN ('. $excluded .')' : '';

        $sql = "
            SELECT
              `id`, `title`
            FROM `" . self::$table . "`
            WHERE `title` LIKE '%" . $q . "%'". $excluded_str ."
            AND `id` NOT IN (
                SELECT `tag_id` FROM `tb_users__tags` WHERE `user_id` = '" . $User->id . "'
            )
            ORDER BY `priority` DESC, `title`
            LIMIT ". configManager::$tags_search_ac_max_results ."
        ";
        $result = $Db->query($sql);

        $i = 1;
        while ($rowArr = $Db->get_stream($result)) {
            $rowArr['order_num'] = $i;
            $tagsArr[] = $rowArr;
            $i++;
        }

        return $tagsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_user_tags($user_id = 0)
    {
        $Db = Database::getInstance();

        $User = User::getInstance();
        $user_id = $user_id == 0 ? $User->id : $user_id;

        $tagsArr = array();

        $sql = "
            SELECT Tag.`id`, Tag.`title` FROM `tb_users__tags` AS Link
              LEFT JOIN `tb_tags` AS Tag ON Tag.`id` = Link.`tag_id`
            WHERE Link.`user_id` = " . $user_id . "
            ORDER BY Tag.`featured` DESC, Tag.`priority` DESC, Tag.`title`
        ";
        $result = $Db->query($sql);

        $i = 1;
        while ($rowArr = $Db->get_stream($result)) {
            $rowArr['order_num'] = $i;
            $i++;
            $tagsArr[] = $rowArr;
        }

        return $tagsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function add_tags_to_user($tag_idsArr, $user_id = 0)
    {
        $Db = Database::getInstance();

        $ts = time();
        $tag_idsArr = array_wrap($tag_idsArr);

        $User = User::getInstance();
        $user_id = $user_id == 0 ? $User->id : $user_id;

        foreach ($tag_idsArr as $tag_id) {
            $db_fieldsArr = array(
                'tag_id' => $tag_id,
                'user_id' => $user_id,
                'last_update' => $ts,
            );
            $Db->replace('tb_users__tags', $db_fieldsArr);
        }

        return true;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function remove_tags_from_user($tag_idsArr, $user_id)
    {
        $Db = Database::getInstance();

        $ts = time();
        $tag_idsArr = array_wrap($tag_idsArr);
        $tag_ids = implode(',', $tag_idsArr);

        $User = User::getInstance();
        $user_id = $user_id == 0 ? $User->id : $user_id;

        $Db->query("DELETE FROM `tb_users__tags` WHERE `user_id` = " . $user_id . " AND `tag_id` IN (" . $tag_ids . ")");

        return true;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function sync_user_tags($tag_idsArr, $user_id)
    {
        $Db = Database::getInstance();

        $ts = time();
        $tag_idsArr = array_wrap($tag_idsArr);

        $User = User::getInstance();
        $user_id = $user_id == 0 ? $User->id : $user_id;

        if (!$user_id) {
            return false;
        }

        $Db->delete('tb_users__tags', 'user_id', $user_id);

        foreach ($tag_idsArr as $tag_id) {
            $db_fieldsArr = array(
                'tag_id' => $tag_id,
                'user_id' => $user_id,
                'last_update' => $ts,
            );
            $Db->insert('tb_users__tags', $db_fieldsArr);
        }

        $featured_tagsArr = array();
        for ($i = 0; $i < count($tag_idsArr) && $i < 4; $i++) {
            $Tag = new self($tag_idsArr[$i]);
            $featured_tagsArr[] = array(
                'id' => $Tag->id,
                'title' => $Tag->title,
                'order_num' => $i + 1,
            );
        }

        $db_fieldsArr = array(
            'featured_tags' => serialize($featured_tagsArr),
            'tags_count' => count($tag_idsArr),
            'last_update' => $ts,
        );
        $Db->update('tb_users', $db_fieldsArr, 'id', $user_id);

        return true;
    }

    //-------------------------------------------------------------------------------------------------------------------//
}