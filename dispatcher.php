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
    private $subjects=array();

    /**
    * Calls the update() function using the reference to each
    * registered observer - used by children of Observable
    * @return void
    */ 
    function notify($state,$clean=null)
    {
        if(isset($this->subjects[$state])){
            $this->subjects[$state]->notify();
            if($clean){
                $this->subjects[$state]->removeAll();
            }
        }
    }

    /**
    * Register the reference to an object object
    * @return void
    */ 
    function addObserver ($observer,$state=null)
    {
        if(empty($observer->name)){
            $observer->name=uniqid(rand());
        }
        if(!isset($this->subjects[$state])){
            $this->subjects[$state] = new Observable($state);
        }
        $this->subjects[$state]->attach($observer);
        return $this->subjects[$state];
    }
 
    /** 
     * Deletes/deattaches an observer from the the object. 
     * @param object $observer 
     */ 
    function deleteObserver($observer)
    {
        foreach($this->subjects as $subject){
            $subject->detach($observer);
        }
    }

    /** 
     * Deletes/detaches every currently attached observer. 
     */
    function deleteObservers($state=null)
    {
        if(is_null($state)){
            $this->subjects = array();
        }else{
            $this->subjects[$state]->removeAll();
        }
    }

    function set($k, $v=null)
    {
        $this->last_config_key = $k;
        call_user_func_array(array('parent','set'),array($k,$v)); 
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
        $last_options=$this->get('setOption');
        return $this->isContain($last_options,$key);
    }
}

?>
