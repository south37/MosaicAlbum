function showAlbums() {
    window.showModalDialog(
        '/select/modal_album',
        this,  //ダイアログに渡すパラメータ（この例では、自分自身のwindowオブジェクト）
        "dialogWidth=800px; dialogHeight=480px;"
    );
}

function showRequestDialog() {
    window.showModalDialog(
         '/select/modal_request',
         this,
        "dialogWidth=800px; dialogHeight=480px;"
    );
    document.getElementById('goal-img-frm').submit();
}
