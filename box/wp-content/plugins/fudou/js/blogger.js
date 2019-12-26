function twitterCallback2(twitters) {
	var statusHTML = [];
	for (var i=0; i<twitters.length; i++){
		var username = twitters[i].user.screen_name;
		var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
			return '<a href="'+url+'">'+url+'</a>';
		}).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
			return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
		});
		statusHTML.push('<li><a class="tw_date" href="http://twitter.com/'+username+'/statuses/'+twitters[i].id_str+'" target="_blank">'+relative_time(twitters[i].created_at)+'</a> <span class="tw_status">'+status+'</span></li>');
	}
	document.getElementById('twitter_update_list').innerHTML = statusHTML.join('');
}


function relative_time(time_value) {
  var values = time_value.split(" ");
  time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
  var parsed_date = Date.parse(time_value);


  var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
  var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
  delta = delta + (relative_to.getTimezoneOffset() * 60);

  var dt = new Date();
  dt.setTime(dt.getTime() - (delta*1000));
  yy = dt.getYear();
  mm = dt.getMonth() + 1;
  dd = dt.getDate();
  dy = dt.getDay();
  hh = dt.getHours();
  mi = dt.getMinutes();
  ss = dt.getSeconds();
  if (yy < 2000) { yy += 1900; }
  if (mm < 10) { mm = "0" + mm; }
  if (dd < 10) { dd = "0" + dd; }
  dy = new Array("日","月","火","水","木","金","土")[dy];
  if (hh < 10) { hh = "0" + hh; }
  if (mi < 10) { mi = "0" + mi; }
  //if (ss < 10) { ss = "0" + ss; }

//  return yy+"/"+mm+"/"+dd+"("+dy+") "+hh+":"+mi;

  return yy+"/"+mm+"/"+dd;


}