FROM nginx:1.25

# Install wait-for-it
RUN apt-get update && apt-get install -y wait-for-it

# Remove the default Nginx configuration
RUN rm /etc/nginx/conf.d/default.conf

# Copy our custom configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy and make the healthcheck script executable
COPY docker/healthcheck.sh /docker-healthcheck.sh
RUN chmod +x /docker-healthcheck.sh

# Add healthcheck
HEALTHCHECK --interval=5s --timeout=3s --retries=3 CMD curl -f http://localhost/ || exit 1