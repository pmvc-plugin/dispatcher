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
        $subject=$dispatcher->attach($mockObserver, 'test');
        $this->assertTrue($subject->contains($mockObserver));
    }

    function testFireEvent()
    {
        $event = 'Test';
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = $this->getMock(
            __NAMESPACE__.'\MockObserver',
            ['on'.$event]
        );
        $mockObserver->expects($this->once())
           ->method('on'.$event);
        $dispatcher->attach($mockObserver, $event);
        $dispatcher->notify($event);
    }

    function testDeleteObserver()
    {
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = new MockObserver();
        $subject=$dispatcher->attach($mockObserver, 'test');
        $dispatcher->detach($mockObserver);
        $this->assertFalse($subject->contains($mockObserver));
    }

    function testDeleteObservers()
    {
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = new MockObserver();
        $subject=$dispatcher->attach($mockObserver, 'test');
        $this->assertContains(
            'MockObserver',
            print_r($subject,true)
        );
        $dispatcher->cleanObserver();
        $this->assertNotContains(
            'MockObserver',
            print_r($subject,true)
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

}

class MockObserver extends PMVC\PlugIn
{
    function onTest(){

    }
}
