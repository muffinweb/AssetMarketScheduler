<?php

/**
 * ENV Registrar
 */
function loadEnv($filePath): bool
{
    if (!file_exists($filePath)) {
        return false;
    }

    // Dosyayı satır satır oku
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Yorum satırlarını ( # ile başlayan ) atla
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Anahtar ve değeri ayır (Örn: API_KEY="şifre")
        list($name, $value) = explode('=', $line, 2);

        $name = trim($name);
        $value = trim($value);

        // Değerin başındaki ve sonundaki tırnak işaretlerini temizle
        $value = trim($value, '"\'');

        // Sistem çevre değişkenlerine, $_ENV ve $_SERVER dizilerine kaydet
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }

    return true;
}

loadEnv(__DIR__ . '/../.env');

/**
 * Autoloader
 */
spl_autoload_register(function ($className) {
    $file = __DIR__ . '/' . $className . '.php';

    // Eğer dosya mevcutsa otomatik olarak dahil et
    if (file_exists($file)) {
        require_once $file;
    }
});