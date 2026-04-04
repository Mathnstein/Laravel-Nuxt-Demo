# Use a modern, slim Node image
FROM node:20-slim

# Set the working directory inside the container
WORKDIR /src

# Copy package files first (helps with caching)
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of your Nuxt code
COPY . .

# Expose the port Nuxt runs on
EXPOSE 3000

# Start the Nuxt dev server and bind to 0.0.0.0
# (Crucial: 0.0.0.0 tells the container to listen to external requests)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]