<?php

class WebApiPlugin extends \Yaf\Plugin_Abstract
{
    public $config;
    /**
     * @var $register \Yaf_Loader
     */
    public $register;

    /**
     **********************routerStartup*******************
     * description
     * 2018/8/31下午7:07
     * author yangkai@rsung.com
     *******************************************
     * @param \Yaf\Request_Abstract $request
     * @param \Yaf\Response_Abstract $response
     * @return mixed|void
     * @throws Exception
     */
    public function routerStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
        $this->config = Yaf\Registry::get('config');
        $this->register = Yaf\Loader::getInstance();
        if (isset($this->config->application->library) && !empty($this->config->application->library)) {
            $library = explode(',',$this->config->application->library);
            foreach($library as $key)
            {
                if (false == is_dir(APPLICATION_PATH . DIRECTORY_SEPARATOR . $key)) {
                    throw new Exception(APPLICATION_PATH . DIRECTORY_SEPARATOR . $key.'目录无效.', 400);
                }
                $this->loader($key, 'class');
            }
        }
    }

    /**
     **********************routerShutdown*******************
     * description
     * 2018/8/31下午7:07
     * author yangkai@rsung.com
     *******************************************
     * @param \Yaf\Request_Abstract $request
     * @param \Yaf\Response_Abstract $response
     * @return mixed|void
     * @throws Exception
     */
    public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
        if (isset($this->config->application->tools) && !empty($this->config->application->tools)) {
            $tools = explode(',',$this->config->application->tools);
            foreach($tools as $key)
            {
                if (false == is_dir(APPLICATION_PATH . DIRECTORY_SEPARATOR.$key)) {
                    throw new Exception(APPLICATION_PATH . DIRECTORY_SEPARATOR.$key.'目录无效.', 400);
                }
                $this->loader($key, 'tools');
            }
        }
//        if (!self::_RequestIgnore($request->module, $request->controller, $request->action)) {
//            //登录状态校验
//            if (true === Authorize::guest()) {
//                throw new Exception('未授权访问，请登录.', 403);
//            }
//        }
    }

    public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {

    }

    public function preDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {

    }

    public function postDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {

    }

    public function dispatchLoopShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {

    }

    /**
     * 获取Referer
     */
    public function getReferer()
    {
        $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        return urlencode($protocol . '://' . $host . $requestUri);
    }

    /**
     **********************loader*******************
     * description 自动加载
     * 2018/9/12下午9:36
     * author yangkai@rsung.com
     *******************************************
     * @param string $directory
     * @param string $type
     * @return mixed
     */
    protected function loader($directory = '', $type = '')
    {
        if ('' == $directory || '' == $type) return false;
        $libraryPath = APPLICATION_PATH.DIRECTORY_SEPARATOR . $directory;
        $handler = opendir($libraryPath);
        $globalLibraryPath = $this->register->getLibraryPath(true);
        $this->register->setLibraryPath($libraryPath, true);
        while(true == ($fileName = readdir($handler)))
        {
            if ($fileName != '.' && $fileName != '..' && count(scandir($libraryPath)) > 2) {
                if (is_dir($libraryPath . DIRECTORY_SEPARATOR . $fileName)) {
                    $this->loader($directory . DIRECTORY_SEPARATOR . $fileName, $type);
                } else {
                    if (true == is_file($libraryPath . DIRECTORY_SEPARATOR . $fileName)) {
                        $fileInfo = pathinfo($fileName);
                        if ('php' == $fileInfo['extension']) {
                            if ('class' == $type) {
                                $this->register->autoload($fileInfo['filename']);
                            }
                            if ('tools' == $type) {
                                $this->register->import($libraryPath . DIRECTORY_SEPARATOR . $fileName);
                            }
                        }
                    }
                }
            }
        }
        closedir($handler);
        //必须还原全局类库的路径
        $this->register->setLibraryPath($globalLibraryPath, true);
    }

    /**
     * 排除登录校验,不需要状态校验的controller集中在这里处理
     * @param $module string
     * @param $controller string
     * @param $method string
     * @description
     * 后期考虑放置到配置文件中管理
     * [
     *     'module'=>'*',
     *     'module'=>
     *     [
     *         'controller'=>'*',
     *         'controller1'=>
     *         [
     *             'action1',
     *             'action2'
     *         ]
     *      ]
     * ]
     * @return bool
     */
    protected function _RequestIgnore($module, $controller, $method)
    {
        $ignoreArr =
            [
                'Index'=>
                    [
                        'Login'=>
                            [
                                'login',
                                'loginout',
                                'conf'
                            ],
                    ],
            ];
        if (isset($ignoreArr[$module]) && "*" == $ignoreArr[$module]) return true;
        if (isset($ignoreArr[$module][$controller]) && "*" == $ignoreArr[$module][$controller]) return true;
        if (isset($ignoreArr[$module][$controller]) && in_array($method,$ignoreArr[$module][$controller])) return true;
        return false;
    }
}