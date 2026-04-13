FROM nginx:alpine AS production

# Copy your Nginx configuration
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

#  Copy the public folder (This includes your local 'build' folder)
COPY src/public /var/www/public

# Fix permissions for Nginx to read the files
RUN chmod -R 755 /var/www/public

WORKDIR /var/www


#
## Build Frontend Assets
#FROM node:20-alpine AS frontend-builder
#WORKDIR /app
#COPY src/package*.json ./
#RUN npm install
#COPY src/ ./
#RUN npm run build
#
## Production Nginx
#FROM nginx:alpine AS production
#
## Copy the Nginx config
#COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
#
## Copy the built assets from the 'frontend-builder' stage
#COPY --from=frontend-builder /app/public /var/www/public
#
##
#RUN chmod -R 755 /var/www/public
#WORKDIR /var/www