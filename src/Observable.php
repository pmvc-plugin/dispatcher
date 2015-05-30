<?php
namespace PMVC\PlugIn\dispatcher;

class Observable implements \SplSubject
{
    /**
     * Alias
     */
    use \PMVC\Alias;

    private $_name;
    private $_storage;
    function __construct($name)
    {
       $this->_name = $name; 
       $this->_storage = new \SplObjectStorage();
       $this->setDefaultAlias($this->_storage);
    }

    function notify()
    {
        foreach ($this->_storage as $obj) {
            $obj->update($this);
        }
    }

    function getName()
    {
        return $this->_name;
    }

    public function attach ( \SplObserver $observer )
    {
       $this->_storage->attach($observer); 
    }

    public function detach ( \SplObserver $observer )
    {
       $ttachthis->_storage->detach($observer); 
    }
}



