## Poweron-Bot

### Dependencies

python, go, curl, jq (all packed into docker)

### Architecture

- `parser.sh` - gets all outages using curl and parses using jq
- `cmd/checker.go` - checks if there is a user subcribed to the outage
- `notifier.py` - sends notification via Telegram

`bot.py` - persistent bot manager (users are stored in directory `users/data`, format: ini file)

the main process is executed using pipes

    ./parser.sh | ./checker | ./notifier.py

Splitting into such "microservices" helped to simplify the codebase. Each of the scripts is responsible only for it's specific task. Networking and json parsing is reduced only to two commands (jq, curl). Not planning any huge growth of functionality so it's perfect for this size of project. Also, this type of segregation helped in debugging. Sometimes there is a need to send a custom message via Telegram for debugging. Just provide the formatted string to the input of the notitifier and all done.

### Docker

    docker-compose up --build
