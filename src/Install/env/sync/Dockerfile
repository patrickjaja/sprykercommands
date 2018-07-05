FROM alpine
MAINTAINER Mike Bertram <bertram@nexus-netsoft.com>

RUN apk update \
 && apk add --no-cache \
    bash \
    unison \
    rsync

COPY entrypoint.sh /
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["-version"]