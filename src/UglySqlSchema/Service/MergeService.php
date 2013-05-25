<?php
namespace UglySqlSchema\Service;

use Zend\Db\Adapter\Adapter;
use Zend\Log\Logger;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MergeService implements ServiceLocatorAwareInterface
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param Logger $logger
     */
    function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function merge($platform = 'mysql')
    {
        $config = $this->getServiceLocator()->get('config');
        if (!array_key_exists('schema', $config)) {
            $this->getLogger()->err('No schema keys found; do any of your modules contribute schemas?');
            return false;
        }
        $fileNames = $this->getSqlFiles($config['schema'], $platform);
        if (empty($fileNames)) {
            $this->getLogger()->err("No schema files found; do data directories contain *.$platform.sql files?");
        }
        $this->getLogger()->info('Found modules ' . implode(', ', array_keys($fileNames)));

        $this->runSqlFiles($fileNames, $platform);
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $modules
     * @param $platform
     * @return array
     */
    public function getSqlFiles($modules, $platform)
    {
        $fileNames = array();
        foreach ($modules as $module => $dir) {
            $moduleSchemaFiles = glob($dir . '/*.' . $platform . '.sql');
            $fileNames[$module] = $moduleSchemaFiles;
        }
        return $fileNames;
    }

    public function runSqlFiles(array $fileNames, $platform)
    {
        if (!$this->getServiceLocator()->has('Zend\Db\Adapter\Adapter')) {
            $this->getLogger()->err('No default adapter found under ServiceManager key Zend\Db\Adapter\Adapter');
            return false;
        }
        /** @var Adapter $adapter */
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        if (strtolower($adapter->getPlatform()->getName()) !== strtolower($platform)) {
            $this->getLogger()->err(
                'Default adapter is of platform "' .
                strtolower($adapter->getPlatform()->getName()) .
                '", passed platform is "' . $platform . '"'
            );
            return false;
        }

        foreach ($fileNames as $module => $dirs) {
            foreach ($dirs as $dir) {
                $sql = file_get_contents($dir);
                try {
                    $adapter->query($sql)->execute();
                } catch (\Exception $e) {
                    $this->getLogger()->err('Error for ' . $module . ': ' . $e->getMessage());
                    $this->getLogger()->err('Has this schema modification been run before?');
                }
            }
        }

    }

}