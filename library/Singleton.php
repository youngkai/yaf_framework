<?php

trait Singleton
{                                                                                    
    /**                                                                              
     * 获取实例                                                                      
     * @return static                                                                
     */                                                                              
    static public function getInstance()                                             
    {                                                                                
        static $instance = [];                                                       
        $cls = get_called_class();                                                   
        if (!isset($instance[$cls]) || $instance[$cls] === null) {                   
            $instance[$cls] = new $cls;                                              
        }                                                                            
        return $instance[$cls];                                                      
    }                                                                                
} 
