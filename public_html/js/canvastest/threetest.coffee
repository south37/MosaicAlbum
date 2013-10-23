$ ->
  window.addEventListener "DOMContentLoaded", ->
    #ajaxで取得するよ
    $.getJSON "/common/mosaic_viewer/ajax_list", (data)->
      console.log data
      
      #for piece in data.mosaicPieces
        #console.log piece.x,":",piece.y

      # レンダラの作成．追加
      width  = window.innerWidth
      height = window.innerHeight
      renderer = new THREE.WebGLRenderer()
      renderer.setSize(width,height)
      $("#container").before(renderer.domElement)
      renderer.setClearColor(0x000000,1)

      # sceneの作成
      scene = new THREE.Scene()

      #cameraの作成・追加
      fov = 80
      aspect = width / height
      nearClip = 1
      farClip = 10000
      camera = new THREE.PerspectiveCamera(fov,aspect,nearClip,farClip)
      target = new THREE.Vector3(0,0,0)
      camera.position.set(0,0,1000)
      scene.add camera
      camera.lookAt target

      # traclball:
      trackball = new THREE.TrackballControls(camera, renderer.domElement)

      # lightの作成．追加
      directioalLight = new THREE.DirectionalLight(0xffffff,3)
      directioalLight.position.z = 300
      scene.add directioalLight

      # textureのロード
      pathList = [
        "resize_0.png"
        "resize_1.png"
        "resize_2.png"
        "resize_3.png"
        "resize_4.png"
        "resize_5.png"
        "resize_6.png"
        "resize_7.png"
        "resize_8.png"
        "resize_9.jpg"
      ]
      texlist = (new THREE.ImageUtils.loadTexture('/img/resize_img/'+path) for path in pathList)
      materials = (new THREE.MeshBasicMaterial {map:tex} for tex in texlist)
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

      sizeX = 10
      sizeY = 10
      geometry = new THREE.PlaneGeometry(sizeX,sizeY,1,1)
      pieces = []
      ###
      for i in [0..col]
        tmppieces = []
        for j in [0..row]
          piece = new THREE.Mesh(geometry,materials[(i+j)%10])
          piece.position.set sizeX*i - 500, -600, 0
          scene.add(piece)
          tmppieces.push piece
        pieces.push tmppieces
      ###
      
      for piecedata in data.mosaicPieces
        piece = new THREE.Mesh( geometry, materials[ materialNumbers[piecedata.resize_image_path]])
        piece.position.set(piecedata.x * sizeX - 500, 500 - piecedata.y * sizeY, 0)
        scene.add(piece)
        

      # ray
      projector = new THREE.Projector()
      $(renderer.domElement).bind 'mousedown',(e)->
        console.log "rendererclicked"
        mouseX = ((e.pageX - e.target.offsetParent.offsetLeft) / renderer.domElement.width) * 2 - 1
        mouseY = ((e.pageY - e.target.offsetParent.offsetTop) / renderer.domElement.height) * 2 - 1
        vec = new THREE.Vector3 mouseX,mouseY,0
        projector.unprojectVector vec,camera
        
        ray = new THREE.Raycaster(camera.position, vec.sub(camera.position).normalize())
        obj = ray.intersectObjects scene.children,true 
    
        if obj.length > 0
          console.log "object clicked",obj[0].object.id
        else
          console.log "no clicked object"
     
      #event
      controlMode = "none"
      pclientX = 0
      pclientY = 0 
      
      $('canvas').mousedown (e)->
        #console.log "mousedown:", e
        controlMode = "move"

      $('canvas').mouseup ->
        #console.log "mouseup"
        controlMode = "none"
        for i in [0..col]
          for j in [0..row]
            movetime = 200 * Math.floor( Math.random() * (row+col))
            trans(pieces[i][j],new THREE.Vector3(sizeX*i-500,sizeY*j-500,0),100,500 + movetime)
        
      $('canvas').mousemove (e) ->
        switch controlMode
          when "move"
            diff = new THREE.Vector3( - e.clientX + pclientX, e.clientY - pclientY, 0)
            camera.position.add diff
          when "zoom"
            diff = new THREE.Vector3( 0, 0, e.clientY - pclientY)
            camera.position.add diff
          when "target"
            diff = new THREE.Vector3( - e.clientX + pclientX, e.clientY - pclientY, 0)
            target.add diff
            camera.lookAt target
          when "reset"
            camera.position.set 0,0,500
            target.set 0,0,0
            camera.lookAt target
            controlMode = "none"
          when "none"
            console.log "none"
        pclientX = e.clientX
        pclientY = e.clientY

      $(this).keypress (e) ->
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
          else
            controlMode = "none"

      # tween用関数
      rendering = ->
        renderer.render(scene,camera)

      trans = (object, target, duration, delay) ->
        #TWEEN.removeAll()
        new TWEEN.Tween(object.position)
          .to(target,duration)
          .delay(delay)
          .easing(TWEEN.Easing.Linear.None)
          .start()
       
      # animation設定
      anim = ->
        requestAnimationFrame anim
        trackball.update()
        TWEEN.update()
        renderer.render(scene,camera)

      # main的なあれ
      renderer.render(scene,camera)
      anim()
