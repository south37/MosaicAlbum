$ ->
  console.log "load threetest.coffee"
  window.addEventListener "DOMContentLoaded", ->
    console.log "load window"
   
    # レンダラの作成．追加
    width = window.innerWidth
    height = window.innerHeight
    #render  = new THREE.WebGLRenderer({'canvas':$('#cvs1')[0]})
    render = new THREE.WebGLRenderer()
    render.setSize(width,height)
    $("body").append render.domElement
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

    # camera controller
    #controls = new THREE.TrackballControls(camera, render.domElement)
    #controls.rotateSpeed = 0.5
    #controls.addEventListener('change',render)

    # lightの作成．追加
    directioalLight = new THREE.DirectionalLight(0xffffff,3)
    directioalLight.position.z = 300
    scene.add directioalLight

    # textureのロード
    miku_tex = new THREE.ImageUtils.loadTexture('/img/miku.jpg')
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
    geometry = new THREE.CubeGeometry(20,20,20)
    material = new THREE.MeshLambertMaterial({map:miku_tex})
    #material = new THREE.MeshLambertMaterial({color:0x226633})
    cubeMesh = new THREE.Mesh(geometry,material)
    scene.add(cubeMesh)

    for i in [0..10]
      console.log "hoge:",Math.random()
      tmesh = new THREE.Mesh(geometry,material)
      tmesh.position.set 20*i,20*i, 0
      scene.add tmesh

    geometry = new THREE.PlaneGeometry(500,500,1,1)
    material = new THREE.MeshBasicMaterial({map:miku_tex})
    planeMesh = new THREE.Mesh(geometry,material)
    #planeMesh.position.set -100,0,0
    #planeMesh.rotation.set Math.PI/2, 0, 0
    scene.add(planeMesh)

    row = 80
    col = 60
    sizeX = 1000/col
    sizeY = 1000/row
    geometry = new THREE.PlaneGeometry(sizeX,sizeY,1,1)
    for i in [0..col]
      for j in [0..row]
        piece = new THREE.Mesh(geometry,materials[(i+j)%10])
        piece.position.set sizeX*i - 500, sizeY * j - 500, -10
        scene.add(piece)

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

    $(this).rightClick (e) ->
      console.log "rightclick"
      controlMode = "right"
    $(this).leftClick (e) ->
      console.log "left click"
  
    $(this).wheelClick (e)->
      console.log "whell click"
    
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

    $(this).exScrollEvent (e,param) ->
      console.log param.scroll.top
      e.preventDefault()

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
    # ループ関数

    theta = 0
    
    anim = ->
      requestAnimationFrame anim
      rad = theta * Math.PI / 180.0
      cubeMesh.rotation.set rad,rad,rad
      #theta++
      #camera.lookAt(new THREE.Vector3(0, 0, 0))
      render.render scene,camera
      #controls.update()
      return true 

    render.render(scene,camera)
    anim()
    return true 
