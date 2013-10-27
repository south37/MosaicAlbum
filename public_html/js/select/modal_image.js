(function () {
  $(".images").imagepicker({hide_select: false, show_label: false});
})();

function selectImage() {
  var select  = document.getElementsByTagName('select')[0];
  var options = select.options;
  var selectedOption = options.item(select.selectedIndex);

  var parentDocument = window.opener.document;

  var goalImageId  = selectedOption.value;
  var goalImageSrc = selectedOption.getAttribute('data-img-src');

  var input   = parentDocument.getElementById('goalImageId');
  input.value = goalImageId;

  var goalImage = parentDocument.getElementById('goalImage');
  goalImage.src = goalImageSrc;

  var message = parentDocument.getElementById('message');
  message.innerHTML = 'この写真がモザイク画像の完成形となります。';

  var selectButton = parentDocument.getElementById('selectButton');
  selectButton.style.display = "block";

  window.close();
}
