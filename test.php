<?php
PMVC\Load::plug();
PMVC\setPlugInFolder('../');
class ObserverTest extends PHPUnit_Framework_TestCase
{
    function testAddObserver()
    {
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = new MockObserver();
        $subject=$dispatcher->addObserver($mockObserver, 'test');
        $this->assertTrue($subject->contains($mockObserver));
    }

    function testFireEvent()
    {
        $event = 'Test';
        $dispatcher = PMVC\plug('dispatcher');
        $mockObserver = $this->getMock('MockObserver',array('on'.$event));
        $mockObserver->expects($this->once())
           ->method('on'.$event);
        $dispatcher->addObserver($mockObserver, $event);
        $dispatcher->notify($event);
    }
}

class MockObserver extends PMVC\PlugIn
{
    function onTest(){

    }
}
