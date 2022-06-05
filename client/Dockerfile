FROM node:lts-bullseye

# Set work directory
WORKDIR /app

# Copy project to 
COPY ./  /app

# Install NPM packages
RUN npm install

EXPOSE 3000

CMD ["npm", "run", "start"]