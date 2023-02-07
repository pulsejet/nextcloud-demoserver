<?php

declare(strict_types=1);

namespace OCA\DemoServer\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\IRequest;
use OCP\IUserSession;
use OCA\DemoServer\Listener;

class Application extends App implements IBootstrap
{
    private $appName = 'demoserver';

    public function __construct()
    {
        parent::__construct($this->appName);
    }

    public function register($context): void
    {
        $context->registerEventListener(\OCP\Files\Events\Node\BeforeNodeCreatedEvent::class, Listener::class);
    }

    public function boot(IBootContext $context): void
    {
        $uid = 'demo'; $password = '56183891677321143025';

        $container = $context->getAppContainer();
        $userSession = $container->query(IUserSession::class);
        $request = $container->query(IRequest::class);

        // Check for nodemo query param
        if ($request->getParam('nodemo') === '1') {
            return;
        }

        // Check if the user is logged in
        if (!$userSession->isLoggedIn()) {
            $userSession->createSessionToken($request, $uid, $uid, $password);

            if ($user = $userSession->getUser()) {
                $userSession->createRememberMeToken($user);
            }
        }
    }
}