<?php

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