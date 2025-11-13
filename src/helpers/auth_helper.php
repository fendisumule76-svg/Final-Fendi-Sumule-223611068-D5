<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../vendor/autoload.php';

function checkToken($token) {
    $secret_key = "Tim_kami_kuat_dan_kami_Ultramen!>=";

    try {
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

        // Jika berhasil decode → token valid
        return [
            "valid" => true,
            "data" => $decoded
        ];

    } catch (\Firebase\JWT\ExpiredException $e) {
        return [
            "valid" => false,
            "message" => "Token kadaluarsa"
        ];

    } catch (Exception $e) {
        return [
            "valid" => false,
            "message" => "Token tidak valid: " . $e->getMessage()
        ];
    }
}
