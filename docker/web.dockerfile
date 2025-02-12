FROM nginx:1.25

# Remove the default Nginx configuration
RUN rm /etc/nginx/conf.d/default.conf

# Copy our custom configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

