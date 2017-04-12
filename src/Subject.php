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
    public function __construct($name)
    {
       $this->_name = $name; 
       $this->setDefaultAlias(new SplObjectStorage());
    }

    public function notify()
    {
        $this->defaultAlias->rewind();
        while ($this->defaultAlias->valid()) {
            $obj = $this->defaultAlias->current();
            $this->defaultAlias->next();
            $obj->update($this);
        }
    }

    public function getName()
    {
        return $this->_name;
    }

    public function attach ( SplObserver $observer )
    {
        $observer = \PMVC\get($observer, \PMVC\THIS, $observer);
        $this->defaultAlias->attach($observer); 
    }

    public function detach ( SplObserver $observer )
    {
        $observer = \PMVC\get($observer, \PMVC\THIS, $observer);
        $this->defaultAlias->detach($observer); 
    }

    public function contains ( SplObserver $observer )
    {
        $observer = \PMVC\get($observer, \PMVC\THIS, $observer);
        return $this->defaultAlias->contains($observer); 
    }

    public function removeAll ( $object=null )
    {
        if(empty($object)){
            $object = $this->defaultAlias;
        }
        $this->defaultAlias->removeAll($object);
    }

    protected function getTypeOfAlias()
    {
        $arr = $this->_getTypeOfAlias(); 
        return [$arr['aliasAsDefault']];
    }
}



