<?php

namespace ZfcUser\Mapper;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use ZfcUser\Entity\UserInterface as UserEntityInterface;

class User implements UserInterface
{
    use EventManagerAwareTrait;
    
    /**
     * @var AbstractTableGateway
     */
    protected $table;
    
    /**
     * @var HydratorInterface
     */
    protected $hydrator;
    
    /**
     * 
     * @param AbstractTableGateway $tg TableGateway instance for user table
     * @param HydratorInterface $hydrator Hydrator for User entity
     */
    public function __construct(AbstractTableGateway $tg, HydratorInterface $hydrator)
    {
        $this->table = $tg;
        $this->hydrator = $hydrator;
    }

    /**
     * Retrieve user account by email address
     * 
     * @param string $email
     * @return \ZfcUser\Entity\UserInterface|null
     */
    public function findByEmail($email)
    {
        $entity = $this->table->select(['email' => $email])->current();
        $this->getEventManager()->trigger('find', $this, ['entity' => $entity]);
        return $entity;
    }

    /**
     * Retrieve user account by username
     * 
     * @param string $username
     * @return \ZfcUser\Entity\UserInterface|null
     */
    public function findByUsername($username)
    {
        $entity = $this->table->select(['username' => $username])->current();
        $this->getEventManager()->trigger('find', $this, ['entity' => $entity]);
        return $entity;
    }

    /**
     * Retrieve user account by primary key
     * 
     * @param int|string $id
     * @return \ZfcUser\Entity\UserInterface|null
     */
    public function findById($id)
    {
        $entity = $this->table->select(['user_id' => $id])->current();
        $this->getEventManager()->trigger('find', $this, ['entity' => $entity]);
        return $entity;
    }

    /**
     * Persist a new user entity instance
     * 
     * @param \ZfcUser\Entity\UserInterface $entity
     * @return boolean
     */
    public function insert(UserEntityInterface $entity)
    {
        $data = $this->hydrator->extract($entity);
        if (!is_array($data) || empty($data)) {
            return false;
        }

        if ($this->table->insert($data) === 0) {
            return false;
        }
        
        $this->hydrator->hydrate(
            ['user_id' => $this->table->getLastInsertValue()],
            $entity
        );
        return true;
    }

    /**
     * Update a previously persisted user entity
     * 
     * @param \ZfcUser\Entity\UserInterface $entity
     * @return boolean
     */
    public function update(UserEntityInterface $entity)
    {
        $data = $this->hydrator->extract($entity);
        if (!is_array($data) || empty($data)) {
            return false;
        }

        return $this->table->update($data, ['user_id' => $data['user_id']]) === 1;
    }

    /**
     * Delete a previously persisted user entity
     * 
     * @param \ZfcUser\Entity\UserInterface $entity
     * @return boolean
     */
    public function delete(UserEntityInterface $entity)
    {
        $data = $this->hydrator->extract($entity);
        if (!is_array($data) || empty($data)) {
            return false;
        }
        
        return $this->table->delete(['user_id' => $data['user_id']]) === 1;
    }
}
