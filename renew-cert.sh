#!/bin/bash

# Ensure we are in the correct directory
cd /var/www/html/impaza

# 1. Run the renewal check
# This will check if certificates are due for renewal and renew them if needed
docker compose -f compose.prod.yaml run --rm certbot renew

# 2. Reload Nginx
# This is CRITICAL. Even if the certificate is renewed on disk, Nginx won't use it
# until it reloads. This command forces Nginx to pick up the new files without downtime.
docker compose -f compose.prod.yaml exec nginx nginx -s reload

echo "Done! Certificate check complete and Nginx reloaded."
