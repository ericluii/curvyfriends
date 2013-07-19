var permissions = 'read_friendlists,friends_status,read_stream';
var friendshipInfo;
var postUserToFriend;
var postFriendToUser;
var commentUserToFriend;
var commentFriendToUser;
var InteractionUserToFriend;
var InteractionFriendToUser;
var graphData = new Array();
var loadedFriends = false;

/*
 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.
 */

var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};

function sortByName(a, b) {
	var x = a.name.toLowerCase();
	var y = b.name.toLowerCase();
	return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

fbLogin = function() {
		window.fbAsyncInit = function() {
	  FB.init({
		appId      : '168751213286274', // App ID
		channelUrl : 'curvyfriends.com/channel.html', // Channel File
		status     : true, // check login status
		cookie     : true, // enable cookies to allow the server to access the session
		xfbml      : true  // parse XFBML
	  });

	  // Additional init code here
	  FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
		  login();
		  console.log("Logged in!");
		} else {
		  // not_logged_in
		  login();
		  console.log("Signed Off!");
		}
	  }, {scope: permissions});
	};

	function login() {
	  FB.login(function(response) {
		if (response.authResponse) {
			  location.href='/webapp/';
			} else {
			  alert('Facebook sign in and permission is required.');
			  location.href='/';
			}
		  });
	}

	// Load the SDK Asynchronously
	(function(d){
	 var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	 if (d.getElementById(id)) {return;}
	 js = d.createElement('script'); js.id = id; js.async = true;
	 js.src = "//connect.facebook.net/en_US/all.js";
	 ref.parentNode.insertBefore(js, ref);
   }(document));
}

fillDropDown = function() {
	  window.fbAsyncInit = function() {
	  FB.init({
		appId      : '168751213286274', // App ID
		channelUrl : 'curvyfriends.com/channel.html', // Channel File
		status     : true, // check login status
		cookie     : true, // enable cookies to allow the server to access the session
		xfbml      : true  // parse XFBML
	  });

	  // Additional init code here
	  FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
		  getFriends();
		} else {
		  location.href='/';
		}
	  }, {scope: permissions});
	};

	$('#submit').click(function(e){
		$('#report-loading').fadeIn();
		var uid = $("#e1").find(":selected").val();
		
		if (uid == '') {
			alert('This only works if you pick someone!');
			$('#report-loading').fadeOut();
			return;
		}
		else if (friendshipInfo && uid == friendshipInfo.friends.data[0].id) {
			alert('You picked the same guy! LOL');
			$('#report-loading').fadeOut();
			return;
		}
		else {
			friendshipInfo = undefined;
			postUserToFriend = undefined;
			postFriendToUser = undefined;
			commentUserToFriend = undefined;
			commentFriendToUser = undefined;
			InteractionUserToFriend = undefined;
			InteractionFriendToUser = undefined;
		}

		FB.api('/me?fields=friends.uid(' + uid + ').fields(first_name,name,picture.type(large)),first_name,name,picture.type(large)', function(response) {
			if (!response.error) {
			  friendshipInfo = response;
			}
			else {
			  alert('Error: Something went wrong while getting data on you and your friend.');
			  $('#report-loading').fadeOut();
			  return;
			}
		});

		FB.api({
		  method: 'fql.query',
		  query: 'SELECT actor_id, created_time FROM stream WHERE message != "" AND source_id = ' + uid + ' AND actor_id = me() AND created_time > 0 LIMIT 5000'
		  }, 
		  function(response) {
			if (!response.error) {
			  postUserToFriend = response;
			}
			else {
			  alert('Error: Something went wrong while gathering your posts.');
			  $('#report-loading').fadeOut();
			  return;
			}
		});

		FB.api({
		  method: 'fql.query',
		  query: 'SELECT actor_id, created_time FROM stream WHERE message != "" AND source_id = me() AND actor_id = ' + uid + ' LIMIT 5000'
		  }, 
		  function(response) {
			if (!response.error) {
			  postFriendToUser = response;
			}
			else {
			  alert('Error: Something went wrong while gathering your friend\'s posts.');
			  $('#report-loading').fadeOut();
			  return;
			}
		});

		FB.api({
		  method: 'fql.query',
		  query: 'SELECT actor_id, created_time, comments.count FROM stream WHERE source_id = me() AND comments.count > 0 AND actor_id = ' + uid + ' AND created_time > 0 LIMIT 5000'
		  }, 
		  function(response) {
			if (!response.error) {
			  commentFriendToUser = response;
			}
			else {
			  alert('Error: Something went wrong while gathering your friend\'s posts.');
			  $('#report-loading').fadeOut();
			  return;
			}
		});

		FB.api({
		  method: 'fql.query',
		  query: 'SELECT actor_id, created_time, comments.count FROM stream WHERE source_id = ' + uid + ' AND comments.count > 0 AND actor_id = me() AND created_time > 0 LIMIT 5000'
		  }, 
		  function(response) {
			if (!response.error) {
			  commentUserToFriend = response;
			}
			else {
			  alert('Error: Something went wrong while gathering your friend\'s posts.');
			  $('#report-loading').fadeOut();
			  return;
			}
		});

		infoLoaded(function() {
			console.log(postUserToFriend);
			console.log(postFriendToUser);
			displayFriendInfo();
			createGraph();
		   	$('#report-loading').fadeOut();
		});
	});

	function displayFriendInfo() {
		$('#picture_area').html('');
		$('#picture_area').append('<hr><div class="span2 offset1"><h3 style="float:right;">' + friendshipInfo.name + '</h3></div><div class=\"span2\"><a href=\'http://www.facebook.com/' + friendshipInfo.id + '\' class=\'thumbnail\' target=\'_blank\'><img src=\'' + friendshipInfo.picture.data.url + "'></img></a></div><div class='span2'><button onclick='window.open('http://www.facebook.com/" + friendshipInfo.id + "?and=" + friendshipInfo.friends.data[0].id + "')' class='btn btn-info'>View Friendship</button></div><div class=\"span2\"><a href='http://www.facebook.com/" + friendshipInfo.friends.data[0].id + "' class='thumbnail' target='_blank'><img src='" + friendshipInfo.friends.data[0].picture.data.url + "'></img></div></a><div class=\"span2\"><h3 style=\"float:left;\">" + friendshipInfo.friends.data[0].name + '</h3></div>');
		$('#picture_area').fadeIn();
	}

	function processPostData() {
		graphData[0] = new Array(); // Date
		graphData[1] = new Array(); // Posts from Me to Friend Count
		graphData[2] = new Array(); // Posts from Friend to Me Count
		graphData[3] = new Array(); // Total Post Count

		var currentDate = (new Date().getTime() / 1000) - 604800;
		var toMeCounter = 0;
		var fromMeCounter = 0;
		var i = 0;

		while (postUserToFriend.length != 0 || postFriendToUser.length != 0){
			if (postFriendToUser.length != 0 && (postFriendToUser[0].created_time - currentDate) >= 0){
					postFriendToUser.shift();
					toMeCounter++;
			}

			if (postUserToFriend.length != 0 && (postUserToFriend[0].created_time - currentDate) >= 0){
					postUserToFriend.shift();
					fromMeCounter++;
			}

			if ((postFriendToUser.length == 0 || (postFriendToUser[0].created_time - currentDate) < 0) && (postUserToFriend.length == 0 || (postUserToFriend[0].created_time - currentDate) < 0)){
					// Adding to week
				  var addDate = new Date(currentDate * 1000);
				  graphData[0][i] = dateFormat(addDate, "mmmm d, yyyy");
				  // Adding from me to Friend Count
				  graphData[1][i] = fromMeCounter + 1;
				  // Adding from Friend to Me Count
				  graphData[2][i] = toMeCounter + 1;
				  // Adding total count
				  graphData[3][i] = fromMeCounter + toMeCounter  + 1;
				  currentDate -= 604800;
				  toMeCounter = 0;
				  fromMeCounter = 0;
				  i++;
			}
		}
	}

	function processCommentData() {
		graphData[4] = new Array(); // Date
		graphData[5] = new Array(); // Posts from Me to Friend Count
		graphData[6] = new Array(); // Posts from Friend to Me Count
		graphData[7] = new Array(); // Total Post Count

		var currentDate = (new Date().getTime() / 1000) - 604800;
		var toMeCounter = 0;
		var fromMeCounter = 0;
		var i = 0;

		while (commentUserToFriend.length != 0 || commentFriendToUser.length != 0){
			if (commentFriendToUser.length != 0 && (commentFriendToUser[0].created_time - currentDate) >= 0){
					commentFriendToUser.shift();
					toMeCounter++;
			}

			if (commentUserToFriend.length != 0 && (commentUserToFriend[0].created_time - currentDate) >= 0){
					commentUserToFriend.shift();
					fromMeCounter++;
			}

			if ((commentFriendToUser.length == 0 || (commentFriendToUser[0].created_time - currentDate) < 0) && (commentUserToFriend.length == 0 || (commentUserToFriend[0].created_time - currentDate) < 0)){
					// Adding to week
				  var addDate = new Date(currentDate * 1000);
				  graphData[4][i] = dateFormat(addDate, "mmmm d, yyyy");
				  // Adding from me to Friend Count
				  graphData[5][i] = fromMeCounter + 1;
				  // Adding from Friend to Me Count
				  graphData[6][i] = toMeCounter + 1;
				  // Adding total count
				  graphData[7][i] = fromMeCounter + toMeCounter + 1;
				  currentDate -= 604800;
				  toMeCounter = 0;
				  fromMeCounter = 0;
				  i++;
			}
		}
	}

	function createGraph() {
		$.when(processPostData(),processCommentData()).done(function() {
			loadPosts();
			$("#chart_area").mousemove(function(e) {
				var shift = e.pageX - $('#tooltip').width();

				if (shift < 85) {
					shift = 85;
				}
				else if (shift > 1155) {
					shift = 1155;
				}

				$("#tooltip").animate({left:shift}, 50);
			});
		}).fail(function() {
			alert('Graph data could not be processed');
		});
	}

	function friendsLoaded(callback) {
        if(!loadedFriends) {
            setTimeout(function() {friendsLoaded(callback);}, 50);
        } else {
            if(callback) {
                callback();
            }
        }
	}

	function infoLoaded(callback) {
        if(!friendshipInfo || !postUserToFriend || !postFriendToUser || !commentFriendToUser || !commentUserToFriend) {
            setTimeout(function() {infoLoaded(callback);}, 50);
        } else {
            if(callback) {
                callback();
            }
        }
    }

	function getFriends() {
		FB.api('/me/friends', function(response) {
			if(response.data) {
				/*
				$.each(response.data,function(index,friend) {
					alert(friend.name + ' has id:' + friend.id);
				});*/
				var friend_data = response.data.sort(sortByName);
				//$.each(response.data,function(index,friend) {
				$.each(friend_data,function(index,friend) {  
					$("#e1").append(new Option(friend.name, friend.id));
				});

				loadedFriends = true;
			} else {
				alert("Error!");
			}
		});

		friendsLoaded(function() {
			$('#report-loading').fadeOut();
		});
	}

	// Load the SDK Asynchronously
	(function(d){
	 var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	 if (d.getElementById(id)) {return;}
	 js = d.createElement('script'); js.id = id; js.async = true;
	 js.src = "//connect.facebook.net/en_US/all.js";
	 ref.parentNode.insertBefore(js, ref);
   }(document));
}

function loadPosts() {
		var data = {
				labels : graphData[0],
				datasets : [
					{
						fillColor : "rgba(200,160,100,0.5)",
						strokeColor : "rgba(80,240,70,1)",
						pointColor : "rgba(80,240,70,1)",
						pointStrokeColor : "#fff",
						data : graphData[3]
					},
					{
						fillColor : "rgba(220,220,220,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						pointColor : "rgba(220,220,220,1)",
						pointStrokeColor : "#fff",
						data : graphData[1]
					},
					{
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,1)",
						pointColor : "rgba(151,187,205,1)",
						pointStrokeColor : "#fff",
						data : graphData[2]
					}
				]
			};
			
			var options = {
				scaleShowGridLines : true,
				scaleShowLabels : true,
				animationSteps : 150,
				scaleOverride: true,
				scaleSteps : Math.max.apply(Math, graphData[3]),
				scaleStepWidth : 1,
				scaleStartValue : 1
			};

			$('#chart_area').fadeIn();
			$('html, body').animate({
		         scrollTop: $("#picture_area").offset().top
		     }, 1000);
	}

	function loadComments(){
		var data = {
				labels : graphData[4],
				datasets : [
					{
						fillColor : "rgba(200,160,100,0.5)",
						strokeColor : "rgba(80,240,70,1)",
						pointColor : "rgba(80,240,70,1)",
						pointStrokeColor : "#fff",
						data : graphData[7]
					},
					{
						fillColor : "rgba(220,220,220,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						pointColor : "rgba(220,220,220,1)",
						pointStrokeColor : "#fff",
						data : graphData[5]
					},
					{
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,1)",
						pointColor : "rgba(151,187,205,1)",
						pointStrokeColor : "#fff",
						data : graphData[6]
					}
				]
			};
			
			var options = {
				scaleShowGridLines : true,
				scaleShowLabels : true,
				animationSteps : 150,
				scaleOverride: true,
				scaleSteps : Math.max.apply(Math, graphData[7]),
				scaleStepWidth : 1,
				scaleStartValue : 1
			};

            $('#chart_area').fadeIn();
			$('html, body').animate({
		         scrollTop: $("#picture_area").offset().top
		     }, 1000);
	}