$ ->
  window.addEventListener "DOMContentLoaded", ->
    #tooltip test
    $('#showMosaic')
      .tooltip
        placement:'top'
        title:'くりっくしてね'
        triger:'hover'

    # modal画面のinit
    $('#modal1 .modal-header')
      .empty()
      .append("members:xx,oo")
      .append('<button id="closeModal" class="btn">x</button>')
    $('#modal1 .modal-body')
      .empty()
    $('#modal1 .modal-footer')
      .empty()
      .append('右クリックで保存できます ')
      .append('<button id="fb_share" class="btni btn-primary">facebookでshare</button>')

    # クリックイベント
    # html
    mosaicImagePath = ""
    selectedImagePath = ""

    $('#showMosaic').click ->
      $('#modal1 .modal-body')
        .empty()
        .append("<img src=#{mosaicImagePath} alt='modaicImg'></img>")
      $('#modal1').modal('toggle')
      console.log mosaicImagePath

    $('#showSelect').click ->
      $('#modal1 .modal-body')
        .empty()
        .append("<img src=#{selectedImagePath} alt='selectedImg'></img>")
      $('#modal1').modal('toggle')
      console.log selectedImagePath

    # modal
    $('#closeModal').click ->
      $('#modal1').modal('toggle')

    $('#fb_share').click ->
      alert "shareしたよ"

    #ajaxで取得するよ
    $.getJSON "/common/mosaic_viewer/ajax_list", (data)->
      console.log data

      # goalImgをmodalに追加
      mosaicImagePath = data.mosaicImage

      # 1.描画ベース(renderer / scene)の作成
      # レンダラの作成．追加
      width  = window.innerWidth 
      height = window.innerHeight - 100
      width  = $('#container').innerWidth()
      #height = $('#container').innerHeight()
      renderer = new THREE.WebGLRenderer()
      renderer.setSize(width,height)
      #$("#container").before(renderer.domElement)
      $('#forCanvas').append(renderer.domElement) 
      renderer.setClearColor(0x000000,1)

      # sceneの作成
      scene = new THREE.Scene()

      #cameraの作成・追加
      fov = 80
      aspect = width / height
      nearClip = 1
      farClip = 10000
      camera = new THREE.PerspectiveCamera(fov,aspect,nearClip,farClip)
      lookTarget = new THREE.Vector3(0,0,0)
      cameraPosition = new THREE.Vector3(0,0,1000)
      camera.position.copy cameraPosition

      scene.add camera
      camera.lookAt lookTarget

      # traclball
      trackball = new THREE.TrackballControls(camera, renderer.domElement)

      # lightの作成．追加
      directioalLight = new THREE.DirectionalLight(0xffffff,3)
      directioalLight.position.z = 300
      scene.add directioalLight


      # 2:描画素材を準備
      # material生成
      # imgpath取得/texture化/material化

      # FB-icon
      fbUserInfoList  = data.userInfo
      fbUserIdList =(info.userID for info in fbUserInfoList)
      fbIconTexList   = (new THREE.ImageUtils.loadTexture(info.iconPath) for info in fbUserInfoList)
      fbIconMaterials = (new THREE.MeshBasicMaterial {map:tex, side:THREE.DoubleSide} for tex in fbIconTexList)
      
      # mosaic piece
      # TODO:DBからpathlistが取得できるようになるはずです．
      mosaicPiecePathList  = data.mosaicTextures
      mosaicPieceTexList   = (new THREE.ImageUtils.loadTexture(path) for path in mosaicPiecePathList)
      mosaicPieceMaterials = (new THREE.MeshBasicMaterial {map:tex, side:THREE.DoubleSide} for tex in mosaicPieceTexList)

      # debug用．pathとmosaicPieceMaterialsを対応させている．
      materialNumbers =
        "img/resize_img/1/1.png":0
        "img/resize_img/1/2.png":1
        "img/resize_img/1/3.png":2
        "img/resize_img/1/4.png":3
        "img/resize_img/1/5.png":4
        "img/resize_img/1/6.png":5
        "img/resize_img/1/7.png":6
        "img/resize_img/1/8.png":7
        "img/resize_img/1/9.png":8


      # ジオメトリの追加
      row = 80
      col = 60
      sizeX = 1000/col
      sizeY = 1000/row

      sizeX = 100
      sizeY = 100
      fbIconGeometry = new THREE.PlaneGeometry(sizeX, sizeY, 1, 1)

      userPosList = {}

      sizeX = 10
      sizeY = 10
      mosaicPieceGeometry = new THREE.PlaneGeometry(sizeX,sizeY,1,1)

      pieces = []
      pieces_tween = []
     
      # メッシュ(ジオメトリ＋マテリアル)の生成．これがシーンにaddされる．
     
      # B
      # fb_icon
      cnt = 0
      for material in fbIconMaterials
        piece = new THREE.Mesh( fbIconGeometry, material)

        position = new THREE.Vector3( 100 * cnt, -300, 100)
        piece.position.copy position
        scene.add piece

        userPosList[fbUserIdList[cnt]] = position
        cnt += 1

      console.log userPosList

      # mosaic
      cnt = 0
      for piecedata in data.mosaicPieces
        piece    = new THREE.Mesh( mosaicPieceGeometry, mosaicPieceMaterials[ materialNumbers[piecedata.resize_image_path]])
        
        # TODO:initial_positionの設定
        # 対応するユーザの位置を初期値にしましょう．
        #position = new THREE.Vector3( sizeX * (cnt % 200) - 1000, -100 - 10 * (cnt / 200), 0) 
        #piece.position.copy position
        piece.position.copy userPosList[piecedata.user_id]

        # クリック時にfb_image_idを取得するための属性追加
        piece.fb_image_id = piecedata.fb_image_id
        scene.add(piece)

        # tween設定
        target = new THREE.Vector3(piecedata.x * sizeX - 500, 500 - piecedata.y * sizeY, 0)
        movetime = 300
        delaytime = 100 + 10 * cnt
        twn = new TWEEN.Tween(piece.position)
          .to(target , movetime)
          .delay(delaytime)
        pieces_tween.push twn
        cnt += 1

      # ray
      projector = new THREE.Projector()
      $(renderer.domElement).bind 'mousedown',(e)->
        console.log "rendererclicked"
       
        #画面上の位置
        mouseX2D = e.clientX - e.target.clientLeft
        mouseY2D = e.clientY - e.target.clientTop
       
        # 3D空間での位置．-1~1に正規化
        mouseX3D = (mouseX2D / e.target.width) * 2 - 1
        mouseY3D = (mouseY2D / e.target.height) * -2 + 1
        
        vec = new THREE.Vector3 mouseX3D,mouseY3D,-1

        projector.unprojectVector vec,camera
        ray = new THREE.Raycaster(camera.position, vec.sub(camera.position).normalize())
        obj = ray.intersectObjects scene.children,true
        
        # クリックされたオブジェクトに対する処理
        if obj.length > 0
          tmp_id = obj[0].object.fb_image_id
          path = '/common/mosaic_viewer/ajax_fb_image/' + tmp_id
          $.getJSON path, (data)->
            console.log data
            selectedImagePath = data.fb_image_path
        else
          console.log "no clicked object"
      
      # マウスイベント：クリック：tweenスタート！
      isTweenInitiaized = false
      $('canvas').mouseup ->
        if not isTweenInitiaized
          console.log "tweenset"
          for twn in pieces_tween
            twn.start()
          isTweenInitiaized = true

      # キーボード入力：TODO:なんかこれ動いてないっぽい
      $('canvas').keypress (e) ->
        console.log e.which
        switch e.which
          when 113 
            #q
            controlMode = if controlMode == "move" then "none" else "move"
          when 119
            #w
            controlMode = if controlMode == "zoom" then "none" else "zoom"
          when 101
            #e
            controlMode = if controlMode == "target" then "none" else "target"
          when 97
            #a
            controlMode = "reset"
            camera.position.set 0,0,1000
            camera.lookAt THREE.Vector3(0,0,0)
          else
            controlMode = "none"

      
      $(window).bind 'resize', ->
        console.log "window resize"
        width = $('#container').innerWidth()
        height = window.innerHeight - 100
        renderer.setSize width,height
        camera.aspect = width / height
        camera.updateProjectionMatrix()

      # animation設定.毎回呼ばれる．
      anim = ->
        requestAnimationFrame anim
        trackball.update()
        TWEEN.update()
        renderer.render(scene,camera)

      # main的なあれ
      renderer.render(scene,camera)
      anim()
