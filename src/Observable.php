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
    public function __construct($name)
    {
       $this->_name = $name; 
       $this->_storage = new \SplObjectStorage();
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

    public function attach ( \SplObserver $observer )
    {
       $this->_storage->attach($observer); 
    }

    public function detach ( \SplObserver $observer )
    {
       $this->_storage->detach($observer); 
    }
}



