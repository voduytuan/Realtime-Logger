<?php

/**
 * Utility Class de request (cURL) toi websocket server de trigger 1 action cho nguoi nao do trong cac socket connect
 */
class WebSocketDebugger
{
    const WEB_SOCKET_URL = 'http://localhost:8080/';

    /**
     * Push socket message to socket server
     * @param $message jsondata
     */
    public static function push($message, $type = "debug", $userid = 0, $emit = 'log_receive')
    {
        //compact message for sending more information
        $sendmessage['_uid'] = $userid;
        $sendmessage['_emit'] = $emit;
        $sendmessage['_data'] = array('type' => strtoupper($type), 'detail' => $message);

        $paramString = json_encode(self::utf8ize($sendmessage));


        $parts = parse_url(self::WEB_SOCKET_URL);

        if (PROTOCOL == 'https') {
            $fp = fsockopen(
                'ssl://' . $parts['host'],
                isset($parts['port']) ? $parts['port'] : 443,
                $errno,
                $errstr,
                30
            );
        } else {
            $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
        }

        if (!$fp) {
            return false;

        } else {
            $out = "POST " . $parts['path'] . "?" . $parts['query'] . " HTTP/1.1\r\n";
            $out .= "Host: " . $parts['host'] . "\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "Content-Length: " . strlen($paramString) . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            if ($paramString != '') {
                $out .= $paramString;
            }

            fwrite($fp, $out);
            fclose($fp);

            return true;
        }
    }

    public static function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = self::utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }
}


