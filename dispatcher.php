<?php
namespace PMVC\PlugIn\dispatcher;
use PMVC as p;
${_INIT_CONFIG}[_CLASS] = 'PMVC\PlugIn\dispatcher\dispatcher';
\PMVC\l(__DIR__.'/src/Observable.php');

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
    private $_subjects=array();

    /**
    * Calls the update() function using the reference to each
    * registered observer - used by children of Observable
    * @return void
    */ 
    function notify($state,$clean=null)
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
    * @return void
    */ 
    function attach($observer,$state)
    {
        if(!isset($this->_subjects[$state])){
            $this->_subjects[$state] = new Observable($state);
        }
        $this->_subjects[$state]->attach($observer);
        return $this->_subjects[$state];
    }
 
    /** 
     * Deletes/deattaches an observer from the the object. 
     * @param object $observer 
     */ 
    function detach($observer,$state=null)
    {
        if (isset($this->_subjects[$state])) {
            $this->_subjects[$state]->detach($observer);
        } else {
            foreach($this->_subjects as $subject){
                $subject->detach($observer);
            }
        }
    }

    /** 
     * Deletes/detaches every currently attached observer. 
     */
    function cleanObserver($state=null)
    {
        if(is_null($state)){
            $this->_subjects = array();
        }else{
            $this->_subjects[$state]->removeAll();
        }
    }

    function set($k, $v=null)
    {
        $this->last_config_key = $k;
        $this[$k] = $v;
        $this->notify('SetConfig');
    }

    function isContain($haystack,$needle)
    {
        if(
            $haystack === $needle
            || isset($haystack[$needle])
        ){
            return true;
        }else{
            return false;
        }
    }

    function isSetOption($key)
    {
        if('setOption'!=$this->last_config_key){
            return false;
        }
        $last_options=$this['setOption'];
        return $this->isContain($last_options,$key);
    }
}

?>
