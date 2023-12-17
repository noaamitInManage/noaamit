<?php
/**
 * Created by PhpStorm.
 * User: inManage_ilanr
 * Date: 02/08/16
 * Time: 15:05
 */

if(class_exists('Thread')){
	class threadJob extends Thread {

		public function __construct($arg) {
			$this->arg = $arg;
		}

		public function run() {
			if ($this->arg) {
				$sleep = mt_rand(1, 2);
				usleep($sleep);
			}
		}
	}
}
if(in_array($_SERVER['REMOTE_ADDR'],array('62.219.212.139','81.218.173.175'))) { 
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}
// ================== Check INI variables ==================

$required_iniArr = array(
	'session.cookie_httponly' => array('name' => 'session.cookie_httponly' , 'required_value' => 1),
	'session.cookie_secure' => array('name' => 'session.cookie_secure' , 'required_value' => 1),
	'short_open_tag' => array('name' => 'short_open_tag' , 'required_value' => 1),
	'post_max_size' => array('name' => 'post_max_size' , 'required_value' => '128M'),
	'upload_max_filesize' => array('name' => 'upload_max_filesize' , 'required_value' => '128M'),
	'memory_limit' => array('name' => 'memory_limit' , 'required_value' => '256M'),
);

$required_iniArr = test_server_for_ini_vars($required_iniArr);
$thArr = array(
	'INI Variable Name',
	'Required Value',
	'Existed Value',
	'Is Equal',
);
array_unshift($required_iniArr, $thArr);
echo '<h1>Test Server Configuration Report</h1>';
echo '<h2>INI Variables</h2>';
echo draw_table($required_iniArr,true);

// ================== Check extensions ==================

$required_extensionsArr = array(
	'wkhtmltopdf' => array('name' => 'Wkhtmltopdf','method'=>'whereis','inspected_value'=>'wkhtmltopdf'),
	'Imageick' => array('name' => 'Imageick','method'=>'class_exist','inspected_value'=>'Imagick'),
	'Gd' => array('name' => 'Gd','method'=>'function_exist','inspected_value'=>'gd_info'),
	'Mbstring' => array('name' => 'Mbstring','method'=>'phpinfo','inspected_value'=>'mbstring'),
	'Mcrypt' => array('name' => 'Mcrypt','method'=>'function_exist','inspected_value'=>'mcrypt_encrypt'),
	'Soap' => array('name' => 'Soap' ,'method'=>'class_exist','inspected_value'=>'SoapClient'),
	'memcache' => array('name' => 'memcache','method'=>'memcache_exist','inspected_value'=>'memcache' ),
	'pthreads' => array('name' => 'pthreads','method'=>'pthreads_exist' ,'inspected_value'=>''),
	'solar' => array('name' => 'solar','method'=>'class_exist','inspected_value'=>'SolrClient'),
	'redis' => array('name' => 'redis','method'=>'class_exist','inspected_value'=>'Redis'),
);
$memcache_output = '';
$pthreads_output = '';
$exceptionsArr = array();

$extensions_validationArr = test_server_for_extensions($required_extensionsArr);


echo '<h2>Extensions</h2>';

$thArr = array(
	'Extension Name',
	'Is Exist',
);
array_unshift($extensions_validationArr, $thArr);
echo draw_table($extensions_validationArr,true);
if(!empty($memcache_output)){
	echo '<h2>Memcache Configuration</h2>';
	echo('<hr /><pre dir="ltr">' . print_r($memcache_output, true) .'</pre><hr />');
}
if(!empty($pthreads_output)){
	echo '<h2>Pthreads Test Output</h2>';
	echo('<hr /><pre dir="ltr">' . print_r($pthreads_output, true) .'</pre><hr />');
}
// ================== Check extensions ==================


if(!empty($exceptionsArr)){
	echo '<h2>Exceptions</h2>';
	die('<hr /><pre dir="ltr">' . print_r($exceptionsArr, true) .'</pre><hr />');
}

echo '<pre>SERVER:';
print_r($_SERVER);
echo '</pre>';

echo '<br /><hr /><br />';
echo '<a href="#phpinfo" onclick="document.getElementById(\'phpinfo\').style.display=(document.getElementById(\'phpinfo\').style.display==\'\'?\'none\':\'\');">Toggle PHPINFO</div>';
echo '<a name="phpinfo"></a><div style="display:none" id="phpinfo">';
phpinfo();
echo '</div>';

/**
 * @param $dataArr
 * @return string of table html
 */
function draw_table($dataArr, $is_echo_style = false){
	if (!is_array($dataArr)) {
		return '';
	}
	$is_first = true;
	$html = '<table>';
	foreach ($dataArr as $row) {

		$html .= '<tr>';

		foreach ($row as $name => $data) {
			$class = '';
			if($name == 'is_valid'){
				if($data == 'X'){
					$class = 'bad';
				}elseif($data == 'V'){
					$class = 'good';
				}
			}
			$html .= $is_first ? '<th>' : '<td class="'.$class.'">';
			$html .=  $data;
			$html .= $is_first ? '</th>' : '</td>';
		}
		$html .= '</tr>';

		if($is_first){
			$is_first = false;
		}
	}
	$html .= '</table>';
	if ($is_echo_style) {
		$html .= <<<HTML
		<style>
			table{
				width: 100%;
				border: 1px solid #000;
			}
			th {
				text-decoration: underline;
				background-color: #ddd;
			}
			th.red {
				text-decoration: underline;
				background-color: red;
			}
			th.yellow {
				text-decoration: underline;
				background-color: yellow;
			}
			td {
    			text-align: center;
				border: 1px solid #ddd;
			}
			tr.odd {
				background-color: #eee;
			}
			td.bad{
				background-color: #ff0000;
			}
			td.good{
				background-color: lawngreen;
			}
		</style>
HTML;
	}
	return $html;
}

/**
 * @param $required_iniArr
 * @return mixed
 */
function test_server_for_ini_vars($required_iniArr){
	$all_iniArr = ini_get_all();
	$value_type = 'global_value'; //{'global_value','local_value'}
	foreach ($required_iniArr as $var_name => $var_data) {

		$required_iniArr[$var_name]['existed_value'] = '(not exist)';
		if (isset($all_iniArr[$var_name])) {
			$required_iniArr[$var_name]['existed_value'] = $all_iniArr[$var_name][$value_type];
			if ($all_iniArr[$var_name][$value_type] == $var_data['required_value']) {
				$required_iniArr[$var_name]['is_valid'] = 'V';
			} else {
				$required_iniArr[$var_name]['is_valid'] = 'X';
			}

		} else {
			$required_iniArr[$var_name]['is_valid'] = 'X';
		}
	}

	return $required_iniArr;
}

/**
 * @param $required_extensionsArr
 * @param $exceptionsArr
 * @return mixed
 */
function test_server_for_extensions($required_extensionsArr){
	global $exceptionsArr,$pthreads_output,$memcache_output;
	$resultArr = array();

	foreach ($required_extensionsArr as $extension_name => $extension_data) {
		$resultArr[$extension_name]['name'] = $extension_name;
		$resultArr[$extension_name]['is_valid'] = 'X';

		switch ($extension_data['method']){
			case 'whereis':
				try {
					$result = shell_exec ( 'whereis '.$extension_data['inspected_value'] );
				} catch (Exception $e) {
					$exceptionsArr[$extension_name] = $e->getMessage();
				}
				$exploaded_result = explode(':',$result);
				if(count($exploaded_result)>1){
					$resultArr[$extension_name]['is_valid'] = 'V';
				}
				break;
			case 'class_exist':
				$resultArr[$extension_name]['is_valid'] = class_exists($extension_data['inspected_value']) ? 'V' : 'X';
				break;
			case 'function_exist':
				$resultArr[$extension_name]['is_valid'] = function_exists($extension_data['inspected_value']) ? 'V' : 'X';
				break;
			case 'phpinfo':
				ob_start();
				phpinfo();
				$php_info_html = ob_get_clean();
				$resultArr[$extension_name]['is_valid'] = (strpos($php_info_html,$extension_data['inspected_value']) !== false) ? 'V' : 'X';
				break;
			case 'memcache_exist':
				$resultArr[$extension_name]['is_valid'] = is_memcache_exist() ? 'V' : 'X';
				break;
			case 'pthreads_exist':

				$resultArr[$extension_name]['is_valid'] = is_pthreads_exist() ? 'V' : 'X';
				break;
		}
	}

	return $resultArr;
}

function is_memcache_exist(){
	global $exceptionsArr,$memcache_output;
	if(class_exists('Memcache')){
		$Memcached = new Memcache();
		$status = $Memcached->addServer('localhost', 11211) ;
		$memcache_output .= print_r($Memcached->getstats(),true);
		return true;
	}else{
		return false;
	}
}

function is_pthreads_exist(){
	global $exceptionsArr,$pthreads_output;
	if(!class_exists('Thread')){
		return false;
	}
	if(!class_exists('threadJob')){
		return false;
	}
	
	try{
		// Create a array and save th threads process
		$stack = array();

		for ($i=0;$i<5;$i++  ) {
			$pthreads_output.= "<p>process {$i} is running<p>";
			$stack[] = new threadJob($i);
		}
		// Start The Threads
		foreach ( $stack as $t ) {
			$t->start();
			$pthreads_output.= " pid :{$t->getThreadId()}<Br>";
		}
		$i=0;
		foreach ( $stack as $t ) {
			$i++;
			if($t->isRunning()){
				$pthreads_output.= "<br>Therad $i is running<br>"	;
			}
		}
	}catch (Exception $e) {
		$exceptionsArr['memcache'] = $e->getMessage();
		return false;
	}

	if(!empty($pthreads_output)){
		return true;
	}
}
