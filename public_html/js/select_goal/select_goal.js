function showAlbums() {
    window.showModalDialog(
        '/master/select_album',
        this,  //ダイアログに渡すパラメータ（この例では、自分自身のwindowオブジェクト）
        "dialogWidth=800px; dialogHeight=480px;"
    );
}

function showImages() {
}
