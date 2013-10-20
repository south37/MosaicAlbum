(function () {
  $(".albums").imagepicker({hide_select: false, show_label: true});
})();

function select() {
  var select  = document.getElementsByTagName('select')[0];
  var options = select.options;
  var albumId = options.item(select.selectedIndex).value;

  var input   = window.opener.document.getElementById('albumId');
  input.value = albumId;
  
  window.close();
}
