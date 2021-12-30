const express = require('express');
const app = express();
const _ = require("underscore");
const http = require('https');
const fs = require('fs');
const options = {
  key: fs.readFileSync('./ssl/biyashadi.key'),
  cert: fs.readFileSync('./ssl/biyashadi.crt')
};

const server = http.createServer(options, app);
const {Server} = require("socket.io");
//const io = new Server(server);

const io = new Server(server, {
    allowEIO3: true,
    cors: {
       // origin: "http://localhost:8080",  //['*'] OR ['URL1', 'URL2'] https://nikah.arranzed.com/
         origin: "https://nikah.arranzed.com",  //['*'] OR ['URL1', 'URL2'] https://nikah.arranzed.com/
        methods: ["GET", "POST"],
        transports: ['websocket', 'polling'],
        credentials: true
    }
});

const users = {};

var online_users = [];

app.get('/', (req, res) => {
    res.sendFile(__dirname + '/index.html');
});

server.listen(4009, () => {
    console.log('listening on *:4009');
});

io.on('connection', (socket) => {
    console.log('a user connected');
    socket.on('ping', function (data) {
        //console.log(data)
        var userid = data.user_id;
        users[userid] = socket;
        online_users = Object.keys(users);
        io.emit('ping_success', {
            'success': true,
            'online_users': online_users
        });
    });
    socket.on('send_message', (data) => {
        var to = data.to;
        if(online_users.includes(to))
            users[to].emit('receive_message', data);
    });

    // tYPING
    socket.on('typing', (data) => {
        var to = data.to;
        if(online_users.includes(to))
            users[to].emit('lis_typing', data);
    });

    //Notification
    socket.on('notification', (data) => {
        var receiver = data.receivers;
        _.each(receiver, function(to, key) {
            if(online_users.includes(to)) {
                users[to].emit('receive_notification', data);
            }
        });
    });

    //Group Chat Start
    socket.on('send_message_in_group', (data) => {
        var receiver = data.receivers;
        console.log(data)
         console.log(receiver)
        _.each(receiver, function(to, key) {
             console.log(to)
            if(online_users.includes(to))
                users[to].emit('receive_message', data);
        });
    });

    // Private Chat Request Send
    socket.on('private_chat_request', (data) => {
        var to = data.to == '1' ? '2' : '1';
        var notification = {
            success:true,
            msg : 'You have received private chat request'
        };

        users[to].emit('private_chat_request_receive', notification);
    });

    // Accept / Recject Chat Request
    socket.on('accept_or_reject_chat_request', (data) => {
        var to = data.to == '1' ? '2' : '1';
        var status = data.accept_or_reject == '1' ? 'Accepted' : 'Rejected';
        var notification = {
            success:true,
            msg : status+' private chat request'
        };

        users[to].emit('accept_or_reject_chat_request_notf', notification);
    });

    //if user leave
    socket.on('disconnect', () => {
        console.log('user disconnected');
        for (user_id in users) {
            if (users[user_id] == socket) {
                delete(users[user_id]);
                online_users = Object.keys(users);
                io.emit('ping_success', {
                    'success': true,
                    'online_users': online_users
                });
                break;
            }
        }
    });
});
