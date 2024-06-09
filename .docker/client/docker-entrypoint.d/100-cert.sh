#!/bin/sh

if [ ! -f /etc/letsencrypt/live/temirkhan.nasukhov.me/fullchain.pem ]; then
  certbot --nginx -d $BLOG_DOMAIN_NAME --no-redirect --non-interactive -m $SSLCERT_OWNER_EMAIL
fi

cron
