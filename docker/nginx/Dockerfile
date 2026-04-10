FROM nginx:alpine AS production

# Copy your Nginx configuration
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

#  Copy the public folder (This includes your local 'build' folder)
COPY src/public /var/www/public

# Fix permissions for Nginx to read the files
RUN chmod -R 755 /var/www/public

WORKDIR /var/www