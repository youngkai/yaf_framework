<?php

use Utility\Cookies;
use Utility\Sessions;


class Request extends Yaf\Request\Http
{
    use Singleton;

    public $enableCsrfCookie = true;

    public $csrfParam = '_csrf';

    public $enableCookieValidation = true;

    const CSRF_HEADER = 'X-CSRF-Token';

    const CSRF_MASK_LENGTH = 8;

    //原始数据流
    private $_rawBody;

    //HTTP头信息
    private $_headers;

    //cookies
    private $_cookies;

    private $_csrfToken = null;

    /**
     * 用于获取原始输入数据流
     * @desc 标准流获取需要关注安全问题
     */
    public function getRawBody()
    {
        if ($this->_rawBody === null) {
            $this->_rawBody = file_get_contents('php://input');
        }
        return $this->_rawBody;
    }

    /**
     * 获取Body数据类型
     */
    public function getContentType()
    {
        if (isset($_SERVER['CONTENT_TYPE'])) {
            return $_SERVER['CONTENT_TYPE'];
        } elseif (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
            return $_SERVER['HTTP_CONTENT_TYPE']; 
        }
        return null;
    }

    /**
     * 获取方法
     */
    public function getMethod()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return null;
    }

    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; 
    }

    public function getIsSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0; 
    }

    public function getServerName()
    {
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
    }

    public function getServerPort()
    {
        return isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : null;
    }

    public function getReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    public function getUserIp()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    public function getUserHost()
    {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }

    public function getHeaders($name = '')
    {
        return '' == $name ? null : $this->getServer($name);
    }

    public function getPostBodyParam($param = null)
    {
        if ('POST' !== $this->getMethod()) {
            return false;
        } 
        if ('application/x-www-form-urlencoded' == $this->getContentType()) {
            $this->_rawBody = $_POST;
        }    
        if ('application/json' == $this->getContentType()) {
            $bodys = $this->getRawBody();
            if (is_string($bodys)) {
                $this->_rawBody = json_decode($bodys, true); 
            }
        }
        return null !== $this->_rawBody && isset($this->_rawBody[$param]) ? $this->_rawBody[$param] : '';
    }

    /**
     **********************getCsrfToken*******************
     * description
     * 2019/3/133:30 PM
     * author yangkai@rsung.com
     *******************************************
     * @param bool $regenerate
     * @return null|string
     * @throws Exception
     */
    public function getCsrfToken($regenerate = false)                                
    {                                                                                
        if ($this->_csrfToken === null || $regenerate) {                             
            if ($regenerate || ($token = $this->loadCsrfToken()) === null) {
                try {
                    $token = $this->generateCsrfToken();
                } catch (Exception $e) {
                    throw new Exception($e->getMessage(), $e->getCode());
                }
            }                                                                        
            $this->_csrfToken = Security::getInstance()->maskToken($token);              
        }                                                                            
        return $this->_csrfToken;                                                    
    }                                                                                

    protected function loadCsrfToken()                                               
    {                                                                                
        if ($this->enableCsrfCookie) {                                               
            return Cookies::get($this->csrfParam);                  
        }                                                                            
        return Sessions::getSession($this->csrfParam);
    }

    /**
     **********************generateCsrfToken*******************
     * description
     * 2019/3/133:30 PM
     * author yangkai@rsung.com
     *******************************************
     * @return bool|string
     * @throws Exception
     */
    protected function generateCsrfToken()                                           
    {
        try {
            $token = Security::getInstance()->generateRandomString();
            if ($this->enableCsrfCookie) {
                $this->createCsrfCookie($token);
            } else {
                Sessions::get($this->csrfParam, $token);
            }
            return $token;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }                                                                                

    public function getCsrfTokenFromHeader()                                         
    {                                                                                
        $key = 'HTTP_' . str_replace('-', '_', strtoupper(static::CSRF_HEADER));
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }                                                                                

    protected function createCsrfCookie($token)                                      
    {                                                                                
        return Cookies::put($this->csrfParam, $token, 0, '/', null, false, true);
    }

    /**
     **********************validateCsrfToken*******************
     * description
     * 2019/3/133:31 PM
     * author yangkai@rsung.com
     *******************************************
     * @param null $clientSuppliedToken
     * @return bool
     * @throws Exception
     */
    public function validateCsrfToken($clientSuppliedToken = null)                   
    {                                                                                
        $method = $this->getMethod();
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;                                                             
        }
        try {
            $trueToken = $this->getCsrfToken();
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        if ($clientSuppliedToken !== null) {                                         
            return $this->validateCsrfTokenInternal($clientSuppliedToken, $trueToken);
        }                                                                            
        return $this->validateCsrfTokenInternal($this->getPostBodyParam($this->csrfParam), $trueToken)
            || $this->validateCsrfTokenInternal($this->getCsrfTokenFromHeader(), $trueToken);
    }                                                                                

    private function validateCsrfTokenInternal($clientSuppliedToken, $trueToken) 
    {                                                                                
        if (!is_string($clientSuppliedToken)) {
            return false;                                                            
        }                                                                            
        return Security::getInstance()->unmaskToken($clientSuppliedToken) === Security::getInstance()->unmaskToken($trueToken);
    }  


}

