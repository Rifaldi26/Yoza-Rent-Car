# Panduan Deployment Production

Panduan ini mencakup setup VPS, Nginx, PHP-FPM, Supervisor, CI/CD, dan monitoring untuk Yoza Rent Car.

## Prasyarat Server

- Ubuntu 24.04 LTS
- Minimal 1 vCPU, 2 GB RAM, 20 GB SSD
- Domain yang sudah mengarah ke IP server

---

## 1. Persiapan Server

```bash
# Update sistem
apt update && apt upgrade -y

# Install dependensi
apt install -y nginx mysql-server redis-server supervisor \
    php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
    php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath \
    php8.2-redis certbot python3-certbot-nginx git unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
```

---

## 2. Konfigurasi PHP-FPM

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
expose_php = Off
upload_max_filesize = 5M
post_max_size = 6M
memory_limit = 256M
max_execution_time = 60
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 60
```

---

## 3. Konfigurasi Nginx

Buat `/etc/nginx/sites-available/yoza-rent-car`:

```nginx
server {
    listen 80;
    server_name yozarentcar.com www.yozarentcar.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yozarentcar.com www.yozarentcar.com;

    root /var/www/yoza-rent-car/public;
    index index.php;

    # Keamanan
    add_header X-Frame-Options "DENY";
    add_header X-Content-Type-Options "nosniff";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript;

    # Aset statis: cache panjang
    location ~* \.(js|css|png|jpg|jpeg|webp|svg|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # SSL (diisi oleh Certbot)
    ssl_certificate     /etc/letsencrypt/live/yozarentcar.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yozarentcar.com/privkey.pem;
}
```

```bash
ln -s /etc/nginx/sites-available/yoza-rent-car /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

# SSL dengan Let's Encrypt
certbot --nginx -d yozarentcar.com -d www.yozarentcar.com
```

---

## 4. Setup Aplikasi

```bash
# Buat direktori aplikasi
mkdir -p /var/www/yoza-rent-car
cd /var/www/yoza-rent-car

# Clone repositori
git clone https://github.com/username/yoza-rent-car.git .

# Install dependensi
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Konfigurasi
cp .env.example .env
php artisan key:generate

# Isi .env dengan nilai production (lihat bagian di bawah)

# Migrasi dan optimasi
php artisan migrate --force
php artisan db:seed --class=AccountSeeder --force
php artisan optimize
php artisan storage:link

# Permission
chown -R www-data:www-data /var/www/yoza-rent-car
chmod -R 755 /var/www/yoza-rent-car/storage
chmod -R 755 /var/www/yoza-rent-car/bootstrap/cache
```

### Konfigurasi .env Production

```dotenv
APP_NAME="Yoza Rent Car"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yozarentcar.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=yoza_production
DB_USERNAME=yoza_user
DB_PASSWORD=kata_sandi_kuat

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1

BROADCAST_CONNECTION=reverb
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=yozarentcar.com
REVERB_PORT=8080
REVERB_SCHEME=https
```

---

## 5. Supervisor — Queue Worker

Buat `/etc/supervisor/conf.d/yoza-queue.conf`:

```ini
[program:yoza-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/yoza-rent-car/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/yoza-rent-car/storage/logs/queue.log
stopwaitsecs=3600
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start yoza-queue:*
```

---

## 6. Cron Job — Task Scheduler

```bash
crontab -e -u www-data
```

Tambahkan:

```
* * * * * cd /var/www/yoza-rent-car && php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. GitHub Actions — CI/CD

Tambahkan ke GitHub Repository Secrets:

| Secret              | Nilai                               |
|---------------------|-------------------------------------|
| `SSH_HOST`          | IP atau hostname server             |
| `SSH_USER`          | User SSH (misal: deployer)          |
| `SSH_PRIVATE_KEY`   | Private key SSH (tanpa passphrase)  |
| `SSH_PORT`          | Port SSH (default 22)               |

Buat `.github/workflows/deploy.yml`:

```yaml
name: Deploy ke Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    name: Deploy via SSH
    runs-on: ubuntu-latest
    needs: [] # tambahkan 'test' dari workflow CI bila ada

    steps:
      - name: Deploy ke server
        uses: appleboy/ssh-action@v1
        with:
          host:     ${{ secrets.SSH_HOST }}
          username: ${{ secrets.SSH_USER }}
          key:      ${{ secrets.SSH_PRIVATE_KEY }}
          port:     ${{ secrets.SSH_PORT }}
          script: |
            cd /var/www/yoza-rent-car
            git pull origin main
            composer install --no-dev --optimize-autoloader
            npm ci && npm run build
            php artisan migrate --force
            php artisan optimize
            sudo supervisorctl restart yoza-queue:*
```

---

## 8. Backup Database

Install Spatie Laravel Backup:

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

Konfigurasi `config/backup.php` untuk backup ke S3 atau Cloudflare R2, kemudian:

```bash
# Test backup manual
php artisan backup:run

# Backup berjalan otomatis via scheduler (sudah dikonfigurasi di Kernel)
```

---

## 9. Monitoring

### Uptime Monitoring
Daftar di [UptimeRobot](https://uptimerobot.com) dan tambahkan monitor HTTP untuk `https://yozarentcar.com`.

### Error Tracking
Daftar di [Flare](https://flareapp.io) (khusus Laravel) atau Sentry, kemudian:

```dotenv
# .env production
FLARE_KEY=kunci-api-anda
```

---

## Checklist Go-Live

- [ ] `APP_DEBUG=false` dan `APP_ENV=production`
- [ ] HTTPS aktif dengan sertifikat valid
- [ ] `php artisan optimize` sudah dijalankan
- [ ] Queue worker berjalan (`supervisorctl status`)
- [ ] Cron job aktif (`crontab -l`)
- [ ] Backup database terkonfigurasi dan berhasil test
- [ ] Uptime monitoring aktif
- [ ] Error tracking aktif
- [ ] Google OAuth `GOOGLE_REDIRECT_URI` ke domain production
- [ ] Test registrasi, login, pemesanan, dan konfirmasi admin
