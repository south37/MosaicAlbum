function sendMessage() {
  var parentDocument = window.opener.document;
  var input          = parentDocument.getElementById('goalImageId');
  var fbGoalImageId  = input.value;
  
  url = 'http://mosaicalbum.me/guest/' + fbGoalImageId;
  FB.ui({
    method: 'send',
    link:   url,
  }, function () {
    window.close()
  });
}
