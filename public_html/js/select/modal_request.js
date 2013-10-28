function sendMessage() {
  var parentDocument = window.opener.document;
  var input          = parentDocument.getElementById('goalImageId');
  var goalImageId  = input.value;
  
  url = 'http://mosaicalbum.me/guest/' + goalImageId;
  alert(url);
  FB.ui({
    method: 'send',
    link:   url,
  }, function () {
    window.close()
  });
}
