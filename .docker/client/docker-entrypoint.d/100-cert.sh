#!/bin/sh

CHAIN_FILE=/etc/letsencrypt/live/$BLOG_DOMAIN_NAME/fullchain.pem

if [ ! -f $CHAIN_FILE ]; then
  certbot --nginx -d $BLOG_DOMAIN_NAME -m $SSLCERT_OWNER_EMAIL --agree-tos --no-redirect --non-interactive
else
  certbot --nginx -d $BLOG_DOMAIN_NAME -m $SSLCERT_OWNER_EMAIL --reinstall
fi

cron
