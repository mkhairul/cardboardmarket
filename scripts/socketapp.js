var io = require('socket.io').listen(3000);
var post = io
  .of('/post')
  .on('connection', function (socket) {
    console.log('connected');
    //socket.broadcast.emit('user connected');
      
    post.on('updates', function(data){
        console.log(data);
        socket.emit('updates', {message: 'received'});
    });
  });

var news = io
  .of('/news')
  .on('connection', function (socket) {
    socket.emit('item', { news: 'item' });
  });