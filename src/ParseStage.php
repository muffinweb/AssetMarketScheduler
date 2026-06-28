<?php
// src/ParseStage.php

class ParseStage implements StageInterface {
    public function handle($payload) {
        $html = $payload;

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);

        $xpath = new DOMXPath($dom);
        $tables = $xpath->query('//table');

        $parsedData = [
            'doviz_grup_1' => [],
            'doviz_grup_2' => []
        ];

        for ($i = 0; $i < min(2, $tables->length); $i++) {
            $table = $tables->item($i);
            $rows = $xpath->query('.//tr', $table);

            $headers = [];
            $tableKey = 'doviz_grup_' . ($i + 1);

            // Hedef web sitesinin ana domainini dinamik olarak ayıklıyoruz (Örn: https://www.dovizsitesi.com)
            // $url değişkeninin FetchStage'e gönderilen veya sınıfta tanımlı olan hedef URL olduğunu varsayıyoruz.
            $urlComponents = parse_url('https://www.balsinirliyetkilimuessese.com/');
            $domainUrl = ($urlComponents['scheme'] ?? 'https') . '://' . $urlComponents['host'];

            foreach ($rows as $rowIndex => $row) {
                $cells = $xpath->query('.//th | .//td', $row);
                $rowData = [];

                foreach ($cells as $cell) {
                    // Hücre içinde <img> etiketi var mı kontrol ediyoruz
                    $imgElements = $cell->getElementsByTagName('img');

                    if ($imgElements->length > 0) {
                        // --- RESİMLİ HÜCRE İÇİN ÖZEL İŞLEM (SAF HTML) ---
                        foreach ($imgElements as $img) {
                            $imgSrc = $img->getAttribute('src');

                            // Eğer resim kaynak adresi '/' ile başlıyorsa, başına sitenin domainini ekliyoruz
                            if (!empty($imgSrc) && strpos($imgSrc, 'http') !== 0) {
                                $img->setAttribute('src', rtrim($domainUrl, '/') . '/' . ltrim($imgSrc, '/'));
                            }
                        }

                        // Hücrenin içindeki tüm alt düğümleri (img tagı dahil) saf HTML string olarak topluyoruz
                        $rawHtmlContent = '';
                        foreach ($cell->childNodes as $child) {
                            $rawHtmlContent .= $cell->ownerDocument->saveHTML($child);
                        }

                        // Satır atlamalarını temizleyip saf HTML halini değişkene atıyoruz
                        $cleanValue = trim(preg_replace('/\s+/', ' ', $rawHtmlContent));

                    } else {
                        // --- STANDART METİN HÜCRESİ (ORİJİNAL MANTIĞINIZ) ---
                        $cleanValue = trim(preg_replace('/\s+/', ' ', $cell->nodeValue));

                        // 1. Kural: Nümerik değer kontrolü (Rakam, nokta ve virgül barındıranlar)
                        if (preg_match('/^[0-9.,]+$/', $cleanValue)) {
                            $cleanValue = str_replace('.', '', $cleanValue);
                            $cleanValue = str_replace(',', '.', $cleanValue);

                            // 2. Kural: Noktadan sonra sadece sıfır varsa o kısmı uçur
                            if (preg_match('/\.0*$/', $cleanValue)) {
                                $cleanValue = preg_replace('/\.0*$/', '', $cleanValue);
                            }

                            $cleanValue = number_format((float)$cleanValue, '2', ',', '.');
                            $cleanValue = str_replace(',00', '', $cleanValue);
                        }
                    }

                    $rowData[] = $cleanValue;
                }

                if (empty($rowData) || (count($rowData) === 1 && $rowData[0] === '')) {
                    continue;
                }

                if (empty($headers)) {
                    $headers = $rowData;
                    continue;
                }

                if (count($rowData) === count($headers)) {
                    $rowObject = [];
                    foreach ($headers as $index => $headerTitle) {
                        $key = !empty($headerTitle) ? $headerTitle : "Sütun_" . ($index + 1);
                        $rowObject[$key] = $rowData[$index];
                    }
                    $parsedData[$tableKey][] = $rowObject;
                } else {
                    $parsedData[$tableKey][] = $rowData;
                }
            }

        }

        libxml_clear_errors();

        return json_encode($parsedData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}