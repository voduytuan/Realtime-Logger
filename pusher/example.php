<?php

    include('WebSocketDebugger.php');

    $msg = '';
    if (isset($_POST['fsubmit'])) {
        //Prepare data for push
        $data = $_POST['ftext'];

        $socketUrl = 'http://localhost:8080/';
        $dbg = new WebSocketDebugger($socketUrl);
        $dbg->push($data, 1, 'debug');

        $msg = '<p style="border:1px solid #eee; padding:5px; color:#08f">Your log had been pushed.</p>';
    }

    echo '<html>
    <head><title>Example Pusher with PHP</title></head>
    <body>
        <h1>Use this form to push log to Log Monitor</h1>
        '.$msg.'
        <form method="post" action="">
            Your text to log:
            <br />
            <textarea name="ftext" cols="100" rows="30">'.$_POST['ftext'].'</textarea>
            <br />
            <input type="submit" name="fsubmit" value="Push" />


        </form>
    </body>
    </html>';
