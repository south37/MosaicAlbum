window.fbAsyncInit = function() {
  // init the FB JS SDK
  FB.init({
    appId      : '638792116141666',                           // App ID from the app dashboard
    channelUrl : '//mosaicalbum.me/', // Channel file for x-domain comms
    status     : true,                                        // Check Facebook Login status
    cookie     : true,
    xfbml      : true                                         // Look for social plugins on the page
  });

  // Additional initialization code such as adding Event Listeners goes here
//  FB.Event.subscribe('auth.authResponseChange', function(response) {
//    if (response.status === 'connected') {
//    } else if (response.status === 'not_authorized') {
//      appLogin();
//    } else {
//      appLogin();
//    }
//  });
};

// Load the SDK asynchronously
(function(d, s, id){
  var head = d.getElementsByTagName("head")[0] || d.documentElement;
  var js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js";
  head.appendChild(js);
}(document, 'script', 'facebook-jssdk'));

//function appLogin() {
//  FB.login(function(response) {
//  }, {scope: 'user_photos,friends_photos'});
//}

//function invite() {
//  FB.ui({
//    method:  'apprequests', 
//    message: '「MosaicAlbum」は友達とモザイクアルバムを作るfacebookアプリです。', 
//    title :  '「MosaicAlbum」に友達を誘おう'
//  }, function(response) {
//    for (var key in response) {
//      console.log(key);
//      console.log(response[key]);
//    }
//  });
//}
//
//function sendMessage() {
//  FB.ui({
//    method: 'send',
//    to:     '100003595468082',
//    name:   'minami',
//    link:   'http://yahoo.co.jp',
//  });
//}

// このdiv要素の後で読み込む
//    <div id="fb-root"></div>
    
//    <fb:login-button show-faces="true" width="200" max-rows="1"></fb:login-button> 
//    <a herf="javascript:;" onclick="invite()">友達を招待する</a>
