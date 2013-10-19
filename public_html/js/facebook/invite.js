window.fbAsyncInit = function() {
  // init the FB JS SDK
  FB.init({
    appId      : '638792116141666',                           // App ID from the app dashboard
    channelUrl : '//dev.mosaicalbum.com/master/start_master', // Channel file for x-domain comms
    status     : true,                                        // Check Facebook Login status
    cookie     : true,
    xfbml      : true                                         // Look for social plugins on the page
  });

// Load the SDK asynchronously
(function(d, s, id){
  var head = d.getElementsByTagName("head")[0] || d.documentElement;
  var js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js";
  head.appendChild(js);
}(document, 'script', 'facebook-jssdk'));

function invite() {
  FB.ui({
    method:  'apprequests', 
    message: '「MosaicAlbum」は友達とモザイクアルバムを作るfacebookアプリです。', 
    title :  '「MosaicAlbum」に友達を誘おう'
  }, function(response) {
    for (var key in response) {
      console.log(key);
      console.log(response[key]);
    }
  });
}
