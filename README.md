# Poweron-Bot

## Dependencies

python, go, curl, jq (all packed into docker)

## Data flow

1. get all outages using curl and parse using jq (`parser.sh`);
2.  push all outages to standard input of Go checker (`cmd/checker.go`);
3. push all outages that should be notified to Python notifier (`notifier.py`).

`bot.py` - persistent bot manager

## Architecture

users storage: `users/data`, format: ini file

for simlicity notifier is runned using pipes like

    ./parser.sh | ./checker | ./notifier.py

Splitting into microservices helped to simplify the codebase. We have three scripts each of one is responsible only for it's specific task. Network and json parsing is reduced only to two commands (jq, curl). Not planning any huge growth of functionality so it's perfect for this size of project.

Used Go for learning purposes. The main business logic is in `cmd/checker.go`. The domain is clean. It has no side effect. 

Also, this type of segregation helped in debugging. Sometimes there is a need to send a custom message via Telegram for debugging. Again, just provide the formatted string to the input of the notitifier and all done.

### Docker

    docker-compose up --build
