<?php
namespace PMVC\PlugIn\dispatcher;
use SplSubject;
use SplObserver;
use SplObjectStorage;
use PMVC;
use PMVC\Alias;

class Subject implements SplSubject
{
    /**
     * Alias
     */
    use Alias {
        getTypeOfAlias as private _getTypeOfAlias;
    }

    private $_name;
    private $_doClean;

    public function __construct($name)
    {
        $this->_name = $name;
        $this->setDefaultAlias(new SplObjectStorage());
    }

    #[\ReturnTypeWillChange]
    public function notify()
    {
        $tmp = new SplObjectStorage();
        $store = $this->defaultAlias;
        $store->rewind();
        while ($store->valid()) {
            $obj = $store->current();
            $store->next();
            if ($this->_doClean) {
                $store->detach($obj);
            }
            $obj->update($this);
            if ($store->contains($obj)) {
                $tmp->attach($obj);
                $store->detach($obj);
            }
        }
        unset($this->defaultAlias);
        $this->setDefaultAlias($tmp);
    }
    #[\ReturnTypeWillChange]
    public function attach(SplObserver $observer)
    {
        $observer = $this->_getThis($observer);
        $this->defaultAlias->attach($observer);
    }

    #[\ReturnTypeWillChange]
    public function detach(SplObserver $observer)
    {
        $observer = $this->_getThis($observer);
        $this->defaultAlias->detach($observer);
    }

    public function getName()
    {
        return strtolower($this->_name);
    }

    public function setDoClean($bool)
    {
        $this->_doClean = $bool;
    }

    private function _getThis(SplObserver $observer)
    {
        return \PMVC\get($observer, \PMVC\THIS, function () use ($observer) {
            return $observer;
        });
    }

    public function contains(SplObserver $observer)
    {
        $observer = $this->_getThis($observer);
        return $this->defaultAlias->contains($observer);
    }

    public function removeAll($object = null)
    {
        if (empty($object)) {
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
