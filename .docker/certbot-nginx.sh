#!/bin/sh

apt update -yq && apt install -y python3 python3-venv libaugeas0 cron \
&& python3 -m venv /opt/certbot/ \
&& /opt/certbot/bin/pip install --upgrade pip \
&& /opt/certbot/bin/pip install certbot certbot-nginx \
&& ln -s /opt/certbot/bin/certbot /usr/bin/certbot \
&& certbot --nginx \
&& echo "0 0 1 * * root certbot renew -q" | tee -a /etc/crontab > /dev/null
