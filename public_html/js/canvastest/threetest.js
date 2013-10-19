// Generated by CoffeeScript 1.6.3
$(function() {
  console.log("load threetest.coffee");
  return window.addEventListener("DOMContentLoaded", function() {
    var anim, aspect, camera, controls, cubeMesh, directioalLight, far, fov, geometry, height, i, isEnableMove, j, material, materials, miku_tex, near, path, pathList, pclientX, pclientY, piece, planeMesh, render, scene, tex, texlist, theta, tmesh, width, _i, _j, _k;
    console.log("load window");
    width = window.innerWidth;
    height = window.innerHeight;
    render = new THREE.WebGLRenderer();
    render.setSize(width, height);
    document.body.appendChild(render.domElement);
    render.setClearColor(0x000000, 1);
    scene = new THREE.Scene();
    fov = 80;
    aspect = width / height;
    near = 1;
    far = 10000;
    camera = new THREE.PerspectiveCamera(fov, aspect, near, far);
    camera.position.z = 500;
    camera.position.x = 100;
    scene.add(camera);
    camera.lookAt(new THREE.Vector3(0, 0, 0));
    controls = new THREE.TrackballControls(camera, render.domElement);
    controls.rotateSpeed = 0.5;
    controls.addEventListener('change', render);
    directioalLight = new THREE.DirectionalLight(0xffffff, 3);
    directioalLight.position.z = 300;
    scene.add(directioalLight);
    miku_tex = new THREE.ImageUtils.loadTexture('/img/miku.jpg');
    pathList = ["resize_0.png", "resize_1.png", "resize_2.png", "resize_3.png", "resize_4.png", "resize_5.png", "resize_6.png", "resize_7.png", "resize_8.png", "resize_9.jpg"];
    texlist = (function() {
      var _i, _len, _results;
      _results = [];
      for (_i = 0, _len = pathList.length; _i < _len; _i++) {
        path = pathList[_i];
        _results.push(new THREE.ImageUtils.loadTexture('/img/resize_img/' + path));
      }
      return _results;
    })();
    materials = (function() {
      var _i, _len, _results;
      _results = [];
      for (_i = 0, _len = texlist.length; _i < _len; _i++) {
        tex = texlist[_i];
        _results.push(new THREE.MeshBasicMaterial({
          map: tex
        }));
      }
      return _results;
    })();
    geometry = new THREE.CubeGeometry(20, 20, 20);
    material = new THREE.MeshLambertMaterial({
      map: miku_tex
    });
    cubeMesh = new THREE.Mesh(geometry, material);
    scene.add(cubeMesh);
    for (i = _i = 0; _i <= 10; i = ++_i) {
      console.log("hoge:", Math.random());
      tmesh = new THREE.Mesh(geometry, material);
      tmesh.position.set(20 * i, 20 * i, 0);
      scene.add(tmesh);
    }
    geometry = new THREE.PlaneGeometry(500, 500, 1, 1);
    material = new THREE.MeshBasicMaterial({
      map: miku_tex
    });
    planeMesh = new THREE.Mesh(geometry, material);
    scene.add(planeMesh);
    geometry = new THREE.PlaneGeometry(100, 100, 1, 1);
    for (i = _j = 0; _j <= 10; i = ++_j) {
      for (j = _k = 0; _k <= 10; j = ++_k) {
        console.log(i, ":", j);
        piece = new THREE.Mesh(geometry, materials[(i + j) % 10]);
        piece.position.set(100 * i - 500, 100 * j - 500, -10 * (i + j));
        scene.add(piece);
      }
    }
    isEnableMove = false;
    pclientX = 0;
    pclientY = 0;
    $('canvas').mousedown(function() {
      console.log("mousedown");
      return isEnableMove = true;
    });
    $('canvas').mouseup(function() {
      console.log("mouseup");
      return isEnableMove = false;
    });
    $('canvas').mousemove(function(e) {
      var diff;
      console.log("mousemove");
      if (isEnableMove) {
        console.log("enable");
        diff = new THREE.Vector3(-e.clientX + pclientX, -e.clientY + pclientY, 0);
        camera.position.add(diff);
      }
      pclientX = e.clientX;
      return pclientY = e.clientY;
    });
    theta = 0;
    anim = function() {
      var rad;
      requestAnimationFrame(anim);
      rad = theta * Math.PI / 180.0;
      cubeMesh.rotation.set(rad, rad, rad);
      theta++;
      render.render(scene, camera);
      controls.update();
      return true;
    };
    render.render(scene, camera);
    anim();
    return true;
  });
});
