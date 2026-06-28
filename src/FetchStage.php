<?php
// src/FetchStage.php

class FetchStage implements StageInterface {
    public function handle($payload) {
        // İlk aşamada payload bizim hedef URL'imiz olacak
        $url = $payload;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_ENCODING, ""); // Sıkıştırılmış içerikleri (gzip, deflate) otomatik açar
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
            "Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL Hatası: " . $error);
        }

        curl_close($ch);

        // Bir sonraki adıma (ParseStage) HTML string'ini gönderiyoruz
        return $html;
    }
}