<?
/**
 * @author : gal zalait
 * @desc : manage site transfer - hold all information ,sort items , dont need to send lang only if we wont to load anthor lang of the item 
 * @var : 1.0
 * @last_update :  07/01/2013
 * @example : $Transfer = new transferManager(1);
 * 
 */

class hotelManager{

	public $id='';	
	public $hotelseoname='';
	public $cityid='';
	public $zip='';
	public $phone='';
	public $fax='';
	public $rating='';
	public $latitude='';
	public $longitude='';
	public $lang_id='';
	public $hotelname='';
	public $descripation='';
	public $address='';

	public $hotel_facilities='';
	public $hotel_images='';


	function __construct($item_id,$lang=''){
		$this->ts = time();
		if(!$lang){
			$lang=(isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
		}
		include($_SERVER['DOCUMENT_ROOT'].'/_static/hotels/'.get_item_dir($item_id).'/'.$lang.'/hotel-'.$item_id.'.inc.php');//$hotelArr
		
		foreach ($hotelArr AS $key=>$value){
			if(property_exists($this,$key)){
				$this->{$key}=$value;
			}
		}  

		// expand images and facilities to full values
		$this->hotel_facilities = explode(',',$hotelArr['hotel_facilities']);

		//$this->hotel_images = eval($this->hotel_images);

	}   

	//-------------------------------------------------------------------------------------------------------------------//

	function __destruct(){

	}

	//-------------------------------------------------------------------------------------------------------------------//

	public function __set($var, $val){
		$this->$var = $val;
	}


	//-------------------------------------------------------------------------------------------------------------------//


	public function __get($var){
		return $this->$var;
	}

	//-------------------------------------------------------------------------------------------------------------------//
}

?>
