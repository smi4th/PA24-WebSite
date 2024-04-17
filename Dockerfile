FROM httpd:2.4

# Expose the port the app runs in
EXPOSE 80

COPY /var/PA24-WebServer/ /usr/local/apache2/htdocs/

# Serve the app
CMD ["httpd", "-D", "FOREGROUND"]