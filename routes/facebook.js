var https = require('https');
var fql = require('fql');
/*
 * GET Facebook Data
 */

exports.facebookData = function(req, res){
	var requestUrl = "https://graph.facebook.com/593058366?fields=feed.fields(from)&access_token=" + req.params.access_token;
	var responseData = "";
	var jsonObject;
	var jsonArray = [];

	res.write('[');

	getMessageForURL(requestUrl);

	function getMessageForURL(url) {
		https.get(url, function(response) {
			console.log("Got response: " + response.statusCode);
			response.setEncoding('utf8');

			response.on('data', function(data) {
				responseData += data.toString();
			});

			response.on('error', function(e) {
				console.log("Got error: " + e.message);
				res.send(e.message);
			});

			response.on('end', function() {
				jsonObject = JSON.parse(responseData);
				res.write(responseData);
				responseData = "";

				if ("feed" in jsonObject) {
					jsonObject = jsonObject.feed;
				}

				if ("paging" in jsonObject && "next" in jsonObject.paging) {
					res.write(',');
					getMessageForURL(jsonObject.paging.next);
				} else {
					res.write(']');
					res.end();
				}

				return;
			});
		});
	}
};