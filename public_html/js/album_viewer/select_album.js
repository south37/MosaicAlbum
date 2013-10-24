function showAlbums() {
    alert('show dialog');
    window.showModalDialog(
        '/album_viewer/modal',
        this,  //ダイアログに渡すパラメータ（この例では、自分自身のwindowオブジェクト）
        "dialogWidth=800px; dialogHeight=480px;"
    );
    alert('before');
    document.getElementById('album-frm').submit();
    alert('after');
}
