<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Validator\RemoteAddr;
use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host' => 'localhost',
                    'user' => 'root',
                    'password' => '',
                    'dbname' => 'intranet',
                    'charset' => 'utf8'
                ]
            ],
            'orm_crawler' => [
                'driverClass'   => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'eventmanager'  => 'orm_crawler',
                'configuration' => 'orm_crawler',
                'params'        => [
                    'host'     => 'localhost',
                    'user'     => 'root',
                    'password' => '',
                    'dbname'   => 'sigas',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8',
                    ],
                ],
            ],
        ],
        'configuration' => [
            'orm_crawler' => [
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',
                'hydration_cache'   => 'array',
                'driver'            => 'orm_crawler_chain',
                'generate_proxies'  => true,
                'proxy_dir'         => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace'   => 'DoctrineORMModule\Proxy',
                'filters'           => [],
            ],
        ],
        'entitymanager' => [
            'orm_crawler' => [
                'connection'    => 'orm_crawler',
                'configuration' => 'orm_crawler',
            ],
        ],
        'eventmanager' => [
            'orm_crawler' => [],
        ],
        'sql_logger_collector' => [
            'orm_crawler' => [],
        ],
        'entity_resolver' => [
            'orm_crawler' => [],
        ],
    ],

    //Session configuration
    'session_config' => [
        //cookie expira em 2 horas
        'cookie_lifetime' => 60 * 60 * 2,
        //sessao é armazenada em até 1 dia
        'gc_maxlifetime' => 60 * 60 * 24 * 1
    ],
    'session_manager' => [
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class
        ],
        'storage' => SessionArrayStorage::class,
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ]
];
