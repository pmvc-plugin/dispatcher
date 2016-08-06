<?php
namespace PMVC\PlugIn\dispatcher;

use PHPUnit_Framework_TestCase;
use PMVC;

PMVC\Load::plug();
PMVC\addPlugInFolders(['../']);

class ObserverTest extends PHPUnit_Framework_TestCase
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
        $mockObserver = $this->getMock(
            __NAMESPACE__.'\MockObserver',
            ['on'.$event]
        );
        $mockObserver->expects($this->once())
           ->method('on'.$event);
        $dispatcher->attach($mockObserver, $event);
        $dispatcher->notify($event);
    }

    function testAttachAfter()
    {
        $event = 'Test';
        $dispatcher = PMVC\plug($this->_plug);
        $mockObserver = $this->getMock(
            __NAMESPACE__.'\MockObserver',
            ['on'.$event]
        );
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

    function testSubjectDefaultAlias()
    {
        $fakeSubject = new Subject('');
        $this->assertEquals(
            32,
            strlen($fakeSubject->getHash($fakeSubject))
        );
            
    }

}

class MockObserver extends PMVC\PlugIn
{
    function onTest(){

    }
}
