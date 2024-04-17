FROM ubuntu 

RUN apt update
RUN apt install apache2 -y 

RUN apt install apache2-utils -y 

RUN apt clean

EXPOSE 80

CMD [“apache2ctl”, “-D”, “FOREGROUND”]