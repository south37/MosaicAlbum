$ ->
  window.addEventListener "DOMContentLoaded", ->
    #tooltip test
    ###
    $('#showMosaic')
      .tooltip
        placement:'top'
        title:'くりっくしてね'
        triger:'hover'

    $("#showOriginal")
      .popover
        title:"hoge"
        html:true
        trigger:"hover"
        placement:'bottom'
        content:"<img src='/img/miku.jpg'></img>" 
    ###

    # modal画面のinit
    $('#modal1 .modal-header')
      .empty()
      .append("画像の詳細  ")
      .append('<button id="closeModal" class="btn">x</button>')
    $('#modal1 .modal-body')
      .empty()
    $('#modal1 .modal-footer')
      .empty()
      .append('右クリックで保存できます ')
      #.append('<button id="fb_share" class="btni btn-primary">facebookでshare</button>')

    $("#link_howToUse").hide()



    # クリックイベント
    # html
    mosaicImagePath   = ""
    selectedImagePath = ""
    originalImagePath = ""
    ajaxpath = ""


    $('#showMosaic').click ->
      $('#modal1 .modal-body')
        .empty()
        .append("<img src=#{mosaicImagePath} alt='modaicImg'></img>")
      $('#modal1').modal('toggle')
      console.log mosaicImagePath

    $('#showSelect').click ->
      console.log "selected click:",ajaxpath
      $.getJSON ajaxpath, (ajaxdata)->
        console.log ajaxdata
        selectedImagePath = ajaxdata.fb_image_path
        $("#selectedThumnail").attr("opacity",1.0)
        $('#modal1 .modal-body')
          .empty()
          .append("<img src=#{selectedImagePath} alt='selectedImg'></img>")
        $('#modal1').modal('toggle')
        console.log selectedImagePath

    $('#showOriginal').click ->
      $('#modal1 .modal-body')
        .empty()
        .append("<img src=#{originalImagePath} alt='originalImg'></img>")
      $('#modal1').modal('toggle')
      console.log originalImagePath


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
      mosaicImagePath   = data.mosaicInfo.mosaicPath
      originalImagePath = data.mosaicInfo.originalPath
    
      # *************************** 
      # 1.描画ベース(renderer / scene)の作成
      # ***************************
     
      # レンダラの作成．追加
      height = window.innerHeight - 150
      width  = $('#canvasField').innerWidth()
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

      # ***********************************
      # 2:描画素材を準備
      # マテリアル + ジオメトリ => メッシュ
      # ***********************************
  
      # -------------------------------------
      # 2-1:マテリアル生成
      # process:imgpath取得/texture化/material化

      # FB-icon
      fbIconMaterials = {}
      console.log data.userInfo
      for key,val of data.userInfo
        # key:val = userId:iconImgPath
        imgpath = '/' + val
        console.log imgpath
        tmpTex = new THREE.ImageUtils.loadTexture(imgpath)
        fbIconMaterials[key] = new THREE.MeshBasicMaterial {map:tmpTex, side:THREE.DoubleSide}
      
      # mosaic piece
      mosaicPieceMaterials = {}
      for key,val of data.mosaicPieceMap
        # key:val = image_id : image_path
        tmpTex = new THREE.ImageUtils.loadTexture('/' + val)
        mosaicPieceMaterials[key] = new THREE.MeshBasicMaterial {map:tmpTex, side:THREE.DoubleSide}

      # -----------------------------------------
      # 2-2:ジオメトリ作成
      # process:種類とサイズを指定

      #TODO:適切なジオメトリサイズをDB情報から取得

      # fb-icon
      sizeX = 100
      sizeY = 100
      fbIconGeometry = new THREE.PlaneGeometry(sizeX, sizeY, 1, 1)

      userPosList = {}

      # mosaic-piece
      sizeX = 20
      sizeY = 20
      mosaicPieceGeometry = new THREE.PlaneGeometry(sizeX,sizeY,1,1)

      tweenList = []
      
      # --------------------------------------
      # 2-3:メッシュ(ジオメトリ＋マテリアル)の生成．これがシーンにaddされる．
      # process:
      # メッシュインスタンス生成
      # 位置指定
      # シーンに追加
      # tween設定

      # fb_icon

      # userpos用変数
      # TODO:ユーザ初期位置設定．現状は直線上.ハードコーディングなので，widthとか取ってきて割合指定にしよう．
      userNum = data.mosaicInfo.userNum
      userPosMin  =  new THREE.Vector3  -width * 0.6, -height * 0.9, 200 
      userPosMax  =  new THREE.Vector3   width * 0.6, -height * 0.9, 200

      cnt = 0
      for key,val of fbIconMaterials
        piece = new THREE.Mesh( fbIconGeometry, val)
        position = new THREE.Vector3().copy(userPosMin).lerp(userPosMax,(cnt+1)/(userNum + 1))
        piece.position.copy position
        scene.add piece

        userPosList[key] = position
        cnt += 1

      console.log userPosList

      # mosaic-piece
     
      mosaicLeftPct  = -0.5 
      mosaicRightPct = 0.5
      mosaicWidth    = sizeX * data.mosaicInfo.splitX
      mosaicHeight   = sizeY * data.mosaicInfo.splitY
      mosaicLeft     = - mosaicWidth/2
      mosaicRight    =   mosaicWidth/2

      zoomVector     = new THREE.Vector3 0,0,1000

      moveTimeMin = 300
      moveTImeMax = 600
      offsetTimeMax = 5000
     
      cnt = 0
      for piecedata in data.mosaicPieces
        piece    = new THREE.Mesh( mosaicPieceGeometry, mosaicPieceMaterials[piecedata.image_id])
        
        # 対応するユーザの位置を初期値にしましょう．
        piece.position.copy userPosList[piecedata.user_id]

        # クリック時にfb_image_idを取得するための属性追加
        piece.fb_image_id = piecedata.fb_image_id
        scene.add(piece)

        # tween設定
        # 終了位置・移動時間・オフセット時間を指定

        #TODO:適切な終了位置をDB情報から計算
        target = new THREE.Vector3(piecedata.x * sizeX + mosaicLeft, height - piecedata.y * sizeY, 0)
        zoompos = new THREE.Vector3().copy(piece.position).lerp(target,0.1).lerp(zoomVector,0.95 * Math.random())
        #console.log zoompos

        moveTime =moveTimeMin + Math.floor(Math.random() * (moveTImeMax-moveTimeMin)) 
        offsetTime = 100 + 10 * Math.floor(Math.random() * offsetTimeMax) 

        # tweenオブジェクト生成
        twn_zoom = new TWEEN.Tween(piece.position)
          .to(zoompos , moveTime * 5)
          .easing(TWEEN.Easing.Quadratic.Out)
          .delay(offsetTime)

        twn_target = new TWEEN.Tween(piece.position)
          .to(target , moveTime * 5 )
          .easing(TWEEN.Easing.Quadratic.In)

        twn_zoom.chain(twn_target)

        tweenList.push twn_zoom
        cnt += 1

      # ***********************************
      # 3.EVENT
      # ***********************************

      # ピッキング処理
      # ray
      projector = new THREE.Projector()
      $(renderer.domElement).bind 'mousedown',(e)->
        #画面上の位置
        mouseX2D = e.clientX - e.target.offsetLeft
        mouseY2D = e.clientY - e.target.offsetTop
       
        # 3D空間での位置．-1~1に正規化
        mouseX3D = (mouseX2D / e.target.width) * 2 - 1
        mouseY3D = (mouseY2D / e.target.height) * -2 + 1
       
        console.log "click:",mouseX3D,":",mouseY3D
        
        vec = new THREE.Vector3 mouseX3D,mouseY3D,-1

        projector.unprojectVector vec,camera
        ray = new THREE.Raycaster(camera.position, vec.sub(camera.position).normalize())
        obj = ray.intersectObjects scene.children,true
        
        # クリックされたオブジェクトに対する処理
        if obj.length > 0
          console.log obj[0].object.material.map.image.outerHTML 
          $("#selectedThumnail").attr("src",$(obj[0].object.material.map.image.outerHTML).attr("src"))
          $("#selectedThumnail").attr("opacity",0.5)
          tmp_id = obj[0].object.fb_image_id

          ajaxpath = '/common/mosaic_viewer/ajax_fb_image/' + tmp_id
          
          #$.getJSON ajaxpath, (ajaxdata)->
            #console.log ajaxdata
            #selectedImagePath = ajaxdata.fb_image_path
            #$("#selectedThumnail").attr("opacity",1.0)
          
        else
          console.log "no object"
      
      # --------------
      # マウスイベント
      isTweenInitiaized = false
      $('canvas').mouseup ->
        if not isTweenInitiaized
          for twn in tweenList
            twn.start()
          isTweenInitiaized = true
    
      # ------------
      # 画面リサイズ
      $(window).bind 'resize', ->
        width = $('#canvasField').innerWidth()
        height = window.innerHeight - 150
        renderer.setSize width,height
        camera.aspect = width / height
        camera.updateProjectionMatrix()


      # ***********************************
      # 4.animation設定.毎回呼ばれる処理．
      # ***********************************
      anim = ->
        requestAnimationFrame anim
        trackball.update()
        TWEEN.update()
        renderer.render(scene,camera)


      # **********************
      # 5.main的なあれ
      # **********************
      renderer.render(scene,camera)
      anim()
