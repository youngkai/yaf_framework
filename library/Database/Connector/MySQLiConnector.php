<?php

namespace Database\Connector;

class MySQLiConnector 
{

    /**
     **********************connect*******************
     * description
     * 2019/3/133:08 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $config
     * @return \mysqli
     * @throws \Exception
     */
	public function connect($config)
	{
        $host = isset($config['host']) && !empty($config['host']) ? $config['host'] : '127.0.0.1';
        $username = isset($config['username']) && !empty($config['username']) ? $config['username'] : 'root';
        $password = isset($config['password']) && !empty($config['password']) ? $config['password'] : '';
        $database = isset($config['database']) && !empty($config['database']) ? $config['database'] : '';
        $port = isset($config['port']) && !empty($config['port']) ? $config['port'] : '3306';
        $charset = isset($config['charset']) && !empty($config['charset']) ? $config['charset'] : 'utf8';
        $connections = new \mysqli($host ,$username, $password, $database, $port);
        if(mysqli_connect_errno())
            throw new \Exception('MySQLi Connect Failed', mysqli_connect_errno());
        $connections->set_charset($charset);
        return $connections;
	}

}

