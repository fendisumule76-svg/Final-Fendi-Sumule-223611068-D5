<?php
namespace Src\Helpers;

class RateLimiter
{
    public static function check($key, $max = 100, $window = 60)
    {
        $dir = __DIR__ . '/../../logs';
        if (!file_exists($dir)) mkdir($dir, 0777, true);

        $file = $dir . '/ratelimit_' . md5($key) . '.txt';
        $now = time();
        $hits = [];

        if (file_exists($file)) {
            $hits = array_filter(array_map('intval', explode("\n", trim(file_get_contents($file)))));
            $hits = array_filter($hits, fn($t) => ($now - $t) < $window);
        }

        if (count($hits) >= $max) {
            return false; // sudah melebihi batas
        }

        $hits[] = $now;
        file_put_contents($file, implode("\n", $hits));
        return true;
    }
}
