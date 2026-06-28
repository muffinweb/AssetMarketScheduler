<?php

class EmailStage implements StageInterface
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function handle($html)
    {
// 1. Gönderilecek Veri Seti (PHP Dizisi olarak hazırlayıp aşağıda JSON'a çevireceğiz)
        $emailData = [
            "from" => "onboarding@resend.dev",
            "to" => getenv('SELF_JOURNAL_EMAIL_ADRESS'),
            "subject" => "Günlük Piyasa Raporu",
            "html" => $html
        ];

// 2. Hedef API URL Tanımı
        $apiUrl = 'https://api.resend.com/emails';

// 3. API Anahtarı (cURL komutundaki Bearer Token)
        $apiKey = getenv('RESEND_API_KEY');

// 4. cURL Oturumunu Başlatma
        $ch = curl_init($apiUrl);

// 5. cURL Seçeneklerini Detaylıca Yapılandırma
        curl_setopt_array($ch, [
            // İstek tipini POST olarak belirliyoruz (-X POST karşılığı)
            CURLOPT_POST => true,

            // Gönderilecek veriyi JSON formatına dönüştürüyoruz (-d karşılığı)
            CURLOPT_POSTFIELDS => json_encode($emailData),

            // Sunucudan dönen yanıtı ekrana direkt basmak yerine bir değişkene kaydetmesini söylüyoruz
            CURLOPT_RETURNTRANSFER => true,

            // HTTP Header (Başlık) bilgilerini tanımlıyoruz (-H karşılıkları)
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ],

            // Bağlantı ve işlem zaman aşımı süreleri (Otomasyonun kilitlenmesini önlemek için önemlidir)
            CURLOPT_CONNECTTIMEOUT => 10, // Bağlanmak için max 10 saniye bekler
            CURLOPT_TIMEOUT => 30, // Toplam işlem için max 30 saniye bekler

            // Localhost veya bazı sunuculardaki SSL sertifika doğrulama hatalarını bypass etmek için:
            CURLOPT_SSL_VERIFYPEER => false
        ]);

// 6. İsteği Gerçekleştirme ve Yanıtı Alma
        $response = curl_exec($ch);

// 7. Hata ve Durum Kontrolleri (Her Detayıyla)
        if (curl_errno($ch)) {
            // Eğer cURL kütüphanesinin kendisinde bir hata oluştuysa (Örn: İnternet yok, DNS çözülemedi vb.)
            throw new Exception("🚨 cURL Bağlantı Hatası: " . curl_error($ch) . "\n");
        } else {
            // HTTP Durum Kodunu alıyoruz (200, 201, 400, 401, 500 vb.)
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Resend'den dönen ham JSON yanıtını PHP nesnesine (Object) çeviriyoruz
            $responseData = json_decode($response, true);

            // Resend'den gelen, yanıt olarak kullanacağımız veri
            $responseMessage = $responseData['message'] ?? $responseData['id'] ?? "";

            echo "📊 HTTP Durum Kodu: " . $httpCode . "\n";
            echo "📩 Sunucu Yanıtı: {$responseMessage} \n";

            if ($httpCode === 200 || $httpCode === 201) {
                echo "✅ E-posta gönderimi başarıyla sıraya alındı!\n";
                // Başarılı olduğunda dönen id'yi otomasyonunda loglayabilirsin: $responseData['id']
            } else {
                throw new Exception("❌ Bir API hatası oluştu!\n");
            }
        }

// 8. cURL Oturumunu Kapatarak Kaynakları Serbest Bırakma
        curl_close($ch);

        return $html;
    }
}