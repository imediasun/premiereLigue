FROM node:16.15.1-alpine

COPY ./frontend /app

WORKDIR /app

RUN yarn install && yarn cache clean

EXPOSE 8080 3000

WORKDIR /var/www/frontend

CMD ["yarn", "run", "start"]

