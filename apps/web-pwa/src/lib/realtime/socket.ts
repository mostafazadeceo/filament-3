export type RealtimeEvent = {
  type: string;
  payload: Record<string, unknown>;
};

export type RealtimeOptions = {
  url: string;
  onEvent: (event: RealtimeEvent) => void;
  onFallback?: () => void;
};

export function connectRealtime({ url, onEvent, onFallback }: RealtimeOptions) {
  let socket: WebSocket | null = null;
  let retries = 0;

  const connect = () => {
    socket = new WebSocket(url);

    socket.onmessage = (message) => {
      try {
        const data = JSON.parse(message.data) as RealtimeEvent;
        onEvent(data);
      } catch {
        // Ignore malformed events.
      }
    };

    socket.onclose = () => {
      retries += 1;
      if (retries >= 3 && onFallback) {
        onFallback();
      }
      setTimeout(connect, Math.min(30_000, 1000 * retries));
    };
  };

  connect();

  return () => {
    socket?.close();
  };
}
