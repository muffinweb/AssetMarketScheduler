<?php

require_once 'src/load.php';

try {

    $serviceUrl = 'https://www.balsinirliyetkilimuessese.com/doviz.aspx';

    $pipeline = new Pipeline();

    $result = $pipeline
        //->pipe(new EnvStage())
        ->pipe(new FetchStage())         // 1. Web sitesinden HTML'i çek
        ->pipe(new ParseStage())         // 2. Tabloları ayıkla, sayıları temizle ve JSON yap
        ->pipe(new HtmlTemplateStage())  // 3. JSON'u oku, e-posta uyumlu HTML tablolara dök
        ->pipe(new EmailStage())         // 4. Nihai HTML çıktısını abonelere email ile gönder
        ->pipe(new ArchiveStage())       // 5. Nihai HTML çıktısını dosya olarak kaydet
        ->process($serviceUrl);

    if ($result) {
        echo "\nTüm pipeline akışı sırasıyla başarıyla tamamlandı!";
    }

} catch (Exception $e) {
    echo "\nSüreç hatası: " . $e->getMessage();
}