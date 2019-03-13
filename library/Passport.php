<?php


class Passport
{
    static public function password($password, $salt)
    {
        return md5($salt . md5($password) . $salt);
    }

    static public function check($password, $password_from_db, $salt_from_db)
    {
        return $password_from_db === static::password($password, $salt_from_db);
    }

    static public function passwordMd5($password, $salt = '')
    {
        return md5($salt . $password . $salt);
    }

    static public function checkMd5($password, $password_from_db, $salt_from_db = '')
    {
        return $password_from_db === static::passwordMd5($password, $salt_from_db);
    }
}
