<?php

namespace Utility;
class Arrays
{
	/**
	 * 按指定key合并两个数组
	 * @param string key    合并数组的参照值
	 * @param array $array1  要合并数组
	 * @param array $array2  要合并数组
	 * @return array 返回合并的数组
	 */
	static public function mergeArrayWithKey($key, array $array1, array $array2) {
		if (!$key || !$array1 || !$array2) {
			return array();
		}
		$array1 = self::rebuildArrayWithKey($key, $array1);
		$array2 = self::rebuildArrayWithKey($key, $array2);
		$tmp = array();
		foreach ($array1 as $key => $array) {
			if (isset($array2[$key])) {
				$tmp[$key] = array_merge($array, $array2[$key]);
				unset($array2[$key]);
			} else {
				$tmp[$key] = $array;
			}
		}
		return array_merge($tmp, (array) $array2);
	}

	/**
	 * 按指定key合并两个数组
	 * @param string key    合并数组的参照值
	 * @param array $array1  要合并数组
	 * @param array $array2  要合并数组
	 * @return array 返回合并的数组
	 */
	static public function filterArrayWithKey($key, array $array1, array $array2) {
		if (!$key || !$array1 || !$array2) {
			return array();
		}
		$array1 = self::rebuildArrayWithKey($key, $array1);
		$array2 = self::rebuildArrayWithKey($key, $array2);
		$tmp = array();
		foreach ($array1 as $key => $array) {
			if (isset($array2[$key])) {
				$tmp[$key] = array_merge($array, $array2[$key]);
			}
		}
		return $tmp;
	}

    /**
     **********************rebuildArrayWithKey*******************
     * description
     * 2019/3/133:42 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $key
     * @param array $array
     * @return array
     */
	static public function rebuildArrayWithKey($key, array $array) {
		if (!$key || !$array) {
			return array();
		}
		$tmp = array();
		foreach ($array as $_array) {
			if (isset($_array[$key])) {
				$tmp[$_array[$key]] = $_array;
			}
		}
		return $tmp;
	}

    /**
     **********************array_get*******************
     * description 获取指定key的value
     * 2019/3/133:43 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $array
     * @param $key
     * @param null $default
     * @return mixed|string
     */
	static public function array_get($array, $key, $default = null) {
		if (is_null($key)) return $array;
		foreach (explode('.', $key) as $segment) {
			if ( ! is_array($array) or ! array_key_exists($segment, $array)) {
				return self::value($default);
			}
			$array = $array[$segment];
		}
		return $array;
	}

    /**
     **********************array_set*******************
     * description 向数组插入指定的key/value
     * 2019/3/133:43 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $array
     * @param $key
     * @param $value
     * @return mixed
     */
	static public function array_set(&$array, $key, $value) {
		if (is_null($key)) return $array = $value;
		$keys = explode('.', $key);
		while (count($keys) > 1) {
			$key = array_shift($keys);
			if ( ! isset($array[$key]) or ! is_array($array[$key]))	{
				$array[$key] = array();
			}
			$array =& $array[$key];
		}
		$array[array_shift($keys)] = $value;
	}
	
	/**
	 * 删除数组指定的key
	 * @param array $array
	 * @param string $key
	 */
	static public function remove(&$array, $key) {
		$keys = explode('.', $key);
		while (count($keys) > 1) {
			$key = array_shift($keys);
			if ( ! isset($array[$key]) or ! is_array($array[$key])) { return; }
			$array =& $array[$key];
		}
		unset($array[array_shift($keys)]);
	}
	

	
	
	static public function array_strip_slashes($array)
	{
		$result = array();
	
		foreach($array as $key => $value)
		{
			$key = stripslashes($key);
	
			if (is_array($value))
			{
				$result[$key] = self::array_strip_slashes($value);
			}
			else
			{
				$result[$key] = stripslashes($value);
			}
		}
	
		return $result;
	}
	
	static public function array_divide($array)
	{
		return array(array_keys($array), array_values($array));
	}
	
	
	static public function array_pluck($array, $key)
	{
		return array_map(function($v) use ($key)
		{
			return is_object($v) ? $v->$key : $v[$key];
	
		}, $array);
	}
	
	static public function array_only($array, $keys)
	{
		return array_intersect_key( $array, array_flip((array) $keys) );
	}
	
	static public function array_except($array, $keys)
	{
		return array_diff_key( $array, array_flip((array) $keys) );
	}
	
    static public function value($value) 
    {
		return (is_callable($value) && !is_string($value)) ? call_user_func($value) : $value;
	}
	
}
