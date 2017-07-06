<?php
namespace PMVC\PlugIn\dispatcher;

use PMVC as p;
use PMVC\Event;
use SplObserver;

${_INIT_CONFIG}[_CLASS] = 'PMVC\PlugIn\dispatcher\dispatcher';
\PMVC\l(__DIR__.'/src/Subject.php');

/**
 * Const
 */
const PREP = '_prep';
const POST = '_post';


/**
 *  Base Observerable class
 */
class dispatcher extends p\PlugIn
{
    /**
     * Last config key.
     */
     private $_lastConfigKey;

    /**
    * Private
    * $observers an array of Observer objects to notify
    */
    private $_subjects = [];

    /**
    * Notify will call all observer update() function 
    * @return void
    */ 
    public function notify($state,$clean=null)
    {
        $state = strtolower($state);
        $this->_notify($state.PREP, $clean);
        $this->_notify($state,$clean);
        $this->_notify($state.POST, $clean);
    }

    private function _notify($state,$clean=null)
    {
        if(isset($this->_subjects[$state])){
            $this->_subjects[$state]->notify();
            if($clean){
                $this->_subjects[$state]->removeAll();
            }
        }
    }

    /**
    * Register the reference to an object object
    *
    * @param SplObserver $observer Trigger target.
    * @param mixed       $state    Trigger type 
    * @param mixed       $name     user by attachBefore and attachAfter
    *
    * @return $this 
    */ 
    public function attach(SplObserver $observer, $state, $name=null)
    {
        $state = strtolower($state);
        if (is_null($name)) {
            $name = $state;
        }
        $name = strtolower($name);
        if (!isset($this->_subjects[$state])) {
            $this->_subjects[$state] = new Subject($name);
        }
        $this->_subjects[$state]->attach($observer);
        return $this[\PMVC\THIS];
    }

    /**
     * Attach Before.
     *
     * @return $this 
     */
    public function attachBefore(SplObserver $observer, $state)
    {
        return $this->attach($observer, $state.PREP, $state);
    }

    /**
     * Attach After. 
     *
     * @return $this 
     */
    public function attachAfter(SplObserver $observer, $state)
    {
        return $this->attach($observer, $state.POST, $state);
    }
 
    /** 
     * Deletes/deattaches an observer from the the object. 
     * @param object $observer 
     */ 
    public function detach(SplObserver $observer, $state=null)
    {
        if (is_null($state)) {
            foreach($this->_subjects as $subject){
                $subject->detach($observer);
            }
        } else {
            $state = strtolower($state);
            $states = [
                $state,
                $state.PREP,
                $state.POST
            ];
            foreach ($states as $state) {
                if (isset($this->_subjects[$state])) {
                    $this->_subjects[$state]->detach($observer);
                }
            }
        }
    }
    
    /**
     * Contain.
     */
    public function contains(SplObserver $observer, $state)
    {
        $subject = $this->_subjects[strtolower($state)];
        return $subject->contains($observer);
    }

    /** 
     * Deletes/detaches every currently attached observer. 
     */
    function cleanObserver($state=null)
    {
        if(is_null($state)){
            foreach($this->_subjects as $subject){
                $subject->removeAll();
            }
        }else{
            $state = strtolower($state);
            $this->_subjects[$state]->removeAll();
        }
    }

    public function getOptionKey($key = null)
    {
        $new = Event\SET_CONFIG;
        if (!is_null($key)) {
            $new .= '_'.(string)$key;
        }
        return  $new;
    }

    public function set($k, $v=null)
    {
        $this->_lastConfigKey = $k;
        $this[$k] = $v;
        $this->notify($k);
        $this->notify($this->getOptionKey($v));
    }

    public function isSetOption($key)
    {
        if(Event\SET_CONFIG != $this->_lastConfigKey){
            return false;
        }
        $last_options = $this[Event\SET_CONFIG];
        return \PMVC\hasKey($last_options,$key);
    }

    public function stop($bool=null)
    {
        if (is_null($bool)) {
            return \PMVC\getOption(\PMVC\PAUSE); 
         } else {
            return \PMVC\option('set',\PMVC\PAUSE,$bool);
        }
    }
}

?>
