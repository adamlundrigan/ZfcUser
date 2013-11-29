<?php

namespace ZfcUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Prompt;
use ZfcUser\Entity\User;

class ConsoleController extends AbstractActionController
{
    protected $userService;

    public function createAction()
    {
        if (!$this->getRequest() instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $user = array();

        $prompt = new Prompt\Line('User ID: ', true);
        $user['user_id'] = $prompt->show();

        $prompt = new Prompt\Line('Username: ', true);
        $user['username'] = $prompt->show();

        $prompt = new Prompt\Line('Email Address: ');
        $user['email'] = $prompt->show();

        $prompt = new Prompt\Line('Display Name: ', true);
        $user['displayName'] = $prompt->show();

        $prompt = new Prompt\Line('Password: ', true);
        $user['password'] = $prompt->show();

        $prompt = new Prompt\Line('Verify Password: ', true);
        $user['passwordVerify'] = $prompt->show();

        $service = $this->getUserService();
        $user = $service->register($user);

        return "\n" . (($user instanceof User) 
            ? sprintf('User created successfully (ID# %d)', $user->getID()) 
            : 'Failed to create user'
        );
    }
    
    public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
        }
        return $this->userService;
    }
    
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }
}
