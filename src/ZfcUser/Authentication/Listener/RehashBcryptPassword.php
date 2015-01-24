<?php
namespace ZfcUser\Authentication\Listener;

use ZfcUser\Mapper\UserInterface;
use ZfcUser\Entity\UserInterface as UserEntity;
use Zend\Crypt\Password\Bcrypt;
use Zend\EventManager\EventInterface;

class RehashBcryptPassword
{
    /**
     * @var UserInterface
     */
    protected $mapper;
    
    /**
     * @var Bcrypt
     */
    protected $bcrypt;
    
    public function __construct(UserInterface $mapper, Bcrypt $bcrypt)
    {
        $this->mapper = $mapper;
        $this->bcrypt = $bcrypt;
    }
    
    public function __invoke(EventInterface $e)
    {
        $user = $e->getParam('result')->getIdentity();
        if ( ! $user instanceof UserEntity ) {
            return;
        }
        
        $cost = explode('$', $user->getPassword())[2];
        if ( $cost === $this->bcrypt->getCost() ) {
            return;
        }
        
        $password = $e->getParam('adapter')->getCredential();        
        $user->setPassword($this->bcrypt->create($password));
        $this->mapper->update($user);        
    }
}
