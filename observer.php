<?php
use PMVC as p;
${_INIT_CONFIG}[_CLASS] = '_PMVC_BASE_OBSERVABLE';

/**
 *  Base Observerable class
 */
class _PMVC_BASE_OBSERVABLE extends p\PLUGIN
{
    /**
     * last config key
     */
     private $last_config_key;

    /**
    * Private
    * $observers an array of Observer objects to notify
    */
    private $observers=array();

    /**
    * Calls the update() function using the reference to each
    * registered observer - used by children of Observable
    * @return void
    */ 
    function fire($state,$clean=null) {
        if(isset($this->observers[$state])){
            foreach($this->observers[$state] as $observer){
                $observer->update($this,$state);
            }
            if($clean){
                unset($this->observers[$state]);
            }
        }
        if(isset($this->observers['all'])){
            foreach($this->observers['all'] as $observer){
                $observer->update($this,$state);
            }
        }
    }

    /**
    * Register the reference to an object object
    * @return void
    */ 
    function addObserver (&$observer,$state=null) {
        if(!isset($observer->name)){
            $observer->name=uniqid(rand());
        }
        if(is_null($state))$state='all';
        $this->observers[$state][$observer->name] = &$observer;
    }
 
    /** 
     * Deletes/deattaches an observer from the the object. 
     * @param object $observer 
     */ 
    function deleteObserver(&$observer){
        foreach($this->observers as $k=>$v){
            if(isset($this->observers[$k][$observer->name])){
                unset($this->observers[$k][$observer->name]);
                break;
            }
        }
    }

    /** 
     * Deletes/detaches every currently attached observer. 
     */
    function deleteObservers($e=null){
        if(is_null($e)){
            $this->observers = array();
        }else{
            unset($this->observers[$e]);
        }
    }

    function set(...$p){
        $this->last_config_key = $p[0];
        call_user_func_array(array('parent','set'),$p); 
        $this->fire('SetConfig');
    }

    function is_in($haystack,$needle){
        if(
            $haystack === $needle
            || isset($haystack[$needle])
        ){
            return true;
        }else{
            return false;
        }
    }

    function isSetOption($key){
        if('setOption'!=$this->last_config_key){
            return false;
        }
        $last_options=$this->get('setOption');
        return $this->is_in($last_options,$key);
    }
}

?>
