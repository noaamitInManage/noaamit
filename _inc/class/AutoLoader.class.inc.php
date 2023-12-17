<?php

class AutoLoader
{
    private static $loader = null;

    private $paths = [];
    private $filename_templates = [
        ".php",
        ".interface.inc.php",
        ".trate.inc.php",
        ".class.inc.php",
        ".inc.class.php",
        ".enum.inc.php",
        ".class.php",
    ];

    private function __construct()
    {
        spl_autoload_register(array($this, 'loader'));
    }

    /**
     * @return AutoLoader
     */
    public static function get_loader()
    {
        if (self::$loader === null) {
            self::$loader = new self();
        }

        return self::$loader;
    }

    /**
     * get nested folders inside the parent's path
     * @param $parent - main path to scan
     * @return array - $parent + internals folders
     */
    private function get_folders($parent)
    {
        $parents = glob("{$parent}/*", GLOB_ONLYDIR);
        $folders = [$parent];
        while ($parents) {
            $dir = array_pop($parents);
            $folders[] = $dir;
            if ($internal_dirs = glob("{$dir}/*", GLOB_ONLYDIR)) {
                $parents = array_merge($parents, $internal_dirs);
            }
        }
        return $folders;
    }


    /**
     * @param $class_name
     */
    private function loader($class_name)
    {

        $total_templates = count($this->filename_templates);
        foreach ($this->paths as $path) {
            for ($i = 0; $i < $total_templates; $i++) {
                $filename = "{$path}/{$class_name}{$this->filename_templates[$i]}";

                if (file_exists($filename) && !class_exists($class_name)) {
                    include_once($filename);
                    if (class_exists($class_name)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * adds folders in order to load files.
     * @param array $paths
     * @return $this
     */
    public function load_from(array $paths = [])
    {
        foreach ($paths as $path) {
            $this->paths = array_merge($this->paths, $this->get_folders($path));
        }
        return $this;
    }

    /**
     * extends the filenames templates in case the resource name is in a different convension.
     * php files must be named in the following template:
     * {classname}.php
     * {classname}.interface.inc.php
     * {classname}.trate.inc.php
     * {classname}.class.inc.php
     * {classname}.inc.class.php
     * {classname}.enum.inc.php
     * {classname}.class.php
     * you can extend the templates list by running this method:
     * AutoLoader::get_loader()->extend_templates([
     * ".test.php",
     * ".test2.php",
     * ]);
     * the final templates list will be set to:
     * {classname}.php
     * {classname}.interface.inc.php
     * {classname}.trate.inc.php
     * {classname}.class.inc.php
     * {classname}.inc.class.php
     * {classname}.enum.inc.php
     * {classname}.class.php
     * {classname}.test.php
     * {classname}.test2.php
     * @param array $templates
     * @return $this
     */
    public function extend_templates(array $templates = [])
    {
        $this->filename_templates = array_merge($this->filename_templates, $templates);
        return $this;
    }
}
