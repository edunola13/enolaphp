<?php
namespace Enola\Lib;

//
//Libreria Actual Composer: "firebase/php-jwt": "v4.0.0"
use Firebase\JWT\JWT;

class Auth {
    private static $secret_key = '123enolaphp789';
    private static $encrypt = ['HS256'];
    private static $aud = null;
    
    public static function signIn($data, $maxTime= null){
        $time = time();        
        if($maxTime == null){
            $maxTime= 60*60;
        }
        $token = array(
            'exp' => $time + $maxTime,
            'aud' => self::aud(),
            'data' => $data
        );
        return JWT::encode($token, self::$secret_key);
    }
    
    public static function check($token){
        if(empty($token)){
            throw new Exception("Invalid token supplied.");
        }        
        $decode = JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        );        
        if($decode->aud !== self::aud()){
            throw new Exception("Invalid user logged in.");
        }
    }
    
    public static function getData($token){
        return (array)JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        )->data;
    }
    
    private static function aud()
    {
        $aud = '';        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }
}