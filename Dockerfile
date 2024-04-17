FROM httpd:2.4

# Copy the files from the host to the container
RUN rm -rf /usr/local/apache2/htdocs/*

COPY /var/PA24-WebSite/* /usr/local/apache2/htdocs/

# Expose the port the app runs in
EXPOSE 80

# Serve the app
CMD ["httpd", "-D", "FOREGROUND"]