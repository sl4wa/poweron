(ns telegram-bot.core
  (:gen-class)
  (:require [clj-http.client :as http]
            [cheshire.core :as json]))

(defn send-telegram-message [token chat-id message]
  (let [url (str "https://api.telegram.org/bot" token "/sendMessage")
        params {:form-params {:chat_id chat-id
                              :text    message
                              :parse_mode "HTML"}
                :throw-exceptions false}
        response (http/post url params)]
    (case (:status response) 
      200 200
      403 403
      500 500
      (:status response))))

(defn -main [& args]
  (let [[token chat-id message] args]
    (if (or (nil? token) (nil? chat-id) (nil? message))
      (do
        (println "Usage: <token> <chat_id> <message_in_html_format>")
        (System/exit 1))
      (let [status (send-telegram-message token chat-id message)]
        (println status)
        (System/exit 0)))))
