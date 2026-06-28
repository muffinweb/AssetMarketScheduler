<?php
// src/SmsStage.php

class ArchiveStage implements StageInterface {
    public function handle($payload) {
        $htmlContent = $payload; // Artık payload bir HTML şablonu

        date_default_timezone_set('Europe/Istanbul');
        $fileName = date('Y-m-d_H-i-s') . '.html'; // Uzantıyı .html yaptık

        $filePath = dirname(__DIR__) . '/' . $fileName;
        $saveResult = file_put_contents($filePath, $htmlContent);

        if ($saveResult === false) {
            throw new Exception("HTML şablon dosyası kaydedilemedi: " . $filePath);
        }

        echo "✅ E-posta Uyumlu Çıktı Dosyalanarak Kaydedildi: " . $fileName . "\n";
        return true;
    }
}