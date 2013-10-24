function showAlbums() {
  window.showModalDialog(
    '/album_viewer/modal',
    this,  //ダイアログに渡すパラメータ（この例では、自分自身のwindowオブジェクト）
    "dialogWidth=800px; dialogHeight=480px;"
  );
  var input = document.getElementById('albumId');
  if (input.value !== '') {
    document.getElementById('album-frm').submit();
  }
}
