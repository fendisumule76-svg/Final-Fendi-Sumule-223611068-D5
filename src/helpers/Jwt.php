<?php
namespace Src\Helpers;

class Jwt
{
    // Fungsi untuk encoding base64 URL-safe
    public static function base64url($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Fungsi untuk membuat JWT (sign)
    public static function sign(array $payload, string $secret, string $alg = 'HS256')
    {
        $header = ['typ' => 'JWT', 'alg' => $alg];

        $segments = [
            self::base64url(json_encode($header)),
            self::base64url(json_encode($payload))
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
        $segments[] = self::base64url($signature);

        return implode('.', $segments);
    }

    // Fungsi untuk memverifikasi JWT
    public static function verify(string $jwt, string $secret)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return null;

        [$header, $body, $signature] = $parts;

        $check = self::base64url(hash_hmac('sha256', "$header.$body", $secret, true));
        if (!hash_equals($check, $signature)) return null;

        $payload = json_decode(base64_decode(strtr($body, '-_', '+/')), true);

        if (isset($payload['exp']) && time() > $payload['exp']) return null;

        return $payload;
    }
}