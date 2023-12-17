<?

class featureFlagManager
{
    function __construct($item_id, $lang = '')
    {

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

    public static function getGroup($group_id)
    {
        $featureFlagGroupArr = array();
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/feature_flags_groups.inc.php'); // $featureFlagsGroupsArr
        if ($featureFlagsGroupsArr[$group_id]) {
            $featureFlagGroupArr = $featureFlagsGroupsArr[$group_id];
        }

        return $featureFlagGroupArr['features'];
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get($key, $group = '')
    {
        $featureFlag = 0;

        if ($group && $key) {
            if (!is_numeric($group)) {
                $group_id = configManager::$feature_flags_groups_idsArr[$group];
            } else {
                $group_id = $group;
            }

            $featureFlagGroupArr = self::getGroup($group_id);
            $featureFlag = $featureFlagGroupArr[$key];
        } elseif ($key) {
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/feature_flags.inc.php'); // $featureFlagsArr
            if ($featureFlagsArr[$key]) {
                $featureFlag = $featureFlagsArr[$key];
            }
        }

        return $featureFlag;
    }
}
