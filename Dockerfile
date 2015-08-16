FROM ety001/min_swoole
MAINTAINER ety001 <ety001@domyself.me>
RUN mkdir /web
ADD src/ /web
EXPOSE 80
ENTRYPOINT ["/usr/local/bin/php"]
CMD ["/web/serv.php"]
