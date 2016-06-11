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
        getTypeOfAlias as private _getTypeOfAlias;
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
        $this->_storage->rewind();
        while ($this->_storage->valid()) {
            $obj = $this->_storage->current();
            $this->_storage->next();
            $obj->update($this);
        }
    }

    public function getName()
    {
        return $this->_name;
    }

    public function attach ( SplObserver $observer )
    {
        $observer = $observer['this'] ?: $observer;
        $this->_storage->attach($observer); 
    }

    public function detach ( SplObserver $observer )
    {
        $observer = $observer['this'] ?: $observer;
        $this->_storage->detach($observer); 
    }

    public function removeAll ( $object=null )
    {
        if(empty($object)){
            $object = $this->_storage;
        }
        $this->_storage->removeAll($object);
    }

    protected function getTypeOfAlias()
    {
        $arr = $this->_getTypeOfAlias(); 
        return [$arr['aliasAsDefault']];
    }
}



