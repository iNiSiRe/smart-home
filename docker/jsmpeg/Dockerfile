FROM node:8

ENV DEBIAN_FRONTEND noninteractive

RUN apt update

RUN apt install -y git

RUN git clone https://github.com/phoboslab/jsmpeg.git && cd jsmpeg

RUN npm install http-server ws

WORKDIR jsmpeg

ENTRYPOINT node

CMD ["websocket-relay.js", "secret", "8081", "8082"]

EXPOSE 8081 8082