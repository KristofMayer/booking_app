<?php
// /includes/jwt_helper.php

require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper {
    // TODO store the secter in an ENV variable file for security
    private static $secret_key = 'webdev';
    private static $alg = 'HS256';

    public static function generateToken($data, $expire_in_seconds = 3600) {
        $issuedAt = time();
        $payload = [
            'iat'  => $issuedAt,
            'exp'  => $issuedAt + $expire_in_seconds,
            'data' => $data,
        ];
        return JWT::encode($payload, self::$secret_key, self::$alg);
    }

    public static function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$alg));
            return (array)$decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }
}
