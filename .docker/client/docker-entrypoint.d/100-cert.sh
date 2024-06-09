#!/bin/sh

# There has to be more adequate way to resolve this. Currently installing certificate before running nginx issues conflict
# on binding port.
run_with_delay() {
    local delay=$1
    shift
    local command="$@"

    # Delay execution
    sleep "$delay" && $command &
}

CHAIN_FILE=/etc/letsencrypt/live/$BLOG_DOMAIN_NAME/fullchain.pem

if [ ! -f $CHAIN_FILE ]; then
  run_with_delay 5 certbot --nginx -d $BLOG_DOMAIN_NAME -m $SSLCERT_OWNER_EMAIL --agree-tos &
else
  run_with_delay 5 certbot --nginx -d $BLOG_DOMAIN_NAME -m $SSLCERT_OWNER_EMAIL --reinstall &
fi

cron
