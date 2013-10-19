(function () {
  $(".images").imagepicker({hide_select: false, show_label: false});
})();

function selectImage() {
  var select  = document.getElementsByTagName('select')[0];
  var options = select.options;
  var goalImageId = options.item(select.selectedIndex).value;

  var input   = window.opener.document.getElementById('goalImageId');
  input.value = goalImageId;

  window.close();
}
