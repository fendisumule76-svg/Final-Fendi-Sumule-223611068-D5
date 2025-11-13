<?php

header('Content-Type: application/json');


$secret = 'Tim_kami_kuat_dan_kami_Ultramen!';
$token_ttl_seconds = 3600; // 1 jam


function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function base64UrlDecode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) $data .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($data, '-_', '+/'));
}
function getAuthorizationHeader() {
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'authorization') return $v;
        }
    }
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) return $_SERVER['HTTP_AUTHORIZATION'];
    if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) return $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    return null;
}
function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}


$authHeader = getAuthorizationHeader();


if ($authHeader) {
    if (!preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
        json_response(['status' => 'error', 'message' => 'Format Authorization salah (gunakan Bearer <token>)'], 400);
    }
    $jwt = $matches[1];

    
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        json_response(['status' => 'error', 'message' => 'Format token tidak valid'], 400);
    }

    list($header64, $payload64, $signatureProvided) = $parts;

   
    $expected = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header64.$payload64", $secret, true)), '+/', '-_'), '=');

  
    if (!hash_equals($expected, $signatureProvided)) {
        json_response(['status' => 'error', 'message' => 'Signature token tidak valid'], 401);
    }

   
    $payload = json_decode(base64UrlDecode($payload64), true);
    if (!is_array($payload)) {
        json_response(['status' => 'error', 'message' => 'Payload tidak bisa dibaca'], 400);
    }

    
    if (!isset($payload['exp'])) {
        json_response(['status' => 'error', 'message' => 'Token tidak memiliki waktu kadaluarsa (exp)'], 400);
    }

    if ($payload['exp'] < time()) {
        json_response([
            'status' => 'expired',
            'message' => 'Token sudah kadaluarsa',
            'expired_at' => date('Y-m-d H:i:s', $payload['exp']),
            'now' => date('Y-m-d H:i:s')
        ], 401);
    }

    
    json_response([
        'status' => 'valid',
        'message' => 'Token masih berlaku',
        'payload' => $payload,
        'expires_in_seconds' => $payload['exp'] - time(),
        'expired_at' => date('Y-m-d H:i:s', $payload['exp']),
        'now' => date('Y-m-d H:i:s')
    ]);
}


$header = ['typ' => 'JWT', 'alg' => 'HS256'];
$payload = [
    'sub' => 2,
    'name' => 'fendisumule',
    'role' => 'admin',
    'iat' => time(),
    'exp' => time() + $token_ttl_seconds
];

$header64 = base64UrlEncode(json_encode($header));
$payload64 = base64UrlEncode(json_encode($payload));
$signature = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header64.$payload64", $secret, true)), '+/', '-_'), '=');
$jwt = "$header64.$payload64.$signature";


json_response([
    'status' => 'token_generated',
    'token' => $jwt,
    'payload' => $payload,
    'expired_at' => date('Y-m-d H:i:s', $payload['exp'])
]);
