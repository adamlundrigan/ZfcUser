<?php
namespace ZfcUserTest\Authentication\Listener;

use ZfcUser\Authentication\Listener\RehashBcryptPassword;
use Zend\EventManager\Event;
use Zend\Authentication\Result;

class RehashBcryptPasswordTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->bcrypt = $this->getMock('Zend\Crypt\Password\Bcrypt');
        $this->mapper = $this->getMock('ZfcUser\Mapper\UserInterface');
        $this->adapter = $this->getMock('ZfcUser\Authentication\Adapter\AdapterChain');
        $this->user = $this->getMock('ZfcUser\Entity\User');
        $this->event = new Event();
        $this->event->setParams(['adapter' => $this->adapter]);   
    }
    
    public function testShortCircuitsWhenResultDoesNotContainAnIdentity()
    {
        $this->bcrypt->expects($this->never())->method('create');
        $this->bcrypt->expects($this->never())->method('getCost');
        $this->mapper->expects($this->never())->method('update');
        
        $this->event->setParam('result', new Result(Result::SUCCESS, null));
        
        $listener = new RehashBcryptPassword($this->mapper, $this->bcrypt);
        $listener($this->event);
    }
    
    public function testShortCircuitsWhenPasswordCostsMatch()
    {
        $this->bcrypt->expects($this->never())->method('create');
        $this->mapper->expects($this->never())->method('update');
        
        $this->user->expects($this->once())->method('getPassword')->will($this->returnValue('$2a$14$KssILxWNR6k62B7yiX0GAe2Q7wwHlrzhF3LqtVvpyvHZf0MwvNfVu'));
        $this->bcrypt->expects($this->once())->method('getCost')->will($this->returnValue('14'));
        
        $this->event->setParam('result', new Result(Result::SUCCESS, $this->user));
        
        $listener = new RehashBcryptPassword($this->mapper, $this->bcrypt);
        $listener($this->event);
    }
    
    public function testPasswordIsRehashedWhenCostHasChanged()
    {
        $this->user->expects($this->once())->method('getPassword')->will($this->returnValue('$2a$14$KssILxWNR6k62B7yiX0GAe2Q7wwHlrzhF3LqtVvpyvHZf0MwvNfVu'));
        $this->bcrypt->expects($this->once())->method('getCost')->will($this->returnValue('12'));
        $this->adapter->expects($this->once())->method('getCredential')->will($this->returnValue('foo'));
        $this->bcrypt->expects($this->once())->method('create');
        $this->user->expects($this->once())->method('setPassword');
        $this->mapper->expects($this->once())->method('update');
                
        $this->event->setParam('result', new Result(Result::SUCCESS, $this->user));
        
        $listener = new RehashBcryptPassword($this->mapper, $this->bcrypt);
        $listener($this->event);
    }
}
