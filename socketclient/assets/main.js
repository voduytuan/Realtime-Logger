

var socket = null;

$.ajax({
    url: 'http://'+socketrooturl+'/socket.io/socket.io.js',
    dataType: 'script',
    cache: true, // otherwise will get fresh copy every page load
    success: function() {
        // script loaded, do stuff!
        socket = io.connect("ws://" + socketrooturl);

        //////////////////////////////////////
        // Receive new chat message from system
        socket.on('log_receive', function(data){
            logSocketReceive(data);
        })

        ///////////////////////////////////
        //Connect to Socket server
        socket.emit('register', {
            uid: $('#fuid').val()
        });
    }
});


function logSocketReceive(data)
{
    if(!$("#fpause").is(':checked')) {

        var show = true;
        //filter by keyword
        var keyword = $('#fkeyword').val();
        if (keyword != '') {
            if (data.detail.text.indexOf(keyword) == -1) {
                show = false;
            }
        }

        if (show) {
            var html = logParsing(data);
            $('#logs ul').prepend(html);
        }

    }

}

function logParsing(data)
{
    return '<li><span class="white">'+data.detail.time+'</span> \
        <span class="orange logtype-'+data.type.toLowerCase()+'">'+data.type+'</span> \
         <pre>' + data.detail.text + '</pre></li>';
}

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
