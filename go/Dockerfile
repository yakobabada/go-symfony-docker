FROM golang:latest

RUN go get github.com/gin-gonic/gin && \
	go get github.com/jackc/pgx && \
	go get github.com/tgirgin23/gin-gonic-docker

RUN mkdir /go/public

# Getting a simple example
ADD ./app /go/public

CMD go run /go/public/main.go

EXPOSE 8080
