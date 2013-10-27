function showAlbums() {
    window.showModalDialog(
        '/select/modal_album',
        this,  //ダイアログに渡すパラメータ（この例では、自分自身のwindowオブジェクト）
        "dialogWidth=800px; dialogHeight=480px;"
    );
}

window.fbAsyncInit = function() {
  // init the FB JS SDK
  FB.init({
    appId      : '638792116141666',                           // App ID from the app dashboard
    channelUrl : '//mosaicalbum.me/', // Channel file for x-domain comms
    status     : true,                                        // Check Facebook Login status
    cookie     : true,
    xfbml      : true                                         // Look for social plugins on the page
  });
}

// Load the SDK asynchronously
(function(d, s, id){
  var head = d.getElementsByTagName("head")[0] || d.documentElement;
  var js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js";
  head.appendChild(js);
}(document, 'script', 'facebook-jssdk'));

function sendMessage() {
  var input       = document.getElementById('goalImageId');
  var fbGoalImageId = input.value;
  
  url = 'http://mosaicalbum.me/guest/' + fbGoalImageId;
  FB.ui({
    method: 'send',
    link:   'http://mosaicalbum.me/',
  });
}
