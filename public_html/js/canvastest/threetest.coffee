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
    document.body.appendChild( render.domElement)
    render.setClearColor(0x000000,1)

    # sceneの作成
    scene = new THREE.Scene()

    #cameraの作成・追加
    fov = 80
    aspect = width / height
    near = 1
    far = 1000
    camera = new THREE.PerspectiveCamera(fov,aspect,near,far)
    camera.position.z = 500
    camera.position.x = 300
    scene.add camera
    camera.lookAt(new THREE.Vector3(0,0,0))

    # lightの作成．追加
    directioalLight = new THREE.DirectionalLight(0xffffff,3)
    directioalLight.position.z = 300
    scene.add directioalLight

    # ジオメトリの追加
    geometry = new THREE.CubeGeometry(200,200,200)
    material = new THREE.MeshLambertMaterial({color:0x226633})
    cubeMesh = new THREE.Mesh(geometry,material)
    scene.add(cubeMesh)

    
    geometry = new THREE.PlaneGeometry(1000,1000,1,1)
    material = new THREE.MeshLambertMaterial({color:0x660066})
    planeMesh = new THREE.Mesh(geometry,material)
    #planeMesh.position.set -100,0,0
    #planeMesh.rotation.set Math.PI/2, 0, 0
    scene.add(planeMesh)  

    # レンダリング
    render.render(scene,camera)

    theta = 0
    anim = ->
      rad = theta * Math.PI / 180.0
      cubeMesh.rotation.set rad,rad,rad
      theta++
      camera.lookAt(new THREE.Vector3(100 * Math.sin(rad), 100 * Math.cos(rad) , 0))
      render.render scene,camera
      requestAnimationFrame anim
    anim()

