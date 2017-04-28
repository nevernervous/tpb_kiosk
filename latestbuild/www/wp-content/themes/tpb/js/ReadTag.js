var serialport = require("node_modules/serialport");
var SerialPort = serialport.SerialPort;

var TagValue;

// Here make sure you have proper path to the Serail Port USB RFID Reader we have was ttyUSB0
var serialPort = new SerialPort("/dev/ttyUSB0", {
  baudrate: 2400,
  parser: serialport.parsers.readline("\r\n")
});


// This Function call is going to Close the COM Port.
function vClosecom()
{
	serialPort.close(function (err) {
    console.log('port closed', err);
	});
}

serialPort.on("open", function () {
	console.log('open');
	serialPort.on('data', function(data) // This is going to receive the data
	{
		console.log(data);
		TagValue = data; // Compare the received data with stored known value and determine the tags

		if (OBJECTS.hasOwnProperty(TagValue)){
			var event = new CustomEvent('object_recognized', {detail: {
				'pattern': TagValue,
				'object_url': OBJECTS[TagValue]
			}});

			document.dispatchEvent(event)
		} else {
			var event = new CustomEvent('no_object_recognized', {detail: {
				'pattern': TagValue,
				'object_url': null
			}});

			document.dispatchEvent(event)
		}
	});
});