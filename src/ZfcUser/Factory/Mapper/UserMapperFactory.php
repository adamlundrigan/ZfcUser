<?php
namespace ZfcUser\Factory\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Mapper\User;

class UserMapperFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $table \Zend\Db\TableGateway\AbstractTableGateway */
        $table = $serviceLocator->get('zfcuser_user_tablegateway');
        
        /* @var $hydrator \Zend\Stdlib\Hydrator\HydratorInterface */
        $hydrator = $serviceLocator->get('zfcuser_user_hydrator');
        
        return new User($table, $hydrator);
    }
}
