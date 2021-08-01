FROM nginx:1.21

ARG BACKEND_URL=http://localhost
ARG BLOG_DOMAIN=localhost
ENV REACT_APP_BACKEND_URL=$BACKEND_URL
ENV BLOG_DOMAIN_NAME=$BLOG_DOMAIN

COPY ./docker/nginx.conf /etc/nginx/templates/default.conf.template
COPY ./ /tmp/app-build-dir

WORKDIR /app/public

RUN set -x && apt update -yq \
    && apt install --no-install-recommends --no-install-suggests -y npm \
    && cd /tmp/app-build-dir \
        && npm install \
        && npm run build \
        && mv /tmp/app-build-dir/build/* /app/public/ \
        && rm -rf /tmp/app-build-dir \
    && apt remove -y npm \
    && apt remove --purge --auto-remove -y \
    && rm -rf /var/lib/apt/lists/*

EXPOSE 80
EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]
