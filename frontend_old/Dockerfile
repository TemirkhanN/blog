FROM nginx:1.21 AS dev

RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash -

RUN apt-get install -yq nodejs && npm install -g npm@latest

WORKDIR /frontend

RUN mkdir /app && ln -s /frontend/build /app/public

FROM dev as builder

ARG BACKEND_URL=""
ARG BLOG_AUTHOR=""
ARG AUTHOR_GITHUB=""
ARG AUTHOR_LINKEDIN=""
ARG AUTHOR_CV=""

ENV REACT_APP_BACKEND_URL=$BACKEND_URL
ENV REACT_APP_AUTHOR_NAME=$BLOG_AUTHOR
ENV REACT_APP_GITHUB_LINK=$AUTHOR_GITHUB
ENV REACT_APP_LINKEDIN_LINK=$AUTHOR_LINKEDIN
ENV REACT_APP_CV_MARKDOWN_LINK=$AUTHOR_CV

COPY ./ ./
RUN npm install && npm run build --prod

FROM nginx:1.21 AS prod

WORKDIR /app/public

COPY ./docker/nginx.conf /etc/nginx/templates/default.conf.template
COPY ./docker/certbot-nginx.sh /app/
COPY --from=builder /frontend/build/ ./

EXPOSE 80
EXPOSE 443

CMD ["nginx", "-g", "daemon off;"]
