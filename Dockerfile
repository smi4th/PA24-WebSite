FROM httpd:2.4

# Expose the port the app runs in
EXPOSE 80

# Serve the app
CMD ["httpd", "-D", "FOREGROUND"]