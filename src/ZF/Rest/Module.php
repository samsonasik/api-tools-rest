<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-rest for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-rest/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-rest/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Rest;

/**
 * Laminas module
 */
class Module
{
    /**
     * Retrieve autoloader configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array('Laminas\Loader\StandardAutoloader' => array('namespaces' => array(
            __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
        )));
    }

    /**
     * Retrieve module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    /**
     * Bootstrap listener
     *
     * Attaches a listener to the RestController dispatch event.
     * 
     * @param  \Laminas\Mvc\MvcEvent $e 
     */
    public function onBootstrap($e)
    {
        $app          = $e->getTarget();
        $services     = $app->getServiceManager();
        $events       = $app->getEventManager();
        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach('Laminas\ApiTools\Rest\RestController', $e::EVENT_DISPATCH, array($this, 'onDispatch'), 100);
        $sharedEvents->attachAggregate($services->get('Laminas\ApiTools\Rest\RestParametersListener'));
    }

    /**
     * RestController dispatch listener
     *
     * Attach the ApiProblem RenderErrorListener when a restful controller is detected.
     * 
     * @param  \Laminas\Mvc\MvcEvent $e 
     */
    public function onDispatch($e)
    {
        $app      = $e->getApplication();
        $events   = $app->getEventManager();
        $services = $app->getServiceManager();
        $listener = $services->get('Laminas\ApiTools\ApiProblem\RenderErrorListener');
        $events->attach($listener);
    }
}
