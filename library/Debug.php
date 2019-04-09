<?php

defined('RUNTIME_START') or define('RUNTIME_START', microtime(true));
defined('USEMEM_START') or define('USEMEM_START', memory_get_usage());
/**
 * 调试工具 
 */
class debug 
{

	/**
	 * @var array 调试点
	 */
	private static $breakpoint = [];

	/**
	 * @var int 保留的小数位数
	 */
	const DECIMAL_DIGITS = 4;

	/**
	 * @var int 记录内存使用标记
	 */
	const MEMORY = 'mem';

	/**
	 * @var int 记录程序运行时时间使用标记
	 */
	const RUN_TIME = 'time';

    /**
     **********************setBreakPoint*******************
     * description
     * 2019/3/133:23 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $point
     * @return bool
     */
    static public function setBreakPoint($point = '') 
    {
		if (isset(self::$breakpoint[$point])) return false;
		self::$breakpoint[$point][self::RUN_TIME] = microtime(true);
		self::$breakpoint[$point][self::MEMORY] = memory_get_usage();
		return true;
	}

	/**
	 * 移除调试点
	 * @param string $point 调试点
	 */
    static public function removeBreakPoint($point = '') 
    {
		if ($point) {
			if (isset(self::$breakpoint[$point])) unset(self::$breakpoint[$point]);
		} else {
			self::$breakpoint = [];
		}
	}

	/**
	 * 取得系统运行所耗内存
	 */
    static public function getMemUsage() 
    {
		$useMem = memory_get_usage() - USEMEM_START;
		return $useMem ? round($useMem / 1024, self::DECIMAL_DIGITS) : 0;
	}

	/**
	 * 取得系统运行所耗时间
	 */
    static public function getExecTime() 
    {
		$useTime = microtime(true) - RUNTIME_START;
		return $useTime ? round($useTime, self::DECIMAL_DIGITS) : 0;
	}

    /**
     **********************getBreakPoint*******************
     * description 获取调试点
     * 2019/3/133:23 PM
     * author yangkai@rsung.com
     *******************************************
     * @param $point
     * @param string $label
     * @return array|mixed
     */
    static public function getBreakPoint($point, $label = '') 
    {
		if (!isset(self::$breakpoint[$point])) return [];
		return $label ? self::$breakpoint[$point][$label] : self::$breakpoint[$point];
	}

	/**
	 * 调试点之间系统运行所耗内存
	 * @param string $beginPoint 开始调试点
	 * @param string $endPoint   结束调试点
	 * @return float 
	 */
    static public function getMemUsageOfp2p($beginPoint, $endPoint = '') 
    {
		if (!isset(self::$breakpoint[$beginPoint])) return 0;
		$endMemUsage = isset(self::$breakpoint[$endPoint]) ? self::$breakpoint[$endPoint][self::MEMORY] : memory_get_usage();
		$useMemUsage = $endMemUsage - self::$breakpoint[$beginPoint][self::MEMORY];
		return round($useMemUsage / 1024, self::DECIMAL_DIGITS);
	}

	/**
	 * 调试点之间的系统运行所耗时间
	 * @param string $beginPoint 开始调试点
	 * @param string $endPoint   结束调试点
	 * @return float 
	 */
    static public function getExecTimeOfp2p($beginPoint, $endPoint = '') 
    {
		if (!isset(self::$breakpoint[$beginPoint])) return 0;
		$endTime = self::$breakpoint[$endPoint] ? self::$breakpoint[$endPoint][self::RUN_TIME] : microtime(true);
		$useTime = $endTime - self::$breakpoint[$beginPoint][self::RUN_TIME];
		return round($useTime, self::DECIMAL_DIGITS);
	}

	/**
	 * 堆栈情况
	 * @param array $trace 堆栈引用，如异常
	 * @return array 
	 */
    static public function trace($trace = []) 
    {
		$debugTrace = $trace ? $trace : debug_backtrace();
		$traceInfo = [];
        foreach ($debugTrace as $info) 
        {
			$info['args'] = self::traceArgs($info['args']);
			$file = isset($info['file']) ? $info['file'] : '';
			$line = isset($info['line']) ? $info['line'] : '';
			$str = '[' . date("Y-m-d H:i:m") . '] ' . $file . ' (line:' . $line . ') ';
			$str .= $info['class'] . $info['type'] . $info['function'] . '(';
			$str .= implode(', ', $info['args']);
			$str .= ")";
			$traceInfo[] = $str;
		}
		return $traceInfo;
	}

	/**
	 * 获取系统所加载的文件
	 */
    static public function loadFiles() 
    {
		return get_included_files();
	}

    static public function run($message = '', $trace = [], $begin = '', $end = '') 
    {
		$runtime = self::getExecTime();
		$useMem = self::getMemUsage();
		$separate = "<br/>";
		$trace = implode("{$separate}", self::trace($trace));
		$debug = '';
		$debug .= "{$message}{$separate}";
		$debug .= "Runtime:{$runtime}s{$separate}";
		$debug .= "Memory consumption:{$useMem}byte{$separate}";
		$debug .= "Stack conditions:{$separate}{$trace}{$separate}";
		if ($begin && $end) {
			$PointUseTime = self::getExecTimeOfp2p($begin, $end);
			$PointUseMem = self::getMemUsageOfp2p($begin, $end);
			$debug .= "Between points {$begin} and {$end} debugging system conditions:{$separate}";
			$debug .= "Runtime:{$PointUseTime}s{$separate}";
			$debug .= "Memory consumption:{$PointUseMem}byte{$separate}";
		}
		return $debug;
    }

    static public function getMethodDefined($funcname)                               
    {                                                                                
        try {                                                                         
            if(is_array($funcname)) {                                                 
                $func = new ReflectionMethod($funcname[0], $funcname[1]);
                $funcname = $funcname[1];                                             
            } else {                                                                  
                $func = new ReflectionFunction($funcname);
            }                                                                         
        } catch (ReflectionException $e) {
            echo $e->getMessage();                                                    
            return;                                                                                                                                                                                         
        }                                                                             
        $start = $func->getStartLine() - 1;                                           
        $end =  $func->getEndLine() - 1;                                              
        $filename = $func->getFileName();                                             
        echo "function $funcname defined by $filename($start - $end)\n";              
    }

    private static function traceArgs($args = []) 
    {
        foreach ($args as $key => $arg) 
        {
			if (is_array($arg))
				$args[$key] = 'array(' . implode(',', $arg) . ')';
			elseif (is_object($arg))
				$args[$key] = 'class ' . get_class($arg);
			else
				$args[$key] = $arg;
		}
		return $args;
	}

}
