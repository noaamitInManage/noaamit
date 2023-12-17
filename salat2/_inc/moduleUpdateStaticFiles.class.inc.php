<?

/**
 * moduleUpdateStaticFiles Class
 * @author Gal Zalait @ inManage 2011
 * @date 14/4/2011
 * @version 1.0
 * @desc : update static file,
 *
 * now you can update static files from front and from the cms
 * this class also conntect to the cms under "main" sction you can update all static files of module - dont forget to fill up function  updateAllStaticsFiles()
 *
 */
abstract class moduleUpdateStaticFiles
{

    public $_Proccess_Main_DB_Table, $_ProcessID;
    public $line, $file;
    public $class = array();
    public $items_per_dir = 1000;

    //------------------------------------ FUNCTIONS -------------------------------------

    function __construct()
    {
        $class = class_implements('moduleUpdateStaticFiles');
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/_static/moduleUpdateStaticFiles.inc.php')) {
            $moduleUpdateStaticFilesArr = array();
            updateStaticFile($moduleUpdateStaticFilesArr,
                '/_static/moduleUpdateStaticFiles',
                'moduleUpdateStaticFilesArr');
        }

    }

    /*----------------------------------------------------------------------------------*/
    /*					 only use in moudle updateStaticFiles 						   */
    /*----------------------------------------------------------------------------------*/

    public function setClassName($classArr)
    {

        foreach ($classArr AS $key => $name) {
            if (strstr($name, 'UpdateStaticFiles')) {
                $this->class[] = trim($name);
            }
        }
        $delKey = array_search('moduleUpdateStaticFiles', $this->class);
        unset($this->class[$delKey]);
    }

    /*----------------------------------------------------------------------------------*/

    public function getGlobal($var)
    {
        global $$var;
        return $$var;
    }

    /*----------------------------------------------------------------------------------*/

    public function writeUpdate()
    {
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/moduleUpdateStaticFiles.inc.php');//$moduleUpdateStaticFilesArr
        $moduleUpdateStaticFilesArr[$this->className] = time();
        updateStaticFile($moduleUpdateStaticFilesArr,
            '/_static/moduleUpdateStaticFiles.inc.php',
            'moduleUpdateStaticFilesArr');
    }

    /*----------------------------------------------------------------------------------*/

    public function getItemsNumber()
    {
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);//$this->itemsArr_name
        $tmp = $this->itemsArr_name;
        return count($$tmp);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param $path
     * @param $item_id
     * order 1000 items per dirctory
     * build a new dir
     *
     * exmple:smartDirctory ('/_static/items/',1800);
     * save it on:  /_static/items/2/item-1800.inc.php"
     *
     */
    public function smartDirctory($path, $item_id)
    {

        $dirNum = ceil($item_id / $this->items_per_dir);
        $dirPath = $path . '/' . $dirNum . '/';
        $fullDirPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $path . '/' . $dirNum . '/';

        if (!is_dir($fullDirPath)) {
            $dirPath = str_replace('//', '/', $dirPath);
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath, 0777);// crate a new dir
            chmod($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath, 0777);// if the first line didnt Success
        }

        return $dirPath;
    }

    /*----------------------------------------------------------------------------------*/

    public function smartLangDirctory($path, $item_id, $lang_id = '')
    {
        global $languagesArr, $module_lang_id;

        if (!$module_lang_id) {
            global $module_lang_id;
        } else {
            $module_lang_id = $lang_id;
        }

        $dirNum = ceil($item_id / $this->items_per_dir);
        $dirPath = $path . '/' . $dirNum . '/';
        $fullDirPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $path . '/' . $dirNum . '/';

        if (!is_dir($fullDirPath)) {
            $dirPath = str_replace('//', '/', $dirPath);
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath, 0777);// crate a new dir
            chmod($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath, 0777);// if the first line didnt Success
        }

        $dirPath .= $languagesArr[$lang_id]['title'] . '/';
        $fullDirLangPath = $_SERVER['DOCUMENT_ROOT'] . $dirPath;

        if (!is_dir($fullDirLangPath)) {
            $dirPath = str_replace('//', '/', $dirPath);
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath, 0777);// crate a new dir
            chmod($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath, 0777);// if the first line didnt Success
        }

        return $dirPath;
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

}

//---------------------------------------------------------------------------//

interface iUpdate
{

    function updateStatics();

    function updateAllStaticsFiles();
}

//---------------------------------------------------------------------------//

/**
 * auto load all class in UpdateStaticFiles dir
 */
$class_path = $_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/UpdateStaticFiles/';
$classArr = scandir(($class_path));
unset($classArr[0]);
unset($classArr[1]);
foreach ($classArr AS $key => $file) {
    //include($class_path . $file);
}


/*
class templateUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate {
   
      //public $_Proccess_Main_DB_Table,$_ProcessID;
      public $className,$file_name,$itemsArr_name;
      
      
      //------------------------------------ FUNCTIONS ------------------------------------
      
      function __construct(){
         parent::__construct();
         $this->_Proccess_Main_DB_Table = 'tb_template';
         $this->_ProcessID = template_ID;
         $this->className= trim(get_class());
         $this->file_name='template.inc.php'; // all Items  for count in moudle update_static
         $this->itemsArr_name='templateArr';
      }
      
      /------------------------------------------------------------------------------------/
      
      function updateStatics(){
         
         
                    	updateStaticFile("SELECT id, title, category_id FROM {$this->_Proccess_Main_DB_Table}", 
            					'/_static/templates.inc.php', 
            					'templatesArr', 'id', true);
            					
           
                  	
                  	if($_REQUEST['inner_id']) {
                  		updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE id='{$_REQUEST['inner_id']}'", 
                  					'/_static/template/template-'.$_REQUEST['inner_id'].'.inc.php', 
                  					'template');
                  	}
                  
        
               
      }
     
      /------------------------------------------------------------------------------------/
      
      function updateAllStaticsFiles(){
         $query= " SELECT id FROM {$this->_Proccess_Main_DB_Table}";
         $result=$Db->query($query);
         
         while($row = mysql_fetch_assoc($result)) {
               updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE id='{$row['id']}'", 
   					'/_static/template/template-'.$row['id'].'.inc.php', 
   					'templateArr');
         }
         
         parent::writeUpdate();
      }

   
}

*/

//---------------------------------------------------------------------------//




?>
