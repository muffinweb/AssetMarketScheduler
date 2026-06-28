<?php

/**
 * Github Actions'da bu pipe kullanımdan çıkarılmalıdır
 */
class EnvStage implements StageInterface
{
    private string $envPath = __DIR__ . '/../.env';

    /**
     * @inheritDoc
     */
    public function handle($payload)
    {
        if(file_exists($this->envPath)){
            $this->loadEnv($this->envPath);
        }
        return $payload;
    }

    /**
     * ENV Registrar
     */
    private function loadEnv($filePath): bool
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
}