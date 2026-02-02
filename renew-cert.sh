#!/bin/bash

# 1. Trigger the renewal check
echo "Checking for certificate renewal..."
docker compose -f compose.prod.yaml run --rm certbot renew

# 2. Reload Nginx to apply changes (zero downtime)
echo "Reloading Nginx..."
docker compose -f compose.prod.yaml exec nginx nginx -s reload

echo "Done! If certificates were renewed, Nginx is now using them."
