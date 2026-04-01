FROM node:18-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run prod

FROM nginx:stable-alpine

ENV TZ=Africa/Harare
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Copy built public assets from assets-builder stage
RUN mkdir -p /var/www/html/public
COPY --from=assets-builder /app/public /var/www/html/public

# Create Laravel public storage symlink for serving /storage/* via public
RUN mkdir -p /var/www/html/storage/app/public \
    && ln -s /var/www/html/storage/app/public /var/www/html/public/storage || true

# Default command
CMD ["nginx", "-g", "daemon off;"]