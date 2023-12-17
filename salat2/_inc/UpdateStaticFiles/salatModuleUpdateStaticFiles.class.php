<?php

class salatModuleUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    function __construct()
    {
        parent::setClassName(get_declared_classes());
    }

    public function updateStatics()
    {
    }

    public function updateAllStaticsFiles()
    {
    }

    public function createInstance($className)
    {
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->newInstance();
    }

}

//---------------------------------------------------------------------------//