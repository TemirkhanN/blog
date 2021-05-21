FROM nginx:alpine

RUN apk add --no-cache \
    npm

COPY ./ /tmp/app-build-dir
RUN cd /tmp/app-build-dir \
        && npm install \
        && npm run build --prod

WORKDIR /app/public

RUN mv /tmp/app-build-dir/build/* ./ && rm -rf /tmp/app-build-dir

COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

CMD ["nginx", "-g", "daemon off;"]