# Realtime-Logger
Simple and Lightweight real-time log debugger via Websocket with Nodejs, SocketIO and Restful API to feed log (PHP, Python...)

![Log Monitor](http://bloghoctap.com/wp-content/uploads/2015/07/Screen-Shot-2015-07-14-at-11.03.09-AM.png "Sample Log screen in real-time mode")

## Introduction ##
Use this small library to create real-time log for your application. There are 3 parts in this system: 
* Socket Server: Run WebSocker server on port 8080, will transmit log from your code (Log Pusher) to connected Log Monitors
* Log Monitor: UI to show all logs of connected socket.
* Log Pusher: Your code, will request POST method to socket server to push log to Log monitor. This repositofy come with PHP example Pusher.

## Installation ##
Required: Nodejs, port 8080 available on server (you can change in server.js sourcecode and client javascript)
If not using localhost, you can change rooturl at socketserver/server.js, socketclient/index.html, pusher/WebSocketDebugger.php

Start socket server with this: `> node socketserver/server.js`

Open Log monitor in browser with URL: `http://localhost/realtimelogger/socketclient/index.html` (i assume that you put all directory in realtimelogger in www)

Test push with `pusher/example.php`

## Persistent ##
For the simplicity, database is not used here in this system. All data/packet send via web socket connections and there is no persistent storage in this system. If you refresh your Log Monitor page, all logs will be cleared.

## Security ##
There is no security layer here. Every Log Monitor will be assigned an User ID (default: 1), you can change in UI of Log Monitor. If Log Pusher do not specify user id in POST data, all connected Log monitors will be received this Log.

## Pushing with Restful ##
You can send your log to Log Monitor with Restful POST request. Data send in Json format and in Request Body. Request json format:
```javascript
{
  _uid: Integer,
  _emit: "log_receive",
  _data: {
    type: String (debug, error, warning, info...),
    detail: {
      time: String,
      text: String (Your log text)
    }
  }
}
```

### PHP Usage ###
This repository comes with php example. Just include file "WebSocketDebugger.php" and use WebSocketDebugger::push() static method to push to socket server. Example:

```php
...

include('WebSocketDebugger.php');

$data = array(
    'time' => date('H:i:s d/m/Y'),
    'text' => "My Log text here"
);

$userId = 123;
WebSocketDebugger::push($data, 'INFO', $userId);
```

## Enhancement & Customization ##
You can change the format of message in socketclient/assets and parsing more data as you want.
