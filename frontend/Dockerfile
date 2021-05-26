FROM nginx:alpine

ARG BACKEND_URL=http://localhost
ARG BLOG_DOMAIN=localhost
ENV REACT_APP_BACKEND_URL=$BACKEND_URL
ENV BLOG_DOMAIN_NAME=$BLOG_DOMAIN

RUN apk add --no-cache \
    npm

COPY ./ /tmp/app-build-dir
RUN cd /tmp/app-build-dir \
        && npm install \
        && npm run build --prod

WORKDIR /app/public

RUN mv /tmp/app-build-dir/build/* ./ && rm -rf /tmp/app-build-dir

COPY ./docker/nginx.conf /etc/nginx/templates/default.conf.template

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]