FROM nginx:1.21

ARG BACKEND_URL=http://localhost
ARG BLOG_DOMAIN=localhost
ENV REACT_APP_BACKEND_URL=$BACKEND_URL
ENV BLOG_DOMAIN_NAME=$BLOG_DOMAIN

RUN apt update -yq && apt install -y npm

COPY ./ /tmp/app-build-dir
RUN cd /tmp/app-build-dir \
        && npm install \
        && npm run build --prod

WORKDIR /app/public

RUN mv /tmp/app-build-dir/build/* ./ && rm -rf /tmp/app-build-dir

COPY ./docker/nginx.conf /etc/nginx/templates/default.conf.template

EXPOSE 80
EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]
