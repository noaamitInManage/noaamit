<?php

class advertisersManager extends BaseManager
{
    private $tb_name = 0;

    /*----------------------------------------------------------------------------------*/

    function __construct()
    {
        parent::__construct();
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val)
    {
        $this->$var = $val;
    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var)
    {
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * @desc: load all banners thet link to this slot and pick on rand  after it update the db = show+1
     * @param  slot id
     */
    /*----------------------------------------------------------------------------------*/

    public static function drawSlot($slot_id)
    {
        $banner_campaign = '';

        include($_SERVER['DOCUMENT_ROOT'] . '/_static/bannersGroup.' . $_SESSION['lang'] . '.inc.php'); //$bannersGroupArr
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/campaigns.inc.php'); //$campaignsArr
        $ts = time();
        $valid_campaign = false;
        foreach ($campaignsArr AS $k => $v) {
            if (($v['start_date'] < $ts) && ($v['end_date'] > $ts)) {
                $valid_campaign = true;
            }
        }
        if (!$valid_campaign) {
            return false;
        }
        $slotBanners = $bannersGroupArr[$slot_id]['items'];
        if (!is_array($slotBanners)) {
            return;
        }
        if ((count($slotBanners) == 0)) {
            return '';
        }

        $slot = $bannersGroupArr[$slot_id];
        unset($slot['items']);

        shuffle($slotBanners);
        $ts = time();
        $load_next_banner = true;
        while ($load_next_banner) {

            $load_next_banner = (count($slotBanners) == 0) ? false : true;
            $banner = array_pop($slotBanners);
            $banner_campaign = $campaignsArr[$banner['campaign_id']];
            $load_next_banner = (($banner_campaign['start_date'] < $ts) && ($ts < $banner_campaign['end_date']) && ($banner_campaign['is_active']) && ($load_next_banner)) ? false : true;

        }
        if ($banner['media_id']) {
            $Media = new mediaManager($banner['media_id']);
        }

        $bannerSrc = $Media->path;
        //$bannerSrc = mediaManager::;
        if ((!is_file($_SERVER['DOCUMENT_ROOT'] . $bannerSrc)) && (!$banner['iframe_src'])) {
            return '';
        }

        if ((trim($banner['file_ext']) == 'swf') || (trim($banner['file_ext']) == 'flv')) {

            $bannerContent = <<<Banner
		<object width="{$slot['width']}" height="{$slot['height']}" wmode="transparent">
			<param name="movie" value="{$bannerSrc}" wmode="transparent">
				<embed src="{$bannerSrc}" width="{$slot['width']}" height="{$slot['height']}" wmode="transparent" >
			</embed>
		</object>
Banner;
        } else {

            $bannerContent = <<<Banner
		<img class="banner" src="{$bannerSrc}" alt="{$Media->alt}"
			title="{$Media->title}" height="{$slot['height']}" width="{$slot['width']}" />
Banner;
        }

        if ($banner['free_html']) {
            $bannerContent = <<<Banner
		{$banner['free_html']}
Banner;

        }
        if (($banner['is_newwin'] == 1) && ((trim($banner['file_ext']) != 'swf')) && ((trim($banner['file_ext']) != 'flv'))) {
            $rel = 'rel="external" ';
        } else {
            $rel = '';
        }
        if (isset($banner['url']) && $banner['url'] == '') {
            $banner['url'] = '#';
        }
        $hide_id = intval($banner['id']) + 333;
        $bannerContent = <<<Banner
	<div  class="banner_link" id="banner_{$hide_id}">
	<a {$rel}  href="{$banner['url']}" title="{$banner['title']}" >{$bannerContent}</a>
	</div>
Banner;

        self::viewsCounter($banner['id']);


        return $bannerContent;
    }

    /*-----------------------------------------------------------------------------------*/
    public static function clickCounter($banner_id)
    {
        $Db = Database::getInstance();

        $Db->query("UPDATE `tb_banners_lang` SET `total_clicks` = `total_clicks` + 1 WHERE `obj_id` = '" . $banner_id . "'");
    }

    /*----------------------------------------------------------------------------------*/

    public static function viewsCounter($banner_id)
    {
        $Db = Database::getInstance();

        $Db->query("UPDATE `tb_banners_lang` SET `total_views` = `total_views` + 1 WHERE `obj_id` = '" . $banner_id . "'");
    }

    /*----------------------------------------------------------------------------------*/

}

?>