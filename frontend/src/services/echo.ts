import Echo from "laravel-echo";
import Pusher from "pusher-js";
import apiClient from "./api";

(window as any).Pusher = Pusher;

const echo = new Echo({
  broadcaster: "pusher",
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: import.meta.env.VITE_PUSHER_SCHEME === "https",
  authorizer: (channel, options) => {
    return {
      authorize: (socketId, callback) => {
        apiClient
          .post("/api/broadcasting/auth", {
            socket_id: socketId,
            channel_name: channel.name,
          })
          .then((response) => {
            callback(null, response.data);
          })
          .catch((error) => {
            callback(error, null);
          });
      },
    };
  },
});

export default echo;
