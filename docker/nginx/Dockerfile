FROM nginx:1.12
RUN apt-get update; \
	apt-get install -y vim git zip curl sudo; \
	rm /etc/nginx/conf.d/default.conf ;\
	echo "upstream php-upstream { server civils-app:9000; }" > /etc/nginx/conf.d/upstream.conf

ADD nginx.conf /etc/nginx/
ADD civils-app.conf /etc/nginx/sites-enabled/

CMD ["nginx"]

EXPOSE 80