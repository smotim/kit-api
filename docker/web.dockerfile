FROM nginx:1.25

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf