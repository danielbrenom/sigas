<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 14/03/2019
 * Time: 12:00
 */

namespace Application\Controller\Factory;


use Application\Controller\MobileController;
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
        $entityManager = $container->get('doctrine.entitymanager.orm_crawler');
        return new MobileController($entityManager);
    }
}