<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 14/03/2019
 * Time: 12:00
 */

namespace Application\Controller\Factory;


use Application\Controller\MobileController;
use Application\Controller\Repository\MobileRepository;
use Authentication\Service\AuthenticationManager;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class MobileControllerFactory implements FactoryInterface
{

    /**
     * Create an object
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mobileManager = $container->get(MobileRepository::class);
        $authManager = $container->get(AuthenticationManager::class);

        return new MobileController($mobileManager, $authManager);
    }
}