FROM nginx:alpine

ARG BACKEND_URL=http://localhost
ENV REACT_APP_BACKEND_URL=$BACKEND_URL

RUN apk add --no-cache \
    npm

COPY ./ /tmp/app-build-dir
RUN cd /tmp/app-build-dir \
        && npm install \
        && npm run build --prod

WORKDIR /app/public

RUN mv /tmp/app-build-dir/build/* ./ && rm -rf /tmp/app-build-dir

COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]