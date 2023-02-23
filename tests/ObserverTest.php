<?php
namespace PMVC\PlugIn\dispatcher;

use PMVC;
use PMVC\TestCase;


class ObserverTest extends TestCase
{
    private $_plug = 'dispatcher';
    function testAddObserver()
    {
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = new MockObserver();
        $return = $dispatcher->attach($mockObserver, 'test');
        $this->assertEquals($return, $dispatcher);
        $this->assertTrue($dispatcher->contains(
            $mockObserver,
            'test'
        ));
    }

    function testFireEvent()
    {
        $event = 'Test';
        $dispatcher = PMVC\plug($this->_plug);

        $mockObserver = $this->getPMVCMockBuilder(__NAMESPACE__.'\MockObserver')
            ->pmvc_onlyMethods(['on'.$event])
            ->getMock();
        $mockObserver->expects($this->once())
           ->method('on'.$event);
        $dispatcher->attach($mockObserver, $event);
        $dispatcher->notify($event);
    }

    public function testFireWithoutClean()
    {
        $event = 'Test';
        $dispatcher = PMVC\plug($this->_plug);

        $mockObserver = $this->getPMVCMockBuilder(__NAMESPACE__.'\MockObserver')
            ->pmvc_onlyMethods(['on'.$event])
            ->getMock();
        $mockObserver->expects($this->once())
           ->method('on'.$event);
        $dispatcher->attach($mockObserver, $event);
        $dispatcher->notify($event);
        $this->assertTrue($dispatcher->contains($mockObserver, $event));
    }

    public function testFireWithClean()
    {
        $event = 'Test';
        $dispatcher = PMVC\plug($this->_plug);

        $mockObserver = $this->getPMVCMockBuilder(__NAMESPACE__.'\MockObserver')
            ->pmvc_onlyMethods(['on'.$event])
            ->getMock();
        $mockObserver->expects($this->once())
           ->method('on'.$event);
        $dispatcher->attach($mockObserver, $event);
        $dispatcher->notify($event, true);
        $this->assertFalse($dispatcher->contains($mockObserver, $event));
    }

    function testFireEventBySetOption()
    {
        $key = 'foo';
        $dispatcher = PMVC\plug($this->_plug);
        $mockObserver = $this->getPMVCMockBuilder(__NAMESPACE__.'\MockObserver')->
            pmvc_onlyMethods([
                'on'.$dispatcher->getOptionKey(),
                'on'.$dispatcher->getOptionKey($key)
            ])->
            getMock();
        $mockObserver->expects($this->once())
           ->method('on'.$dispatcher->getOptionKey());
        $mockObserver->expects($this->once())
           ->method('on'.$dispatcher->getOptionKey($key));
        $dispatcher->attach($mockObserver, $dispatcher->getOptionKey());
        $dispatcher->attach($mockObserver, $dispatcher->getOptionKey($key));
        $dispatcher->set($dispatcher->getOptionKey(), 'foo');
    }

    function testAttachAfter()
    {
        $event = 'Test';
        $dispatcher = PMVC\plug($this->_plug);
        $mockObserver = $this->getPMVCMockBuilder(__NAMESPACE__.'\MockObserver')
            ->pmvc_onlyMethods(['on'.$event])
            ->getMock();
        $mockObserver->expects($this->once())
           ->method('on'.$event);
        $dispatcher->attachAfter($mockObserver, $event);
        $dispatcher->notify($event);
    }

    function testDeleteObserver()
    {
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = new MockObserver();
        $dispatcher->attach($mockObserver, 'test');
        $this->assertTrue($dispatcher->contains(
            $mockObserver,
            'test'
        ));
        $dispatcher->detach($mockObserver);
        $this->assertFalse($dispatcher->contains(
            $mockObserver,
            'test'
        ));
    }

    function testDeleteObservers()
    {
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = new MockObserver();
        $dispatcher->attach($mockObserver, 'test');
        $this->assertTrue($dispatcher->contains(
            $mockObserver,
            'test'
        ));
        $dispatcher->cleanObserver();
        $this->assertFalse($dispatcher->contains(
            $mockObserver,
            'test'
        ));
    }

    function testDeleteByItself()
    {
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = new MockObserver('foo');
        $mockObserver2 = new MockObserver2();
        $event = 'del';
        $dispatcher->attach($mockObserver, $event);
        $dispatcher->attach($mockObserver2, $event);
        $dispatcher->notify($event);
        $this->assertEquals(
            1,
            $mockObserver->i
        );
        $this->assertEquals(
            1,
            $mockObserver2->i
        );
        $dispatcher->notify($event);
        $this->assertEquals(
            2,
            $mockObserver->i
        );
    }

    function testSubjectDefaultAlias()
    {
        $fakeSubject = new Subject('');
        $this->assertEquals(
            32,
            strlen($fakeSubject->getHash($fakeSubject))
        );
    }

    function testStop()
    {
        $dispatcher = PMVC\plug('dispatcher');

        $bool = $dispatcher->stop(false);
        $this->assertFalse($bool);

        $bool = $dispatcher->stop();
        $this->assertFalse($bool);

        $bool = $dispatcher->stop(true);
        $this->assertTrue($bool);

        $bool = $dispatcher->stop();
        $this->assertTrue($bool);
    }

}

class MockObserver extends PMVC\PlugIn
{
    public $i = 0;
    public $name;

    function __construct($name=null)
    {
        $this->name = $name;
    }

    function onTest(){
    }

    function onSetConfig()
    {

    }

    function onSetConfig_foo()
    {

    }

    function onDel($subject){
        $this->i++;
    }
}

class MockObserver2 extends PMVC\PlugIn
{
    public $i = 0;

    function onDel($subject){
        $subject->detach($this);
        $this->i++;
    }
}
