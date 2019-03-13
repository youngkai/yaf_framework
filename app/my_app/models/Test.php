<?php
/**
 * Created by PhpStorm.
 * User: youngk
 * Date: 2019/3/13
 * Time: 4:05 PM
 */

class TestModel {

    public function getAll()
    {
        try {
            $result = DB::getInstance('master')->getOne('i', 'SELECT * FROM yuan WHERE id=?', ['id' => 1]);
            return $result;
        } catch (\Exception $e) {

        }
    }
}