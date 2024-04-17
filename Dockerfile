FROM httpd:2.4

# Copy the files from the host to the container
COPY . .

# Expose the port the app runs in
EXPOSE 80

# Serve the app
CMD ["httpd", "-D", "FOREGROUND"]