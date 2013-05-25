<?php
namespace UglySqlSchema;

use UglySqlSchema\Controller\ConsoleController;
use UglySqlSchema\Service\MergeService;
use Zend\Log\Formatter\Simple;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'UglySqlSchema\Controller\ConsoleController' => function (ControllerManager $cm) {
                    $mergeService = $cm->getServiceLocator()->get('UglySqlSchema\Service\MergeService');
                    return new ConsoleController($mergeService);
                }
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Log\Logger' => function (ServiceManager $sm) {
                    $writer = new Stream('php://output');
                    $format = "%priorityName%: %message%" . PHP_EOL;
                    $formatter = new Simple($format);
                    $writer->setFormatter($formatter);
//                    $writer = new Stream('log.txt');
                    $logger = new Logger();
                    $logger->addWriter($writer);
                    return $logger;
                },
                'UglySqlSchema\Service\MergeService' => function (ServiceManager $sm) {
                    $logger = $sm->get('Zend\Log\Logger');
                    return new MergeService($logger);
                }
            ),

        );
    }
}
