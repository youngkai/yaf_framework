<?php
/**
 * Created by PhpStorm.
 * User: youngk
 * Date: 2019/3/12
 * Time: 8:42 PM
 */
class Bootstrap extends Yaf\Bootstrap_Abstract
{
    protected $config;

    /**
     * Initialization Yaf
     * @param Yaf\Dispatcher $dispatcher
     */
    public function _initializers(Yaf\Dispatcher $dispatcher)
    {
        $this->config = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('config', $this->config);
        null !== $this->config->routes && $dispatcher->getRouter()->addConfig($this->config->routes);
        $dispatcher->registerPlugin((new WebApiPlugin()));
        $driver = $this->config->get('auth.driver');
        unset($this->config, $driver);
    }

}