# Stage 1: PHP Dependencies (To provide Ziggy files to Vite)
FROM composer:2 AS vendor-builder
WORKDIR /app
# Adjust "src/" if your composer files are inside that folder
COPY src/composer.json src/composer.lock ./
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Stage 2: Build Frontend Assets
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Copy package files and install
COPY src/package*.json ./
RUN npm install

# Copy the rest of the source code
COPY src/ ./

# CRITICAL: Copy the vendor folder from the PHP stage so Vite finds Ziggy
COPY --from=vendor-builder /app/vendor ./vendor

# Now run the build
RUN npm run build

# Stage 3: Production Nginx
FROM nginx:alpine AS production

# Copy the Nginx configs
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy the ENTIRE public folder (including the newly created 'build' folder)
COPY --from=frontend-builder /app/public /var/www/public

RUN chmod -R 755 /var/www/public
WORKDIR /var/www






#Nginx
#FROM nginx:alpine AS production
#
## Copy your Nginx configuration
#COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
#
##  Copy the public folder (This includes your local 'build' folder)
#COPY src/public /var/www/public
#
## Fix permissions for Nginx to read the files
#RUN chmod -R 755 /var/www/public
#
#WORKDIR /var/www



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
#RUN chmod -R 755 /var/www/public
#WORKDIR /var/www