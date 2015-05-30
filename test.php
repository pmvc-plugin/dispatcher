<?php
PMVC\Load::plug();
PMVC\setPlugInFolder('../');
class ObserverTest extends PHPUnit_Framework_TestCase
{
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
        $mockObserver = $this->getMock('MockObserver',array('on'.$event));
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
            print_r($dispatcher,true)
        );
        $dispatcher->cleanObserver();
        $this->assertNotContains(
            'MockObserver',
            print_r($dispatcher,true)
        );
    }

}

class MockObserver extends PMVC\PlugIn
{
    function onTest(){

    }
}
