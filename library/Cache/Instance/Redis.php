<?php

namespace Cache\Instance;



use \Cache\Driver;



class Redis extends Driver 
{
    /**
     * @var $redis \Redis
     */
	protected $redis;
	
	public function __construct(\Cache\Connector\RedisConnection $redis)
	{
		$this->redis = $redis;
	}
	
	public function has($key)
	{
		return false === $this->redis->get($key) ? false : true;
	}
	
	public function get($key)
	{
		if(!is_null($cache = $this->redis->get($key)))
		{
			return $cache;
		}else {
		    return null;
        }
	}
	
	public function put($key, $value, $seconds = 0)
	{
		return true === $this->forever($key, $value) && true === $this->redis->expire($key, $seconds) ? true : false;
	}
	
	public function forever($key, $value)
	{
		return $this->redis->set($key, $value);
	}

    public function ttl($key)
    {
        return $this->redis->ttl($key);
    }

	public function delete($key)
	{
        return $this->redis->del($key);
    }

	public function __call($method, $parameters)
	{
		return call_user_func_array([$this->redis, $method], $parameters);
    }
}
