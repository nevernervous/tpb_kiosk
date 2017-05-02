var express = require('express');
var http = require('http');
var app = express();
var events = require('events');

var server = http.Server(app);

var io = require('socket.io').listen(server);

var eventEmitter = new events.EventEmitter();

var watch1 = 0;
var watch2 = 0;
var flag = false;

io.sockets.on('connection', function (socket) {
    console.log('socket connected');
    eventEmitter.on('tag_put', function (data) {
        socket.emit('tag_put', data)
    });

    eventEmitter.on('tag_remove', function (data) {
        console.log('tag removed');
        socket.emit('tag_remove')
    })
});

server.listen(3000, function () {
    console.log('listening on 3000.');
});


var serialPort = require('serialport');
var serial = new serialPort("/dev/ttyUSB0", {
    baudrate: 2400,
    parser: serialPort.parsers.readline("\r\n")
});



serial.on("open", function () {
    console.log('RFID reader connected.');
    serial.on('data', function(data)
    {
        flag = true;
        console.log('data', data);
        watch1++;
        eventEmitter.emit('tag_put', data);
    });

    serial.on('error', function(err) {
        console.log('Error: ', err.message);
    });

});

setInterval(function () {
    if(flag) {
        if(watch1 != watch2) {
            watch2 = watch1;
        } else {
            eventEmitter.emit('tag_remove');
            flag = false;
        }
    }
}, 1000);
