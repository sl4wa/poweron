import asyncio
import logging
import subprocess
import sys
from logging.handlers import TimedRotatingFileHandler

from bot import load_bot_token
from outages import outage_reader
from users import user_storage

LOG_FILE = "notifier.log"

def configure_logging() -> None:
    file_handler = TimedRotatingFileHandler(
        LOG_FILE,
        when="midnight",
        interval=1,
        backupCount=5,
        encoding="utf-8",
        utc=True,
    )
    file_handler.suffix = "%Y-%m-%d.log"

    logging.basicConfig(
        level=logging.INFO,
        format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
        handlers=[file_handler, logging.StreamHandler(sys.stdout)],
    )

    httpx_logger = logging.getLogger("httpx")
    httpx_logger.setLevel(logging.WARNING)

    logging.info("Starting notification script.")

def main() -> None:
    configure_logging()

    outages = outage_reader.all()
    users = user_storage.all()

    for chat_id, user in users:
        outage = user.get_first_outage(outages)

        if outage:
            if user.is_notified(outage):
                logging.info(f"Outage already notified for user {chat_id} - {user.street_name}, {user.building}")
                continue

            result = subprocess.run(
                [
                    "java",
                    "-Djava.net.preferIPv6Addresses=true",  # Ensure IPv6 preference
                    "-jar",
                    "telegram-bot/target/uberjar/telegram-bot-0.1.0-SNAPSHOT-standalone.jar",
                    load_bot_token(),
                    str(chat_id),
                    outage.format_message(),
                ],
                capture_output=True,
                text=True
            )

            if result.returncode == 0:
                status_code = int(result.stdout.strip())
                if status_code == 200:
                    user.set_outage(outage)
                    user_storage.save(chat_id, user)
                    logging.info(f"Notification sent to {chat_id} - {user.street_name}, {user.building}")
                elif status_code == 403:
                    user_storage.remove(chat_id)
                    logging.info(f"Subscription removed for blocked user {chat_id}.")
                else:
                    logging.error(f"Failed to send message to {chat_id}: Status {status_code}")
            else:
                logging.error(f"Command failed with return code {result.returncode}: {result.stderr}")
        else:
            logging.info(f"No relevant outage found for user {chat_id} - {user.street_name}, {user.building}")

if __name__ == "__main__":
    main()
