## AssetMarketScheduler

ℹ️ It researches the buying and selling prices of physical assets such as fiat currencies and precious metals, extracts the information, creates a clean table, and sends informational messages via SMS and email.

⚡️ Lite, package or framework independent, easy installation of personalized sms, email information via CI/CD

#### Env File Sample
```php
// .env

RESEND_API_KEY=YOUR_RESEND_API_KEY_HERE
SELF_JOURNAL_EMAIL_ADRESS=YOUR_RESEND_REGISTERED_EMAIL_HERE
```

### Installation CI/CD Sample
```yaml
name: Daily Scheduler

on:
  schedule:
    - cron: '0 0 * * *'
  workflow_dispatch:

jobs:
  run-script:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, ctype, iconv

      - name: Install Composer Dependencies
        run: composer install --no-progress --prefer-dist --optimize-autoloader

      - name: Run PHP Script
        env:
          RESEND_API_KEY: ${{ secrets.RESEND_API_KEY }}
          SELF_JOURNAL_EMAIL_ADRESS: ${{ secrets.SELF_JOURNAL_EMAIL_ADRESS }}
        run: php bootstrap.php
```

### 📫 E-Mail Template
<img width="320" height="505" alt="_Users_ugurcengiz_Desktop_Laralearn_baldovizotomasyon_2026-06-28_12-29-31 html" src="https://github.com/user-attachments/assets/6073d539-7bce-498e-a60a-b168ee596a93" />
