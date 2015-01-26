<?php
namespace ZfcUser\Factory\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\HydratingResultSet;

class UserTableGatewayFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $options ZfcUser\Options\ModuleOptions */
        $options = $serviceLocator->get('zfcuser_module_options');
        
        /** @var $adapter Zend\Db\Adapter\AdapterInterface */
        $adapter = $serviceLocator->get('zfcuser_zend_db_adapter');
        
        /** @var $hydrator ZfcUser\Mapper\UserHydrator */
        $hydrator = $serviceLocator->get('zfcuser_user_hydrator');
        
        /** @var $resultSetPrototype Zend\Db\ResultSet\HydratingResultSet */
        $entityClass = $options->getUserEntityClass();
        $resultSetPrototype = new HydratingResultSet($hydrator, new $entityClass);
        
        return new TableGateway($options->getTableName(), $adapter, null, $resultSetPrototype);
    }
}
