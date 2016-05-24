<?php
namespace PMVC\PlugIn\dispatcher;
use PMVC as p;
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
    private $_subjects=array();

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
    public function attach($observer,$state)
    {
        if(!isset($this->_subjects[$state])){
            $this->_subjects[$state] = new Subject($state);
        }
        $this->_subjects[$state]->attach($observer);
        return $this->_subjects[$state];
    }
    /**
     * Attach Before
     */
    public function attachBefore($observer,$state)
    {
        $this->attach($observer, $state.PREP);
    }
    /**
     * Attach After 
     */
    public function attachAfter($observer,$state)
    {
        $this->attach($observer, $state.POST);
    }
 
    /** 
     * Deletes/deattaches an observer from the the object. 
     * @param object $observer 
     */ 
    function detach($observer,$state=null)
    {
        if (!is_null($state)) {
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
            foreach($this->_subjects as $subject){
                $subject->removeAll();
            }
        }else{
            $this->_subjects[$state]->removeAll();
        }
    }

    function set($k, $v=null)
    {
        $this->last_config_key = $k;
        $this[$k] = $v;
        $this->notify(p\Event\SET_CONFIG);
        $this->notify(p\Event\SET_CONFIG.'_'.$v);
    }

    function isSetOption($key)
    {
        if('setOption'!=$this->last_config_key){
            return false;
        }
        $last_options=$this['setOption'];
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
