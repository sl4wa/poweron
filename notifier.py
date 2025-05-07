#!/usr/bin/env python3

import asyncio
import logging
import sys
from dataclasses import dataclass
from datetime import datetime
from typing import Optional, Tuple

from env import load_bot_token
from telegram import Bot
from telegram.constants import ParseMode
from telegram.error import TelegramError, Forbidden
from users import UserStorage

@dataclass
class Outage:
    start_date: str
    end_date: str
    city: str
    street_id: Optional[int]
    street: str
    building: str
    comment: str

    @staticmethod
    def _fmt(iso: str) -> str:
        try:
            return datetime.fromisoformat(iso).strftime("%Y-%m-%d %H:%M")
        except ValueError:
            return iso

    def format_message(self) -> str:
        return (
            "Поточні відключення:\n"
            f"Місто: {self.city}\n"
            f"Вулиця: {self.street}\n"
            f"<b>{self._fmt(self.start_date)} – {self._fmt(self.end_date)}</b>\n"
            f"Коментар: {self.comment}\n"
            f"Будинки: {self.building}"
        )

def parse_row(raw: str) -> Tuple[int, Outage]:
    parts = raw.rstrip("\n").split("\t")
    if len(parts) != 7:
        raise ValueError(f"Expected 7 columns, got {len(parts)}")
    chat_id_s, start, end, city, street, building, comment = parts
    return int(chat_id_s), Outage(start, end, city, None, street, building, comment)

async def main() -> None:
    logging.basicConfig(level=logging.INFO)
    logger = logging.getLogger("notifier")

    bot = Bot(load_bot_token())
    user_storage = UserStorage()

    for lineno, line in enumerate(sys.stdin, 1):
        if not line.strip():
            continue

        try:
            chat_id, outage = parse_row(line)
        except ValueError as exc:
            logger.warning(f"line {lineno}: {exc}")
            continue

        try:
            await bot.send_message(
                chat_id=chat_id,
                text=outage.format_message(),
                parse_mode=ParseMode.HTML,
            )
            user_storage.update_outage(
                chat_id,
                outage.start_date,
                outage.end_date,
                outage.comment,
            )
            logger.info(f"Notification sent to {chat_id}")
        except Forbidden:
            user_storage.remove(chat_id)
            logger.info(f"Subscription removed for blocked user {chat_id}.")
        except TelegramError as err:
            logger.error(f"Failed to send message to {chat_id}: {err}")

if __name__ == "__main__":
    asyncio.run(main())
