FROM httpd:2.4

COPY /var/PA24-WebSite/ /usr/local/apache2/htdocs/*

# Expose the port the app runs in
EXPOSE 80

# Serve the app
CMD ["httpd", "-D", "FOREGROUND"]