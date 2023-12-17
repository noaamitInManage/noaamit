<?php

Class hotelsUpdate {
	
	static private $instance = null;
	protected $dir;
	protected $xml;
	protected $counter = 0;
	protected $_countries = array();
	protected $_stack = array();
    protected $_file = "";
    protected $_parser = null;
	protected $_record = array();
    protected $_currentId = "";
    protected $_current = "";
    protected $_depth = 0;
    protected $last_query;
	public $show_queries = false;
	protected $hotelid = '';
	protected $isDomainNode = false;
	protected $imRec = 0;
	/**
	 * Returns a singleton instance of an http request manager
	 *
	 * @return hotelsUpdate An http request manager instance
	 */

	public static function instance()
	{
		if (self::$instance === null)
		{
			self::$instance = new hotelsUpdate();
		}
		return self::$instance;
	}
	
	public function __construct() {
		$this->dir = $_SERVER['DOCUMENT_ROOT'].'/downloads/';
		require_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/Arr.class.inc.php');
		$this->loadCountries();
		$url = $_SERVER['SERVER_NAME']; 
		
	}
	
	
	protected function utf8capture($captures) {
	  if ($captures[1] != "") {
	    // Valid byte sequence. Return unmodified.
	    return $captures[1];
	  }
	  elseif ($captures[2] != "") {
	    // Invalid byte of the form 10xxxxxx.
	    // Encode as 11000010 10xxxxxx.
	    return "\xC2".$captures[2];
	  }
	  else {
	    // Invalid byte of the form 11xxxxxx.
	    // Encode as 11000011 10xxxxxx.
	    return "\xC3".chr(ord($captures[3])-64);
	  }
	}
	protected function utf8Replacer($txt) {
		$regex = '
			/( 		[\x00-\x7F]                 
			    |   [\xC0-\xDF][\x80-\xBF]      
			    |   [\xE0-\xEF][\x80-\xBF]{2}   
			    |   [\xF0-\xF7][\x80-\xBF]{3}   
			    )+/x';
		$replaces = array(
			chr(0) => '',
			chr(1) => '',
			chr(2) => '',
			chr(3) => '',
			chr(4) => '',
			chr(5) => '',
			chr(6) => '',
			chr(7) => '',
			chr(8) => '',
			chr(9) => '',
			chr(10) => '',
			chr(11) => '',
			chr(12) => '',
			chr(13) => '',
			chr(14) => '',
			chr(15) => '',
			chr(16) => '',
			chr(17) => '',
			chr(18) => '',
			chr(19) => '',
			chr(20) => '',
			chr(21) => '',
			chr(22) => '',
			chr(23) => '',
			chr(24) => '',
			chr(25) => '',
			chr(26) => '',
			chr(27) => '',
			chr(28) => '',
			chr(29) => '',
			chr(30) => '',
			chr(31) => '',
	);

		return strtr($txt,$replaces);
	}
	/**
	 * load countries code into array
	 *
	 */
	protected function loadCountries() {
		$this->_countries = array();
		$sql = 'SELECT * FROM `tb_countries`';
		$result = mysql_query($sql);
		while ( $line = mysql_fetch_assoc($result) ) {
			$this->_countries[$line['code']] = $line['id'];
		}
	}
	
	protected function make_insert_sql($table,$array) {
		foreach($array AS $key=>$value) {
			$array[$key] = $Db->make_escape($value);
		}
		$query = "INSERT INTO `{$table}` (`".implode('`,`',array_keys($array))."`) VALUES ('".implode("','",array_values($array))."');";
		return $query;
	}
	
	protected function make_insert_cond_sql($table,$array,$fields_conditions) {
		foreach($array AS $key=>$value) {
			$array[$key] = $Db->make_escape($value);
		}
		$conds = array();
		foreach ( $fields_conditions as $field ) {
			$conds[] = "`c`.`$field`='{$array[$field]}'";
		}
		$conds = implode(' AND ',$conds);
		$query = "INSERT INTO `{$table}` (`".implode('`,`',array_keys($array))."`) 
		SELECT '".implode("','",array_values($array))."' FROM `country`
		WHERE NOT EXISTS (SELECT 1 FROM `{$table}` `c` 
		WHERE $conds) LIMIT 1;";
		return $query;
	}
	
	
	public function error_register() {
		$trace = debug_backtrace();
		//echo '<hr /><pre>' . print_r($trace, true) . '</pre><hr />';
		echo '<div class="ltr">'.mysql_errno().':  '.mysql_error().', line:'.$trace[0]['line'].', file:'.$trace[0]['file'].", query: ".$this->last_query."</div>\n";
	}
	
	public function query($sql) {
		$this->last_query = $sql;
		if ($this->show_queries)  echo '<hr /><pre>' . print_r('run query: '.$sql, true) . '</pre><hr />';
		return mysql_query($sql);
	}
	
	protected function make_update_sql($table,$array,$where) {
		$items = array();
		foreach($array AS $key=>$value) {
			$items[] = "`{$key}`='".$Db->make_escape($value)."'";
		}
		$query = "UPDATE `{$table}` SET ".implode(",",$items)." {$where}";
		return $query;
	}
	/**
	 * make record for xml file
	 *
	 * @param string $file file name
	 */
	public function makeFile($file) {
		$file = basename($file);
		if ( preg_match('/\.xml$/',$file) == 0 ) {
			$file .= '.xml';
		}
		$sql = sprintf('SELECT * FROM `tb_loads` WHERE `status`=0 AND `name`="%s"',$file);
		$record = array(
			'name' => $file,
			'status' => 0,
			'insertion_date' => date('Y-m-d H:i:s'),
		);
		$result = $this->query($sql) OR $this->error_register();
		if ( mysql_num_rows($result) > 0 ) {
			$id = mysqli_result($result,0,'id');
			$sql = $this->make_update_sql('tb_loads',$record,'WHERE `id`='.$id);
		} else {
			$sql = $this->make_insert_sql('tb_loads',$record);
		}
		$this->query($sql) OR $this->error_register();
	}
	
	public function getLastDir() {
		$dir = $this->dir;
		$files = array();
		foreach (glob($dir.'*') as $filename) {
			if ( is_dir($filename) ) {
		  		$time = filemtime($filename);
		  		$files[$time] = $filename;
			}
		}
		
		krsort($files);
		$files = array_values($files);
		echo '<hr /><pre>' . print_r(array($files,$files[0]), true) . '</pre><hr />';
		return $files[0].'/'; 
	}
	/**
	 * downlad from url tar to downloads dircetory and extracts it there
	 *
	 * @param string $url source
	 */
	public function downloadTar($url) {
		
		$file = basename($url);
		$sql = sprintf("SELECT * FROM `tb_loads` WHERE `status`=1");
		$result = $this->query($sql) OR $this->error_register();
		if ( mysql_num_rows($result) > 0 ) {
			echo 'processing files in progress aborting download...<br/>'.PHP_EOL;
		}
		$target_dir = $this->dir.time().'/';
		mkdir($target_dir);
		chmod($target_dir,0777);
		foreach (glob($this->dir.'*') as $filename) {
			if ( is_dir($filename) ) {
		  		$time = filemtime($filename);
		  		$files[$time] = $filename;
			}
		}
		ksort($files);
		if ( count($files) > 5 ) {
			$files = array_values($files);
			exec('rm -rf '.$files[0],$output);
		}
		echo '<hr /><pre>' . print_r($output, true) . '</pre><hr />';
		exec('curl -o '.$target_dir.$file.' '.$url,$output);
		echo '<hr /><pre>' . print_r($output, true) . '</pre><hr />';
		chmod($target_dir.$file,0777);
		$s = 'tar -C '.$target_dir.' -xvzf '.$target_dir.$file;
		exec($s,$output);
		echo '<hr /><pre>' . print_r(compact('s','output'), true) . '</pre><hr />';
		foreach ( $output as $f ) {
			chmod($target_dir.$f,0777);
			$this->makeFile($f);
		}
		
	}
	/**
	 * read XML file and parsing it according to given name handlers
	 *
	 * @param string $file file name in downloads
	 * @param string $start_tag_function method name for start tag handler
	 * @param string $end_tag_function method name for end tag handler
	 * @param string $data_handler method name for content of tag handler
	 */
	public function loadFile($file,$start_tag_function,$end_tag_function,$data_handler,$unparsed_handler='') {
		$this->_file = strrpos($file,'/')!=false?$file:$this->getLastDir().$file;

        $this->_parser = xml_parser_create('UTF-8');
        xml_set_object($this->_parser, $this);
        xml_set_element_handler($this->_parser, Array(&$this,$start_tag_function), Array(&$this,$end_tag_function));
        xml_set_character_data_handler($this->_parser, Array(&$this, $data_handler));
        if ( !empty($unparsed_handler)) xml_set_unparsed_entity_decl_handler($this->_parser, Array(&$this, $unparsed_handler));
        xml_parser_set_option($this->_parser,XML_OPTION_SKIP_WHITE,0);
        if (!($fp = @fopen($this->_file, 'rb'))) { 
            throw new Exception("Cannot open {$this->_file}"); 
        } 

        while (($data = fread($fp, 8192))) { 
        	//$data = mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data));
        	//$data = html_entity_decode($data,ENT_NOQUOTES,'UTF-8'); 
        	$data = $this->utf8Replacer($data);
            if (!xml_parse($this->_parser, $data, feof($fp))) { 
            	$ec = xml_get_error_code($this->_parser);
            	$estring = xml_error_string($ec);
                throw new Exception(sprintf('XML error at line %d column %d, error %d: %s', 
                xml_get_current_line_number($this->_parser), 
                xml_get_current_column_number($this->_parser),$ec,$estring)); 
            } 
        } 
	}
	/**
	 * generic load data to handle cdata and normal data handling
	 *
	 * @param resource $parser xml parser
	 * @param string $data given data
	 * @param string $record_name the uppear tag contain the fields
	 */
	protected function loadData(&$parser,$data,$record_name) {
		if ( count($this->_stack) >= 2 && strcmp($this->_stack[count($this->_stack)-2],$record_name)==0 ) {
			$this->_record[$this->_stack[count($this->_stack)-1]] = $data;
		} elseif ( count($this->_stack) >= 3 && strcmp($this->_stack[count($this->_stack)-3],$record_name)==0 ) {
			$this->_record[$this->_stack[count($this->_stack)-2]] = trim($data);
		} 
	}
	/**
	 * genric detection for start record tag
	 *
	 * @param resource $parser the xml parser object
	 * @param string $name name of tag
	 * @param array $attribs attributes of tag
	 * @param string $record_name uppear tag to check for record initiating
	 */
	protected function startTag($parser, $name, array $attribs,$record_name) {
		$name = strtolower($name);
		$this->_stack[] = $name;
		if ( strcmp($name,$record_name) == 0 ) {
			$this->_record = array();
		}
	}
	/**
	 * automatic handler for fields in city file, load value into the record
	 *
	 * @param resource $parser XML parser calling the function
	 * @param string $data field value
	 */
	public function dataCities($parser,$data) {
		$this->loadData($parser,$data,'city');
		
		/*if ( $this->counter < 60 ) {
			$record = $this->_record;
			$stack = $this->_stack;
			echo '<hr /><pre>' . print_r(compact('data','stack','record'), true) . '</pre><hr />';
			$this->counter++;
		}*/
	}
	/**
	 * handling open tag in cities file
	 *
	 * @param resource $parser XML parser handling the file
	 * @param string $name tag name
	 * @param array $attribs attributes of tag
	 */
	public function startCities($parser, $name, array $attribs) {
		$this->startTag($parser, $name,$attribs,'city');
	}
	
	/**
	 * handling open tag in hotels file
	 *
	 * @param resource $parser XML parser handling the file
	 * @param string $name tag name
	 * @param array $attribs attributes of tag
	 */
	public function startHotels($parser, $name, array $attribs) {
		$this->startTag($parser, $name,$attribs,'hotel');
	}
	
	
	/**
	 * handling open tag in descriptions file
	 *
	 * @param resource $parser XML parser handling the file
	 * @param string $name tag name
	 * @param array $attribs attributes of tag
	 */
	public function startDescriptions($parser, $name, array $attribs) {
		$name = strtolower($name);
		$this->startTag($parser, $name,$attribs,'description');
		if ( strcmp($name,'description') == 0 ) {
			$this->hotelid = $attribs['HOTELID'];
		}
		if ( $this->counter < 30 ) {
			echo  print_r(compact('name','attribs'), true);
			$this->counter++;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param resource $parser xml parser
	 * @param string $data given data
	 */
	public function dataDescriptions($parser,$data) {
		if ( $this->counter < 30 ) {
			$stack = $this->_stack;
			$hotel_id = $this->hotelid;
			echo  print_r(compact('data','stack','hotel_id'), true);
			$this->counter++;
		}
		if ( preg_match('/\S/',$data) == 1 && !empty($this->hotelid) && !empty($this->_stack[2]) && strcmp($this->_stack[2],'description')==0 ) {
			//description detected updating at once
			$this->_record['description'] = preg_replace('/[\r\n]/',' ',$data);
			
		}
	}
	
	public function endDescriptions($parser, $name) {
		$name = strtolower($name);
		$this->_current = array_pop($this->_stack);
		$test_descriptions = $this->query(sprintf("SELECT * FROM `tb_hotels_lang` WHERE `obj_id`=%d AND `lang_id`='2'",$this->hotelid)) OR $this->error_register();
		if ( mysql_num_rows($test_descriptions) > 0 && strcmp($name,'description') == 0 ) {
			$record = array('descripation'=>$Db->make_escape($this->_record['description']));
			$this->query($this->make_update_sql('tb_hotels_lang',$record,"WHERE `obj_id`='{$this->hotelid}' AND `lang_id`='2'")) OR $this->error_register();
			$this->hotelid = 0;
		}
	}
	
	/**
	 * get description from file
	 *
	 * @param string $file xml file to load descriptions from
	 */
	public function getDescriptions($file) {
		$file = $this->getLastDir().$file;
		if ( !file_exists($file) ) {
			throw new Exception(sprintf('file %s is missing, aborting',$file),11);
		}
		if ( !$this->testFile($file) ) return false;
		$this->loadFile($file,'startDescriptions','endDescriptions','dataDescriptions','dataDescriptions');
		$this->finishFile($file);
	}
	/**
	 * automatic handler for fields in hotels file, load value into the record
	 *
	 * @param resource $parser XML parser calling the function
	 * @param string $data field value
	 */
	public function dataHotels($parser,$data) {
		$this->loadData($parser,$data,'hotel');
	}
	
	
	/**
	 * handling close tag in cities file
	 *
	 * @param resource $parser XML parser handling the file
	 * @param string $name tag name
	 */
	public function endCities($parser, $name) {
		$name = strtolower($name);
		$this->_current = array_pop($this->_stack);
		if ( strcmp($name,'city') == 0 && 
				isset($this->_countries[$this->_record['countrycode']]) && 
				$this->_countries[$this->_record['countrycode']] == 100 ) {
			//echo '<hr /><pre>' . print_r(compact('city'), true) . '</pre><hr />';
			if ( !isset($this->_countries[$this->_record['countrycode']]) ) {
				$country = array(
					'code' => $this->_record['countrycode'],
				);
				$this->query($this->make_insert_cond_sql('tb_countries',$country,array('code'))) OR $this->error_register();
				$this->loadCountries();
				$country_lang = array(
					'obj_id' => $this->_countries[$this->_record['countrycode']],
					'lang_id' => 2,
					'title' => $this->_record['countryname'],
				);
				$this->query($this->make_insert_cond_sql('tb_countries_lang',$country_lang,array('obj_id','lang_id'))) OR $this->error_register();
			}
			$city = array(
					'id' => $this->_record['id'],
					'cityseoname' => $this->_record['cityseoname'],
					'country_id' => $this->_countries[$this->_record['countrycode']],
			);
			$this->query($this->make_insert_cond_sql('tb_cities',$city,array('id'))) OR $this->error_register();
			$city_lang = array(
					'obj_id' => $this->_record['id'],
					'lang_id' => 2,
					'city_name' => $this->_record['cityname'],
					'city_state' => $this->_record['citystate'],
					'priority' => $this->_record['priority'],
			);
			$this->query($this->make_insert_cond_sql('tb_cities_lang',$city_lang,array('obj_id','lang_id'))) OR $this->error_register();
		}
		
	}
	/**
	 * load cities table from cities xml file
	 *
	 * @param string $file file name to load
	 */
	public function getCities($file) {
		$file = $this->getLastDir().$file;
		if ( !file_exists($file) ) {
			throw new Exception(sprintf('file %s is missing, aborting',$file),11);
		}
		if ( !$this->testFile($file) ) return false;
		$this->loadFile($file,'startCities','endCities','dataCities');
		$this->finishFile($file);
	}
	
	/**
	 * handling close tag in cities file
	 *
	 * @param resource $parser XML parser handling the file
	 * @param string $name tag name
	 */
	public function endHotels($parser, $name) {
		$name = strtolower($name);
		$this->_current = array_pop($this->_stack);
		if ( strcmp($name,'hotel') == 0 ) {
			
			$hotel = Arr::extract($this->_record,array('id','hotelseoname','cityid','zip','phone','fax','longitude','latitude','rating'));
			$test_hotel = $this->query(sprintf('SELECT * FROM `tb_cities` WHERE `id`=%d',$hotel['cityid'])) OR $this->error_register();
			if ( mysql_num_rows($test_hotel) > 0 ) {
				$this->query($this->make_insert_cond_sql('tb_hotels',$hotel,array('id'))) OR $this->error_register();
				$hotel_lang = array(
						'obj_id' => $this->_record['id'],
						'lang_id' => 2,
						'hotelname' => $this->_record['hotelname'],
						'address' => $this->_record['address'],
				);
				$this->query($this->make_insert_cond_sql('tb_hotels_lang',$hotel_lang,array('obj_id','lang_id'))) OR $this->error_register();
			}
		}
		
	}
	
	public function getHotels($file) {
		$file = $this->getLastDir().$file;
		if ( !file_exists($file) ) {
			throw new Exception(sprintf('file %s is missing, aborting',$file),11);
		}
		if ( !$this->testFile($file) ) return false;
		$this->loadFile($file,'startHotels','endHotels','dataHotels');
		$this->finishFile($file);
	}
	
	public function startFacilities($parser, $name, array $attribs) {
		$name = strtolower($name);
		$this->_stack[] = $name;
		
		if ( in_array('general',$this->_stack) ) {
			if ( strcmp($name,'facility') == 0 ) {
				$this->_record = array();
			}
		} else {
			// connect facilities to hotels
			
			if ( strcmp($name,'hotel') == 0 ) {
				$this->_record = array('hotelid'=>$attribs['ID'],'facilities'=>array());
			}
			/*if ( $this->counter < 40 ) {
				$this->counter++;
				$stack = $this->_stack;
				$record = $this->_record;
				echo '<hr /><pre>' . print_r(compact('record','stack','name','attribs'), true) . '</pre><hr />';
			}*/
			
		}
		
	}
	
	public function dataFacilities($parser,$data) {
		
		if ( in_array('general',$this->_stack) ) {
			// load standard facilities record
			$this->loadData($parser,$data,'facility');
		} else {
			// load facilities to hotel record
			if ( strcmp($this->_stack[count($this->_stack)-1],'facilityid') == 0 ) {
				$this->_record['facilities'][$data] = $data;
			}
		}
		/*if ( $this->counter < 40 ) {
			$this->counter++;
			$record = $this->_record;
			echo '<hr /><pre>' . print_r(compact('record','name','attribs'), true) . '</pre><hr />';
		}*/
		
	}
	
	public function endFacilities($parser, $name) {
		$name = strtolower($name);
		$this->_current = array_pop($this->_stack);
		if ( in_array('general',$this->_stack) ) {
			if ( strcmp($name,'facility') == 0 ) {
				$record = array(
					'id' => $this->_record['facilityid'],
				);
				$this->query($this->make_insert_cond_sql('tb_facilities',$record,array('id'))) OR $this->error_register();
				$record = array(
					'obj_id' => $this->_record['facilityid'],
					'lang_id' => 2,
					'title' => $this->_record['title'],
				);
				$this->query($this->make_insert_cond_sql('tb_facilities_lang',$record,array('obj_id','lang_id'))) OR $this->error_register();
			}
		} else {
			if ( strcmp($name,'hotel') == 0 ) {
				$hotel_id = $this->_record['hotelid'];
				$sql = sprintf('SELECT * FROM `tb_hotels` WHERE `id`=%d',$hotel_id);
				$result = $this->query($sql) OR $this->error_register();
				if ( mysql_num_rows($result) > 0 ) {
					foreach ( $this->_record['facilities'] as $facility_id ) {
						$record =  array('hotel_id'=>$hotel_id,'facility_id'=>$facility_id);
						$this->query($this->make_insert_cond_sql('tb_hotel_facilities',$record,array('hotel_id','facility_id'))) OR $this->error_register();
					}
				}
			}
		}
	}
	
	/**
	 * load facilities table from facilities xml file
	 *
	 * @param string $file file name to load
	 */
	public function getFacilities($file) {
		$file = $this->getLastDir().$file;
		if ( !file_exists($file) ) {
			throw new Exception(sprintf('file %s is missing, aborting',$file),11);
		}
		if ( !$this->testFile($file) ) return false;
		//echo 'getting facilities'."\n";
		$this->loadFile($file,'startFacilities','endFacilities','dataFacilities');
		$this->finishFile($file);
	}
	
	
	/**
	 * handle start images tags
	 *
	 * @param resource $parser xml parser
	 * @param string $name tag name
	 * @param array $attribs attributes of tag
	 */
	public function startImages($parser, $name, array $attribs) {
		$name = strtolower($name);
		$this->_stack[] = $name;
		
		if ( strcmp($name,'image') == 0 ) {
			$this->_record = array('is_main'=>$attribs['ISMAIN']);
		}

		if ( strcmp($name,'domain') == 0 ) {
			$this->isDomainNode = true;
		}
		
	}
	/**
	 * handles charachter data of images fields
	 *
	 * @param unknown_type $parser
	 * @param unknown_type $data
	 */
	public function dataImages($parser,$data) {

		if ($this->isDomainNode){
			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/_static/cdndomain.txt',$data);
		}

		if ( in_array('images',$this->_stack) ) {
			// load standard images record
			$this->loadData($parser,$data,'image');
		}
		if ( $this->counter < 40 ) {
			$this->counter++;
			$record = $this->_record;
			echo '<hr /><pre>' . print_r(compact('record','name','attribs'), true) . '</pre><hr />';
		}
		
	}
	
	public function endImages($parser, $name) {
		$name = strtolower($name);
		$this->_current = array_pop($this->_stack);
		$sql = sprintf('SELECT * FROM `tb_hotels` WHERE `id`=%d',$this->_record['hotelid']);
		$test_image = $this->query($sql) OR $this->error_register();
		if ( in_array('images',$this->_stack) && mysql_num_rows($test_image) > 0 ) {
			if ( strcmp($name,'image') == 0 ) {
				$this->imRec++;
				file_put_contents($_SERVER['DOCUMENT_ROOT'].'/_static/images_records.txt','images read already: '.$this->imRec);
				$record = array(
					'hotel_id' => $this->_record['hotelid'],
					'is_main' => $this->_record['is_main'],
					'last_update' => time(),
				);
				$this->query($this->make_insert_sql('tb_hotel_images',$record)) OR $this->error_register();
				$mid = mysql_insert_id();
				$record = array(
					'obj_id' => $mid,
					'lang_id' => 2,
					'title' => isset($this->_record['title'])?$this->_record['title']:'',
					'url' => $this->_record['url'],
				);
				$this->query($this->make_insert_sql('tb_hotel_images_lang',$record)) OR $this->error_register();
			}
		}

		if ( strcmp($name,'domain') == 0 ) {
			$this->isDomainNode = false;
		}
	}
	
	/**
	 * load images table from images xml file
	 *
	 * @param string $file file name to load
	 */
	public function getImages($file) {
		
		$file = $this->getLastDir().$file;
		if ( !file_exists($file) ) {
			throw new Exception(sprintf('file %s is missing, aborting',$file),11);
		}
		//echo 'now testing '.$file."<br/>\n";
		if ( !$this->testFile($file) ) return false;
		//echo 'getting images'."\n";
		$this->loadFile($file,'startImages','endImages','dataImages');
		$this->finishFile($file);
	}
	/**
	 * gets country code and only the cities,hotels,images and facilities belongs to it remain
	 *
	 * @param string $code country code for Israel set 'IL'
	 */
	public function countrySection($code) {
		$sql = sprintf("SELECT `id` FROM `tb_countries` WHERE `code`='%s'",$code);
		$res = mysql_query($sql);
		$id = mysqli_result($res,0);
		if ( empty($id) ) {
			throw new Exception(sprintf('%s is not country',$code),12);
		}
		$this->makeFile('section.xml');
		$this->testFile('section.xml');
		$sql = sprintf("DELETE FROM `tb_cities` WHERE `country_id` NOT IN (100,355)");
		echo $sql."\n";
		mysql_query($sql);
		$sql = sprintf("DELETE FROM `tb_cities_lang` WHERE `obj_id` NOT IN (SELECT `id` FROM `tb_cities`)");
		echo $sql."\n";
		mysql_query($sql);
		$sql = sprintf("DELETE FROM `tb_hotels` WHERE `cityid` NOT IN (SELECT `id` FROM `tb_cities`)");
		echo $sql."\n";
		mysql_query($sql);
		$sql = sprintf("DELETE FROM `tb_hotels_lang` WHERE `obj_id` NOT IN (SELECT `id` FROM `tb_hotels`)");
		echo $sql."\n";
		mysql_query($sql);
		$sql = sprintf("DELETE FROM `tb_hotel_facilities` WHERE `hotel_id` NOT IN (SELECT `id` FROM `tb_hotels`)");
		echo $sql."\n";
		mysql_query($sql);
		$sql = sprintf("DELETE FROM `tb_hotel_images` WHERE `hotel_id` NOT IN (SELECT `id` FROM `tb_hotels`)");
		mysql_query($sql);
		$sql = sprintf("DELETE FROM `tb_hotel_images_lang` WHERE `obj_id` NOT IN (SELECT `id` FROM `tb_hotel_images`)");
		echo $sql."\n";
		mysql_query($sql);
		$this->finishFile('section.xml');
	}
	/**
	 * tests that there is unprocessed file to run
	 *
	 * @param string $file file name
	 * @return true if the file ready to run and false otherwise
	 */
	public function verifyXML($file) {
		$file = basename($file);
		if ( preg_match('/\.xml$/',$file) == 0 ) {
			$file .= '.xml';
		}
		echo 'verifies '.$file."<br/>\n";
		$sql = 'SELECT * FROM `tb_loads` WHERE `status`=1';
		$result = $this->query($sql) OR $this->error_register();
		if ( empty($result) || mysql_num_rows($result) > 0 ) {
			return 1;
		}
		echo 'passed not running test '."<br/>\n";
		$sql = sprintf('SELECT * FROM `tb_loads` WHERE `name`="%s" AND `status`=0',$file);
		$result = $this->query($sql) OR $this->error_register();
		if ( empty($result) || mysql_num_rows($result) == 0 ) {
			return 0;
		}
		echo 'passed '.$file.' availability'."<br/>\n";
		return 2;
	}
	/**
	 * change status of file to run
	 *
	 * @param string $file file name
	 */
	public function approveXML($file) {
		$file = basename($file);
		if ( preg_match('/\.xml$/',$file) == 0 ) {
			$file .= '.xml';
		}
		$record = array('status'=>1);
		$sql = $this->make_update_sql('tb_loads',$record,sprintf('WHERE `name`="%s" AND `status`=0',$file));
		$this->query($sql) OR $this->error_register();
	}
	/**
	 * verify file if failed throws exception else approving the file
	 * @throws Exception failed to verify
	 * @param string $file file name
	 */
	public function testFile($file) {
		$vxml = $this->verifyXML($file);
		if ( $vxml == 1 ) {
			throw new Exception(sprintf('%s not ready for upload',$file),13);
		} else if ( $vxml == 0 ) {
			return false;
		}
		$this->approveXML($file);
		echo 'testing complete successesfuly for '.$file."\n";
		return true;
	}
	
	public function finishFile($file) {
		$file = basename($file);
		if ( preg_match('/\.xml$/',$file) == 0 ) {
			$file .= '.xml';
		}
		$record = array('status'=>2,'completion_date'=>date('Y-m-d H:i:s'));
		$sql = $this->make_update_sql('tb_loads',$record,sprintf('WHERE `name`="%s" AND `status`=1',$file));
		$this->query($sql) OR $this->error_register();
	}
}
