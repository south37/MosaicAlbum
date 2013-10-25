(function () {
  $(".images").imagepicker({hide_select: false, show_label: false});
})();

function selectImage() {
  var select  = document.getElementsByTagName('select')[0];
  var options = select.options;
  var selectedOption = options.item(select.selectedIndex);

  var goalImageId  = selectedOption.value;
  var goalImageSrc = selectedOption.getAttribute('data-img-src');

  var input   = window.opener.document.getElementById('goalImageId');
  input.value = goalImageId;

  var goalImage = window.opener.document.getElementById('goalImage');
  goalImage.src = goalImageSrc;

  window.close();
}
