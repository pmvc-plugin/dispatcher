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
     * last config key
     */
     private $last_config_key;

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
        $this->_notify($state.PREP, $clean);
        $this->_notify($state,$clean);
        $this->_notify($state.POST, $clean);
    }

    private function _notify($state,$clean=null)
    {
        $state = strtolower($state);
        if(isset($this->_subjects[$state])){
            $this->_subjects[$state]->notify();
            if($clean){
                $this->_subjects[$state]->removeAll();
            }
        }
    }

    /**
    * Register the reference to an object object
    * @return void
    */ 
    public function attach(SplObserver $observer, $state, $name=null)
    {
        $state = strtolower($state);
        if (is_null($name)) {
            $name = $state;
        }
        if (!isset($this->_subjects[$state])) {
            $this->_subjects[$state] = new Subject($name);
        }
        $this->_subjects[$state]->attach($observer);
        return $this->_subjects[$state];
    }
    /**
     * Attach Before
     */
    public function attachBefore(SplObserver $observer, $state)
    {
        $this->attach($observer, $state.PREP, $state);
    }
    /**
     * Attach After 
     */
    public function attachAfter(SplObserver $observer, $state)
    {
        $this->attach($observer, $state.POST, $state);
    }
 
    /** 
     * Deletes/deattaches an observer from the the object. 
     * @param object $observer 
     */ 
    function detach(SplObserver $observer, $state=null)
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

    function set($k, $v=null)
    {
        $this->last_config_key = $k;
        $this[$k] = $v;
        $this->notify(Event\SET_CONFIG);
        $this->notify(Event\SET_CONFIG.'_'.$v);
    }

    function isSetOption($key)
    {
        if(Event\SET_CONFIG != $this->last_config_key){
            return false;
        }
        $last_options = $this[Event\SET_CONFIG];
        return \PMVC\hasKey($last_options,$key);
    }

    function stop($bool=null)
    {
        if (is_null($bool)) {
            return \PMVC\getOption(\PMVC\PAUSE); 
         } else {
            return \PMVC\option('set',\PMVC\PAUSE,$bool);
        }
    }
}

?>
