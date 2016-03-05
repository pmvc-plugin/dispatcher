<?php
namespace PMVC\PlugIn\dispatcher;
use SplSubject;
use SplObserver;
use SplObjectStorage;
use PMVC;

class Subject implements SplSubject
{
    /**
     * Alias
     */
    use PMVC\Alias {
        initAliasFunction as private aliasFunction;
    }

    private $_name;
    private $_storage;
    public function __construct($name)
    {
       $this->_name = $name; 
       $this->_storage = new SplObjectStorage();
       $this->setDefaultAlias($this->_storage);
    }

    public function notify()
    {
        foreach ($this->_storage as $obj) {
            $obj->update($this);
        }
    }

    public function getName()
    {
        return $this->_name;
    }

    public function attach ( SplObserver $observer )
    {
       $this->_storage->attach($observer); 
    }

    public function detach ( SplObserver $observer )
    {
       $this->_storage->detach($observer); 
    }

    public function removeAll ( $object=null )
    {
        if(empty($object)){
            $object = $this->_storage;
        }
        $this->_storage->removeAll($object);
    }

    public function initAliasFunction()
    {
        $arr = $this->aliasFunction(); 
        return [$arr['aliasDefaultClass']];
    }
}



