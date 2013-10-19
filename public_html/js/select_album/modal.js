(function () {
  $(".albums").imagepicker({hide_select: false, show_label: true});
})();

function select() {
  var select  = document.getElementsByTagName('select')[0];
  var options = select.options;
  var goalImageId = options.item(select.selectedIndex).value;

  window.close();
}
