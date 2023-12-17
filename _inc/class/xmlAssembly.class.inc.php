<?

/**
 * class to translate this kind of arrays
 * array(
 *    'tag'=>'START',
 *    'params'=>array('a'=>1,'b'=>3),
 *    'items'=>array(
 *        0=>array('tag'=>'ITEM',
 *
 *        'value'=>1,
 *    'params'=>array('a'=>1,'b'=>3),
 *    'items'=>array()
 *    ),
 *    )
 *    );
 *
 */
class xmlAssembly
{
    public static function assembly($data, $headers = false)
    {
        $result = '';
        if ($headers) {
            $result = '<?xml version="1.0" encoding="UTF-8"?>';
        }
        if (is_array($data) && count($data) == 0) {
            return '';
        }
        if (!is_array($data)) {
            return $data;
        } else {
            $result .= '<' . $data['tag'] . ((isset($data['params']) && (count($data['params']))) ? ' ' . self::attributes($data['params']) . ' ' : '');

            if (isset($data['value'])) {
                $result .= '>' . $data['value'] . '</' . $data['tag'] . '>';
            } elseif (isset($data['items'])) {
                $result .= '>';
                foreach ($data['items'] as $val) {
                    $result .= self::assembly($val);
                }
                $result .= '</' . $data['tag'] . '>';
            } else {
                $result .= ' />';
            }
        }
        //echo $result."\n\n\n";
        return $result;
    }


    protected static function attributes($attributes)
    {
        $result = array();
        foreach ($attributes as $k => $v) {
            $result[] = $k . '="' . $v . '"';
        }
        return implode(' ', $result);
    }

    public static function loadParam($arr, $tag = 'param')
    {
        $result = array();
        foreach ($arr as $key => $val) {
            $result[] = array('tag' => $tag, 'params' => array('name' => $key), 'value' => $val);
        }
        return $result;
    }

    public function sterilizeReservationXml($soapResponse)
    {
        $dataArr = json_decode(json_encode($soapResponse), TRUE);
        $method = "AllPricesXML";
        $xml = str_replace("<![CDATA[", "", $dataArr['responds']);
        $xml = str_replace("]]>", "", $xml);
        preg_match("/\<" . $method . ">(.*)\<\/" . $method . ">/s", $xml, $matches);
        $xml = $matches[1];
        return ($xml);
    }

    /**
     * takes simple xml array and flatten params
     *
     * @param array $xml_array
     */
    public static function syntesis($xml_array)
    {
        $result = array();
        if (!is_array($xml_array)) {
            return $xml_array;
        }
        foreach ($xml_array as $key => $val) {

            if (is_array($val) && isset($val['@attributes']) && count($val['@attributes']) == 1 && isset($val['@attributes']['name']) && isset($val['data'])) {
                $result[$val['@attributes']['name']] = $val['data'];
            } else if (is_array($val) && count($val) == 1 && isset($val['data'])) {
                $result[$key] = $val['data'];
            } elseif (!is_array($val)) {
                $result[$key] = $val; // terminate recursive
            } else {

                $result[$key] = self::syntesis($val);
            }
        }
        return $result;
    }

    public static function xml_to_array($xml)
    {
        $counter = 0;
        while (preg_match('/\<!\[CDATA\[/', $xml) == 1 && $counter < 5) {
            $xml = preg_replace('/\<!\[CDATA\[\]\]\>/', '', $xml);
            $xml = preg_replace('/\<!\[CDATA\[([^\]]+)\]\]\>/', '<data>$1</data>', $xml);
            $xml = str_replace(array('&lt;', '&gt;', '&lt', '&gt', "'"), array('<', '>', '<', '>', '"'), $xml);
            $counter++;
        }
        if ($counter > 4) {

            throw new Exception('Failed to remove cdata from: ' . $xml);
        }
        //$xml = str_replace("'",'"',$xml);
        //echo '<hr /><pre>' . print_r($xml, true) . '</pre><hr />';
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        //return json_decode($json,TRUE);
        return self::syntesis(json_decode($json, TRUE));
    }

    /**
     * load data to CData structure
     *
     * @param array $remarks array of values to remark
     * @param string $remark_tag the tag of each remark
     * @param string $remarks_tag tag of all remarks
     * @return unknown
     */
    public static function loadRemarks($remarks, $remark_tag = '', $remarks_tag = '')
    {
        if (empty($remark_tag)) {
            $remark_tag = 'remark';
        }
        if (empty($remarks_tag)) {
            $remarks_tag = 'remarks';
        }
        $result = array();
        foreach ($remarks as $remark) {
            $result[] = array('tag' => $remark_tag, 'value' => '<![CDATA[' . $remark . ']]>');
        }
        return array(array('tag' => $remarks_tag, 'items' => $result));
    }

    /**
     * load unasscoiative array to xml structure according to tag name and tag to contain all array
     *
     * @param array $data the items for xml
     * @param string $tag
     * @param unknown_type $main_tag
     * @return unknown
     */
    public static function loadTag($data, $tag = '', $main_tag = '')
    {
        if (empty($tag)) {
            $tag = 'remark';
        }
        if (empty($main_tag)) {
            $main_tag = 'remarks';
        }
        $result = array();
        foreach ($data as $text) {
            $result[] = array('tag' => $tag, 'value' => $text);
        }
        return array(array('tag' => $main_tag, 'items' => $result));
    }
}

?>