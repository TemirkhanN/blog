FROM nginx:alpine

RUN apk add --no-cache \
    npm

COPY ./ /tmp/app-build-dir
WORKDIR /tmp/app-build-dir
RUN npm install && npm run build --prod
COPY ./build /app/public
RUN rm -rf /tmp/app-build-dir

WORKDIR /app/public

CMD ["nginx", "-g", "daemon off;"]