<?php

namespace Encryptions;

class Rsa 
{

    /**
     * Rsa constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if(false == extension_loaded('openssl')) throw new \Exception('not found openssl extensions.',500);
    }


    /**
     **********************privateKeyEncode*******************
     * description
     * 2019/3/133:16 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $plainText
     * @param string $privateKey
     * @return bool|string
     * @throws \Exception
     */
    static public function privateKeyEncode($plainText = '', $privateKey = '')
    {
        if('' == $plainText || '' == $privateKey) throw new \Exception('The plain text and private key can not be null.',500);
        if(false == $privateKey = openssl_pkey_get_private($privateKey)) throw new \Exception('The private key it is invalid.',500);
        if(false === openssl_private_encrypt($plainText, $crypted, $privateKey)) throw new \Exception('Encrytion failed.',500);
        openssl_free_key(openssl_pkey_get_private($privateKey));
        return static::strToHex(base64_encode($crypted));
    }

    /**
     **********************privateKeyDecode*******************
     * description
     * 2019/3/133:16 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $cipherText
     * @param string $privateKey
     * @return mixed
     * @throws \Exception
     */
    static public function privateKeyDecode($cipherText = '', $privateKey = '')
    {
        if('' == $cipherText || '' == $privateKey) throw new \Exception('The cipher text and private key can not be null.',500);
        if(false == $privateKey = openssl_pkey_get_private($privateKey)) throw new \Exception('The private key it is invalid',500);
        if(false === openssl_private_decrypt(base64_decode(static::hexToStr($cipherText)), $decrypted, $privateKey)) throw new \Exception('Decryption failed',500);
        openssl_free_key(openssl_pkey_get_private($privateKey));
        return $decrypted;
    }

    /**
     **********************publicKeyEncode*******************
     * description
     * 2019/3/133:16 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $plainText
     * @param string $publicKey
     * @return bool|string
     * @throws \Exception
     */
    static public function publicKeyEncode($plainText = '', $publicKey = '')
    {
        if('' == $plainText || '' == $publicKey) throw new \Exception('The plain text and public key can not be null.',500);
        if(false == $publicKey = openssl_pkey_get_public($publicKey)) throw new \Exception('The public key it is invalid',500);
        if(false === openssl_public_encrypt($plainText,$encrypted,$publicKey)) throw new \Exception('Encryption failed.',500);
        openssl_free_key($publicKey);
        return static::strToHex(base64_encode($encrypted));
    }

    /**
     **********************publicKeyDecode*******************
     * description
     * 2019/3/133:17 PM
     * author yangkai@rsung.com
     *******************************************
     * @param string $cipherText
     * @param string $publicKey
     * @return mixed
     * @throws \Exception
     */
    static public function publicKeyDecode($cipherText = '', $publicKey = '')
    {
        if('' == $cipherText || '' == $publicKey) throw new \Exception('The cipher text and public key can not be null',500);
        if(false == $publicKey = openssl_pkey_get_public($publicKey)) throw new \Exception('The public key it is invalid',500);
        if(false === openssl_public_decrypt(base64_decode(static::hexToStr($cipherText)), $decrypted,$publicKey)) throw new \Exception('Decryption failed.',500); 
        openssl_free_key($publicKey);
        return $decrypted;
    }

    static protected function hexToStr($hex = '')
    {
        if('' == $hex) return false;
        $string = '';
        for($i = 0; $i < strlen($hex) - 1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

    static protected function strToHex($string = '')
    {
        if('' == $string) return false;
        $hex = '';
        for($i = 0; $i < strlen($string); $i++){
            $hex .= dechex(ord($string[$i]));
        }
        return strtoupper($hex);
    }

}




