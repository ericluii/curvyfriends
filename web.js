var express = require("express");
var app = express();

// Routing
app.get('/', function(request, response) {
	response.sendfile(__dirname + '/index.html');
});
app.get('/test1', function(request, response) {
	response.send('test page one');
});
app.get('/test2', function(request, response) {
	response.send('test page two');
});

var port = process.env.PORT || 5000;
app.listen(port, function() {
	console.log("Listening on " + port);
});