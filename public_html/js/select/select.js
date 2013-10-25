function showAlbums() {
    window.showModalDialog(
        '/select/modal_album',
        this,  //ダイアログに渡すパラメータ（この例では、自分自身のwindowオブジェクト）
        "dialogWidth=800px; dialogHeight=480px;"
    );
}
