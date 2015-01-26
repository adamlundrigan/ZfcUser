<?php

namespace ZfcUser\Mapper;

use Zend\Crypt\Password\PasswordInterface as ZendCryptPassword;
use Zend\Stdlib\Hydrator\ClassMethods;
use ZfcUser\Entity\UserInterface as UserEntity;

class UserHydrator extends ClassMethods
{
    public function __construct()
    {
        // We require that underscore be the key separator
        parent::__construct(true);
    }

    /**
     * Extract values from an object
     *
     * @param  UserEntity $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object)
    {
        $this->guardUserObject($object);
        $data = parent::extract($object);
        return $this->mapField('id', 'user_id', $data);
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array               $data
     * @param  UserEntity $object
     * @return UserEntity
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        $this->guardUserObject($object);
        $data = $this->mapField('user_id', 'id', $data);
        return parent::hydrate($data, $object);
    }

    /**
     * Remap an array key
     *
     * @param  string $keyFrom
     * @param  string $keyTo
     * @param  array  $array
     * @return array
     */
    protected function mapField($keyFrom, $keyTo, array $array)
    {
        if (isset($array[$keyFrom])) {
            $array[$keyTo] = $array[$keyFrom];
        }
        unset($array[$keyFrom]);
        return $array;
    }

    /**
     * Ensure $object is an UserEntity
     *
     * @param  mixed $object
     * @throws Exception\InvalidArgumentException
     */
    protected function guardUserObject($object)
    {
        if (!$object instanceof UserEntity) {
            throw new Exception\InvalidArgumentException(
                '$object must be an instance of ZfcUser\Entity\UserInterface'
            );
        }
    }
}
