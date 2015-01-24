<?php
namespace ZfcUser\Factory\Authentication\Listener;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Crypt\Password\Bcrypt;
use ZfcUser\Authentication\Listener\RehashBcryptPassword;

class RehashBcryptPasswordFactory implements DelegatorFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $bcrypt = call_user_func($callback);
                
        // This delegator is applicable only to bcrypt, so short-circuit
        // if we're attached to a factory that returns something else
        if ( ! $bcrypt instanceof Bcrypt ) {
            return $bcrypt;
        }

        $sem = $serviceLocator->get('SharedEventManager');

        $mapper = $serviceLocator->get('zfcuser_user_mapper');
        
        // Attach listener to persist password hash on authentication success
        $sem->attach(
            'ZfcUser\Authentication\Adapter\AdapterChain',
            'authenticate.success',
            new RehashBcryptPassword($mapper, $bcrypt)
        );

        return $bcrypt;
    }
}
