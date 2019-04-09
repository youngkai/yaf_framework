<?php


use Yaf\Application;

class Controller extends Yaf\Controller_Abstract
{

    /**
     * @var $response Response
     */
    protected $response = null;

    /**
     **********************init*******************
     * description
     * 2019/3/132:54 PM
     * author yangkai@rsung.com
     *******************************************
     */
    public function init()
    {
        $this->response = new Response();
        $this->beforeInit();
        $response_type = Yaf\Application::app()->getConfig()->get('application.response_type');
        $this->responseType($response_type);
    }

    /**
     **********************beforeInit*******************
     * description
     * 2019/3/132:54 PM
     * author yangkai@rsung.com
     *******************************************
     */
    protected function beforeInit() { }

    /**
     **********************responseType*******************
     * description
     * 2019/3/132:54 PM
     * author yangkai@rsung.com
     *******************************************
     * @param null $responseType
     */
    protected function responseType($responseType = NULL)
    {
        $responseType = NULL === $responseType ? Yaf\Registry::get('responseType') : $responseType;
        switch($responseType)
        {
            case 'json':
            case 'msgpack':
                Yaf\Dispatcher::getInstance()->disableView(); 
                Yaf\Dispatcher::getInstance()->autoRender(false);
                $this->_view->engine = NULL;
                break;
            case 'tpl':
                Yaf\Dispatcher::getInstance()->autoRender(true);
                $this->_view->engine = Template::instance();
                break;
            case 'yaf':
            default:
                $responseType = 'yaf';
                Yaf\Dispatcher::getInstance()->autoRender(true);
                $this->_view->engine = NULL;
        }
        Yaf\Registry::set('responseType', $responseType);
    }

    /**
     **********************response*******************
     * description
     * 2019/3/132:54 PM
     * author yangkai@rsung.com
     *******************************************
     * @param array $data
     */
    protected function response($data = [])
    {
        $this->response->send($data);
    }
}
