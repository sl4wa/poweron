import asyncio
import logging
import os
import sys
import time
from logging.handlers import TimedRotatingFileHandler

from telegram import Bot

from env import load_bot_token
from outages import OutageNotifier
from outages.outage_processor import OutageProcessor
from users import UserStorage

LOG_FILE = "notifier.log"

os.environ["TZ"] = "Europe/Kyiv"
time.tzset()

def configure_logging() -> None:
    file_handler = TimedRotatingFileHandler(
        LOG_FILE,
        when="midnight",
        interval=1,
        backupCount=5,
        encoding="utf-8",
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

async def main() -> None:
    logger = logging.getLogger("notifier")
    bot = Bot(token=load_bot_token())
    user_storage = UserStorage()
    outage_processor = OutageProcessor()

    outage_notifier = OutageNotifier(
        logger=logger,
        bot=bot,
        user_storage=user_storage,
        outage_processor=outage_processor,
    )
    await outage_notifier.notify()

if __name__ == "__main__":
    configure_logging()
    asyncio.run(main())
