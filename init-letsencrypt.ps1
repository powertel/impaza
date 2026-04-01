# SSL Certificate Setup Script for Impaza Production (PowerShell)
# This script initializes Let's Encrypt SSL certificates for your domain

$domains = @("impazamon.powertel.co.zw")
$rsa_key_size = 4096
$data_path = "./certbot"
$email = "admin@powertel.co.zw"  # Replace with a valid email address
$staging = 0  # Set to 1 if you're testing your setup to avoid hitting request limits

Write-Host "### SSL Certificate Setup for Impaza Production ###" -ForegroundColor Green

if (Test-Path $data_path) {
    $decision = Read-Host "Existing data found for $($domains -join ', '). Continue and replace existing certificate? (y/N)"
    if ($decision -ne "Y" -and $decision -ne "y") {
        exit
    }
}

# Create directories and download TLS parameters
if (!(Test-Path "$data_path/conf/options-ssl-nginx.conf") -or !(Test-Path "$data_path/conf/ssl-dhparams.pem")) {
    Write-Host "### Downloading recommended TLS parameters ..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Force -Path "$data_path/conf" | Out-Null
    
    try {
        Invoke-WebRequest -Uri "https://raw.githubusercontent.com/certbot/certbot/master/certbot-nginx/certbot_nginx/_internal/tls_configs/options-ssl-nginx.conf" -OutFile "$data_path/conf/options-ssl-nginx.conf"
        Invoke-WebRequest -Uri "https://raw.githubusercontent.com/certbot/certbot/master/certbot/certbot/ssl-dhparams.pem" -OutFile "$data_path/conf/ssl-dhparams.pem"
        Write-Host "TLS parameters downloaded successfully." -ForegroundColor Green
    }
    catch {
        Write-Host "Error downloading TLS parameters: $_" -ForegroundColor Red
        exit 1
    }
}

# Create dummy certificate
Write-Host "### Creating dummy certificate for $($domains -join ', ') ..." -ForegroundColor Yellow
$path = "/etc/letsencrypt/live/$($domains[0])"
New-Item -ItemType Directory -Force -Path "$data_path/conf/live/$($domains[0])" | Out-Null

$dummyCertCmd = "openssl req -x509 -nodes -newkey rsa:$rsa_key_size -days 1 -keyout '$path/privkey.pem' -out '$path/fullchain.pem' -subj '/CN=localhost'"
docker-compose -f compose.prod.yaml run --rm --entrypoint $dummyCertCmd certbot

# Start nginx
Write-Host "### Starting nginx ..." -ForegroundColor Yellow
docker-compose -f compose.prod.yaml up --force-recreate -d nginx

# Delete dummy certificate
Write-Host "### Deleting dummy certificate for $($domains -join ', ') ..." -ForegroundColor Yellow
$deleteDummyCmd = "rm -Rf /etc/letsencrypt/live/$($domains[0]) && rm -Rf /etc/letsencrypt/archive/$($domains[0]) && rm -Rf /etc/letsencrypt/renewal/$($domains[0]).conf"
docker-compose -f compose.prod.yaml run --rm --entrypoint "/bin/sh -c `"$deleteDummyCmd`"" certbot

# Request Let's Encrypt certificate
Write-Host "### Requesting Let's Encrypt certificate for $($domains -join ', ') ..." -ForegroundColor Yellow

$domain_args = ""
foreach ($domain in $domains) {
    $domain_args += " -d $domain"
}

$email_arg = if ($email -eq "") { "--register-unsafely-without-email" } else { "--email $email" }
$staging_arg = if ($staging -ne 0) { "--staging" } else { "" }

$certbotCmd = "certbot certonly --webroot -w /var/www/certbot $staging_arg $email_arg $domain_args --rsa-key-size $rsa_key_size --agree-tos --force-renewal"
docker-compose -f compose.prod.yaml run --rm --entrypoint "/bin/sh -c `"$certbotCmd`"" certbot

# Reload nginx
Write-Host "### Reloading nginx ..." -ForegroundColor Yellow
docker-compose -f compose.prod.yaml exec nginx nginx -s reload

Write-Host "### SSL Certificate setup completed! ###" -ForegroundColor Green
Write-Host "Your application should now be accessible at: https://impazamon.powertel.co.zw" -ForegroundColor Cyan