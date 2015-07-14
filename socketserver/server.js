var http = require('http');
var socketio = require('socket.io');
var qs = require('querystring');

var users = {}; //This will hold all of our registered clients


var app = http.createServer(function(req, res) {

    var postData = "";
    req.on('data', function(chunk) {
        postData += chunk; //Get the POST data
    });

    req.on('end', function() {

		if (postData !== "") {

			var message = JSON.parse(postData);

			//console.log(message);
            var uidreceiver = message._uid;
            uidreceiver = parseInt(uidreceiver);

            //if there is no specified uid receiver, Emit to ALL
            if (uidreceiver == 0) {
                for (var key in users) {
                    users[key].sock.emit(message._emit, message._data)
                }
            } else if(typeof(users[uidreceiver]) !== "undefined") {
                users[uidreceiver].sock.emit(message._emit, message._data);
            }
		}
    });
    res.end();
}).listen(8080);  //Use a non-standard port so it doesn't override your Apache


var io = socketio.listen(app); //Attach socket.io to port 8080

io.sockets.on('connection', function(socket) {

    //////////////////////////////////////////////
    //Called when new client connect to server
    socket.on('register', function(data) {
        users[data.uid] = {'uid': data.uid, 'sock': socket};
    });

    //////////////////////////////////////////////
    //Called when client disconnect (exit browser)
    socket.on('disconnect', function () {
        var deletesession = '';
        for (var key in users) {
            if (users[key].sock.id == socket.id) {
                deletesession = key;
            }
        }

        //found session id to delete
        if (deletesession.length > 0) {
            //remove from all session
            delete users[deletesession];
        }
    });

});



Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};



function strip_tags(input, allowed) {

  allowed = (((allowed || '') + '')
    .toLowerCase()
    .match(/<[a-z][a-z0-9]*>/g) || [])
    .join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return input.replace(commentsAndPhpTags, '')
    .replace(tags, function ($0, $1) {
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}