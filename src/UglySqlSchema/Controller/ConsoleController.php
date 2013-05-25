<?php
namespace UglySqlSchema\Controller;

use UglySqlSchema\Service\MergeService;
use Zend\Filter\StaticFilter;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    /**
     * @var MergeService
     */
    protected $mergeService;

    /**
     * @param MergeService $mergeService
     */
    function __construct(MergeService $mergeService)
    {
        $this->mergeService = $mergeService;
    }

    public function mergeAction()
    {

        $platform = $this->params()->fromRoute('platform', 'mysql');
        $this->mergeService->merge($platform);
        return 'FIN';
    }


}