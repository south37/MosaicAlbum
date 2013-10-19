$ ->
  console.log "load threetest.coffee"
  window.addEventListener "DOMContentLoaded", ->
    
    # レンダラの作成．追加
    width = window.innerWidth
    height = window.innerHeight
    render = new THREE.WebGLRenderer()
    render.setSize(width,height)
    $("#container").before render.domElement
    render.setClearColor(0x000000,1)

    # sceneの作成
    scene = new THREE.Scene()

    #cameraの作成・追加
    fov = 80
    aspect = width / height
    near = 1
    far = 10000
    camera = new THREE.PerspectiveCamera(fov,aspect,near,far)
    target = new THREE.Vector3(0,0,0)
    camera.position.set 0,0,500
    scene.add camera
    camera.lookAt target

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

    # ジオメトリの追加
    row = 80
    col = 60
    sizeX = 1000/col
    sizeY = 1000/row
    geometry = new THREE.PlaneGeometry(sizeX,sizeY,1,1)
    pieces = []
    for i in [0..col]
      tmppieces = []
      for j in [0..row]
        piece = new THREE.Mesh(geometry,materials[(i+j)%10])
        piece.position.set sizeX*i - 500, sizeY * j - 500, -10
        scene.add(piece)
        tmppieces.push piece
      pieces.push tmppieces
    console.log pieces
        

    #event
    controlMode = "none"
    pclientX = 0
    pclientY = 0 
    
    $('canvas').mousedown (e)->
      console.log "mousedown:", e
      controlMode = "move"

    $('canvas').mouseup ->
      console.log "mouseup"
      controlMode = "none"
      trans(piece,new THREE.Vector3(0,0,300),2000)
      
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
      render.render(scene,camera)

    trans = (object, target, duration, delay) ->
      new TWEEN.Tween(object.position)
        .to({x:target.x , y:target.y , z:target.z} , duration)
        .delay(delay)
        .easing(TWEEN.Easing.Linear.None)
        .start()

      new TWEEN.Tween(this)
        .to({},duration)
        .onUpdate(rendering)
        .start()

    # animation設定
    anim = ->
      requestAnimationFrame anim
      TWEEN.update()
      render.render scene,camera

    # main的なあれ
    render.render(scene,camera)
    anim()
