<?php

namespace ZfcUserTest\Mapper;

use ZfcUser\Mapper\User as UserMapper;
use Zend\Db\ResultSet\ResultSet;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->table = $this->getMock('Zend\Db\TableGateway\AbstractTableGateway');
        $this->hydrator = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');
        
        $this->mapper = new UserMapper($this->table, $this->hydrator);
    }
    
    public function testFindByEmailWhenNoMatchingUserAccountsExists()
    {
        $resultset = new ResultSet();
        $resultset->initialize([]);
        
        $this->table->expects($this->once())
                    ->method('select')
                    ->with(['email' => 'foo@bar.com'])
                    ->will($this->returnValue($resultset));
        
        $this->assertNull($this->mapper->findByEmail('foo@bar.com'));
    }
    
    public function testFindByIdWhenNoMatchingUserAccountsExists()
    {
        $resultset = new ResultSet();
        $resultset->initialize([]);
        
        $this->table->expects($this->once())
                    ->method('select')
                    ->with(['user_id' => 42])
                    ->will($this->returnValue($resultset));
        
        $this->assertNull($this->mapper->findById(42));
    }
    
    public function testFindByIdWhenOneMatchingAccountExists()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        
        $resultset = new ResultSet();
        $resultset->initialize([$mockUserOne]);
        
        $this->table->expects($this->once())
                    ->method('select')
                    ->with(['user_id' => 42])
                    ->will($this->returnValue($resultset));
        
        $this->assertSame($mockUserOne, $this->mapper->findById(42));
    }
    
    public function testFindByEmailWhenOneMatchingAccountExists()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        
        $resultset = new ResultSet();
        $resultset->initialize([$mockUserOne]);
        
        $this->table->expects($this->once())
                    ->method('select')
                    ->with(['email' => 'foo@bar.com'])
                    ->will($this->returnValue($resultset));
        
        $this->assertSame($mockUserOne, $this->mapper->findByEmail('foo@bar.com'));
    }
    
    public function testFindByEmailReturnsFirstResultWhenMultipleMatchingAccountsExists()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserTwo = $this->getMock('ZfcUser\Entity\User');
        
        $resultset = new ResultSet();
        $resultset->initialize([$mockUserOne, $mockUserTwo]);
        
        $this->table->expects($this->once())
                    ->method('select')
                    ->with(['email' => 'foo@bar.com'])
                    ->will($this->returnValue($resultset));
        
        $result = $this->mapper->findByEmail('foo@bar.com');
        $this->assertSame($mockUserOne, $result);
        $this->assertNotSame($mockUserTwo, $result);
    }
    
    public function testFindByUsernameWhenNoMatchingUserAccountsExists()
    {
        $resultset = new ResultSet();
        $resultset->initialize([]);
        
        $this->table->expects($this->once())
                    ->method('select')
                    ->with(['username' => 'someuser'])
                    ->will($this->returnValue($resultset));
        
        $this->assertNull($this->mapper->findByUsername('someuser'));
    }
    
    public function testFindByUsernameWhenOneMatchingAccountExists()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        
        $resultset = new ResultSet();
        $resultset->initialize([$mockUserOne]);
        
        $this->table->expects($this->once())
                    ->method('select')
                    ->with(['username' => 'someuser'])
                    ->will($this->returnValue($resultset));
        
        $this->assertSame($mockUserOne, $this->mapper->findByUsername('someuser'));
    }
    
    public function testFindByUsernameReturnsFirstResultWhenMultipleMatchingAccountsExists()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserTwo = $this->getMock('ZfcUser\Entity\User');
        
        $resultset = new ResultSet();
        $resultset->initialize([$mockUserOne, $mockUserTwo]);
        
        $this->table->expects($this->once())
                    ->method('select')
                    ->with(['username' => 'someuser'])
                    ->will($this->returnValue($resultset));
        
        $result = $this->mapper->findByUsername('someuser');
        $this->assertSame($mockUserOne, $result);
        $this->assertNotSame($mockUserTwo, $result);
    }
    
    public function testInsertUserEntityHappyCase()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue($mockUserOneData));
        
        $this->table->expects($this->once())
                    ->method('insert')
                    ->with($mockUserOneData)
                    ->will($this->returnValue(1));
        
        $this->table->expects($this->once())
                    ->method('getLastInsertValue')
                    ->will($this->returnValue(42));
        
        $this->hydrator->expects($this->once())
                       ->method('hydrate')
                       ->with(['user_id' => 42])
                       ->will($this->returnValue($mockUserOne));
        
        $this->assertTrue($this->mapper->insert($mockUserOne));
    }
    
    public function testInsertUserShortCircuitsIfExtractionFails()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue([]));
        
        $this->table->expects($this->never())
                    ->method('insert');
        
        $this->assertFalse($this->mapper->insert($mockUserOne));
    }
    
    public function testInsertUserShortCircuitsIfInsertFails()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue($mockUserOneData));
        
        $this->table->expects($this->once())
                    ->method('insert')
                    ->with($mockUserOneData)
                    ->will($this->returnValue(0));
        
        $this->table->expects($this->never())
                    ->method('getLastInsertValue');
        
        $this->assertFalse($this->mapper->insert($mockUserOne));
    }
    
    public function testUpdateUserEntityHappyCase()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['user_id' => 42, 'username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue($mockUserOneData));
        
        $this->table->expects($this->once())
                    ->method('update')
                    ->with($mockUserOneData, ['user_id' => 42])
                    ->will($this->returnValue(1));
        
        $this->assertTrue($this->mapper->update($mockUserOne));
    }
    
    public function testUpdateUserShortCircuitsIfExtractionFails()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['user_id' => 42, 'username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue([]));
        
        $this->table->expects($this->never())
                    ->method('update');
        
        $this->assertFalse($this->mapper->update($mockUserOne));
    }
    
    public function testUpdateUserShortCircuitsIfUpdateFails()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['user_id' => 42, 'username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue($mockUserOneData));
        
        $this->table->expects($this->once())
                    ->method('update')
                    ->with($mockUserOneData, ['user_id' => 42])
                    ->will($this->returnValue(0));
        
        $this->assertFalse($this->mapper->update($mockUserOne));
    }
    
    public function testDeleteUserEntityHappyCase()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['user_id' => 42, 'username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue($mockUserOneData));
        
        $this->table->expects($this->once())
                    ->method('delete')
                    ->with(['user_id' => 42])
                    ->will($this->returnValue(1));
        
        $this->assertTrue($this->mapper->delete($mockUserOne));
    }
    
    public function testDeleteUserShortCircuitsIfExtractionFails()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['user_id' => 42, 'username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue([]));
        
        $this->table->expects($this->never())
                    ->method('delete');
        
        $this->assertFalse($this->mapper->delete($mockUserOne));
    }
    
    public function testDeleteUserShortCircuitsIfDeleteFails()
    {
        $mockUserOne = $this->getMock('ZfcUser\Entity\User');
        $mockUserOneData = ['user_id' => 42, 'username' => 'someuser'];
        
        $this->hydrator->expects($this->once())
                       ->method('extract')
                       ->with($mockUserOne)
                       ->will($this->returnValue($mockUserOneData));
        
        $this->table->expects($this->once())
                    ->method('delete')
                    ->with(['user_id' => 42])
                    ->will($this->returnValue(0));
        
        $this->assertFalse($this->mapper->delete($mockUserOne));
    }
}
