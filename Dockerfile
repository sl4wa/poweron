# go build

FROM golang:1.24.2 AS builder

WORKDIR /app

COPY go.mod ./

RUN go mod tidy

COPY cmd/checker.go cmd/checker.go
RUN go build -o checker cmd/checker.go

# python build

FROM python:3.11-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
        cron \
        tzdata \
        jq \
        curl \
        bash \
    && rm -rf /var/lib/apt/lists/*

ENV TZ=Europe/Kyiv

WORKDIR /app

COPY --from=builder /app/checker /usr/local/bin/checker

COPY . /app

RUN pip install --upgrade pip \
 && pip install -r requirements.txt

RUN chmod +x /app/parser.sh

RUN printf '*/10 * * * * cd /app && ./parser.sh | /usr/local/bin/checker | /usr/local/bin/python /app/notifier.py\n' \
    | crontab -

CMD cron && python /app/bot.py