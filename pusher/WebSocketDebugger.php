<?php

class WebSocketDebugger
{
    private $socketUrl = '';

    public function __construct($socketUrl = 'http://localhost:8080/')
    {
        $this->socketUrl = $socketUrl;
    }

    /**
     * Push socket message to socket server
     * @param $message
     * @param $type
     * @param $userid
     * @param $emit
     *
     * @return boolean
     */
    public function push($message, $userid = 0, $type = "debug", $emit = 'log_receive')
    {
        //compact message for sending more information
        $sendmessage['uid'] = $userid;
        $sendmessage['emit'] = $emit;
        $sendmessage['time'] = date('H:i:s d/m/Y');
        $sendmessage['type'] = $type;
        $sendmessage['detail'] = $message;

        $paramString = json_encode($this->utf8ize($sendmessage));


        $parts = parse_url($this->socketUrl);

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

    public function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }
}