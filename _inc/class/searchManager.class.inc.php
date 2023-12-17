<?php

use Illuminate\Support\Str;

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 04/07/2017
 * Time: 14:20
 */
class searchManager extends BaseManager
{

    public static $cache_prefix = 'cached_search_';

    /**
     * @name write_log
     * @description Writes a search log asynchronously
     * @param $query
     * @param $user_id
     * @param $platform
     * @return bool
     */
    public static function write_log($query, $user_id, $platform)
    {
        $Db = Database::getInstance();

        $db_fieldsArr = array(
            'query' => $query,
            'user_id' => $user_id,
            'platform' => $platform,
            'last_update' => time(),
        );

        $res = $Db->insert('tb_search_log', $db_fieldsArr, false);

        return $res ? true : false;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name search_users
     * @description Searches for users
     * @param $query
     * @return array
     */
    public static function search_users($query)
    {
        $Db = Database::getInstance();
        $DefaultImage = new mediaManager(User::$default_image_id);
        $default_image_path = siteFunctions::get_url_by_env() . $DefaultImage->path;

        $resultsArr = array();

        if (Str::length($query) < 2) {
            return $resultsArr;
        }

        $query = Str::lower($query);

        $sql = "
            SELECT
              Main.`id`, Main.`full_name`, Main.`picture`, Main.`featured_tags`, Company.`name` AS `company_name`, Fb.`picture_url` AS `fb_picture`, Li.`picture_url` AS `li_picture`,
              Site.`id` AS `site_id`, Site.`country_id`, Company.`floor`
            FROM `tb_users` AS Main
              LEFT JOIN `tb_companies` AS Company ON Company.`id` = Main.`company_id`
              LEFT JOIN `tb_sites` AS Site ON Site.`id` = Company.`site_id`
              LEFT JOIN `tb_users__facebook_information` AS Fb ON Fb.`user_id` = Main.`id`
              LEFT JOIN `tb_users__linkedin_information` AS Li ON Li.`user_id` = Main.`id`
            WHERE Main.`full_name` LIKE '" . $query . "%' AND Main.`active` = 1 AND Main.`is_public` = 1
        ";
        $result = $Db->query($sql);

        while ($rowArr = $Db->get_stream($result)) {
            $tagsArr = $rowArr['featured_tags'] ? unserialize($rowArr['featured_tags']) : array();
            $rowArr['picture'] = $rowArr['picture'] ? siteFunctions::get_url_by_env() . $rowArr['picture'] : '';
            $resultsArr[] = array(
                'id' => $rowArr['id'],
                'country_id' => $rowArr['country_id'],
                'site_id' => $rowArr['site_id'],
                'floor' => $rowArr['floor'],
                'name' => $rowArr['full_name'] ?: '',
                'picture' => ($rowArr['picture'] ?: ($rowArr['li_picture'] ?: $rowArr['fb_picture'])) ?: $default_image_path,
                'is_base64_image' => false,
                'subtitle' => $rowArr['company_name'] ?: '',
                'tagsArr' => $tagsArr,
            );
        }

        return $resultsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name search_users_by_tags
     * @description Searches users by their tags
     * @param $query
     * @return array
     */
    public static function search_users_by_tags($query)
    {
        $Db = Database::getInstance();
        $DefaultImage = new mediaManager(companiesManager::$default_image_id);
        $default_image_path = siteFunctions::get_url_by_env() . $DefaultImage->path;

        $resultsArr = array();

        if (Str::length($query) < 2) {
            return $resultsArr;
        }

        $query = Str::lower($query);

        $sql = "
            SELECT Main.`id`, Main.`full_name`, Main.`picture`, Main.`featured_tags`, Company.`name` AS `company_name`, Fb.`picture_url` AS `fb_picture`, Li.`picture_url` AS `li_picture`, Tag.`id` AS `tag_id`, Tag.`title` AS `tag_title`,
            Site.`id` AS `site_id`, Site.`country_id`, Company.`floor`
            FROM `tb_users__tags` AS Link
              LEFT JOIN `tb_tags` AS Tag ON Tag.`id` = Link.`tag_id`
              LEFT JOIN `tb_users` AS Main ON Main.`id` = Link.`user_id`
              LEFT JOIN `tb_companies` AS Company ON Company.`id` = Main.`company_id`
              LEFT JOIN `tb_sites` AS Site ON Site.`id` = Company.`site_id`
              LEFT JOIN `tb_users__facebook_information` AS Fb ON Fb.`user_id` = Main.`id`
              LEFT JOIN `tb_users__linkedin_information` AS Li ON Li.`user_id` = Main.`id`
            WHERE Tag.`title` LIKE '" . $query . "%' AND Main.`active` = 1 AND Main.`is_public` = 1 AND Tag.`active` = 1
            GROUP BY Link.`user_id`
        ";
        $result = $Db->query($sql);

        while ($rowArr = $Db->get_stream($result)) {
            $tagsArr = $rowArr['featured_tags'] ? collect(unserialize($rowArr['featured_tags'])) : collect();
            $rowArr['picture'] = $rowArr['picture'] ? siteFunctions::get_url_by_env() . $rowArr['picture'] : '';
            if ($tagsArr->count()) {
                $tagsArr = $tagsArr->keyBy('id');
                $found_tagArr = $tagsArr->where('id', $rowArr['tag_id']);
                if ($found_tagArr->count()) {
                    $temp = collect($found_tagArr);
                    $tagsArr = $tagsArr->forget($rowArr['tag_id'])->prepend($temp->first())->addOrderNum()->toArray();
                }
            } else {
                $tagsArr = array(
                    array(
                        'id' => $rowArr['tag_id'],
                        'title' => $rowArr['tag_title'],
                        'order_num' => 1,
                    ),
                );
            }

            $resultsArr[] = array(
                'id' => $rowArr['id'],
                'country_id' => $rowArr['country_id'],
                'site_id' => $rowArr['site_id'],
                'floor' => $rowArr['floor'],
                'name' => $rowArr['full_name'] ?: '',
                'picture' => ($rowArr['picture'] ?: ($rowArr['li_picture'] ?: $rowArr['fb_picture'])) ?: $default_image_path,
                'is_base64_image' => false,
                'subtitle' => $rowArr['company_name'] ?: '',
                'tagsArr' => $tagsArr,
            );
        }

        return $resultsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name search_companies
     * @description Searches companies
     * @param $query
     * @param $items_per_page
     * @return array
     */
    public static function search_companies($query, $items_per_page = 0)
    {
        $Db = Database::getInstance();
        $DefaultImage = new mediaManager(companiesManager::$default_image_id);
        $default_image_path = siteFunctions::get_url_by_env() . $DefaultImage->path;

        $resultsArr = array();

        if (Str::length($query) < 2) {
            return $resultsArr;
        }

        $query = Str::lower($query);

        $limit = "";
        if ($items_per_page) {
            $limit = "LIMIT " . $items_per_page;
        }

        $sql = "
            SELECT Main.`id`, Main.`name`, Main.`picture`,
            Site.`id` AS `site_id`, Site.`country_id`, Main.`floor`
            FROM `tb_companies` AS Main
              LEFT JOIN `tb_sites` AS Site ON Site.`id` = Main.`site_id`
            WHERE Main.`name` LIKE '" . $query . "%' AND Main.`active` = 1 AND Main.`is_public` = 1
            " . $limit . "
        ";
        $result = $Db->query($sql);

        while ($rowArr = $Db->get_stream($result)) {
            $rowArr['picture'] = $rowArr['picture'] ? siteFunctions::get_url_by_env() . $rowArr['picture'] : '';
            $resultsArr[] = array(
                'id' => $rowArr['id'],
                'country_id' => $rowArr['country_id'],
                'site_id' => $rowArr['site_id'],
                'floor' => $rowArr['floor'],
                'name' => $rowArr['name'] ?: '',
                'picture' => $rowArr['picture'] ?: $default_image_path,
                'is_base64_image' => false,
                'subtitle' => '',
                'tagsArr' => array(),
            );
        }

        return $resultsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name search_companies_by_industries
     * @description Searches companies by their industries
     * @param $query
     * @return array
     */
    public static function search_companies_by_industries($query)
    {
        $Db = Database::getInstance();
        $DefaultImage = new mediaManager(companiesManager::$default_image_id);
        $default_image_path = siteFunctions::get_url_by_env() . $DefaultImage->path;

        $resultsArr = array();

        if (Str::length($query) < 2) {
            return $resultsArr;
        }

        $query = Str::lower($query);

        $sql = "
            SELECT Main.`id`, Main.`name`, Main.`picture`, Industry.`id` AS `industry_id`, Industry.`title` AS `industry_title`
            Site.`id` AS `site_id`, Site.`country_id`, Main.`floor`
            FROM `tb_companies__industries` AS Link
              LEFT JOIN `tb_industries` AS Industry ON Industry.`id` = Link.`industry_id`
              LEFT JOIN `tb_companies` AS Main ON Main.`id` = Link.`company_id`
              LEFT JOIN `tb_sites` AS Site ON Site.`id` = Main.`site_id`
            WHERE Industry.`title` LIKE '" . $query . "%' AND Main.`active` = 1 AND Main.`is_public` = 1 AND Industry.`active` = 1
            GROUP BY Link.`company_id`
        ";
        $result = $Db->query($sql);

        while ($rowArr = $Db->get_stream($result)) {
            $Company = new companiesManager($rowArr['id']);
            $industriesArr = collect($Company->industriesArr);
            if ($industriesArr->count()) {
                $industriesArr = $industriesArr->keyBy('id');
                $found_tagArr = $industriesArr->where('id', $rowArr['industry_id']);
                if ($found_tagArr->count()) {
                    $temp = collect($found_tagArr);
                    $industriesArr = $industriesArr->forget($rowArr['industry_id'])->prepend($temp->first())->addOrderNum()->toArray();
                }
            } else {
                $industriesArr = array(
                    array(
                        'id' => $rowArr['industry_id'],
                        'title' => $rowArr['industry_title'],
                        'order_num' => 1,
                    ),
                );
            }

            $rowArr['picture'] = $rowArr['picture'] ? siteFunctions::get_url_by_env() . $rowArr['picture'] : '';

            $resultsArr[] = array(
                'id' => $rowArr['id'],
                'country_id' => $rowArr['country_id'],
                'site_id' => $rowArr['site_id'],
                'floor' => $rowArr['floor'],
                'name' => $rowArr['full_name'] ?: '',
                'picture' => ($rowArr['picture'] ?: ($rowArr['li_picture'] ?: $rowArr['fb_picture'])) ?: $default_image_path,
                'is_base64_image' => false,
                'subtitle' => $rowArr['company_name'] ?: '',
                'tagsArr' => $industriesArr,
            );
        }

        return $resultsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name get_cached_results
     * @description Returns cached results for the query
     * @param $query
     * @return array|bool|string
     */
    public static function get_cached_results($query)
    {
        if (!configManager::$cache_search_results) {
            return false;
        }

        $query_snake = Str::snake(strtolower($query));
        if (($answerArr = siteFunctions::load_from_memory(self::$cache_prefix . $query_snake)) !== false) {
            return $answerArr;
        }

        return false;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name cache_results
     * @description Saved resutls to cache
     * @param $query
     * @param $resultsArr
     * @return bool
     */
    public static function cache_results($query, $resultsArr)
    {
        if (!configManager::$cache_search_results) {
            return false;
        }

        $query_snake = Str::snake(strtolower($query));

        return siteFunctions::save_to_memory(self::$cache_prefix . $query_snake, $resultsArr, configManager::$search_results_cache_time);
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name remove_user_from_results
     * @description Removes a user from the search results
     * @param $resultsArr
     * @param $user_id
     * @return array
     */
    public static function remove_user_from_results($resultsArr, $user_id)
    {
        $user_arrs = array('users', 'users_by_tags');

        $resultsArr = collect($resultsArr);
        $resultsArr->transform(function ($groupArr, $group_key) use ($user_id, $user_arrs) {
            if (!in_array($group_key, $user_arrs)) {
                return $groupArr;
            }

            $groupArr = collect($groupArr);
            $groupArr = $groupArr->filter(function ($itemArr) use ($user_id) {
                return $itemArr['id'] != $user_id;
            });

            return $groupArr->toArray();
        });

        return $resultsArr->toArray();
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name sort_results
     * @description Sorts results by sorting algorithm
     * @param $resultsArr
     * @param $User
     * @param $Company
     * @param $Site
     * @return array
     */
    public static function sort_results($resultsArr, $User, $Company, $Site)
    {
        if (!count($resultsArr)) {
            return $resultsArr;
        }

        $sorted_resultsArr = collect($resultsArr)->map(function ($resultArr) use ($User, $Company, $Site) {
            $match = 999;
            if ($resultArr['site_id'] == $Company->site_id && $resultArr['floor'] == $Company->floor) {
                $match = 1;
            } elseif ($resultArr['site_id'] == $Company->site_id) {
                $match = 2;
            } elseif ($resultArr['country_id'] == $Site->country_id) {
                $match = 3;
            }

            $resultArr['match'] = $match;

            return $resultArr;
        })->sortBy(function ($resultArr) {
            return sprintf('%d-%s', $resultArr['match'], $resultArr['name']);
        });

        return $sorted_resultsArr->toArray();
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name sort_all_results
     * @description Runs on the results and sorts its groups
     * @param $result_groupsArr
     * @param $User
     * @param $Company
     * @param $Site
     * @return array|\Illuminate\Support\Collection
     */
    public static function sort_all_results($result_groupsArr, $User, $Company, $Site)
    {
        $sorted_resultsArr = array();

        foreach ($result_groupsArr as $key => $resultsArr) {
            $resultsArr = self::sort_results($resultsArr, $User, $Company, $Site);

            $sorted_resultsArr[$key] = $resultsArr;
        }

        $sorted_resultsArr = collect(self::remove_user_from_results($sorted_resultsArr, $User->id));
        $sorted_resultsArr->transform(function ($resultArr) {
            return collect($resultArr)->addOrderNum()->toArray();
        });

        return $sorted_resultsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name get_search_results
     * @description Returns all search results for the given query
     * @param $query
     * @return array|bool|string
     */
    public static function get_search_results($query)
    {
        $query = strtolower($query);

        if ($cached_resultsArr = self::get_cached_results($query)) {
            return $cached_resultsArr;
        }

        $base_url = configManager::$internal_url . '/api/server/1.0/';

        $postArr = array(
            'query' => $query,
        );

        $request_titlesArr = array(
            0 => 'users',
            1 => 'users_by_tags',
            2 => 'companies',
            3 => 'companies_by_industries',
        );

        $requestArr = array(
            array(
                'url' => $base_url . 'searchUsers',
                'post' => $postArr,
            ),
            array(
                'url' => $base_url . 'searchUsersByTags',
                'post' => $postArr,
            ),
            array(
                'url' => $base_url . 'searchCompanies',
                'post' => $postArr,
            ),
            array(
                'url' => $base_url . 'searchCompaniesByIndustries',
                'post' => $postArr,
            ),
        );

        $responsesArr = siteFunctions::parallel_requests($requestArr);
        $answerArr = array();
        foreach ($responsesArr as $key => $responseArr) {
            $responseArr = json_decode($responseArr, true);

            $search_resultsArr = array();
            if ($responseArr['status']) {
                $search_resultsArr = $responseArr['data']['resultsArr'];
            }

            $answerArr[$request_titlesArr[$key]] = $search_resultsArr;
        }

        self::cache_results($query, $answerArr);

        return $answerArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @name search
     * @description Performs a search
     * @param $query
     * @param string $type
     * @param int $start_order_num
     * @param int $items_per_page
     * @return array
     */
    public static function search($query, $type = '', $start_order_num = 1, $items_per_page = 0)
    {
        $User = User::getInstance();
        $Company = new companiesManager($User->company_id);
        $Site = new sitesManager($Company->site_id);
        $items_per_page = $items_per_page ?: configManager::$search_results_per_page;
        $start_order_num = $start_order_num ?: 1;

        $sorted_resutlsArr = array();
        $all_resutlsArr = collect(self::get_search_results($query));
        $all_resutlsArr->each(function ($resultsArr, $resutls_group_key) use ($type, &$sorted_resutlsArr) {
            if ($type == '' || $type == $resutls_group_key) {
                $sorted_resutlsArr[$resutls_group_key] = $resultsArr;
            }
        });

        $block_order_num = 1;
        $sorted_resutlsArr = collect(self::sort_all_results($sorted_resutlsArr, $User, $Company, $Site))->transform(function ($resultsArr, $key) use ($start_order_num, $items_per_page, &$block_order_num) {
            $resultsArr = collect($resultsArr);
            $more_results = 0;
            if ($resultsArr->count()) {
                $sliced_resultsArr = $resultsArr->where('order_num', '>=', $start_order_num)->values()->slice(0, $items_per_page);
                $more_results = $resultsArr->where('order_num', $start_order_num + 1)->count() ? 1 : 0;
            } else {
                $sliced_resultsArr = $resultsArr;
            }

            $sliced_resultsArr = array(
                'key' => $key,
                'title' => lang('search_' . $key . '_title'),
                'resultsArr' => $sliced_resultsArr->values(),
                'more_results' => $more_results,
                'order_num' => $block_order_num,
            );
            $block_order_num++;

            return $sliced_resultsArr;
        });

        self::write_log($query, $User->id, siteFunctions::get_platform_id());

        return $sorted_resutlsArr->toArray();
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_trending_users_searches()
    {
        $Db = Database::getInstance();
        $ts = time() - (60 * 60 * 24 * 7);
        $DefaultImage = new mediaManager(User::$default_image_id);
        $default_image_path = siteFunctions::get_url_by_env() . $DefaultImage->path;

        $sql = "
            SELECT
              Usr.`id`, Usr.`full_name`, Usr.`picture`, Usr.`featured_tags`, Company.`name` AS `company_name`, Fb.`picture_url` AS `fb_picture`, Li.`picture_url` AS `li_picture`,
              Site.`id` AS `site_id`, Site.`country_id`, Company.`floor`,
              Main.`user_id`, COUNT(Main.`user_id`) AS `count`
            FROM `tb_users__views_log` AS Main
              LEFT JOIN `tb_users` as Usr ON Usr.`id` = Main.`user_id`
               LEFT JOIN `tb_companies` AS Company ON Company.`id` = Usr.`company_id`
              LEFT JOIN `tb_sites` AS Site ON Site.`id` = Company.`site_id`
              LEFT JOIN `tb_users__facebook_information` AS Fb ON Fb.`user_id` = Usr.`id`
              LEFT JOIN `tb_users__linkedin_information` AS Li ON Li.`user_id` = Usr.`id`
            WHERE Main.`user_id` > 0 AND Usr.`active` = 1 AND Main.`from_screen` = 'search' AND Main.`last_update` > {$ts}
            GROUP BY `user_id`
            ORDER BY `count` DESC
            LIMIT " . (configManager::$trending_search_items_nubmer + 1) . "
        ";
        $result = $Db->query($sql);
        $resultsArr = array();
        $order_num = 1;
        while ($rowArr = $Db->get_stream($result)) {
            $tagsArr = $rowArr['featured_tags'] ? unserialize($rowArr['featured_tags']) : array();
            $rowArr['picture'] = $rowArr['picture'] ? siteFunctions::get_url_by_env() . $rowArr['picture'] : '';
            $resultsArr[] = array(
                'id' => $rowArr['id'],
                'country_id' => $rowArr['country_id'],
                'site_id' => $rowArr['site_id'],
                'floor' => $rowArr['floor'],
                'name' => $rowArr['full_name'] ?: '',
                'picture' => ($rowArr['picture'] ?: ($rowArr['li_picture'] ?: $rowArr['fb_picture'])) ?: $default_image_path,
                'is_base64_image' => false,
                'subtitle' => $rowArr['company_name'] ?: '',
                'tagsArr' => $tagsArr,
                'order_num' => $order_num,
            );
            $order_num++;
        }

        return $resultsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_trending_companies_searches()
    {
        $Db = Database::getInstance();
        $ts = time() - (60 * 60 * 24 * 7);
        $DefaultImage = new mediaManager(companiesManager::$default_image_id);
        $default_image_path = siteFunctions::get_url_by_env() . $DefaultImage->path;

        $sql = "
            SELECT
              Company.`id`, Company.`name`, Company.`picture`,
              Site.`id` AS `site_id`, Site.`country_id`, Company.`floor`,
              Main.`company_id`, COUNT(Main.`company_id`) AS `count`
            FROM `tb_companies__views_log` AS Main
              LEFT JOIN `tb_companies` AS Company ON Company.`id` = Main.`company_id`
               LEFT JOIN `tb_sites` AS Site ON Site.`id` = Company.`site_id`
            WHERE Main.`company_id` > 0 AND Company.`active` = 1 AND Main.`from_screen` = 'search' AND Main.`last_update` > {$ts}
            GROUP BY `company_id`
            ORDER BY `count` DESC
            LIMIT " . (configManager::$trending_search_items_nubmer) . "
        ";
        $result = $Db->query($sql);
        $resultsArr = array();
        $order_num = 1;
        while ($rowArr = $Db->get_stream($result)) {
            $resultsArr[] = array(
                'id' => $rowArr['id'],
                'country_id' => $rowArr['country_id'],
                'site_id' => $rowArr['site_id'],
                'floor' => $rowArr['floor'],
                'name' => $rowArr['name'] ?: '',
                'picture' => $rowArr['picture'] ?: $default_image_path,
                'is_base64_image' => false,
                'subtitle' => '',
                'tagsArr' => array(),
                'order_num' => $order_num,
            );
            $order_num++;
        }

        return $resultsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_trending_searches_from_db()
    {
        $trending_searchesArr = array(
            'users' => array(
                'key' => 'users',
                'title' => lang('search_users_title'),
                'resultsArr' => self::get_trending_users_searches(),
                'more_results' => 0,
                'order_num' => 1,
            ),
            'companies' => array(
                'key' => 'companies',
                'title' => lang('search_companies_title'),
                'resultsArr' => self::get_trending_companies_searches(),
                'more_results' => 0,
                'order_num' => 2,
            ),
        );

        return $trending_searchesArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function save_trending_searches_to_memory($trending_searchesArr)
    {
        $res = siteFunctions::save_to_memory('trending_searches', $trending_searchesArr, 0);

        return $res;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function save_trending_searches_to_static_file($trending_searchesArr)
    {
        $UpdateStaticFiles = new generalStaticFilesUpdateStaticFiles();
        $UpdateStaticFiles->build_static_file($trending_searchesArr, 'trending_searches', 'trending_searchesArr');

        return true;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function cache_trending_searches()
    {
        $trending_searchesArr = self::get_trending_searches_from_db();

        self::save_trending_searches_to_memory($trending_searchesArr);
        self::save_trending_searches_to_static_file($trending_searchesArr);

        return true;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_trending_searches_from_static_file()
    {
        $trending_searchesArr = array();

        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/_static/trending_searches.inc.php';
        if (!file_exists($file_path)) {
            return $trending_searchesArr;
        }

        include($_SERVER['DOCUMENT_ROOT'] . '/_static/trending_searches.inc.php'); // $trending_searchesArr

        return $trending_searchesArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function get_trending_searches()
    {
        $resultsArr = siteFunctions::load_from_memory('trending_searches');
        if (!$resultsArr) {
            $resultsArr = self::get_trending_searches_from_static_file();
        }

        return $resultsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//
}