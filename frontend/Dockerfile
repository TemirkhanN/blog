FROM nginx:1.21 AS builder

ARG BACKEND_URL=http://localhost
ARG BLOG_DOMAIN=localhost
ENV REACT_APP_BACKEND_URL=$BACKEND_URL
ENV BLOG_DOMAIN_NAME=$BLOG_DOMAIN

RUN apt update -yq && apt install --no-install-recommends --no-install-suggests -y \
    npm

WORKDIR /app

COPY ./ ./

RUN npm install && npm run build --prod


FROM nginx:alpine AS release

WORKDIR /app/public

COPY ./docker/nginx.conf /etc/nginx/templates/default.conf.template
COPY --from=builder /app/build/ ./

EXPOSE 80
EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]
