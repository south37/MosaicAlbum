function showAlbums() {
    window.showModalDialog(
        '/album_viewer/modal',
        this,  //ダイアログに渡すパラメータ（この例では、自分自身のwindowオブジェクト）
        "dialogWidth=800px; dialogHeight=480px;"
    );
    document.getElementById('album-frm').submit();
}
