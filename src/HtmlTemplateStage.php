<?php
// src/HtmlTemplateStage.php

class HtmlTemplateStage implements StageInterface {
    public function handle($payload) {
        $jsonData = $payload;
        $data = json_decode($jsonData, true);

        if (!$data) {
            throw new Exception("HtmlTemplateStage: Geçersiz veya boş JSON verisi.");
        }

        // E-posta şablonunun başlangıcı (Genel wrapper ve font tanımları)
        $htmlOutput = '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; padding: 20px; max-width: 600px; margin: 0 auto; color: #333333;">';

        // Ortak tablo stil tanımları (Inline CSS)
        $tableStyle = 'width: 100%; border-collapse: collapse; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); font-size: 14px;';
        $thStyleBase = 'padding: 12px 15px; text-align: left; font-weight: 600; color: #ffffff;';
        $tdStyle = 'padding: 10px 15px; border-bottom: 1px solid #e0e0e0;';

        // Her iki döviz/altın grubunu da sırayla dönüyoruz
        foreach ($data as $groupKey => $rows) {
            if (empty($rows)) {
                continue;
            }

            // Grup başlıklarına göre renk teması seçelim (Örn: Döviz için Lacivert, Altın için Altın Sarısı/Kahve tonları)
            $isGold = ($groupKey === 'doviz_grup_2');
            $headerBgColor = $isGold ? '#b58921' : '#2c3e50';
            $groupTitle = $isGold ? '🏆 Altın ve Kıymetli Maden Kurları' : '💵 Güncel Döviz Kurları';

            $htmlOutput .= "<h3 style='color: {$headerBgColor}; border-bottom: 2px solid {$headerBgColor}; padding-bottom: 5px; margin-top: 10px;'>{$groupTitle}</h3>";
            $htmlOutput .= "<table style='{$tableStyle}'>";

            // 1. Dinamik Başlık Satırı (TH) Oluşturma
            $htmlOutput .= "<thead><tr style='background-color: {$headerBgColor};'>";
            $headers = array_keys($rows[0]);
            foreach ($headers as $header) {
                // İlk sütunları (Cins adını) sola, sayısal değerleri sağa yaslayalım
                $textAlign = ($header === 'Döviz Cinsi' || $header === 'Altın Cinsi') ? 'left' : 'right';
                $htmlOutput .= "<th style='{$thStyleBase} text-align: {$textAlign};'> " . htmlspecialchars($header) . "</th>";
            }
            $htmlOutput .= "</tr></thead>";

            // 2. Veri Satırları (TD) Oluşturma
            $htmlOutput .= "<tbody>";
            foreach ($rows as $index => $row) {
                // Satırlara hafif kırık beyaz zebra deseni verelim (Okunabilirliği artırır)
                $rowBg = ($index % 2 === 0) ? '#ffffff' : '#f8f9fa';

                $htmlOutput .= "<tr style='background-color: {$rowBg};'>";
                foreach ($headers as $header) {
                    $value = $row[$header] ?? '-';
                    $textAlign = ($header === 'Döviz Cinsi' || $header === 'Altın Cinsi') ? 'left' : 'right';
                    if($header === 'Sembol'){
                        $textAlign = 'center';
                    }

                    // Sayısal değerlerin fontunu biraz daha belirgin yapalım
                    $fontWeight = ($textAlign === 'right') ? 'font-weight: 500; font-family: monospace; font-size: 15px;' : '';

                    $htmlOutput .= "<td style='{$tdStyle} text-align: {$textAlign}; {$fontWeight}'>" . $value . "</td>";
                }
                $htmlOutput .= "</tr>";
            }
            $htmlOutput .= "</tbody></table>";
        }

        // Şablon kapanışı
        $htmlOutput .= '<p style="font-size: 11px; color: #7f8c8d; text-align: center; margin-top: 20px;">Bu veri otomatik olarak üretilmiştir. Tetiklenme Zamanı: ' . date('d.m.Y H:i:s') . '</p>';
        $htmlOutput .= '</div>';

        // Artık bir sonraki katmana (SmsStage) JSON yerine bu şık HTML string gidiyor
        return $htmlOutput;
    }
}