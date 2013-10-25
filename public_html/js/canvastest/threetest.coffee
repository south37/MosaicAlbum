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

    # *************************** 
    # three.jsの処理はこれ以降！
    # ***************************
    
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
      # マテリアル + ジオメトリ => メッシュ

      # 2-1:マテリアル生成
      # process:imgpath取得/texture化/material化

      # FB-icon
      fbIconMaterials = {}
      for key,val of data.userInfo
        # key:val = userId:iconImgPath
        tmpTex = new THREE.ImageUtils.loadTexture(val)
        fbIconMaterials[key] = new THREE.MeshBasicMaterial {map:tmpTex, side:THREE.DoubleSide}
      
      # mosaic piece
      mosaicPieceMaterials = {}
      for key,val of data.mosaicPieceMap
        # key:val = image_id : image_path
        tmpTex = new THREE.ImageUtils.loadTexture('/' + val)
        mosaicPieceMaterials[key] = new THREE.MeshBasicMaterial {map:tmpTex, side:THREE.DoubleSide}


      # 2-2:ジオメトリ作成
      # process:種類とサイズを指定

      # fb-icon
      sizeX = 100
      sizeY = 100
      fbIconGeometry = new THREE.PlaneGeometry(sizeX, sizeY, 1, 1)

      userPosList = {}

      # mosaic-piece
      sizeX = 10
      sizeY = 10
      mosaicPieceGeometry = new THREE.PlaneGeometry(sizeX,sizeY,1,1)

      tweenList = []
      
     
      # 2-3:メッシュ(ジオメトリ＋マテリアル)の生成．これがシーンにaddされる．
      # process:
      # メッシュインスタンス生成
      # 位置指定
      # シーンに追加
      # tween設定

      # fb_icon
      cnt = 0
      for key,val of fbIconMaterials
        piece = new THREE.Mesh( fbIconGeometry, val)

        position = new THREE.Vector3( 100 * cnt, -300, 100)
        piece.position.copy position
        scene.add piece

        userPosList[key] = position
        cnt += 1

      console.log userPosList

      # mosaic-piece
      cnt = 0
      for piecedata in data.mosaicPieces
        piece    = new THREE.Mesh( mosaicPieceGeometry, mosaicPieceMaterials[piecedata.image_id])
        
        # TODO:initial_positionの設定
        # 対応するユーザの位置を初期値にしましょう．
        piece.position.copy userPosList[piecedata.user_id]

        # クリック時にfb_image_idを取得するための属性追加
        piece.fb_image_id = piecedata.fb_image_id
        scene.add(piece)

        # tween設定
        # 終了位置・移動時間・オフセット時間を指定
        target = new THREE.Vector3(piecedata.x * sizeX - 500, 500 - piecedata.y * sizeY, 0)
        moveTime = 300
        offsetTime = 100 + 10 * cnt

        # tweenオブジェクト生成
        twn = new TWEEN.Tween(piece.position)
          .to(target , moveTime)
          .delay(offsetTime)
        tweenList.push twn
        cnt += 1

      # ray
      projector = new THREE.Projector()
      $(renderer.domElement).bind 'mousedown',(e)->
        console.log "rendererclicked"
      
        #TODO:picker修復
        #画面上の位置
        #
        console.log "client:",e
        
        mouseX2D = e.clientX - e.target.offsetLeft
        mouseY2D = e.clientY - e.target.offsetTop
       
        # 3D空間での位置．-1~1に正規化
        mouseX3D = (mouseX2D / e.target.width) * 2 - 1
        mouseY3D = (mouseY2D / e.target.height) * -2 + 1
        
        console.log "mouseX:",mouseX3D,"mouseY:",mouseY3D

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
          for twn in tweenList
            twn.start()
          isTweenInitiaized = true
      
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
