#!/bin/sh

if [ ! -f /etc/letsencrypt/live/temirkhan.nasukhov.me/fullchain.pem ]; then
  certbot --nginx -d $BLOG_DOMAIN_NAME -m $SSLCERT_OWNER_EMAIL --agree-tos --no-redirect --non-interactive
else
  certbot --nginx -d $BLOG_DOMAIN_NAME -m $SSLCERT_OWNER_EMAIL --reinstall
fi

cron
