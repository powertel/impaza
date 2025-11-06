# Impaza Production Deployment Guide

## Overview
This guide covers deploying the Impaza application to production on server `154.73.80.26` with domain `impazamon.powertel.co.zw` using Docker and Let's Encrypt SSL.

## Prerequisites

1. **Server Setup**
   - Ubuntu/Debian server with Docker and Docker Compose installed
   - Domain `impazamon.powertel.co.zw` pointing to IP `154.73.80.26`
   - Ports 80 and 443 open in firewall

2. **DNS Configuration**
   - Ensure your domain `impazamon.powertel.co.zw` has an A record pointing to `154.73.80.26`
   - Verify DNS propagation: `nslookup impazamon.powertel.co.zw`

## Deployment Steps

### 1. Prepare Environment File
```bash
# Copy the production environment template
cp .env.prod.example .env.prod

# Edit the .env.prod file and set your actual values:
# - APP_KEY (generate with: php artisan key:generate --show)
# - Database passwords
# - Mail configuration
# - API keys
```

### 2. Build and Start Services (Without SSL)
```bash
# First, start without SSL to get Let's Encrypt certificate
docker-compose -f compose.prod.yaml up -d app mysql redis

# Wait for services to be ready
docker-compose -f compose.prod.yaml logs -f app
```

### 3. Initialize SSL Certificate
```bash
# Make the script executable (Linux/Mac)
chmod +x init-letsencrypt.sh
./init-letsencrypt.sh

# Or use PowerShell on Windows
PowerShell -ExecutionPolicy Bypass -File init-letsencrypt.ps1
```

### 4. Start All Services with SSL
```bash
# Start all services including nginx with SSL
docker-compose -f compose.prod.yaml up -d

# Check all services are running
docker-compose -f compose.prod.yaml ps
```

### 5. Run Laravel Setup Commands
```bash
# Generate application key if not set
docker-compose -f compose.prod.yaml exec app php artisan key:generate

# Run database migrations
docker-compose -f compose.prod.yaml exec app php artisan migrate --force

# Seed database (if needed)
docker-compose -f compose.prod.yaml exec app php artisan db:seed --force

# Clear and cache configuration
docker-compose -f compose.prod.yaml exec app php artisan config:cache
docker-compose -f compose.prod.yaml exec app php artisan route:cache
docker-compose -f compose.prod.yaml exec app php artisan view:cache

# Set proper permissions
docker-compose -f compose.prod.yaml exec app chown -R www-data:www-data storage bootstrap/cache
```

## SSL Certificate Management

### Automatic Renewal
The Certbot container is configured to automatically renew certificates every 12 hours.

### Manual Renewal
```bash
# Force certificate renewal
docker-compose -f compose.prod.yaml run --rm certbot renew --force-renewal

# Reload nginx after renewal
docker-compose -f compose.prod.yaml exec nginx nginx -s reload
```

## Security Considerations

### 1. Environment Variables
- Never commit `.env.prod` to version control
- Use strong, unique passwords for database and other services
- Regularly rotate API keys and passwords

### 2. Firewall Configuration
```bash
# Allow only necessary ports
ufw allow 22    # SSH
ufw allow 80    # HTTP (for Let's Encrypt)
ufw allow 443   # HTTPS
ufw enable
```

### 3. Database Security
- Change default MySQL root password
- Use strong passwords for application database user
- Consider restricting database access to application container only

### 4. Regular Updates
```bash
# Update Docker images regularly
docker-compose -f compose.prod.yaml pull
docker-compose -f compose.prod.yaml up -d

# Update application code
git pull origin main
docker-compose -f compose.prod.yaml build app
docker-compose -f compose.prod.yaml up -d app
```

## Monitoring and Logs

### View Logs
```bash
# All services
docker-compose -f compose.prod.yaml logs -f

# Specific service
docker-compose -f compose.prod.yaml logs -f app
docker-compose -f compose.prod.yaml logs -f nginx
docker-compose -f compose.prod.yaml logs -f mysql
```

### Health Checks
```bash
# Check service status
docker-compose -f compose.prod.yaml ps

# Test application
curl -I https://impazamon.powertel.co.zw

# Check SSL certificate
openssl s_client -connect impazamon.powertel.co.zw:443 -servername impazamon.powertel.co.zw
```

## Backup Strategy

### Database Backup
```bash
# Create backup
docker-compose -f compose.prod.yaml exec mysql mysqldump -u root -p impaza > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore backup
docker-compose -f compose.prod.yaml exec -T mysql mysql -u root -p impaza < backup_file.sql
```

### Automated Remote Backups (192.168.15.64)

The `dbbackup` service performs scheduled nightly backups and uploads them to a remote machine.

1. Prerequisites on remote (192.168.15.64)
   - Create a user for backups: `sudo adduser backup`
   - Create destination folder: `sudo mkdir -p /backups/impaza && sudo chown backup:backup /backups/impaza`

2. SSH key setup on production host
   - Create secrets directory: `mkdir -p secrets/backup_ssh`
   - Generate an SSH key (no passphrase): `ssh-keygen -t ed25519 -f secrets/backup_ssh/id_rsa`
   - Copy public key to remote: `ssh-copy-id -i secrets/backup_ssh/id_rsa.pub backup@192.168.15.64`
   - Optional: add remote host to known_hosts: `ssh-keyscan 192.168.15.64 >> secrets/backup_ssh/known_hosts`

3. Start backup service
```bash
docker-compose -f compose.prod.yaml up -d dbbackup
docker-compose -f compose.prod.yaml logs -f dbbackup
```

4. On-demand test backup
```bash
docker-compose -f compose.prod.yaml exec dbbackup /opt/backup/backup-db.sh
```

5. Configuration
- Schedule: `BACKUP_SCHEDULE` env in `compose.prod.yaml` (default `0 2 * * *`)
- Remote target: `REMOTE_USER`, `REMOTE_HOST`, `REMOTE_PATH` envs in `compose.prod.yaml`
- MySQL connection: `MYSQL_*` envs (host `mysql` within the Compose network)

Backups are compressed (`.sql.gz`) and copied to `/backups/impaza` on the remote machine.

### Application Files Backup
```bash
# Backup storage directory
tar -czf storage_backup_$(date +%Y%m%d_%H%M%S).tar.gz storage/
```

## Troubleshooting

### Common Issues

1. **SSL Certificate Issues**
   ```bash
   # Check certificate status
   docker-compose -f compose.prod.yaml logs certbot
   
   # Recreate certificate
   ./init-letsencrypt.sh
   ```

2. **Application Not Loading**
   ```bash
   # Check application logs
   docker-compose -f compose.prod.yaml logs app
   
   # Verify environment configuration
   docker-compose -f compose.prod.yaml exec app php artisan config:show
   ```

3. **Database Connection Issues**
   ```bash
   # Check MySQL logs
   docker-compose -f compose.prod.yaml logs mysql
   
   # Test database connection
   docker-compose -f compose.prod.yaml exec app php artisan tinker
   # Then run: DB::connection()->getPdo();
   ```

## Performance Optimization

### 1. Enable OPcache
OPcache is already enabled in the production Dockerfile for better PHP performance.

### 2. Redis Caching
Redis is configured for session and cache storage for improved performance.

### 3. Nginx Optimization
The nginx configuration includes gzip compression and proper caching headers.

## Support

For issues or questions:
- Check application logs: `docker-compose -f compose.prod.yaml logs app`
- Review nginx logs: `docker-compose -f compose.prod.yaml logs nginx`
- Monitor system resources: `docker stats`

## Access URLs

- **Production Site**: https://impazamon.powertel.co.zw
- **IP Access**: https://154.73.80.26 (will redirect to domain)
- **HTTP**: Automatically redirects to HTTPS