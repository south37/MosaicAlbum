// Generated by CoffeeScript 1.6.3
$(function() {
  return window.addEventListener("DOMContentLoaded", function() {
    $('#closeModal').click(function() {
      return $('#modal1').toggle('toggle');
    });
    return $.getJSON("/common/mosaic_viewer/ajax_list", function(data) {
      var anim, aspect, camera, cameraPosition, cnt, col, delaytime, directioalLight, farClip, fb_icon_materials, fov, geometry, height, isTweenInitiaized, lookTarget, materialNumbers, materials, mosaicContentsStr, movetime, nearClip, path, pathList, piece, piecedata, pieces, pieces_tween, position, projector, renderer, row, scene, sizeX, sizeY, target, tex, texlist, trackball, twn, width, _i, _len, _ref;
      console.log(data);
      mosaicContentsStr = "<img src=" + data.mosaicImage + " alt='mosaicdayo'></img>";
      $('#modal1 .modal-body').append(mosaicContentsStr);
      width = window.innerWidth;
      height = window.innerHeight;
      renderer = new THREE.WebGLRenderer();
      renderer.setSize(width, height);
      $("#container").before(renderer.domElement);
      renderer.setClearColor(0x000000, 1);
      scene = new THREE.Scene();
      fov = 80;
      aspect = width / height;
      nearClip = 1;
      farClip = 10000;
      camera = new THREE.PerspectiveCamera(fov, aspect, nearClip, farClip);
      lookTarget = new THREE.Vector3(0, 0, 0);
      cameraPosition = new THREE.Vector3(0, 0, 1000);
      camera.position.copy(cameraPosition);
      scene.add(camera);
      camera.lookAt(lookTarget);
      trackball = new THREE.TrackballControls(camera, renderer.domElement);
      directioalLight = new THREE.DirectionalLight(0xffffff, 3);
      directioalLight.position.z = 300;
      scene.add(directioalLight);
      fb_icon_materials = data.userIcons;
      pathList = ["1.png", "2.png", "3.png", "4.png", "5.png", "6.png", "7.png", "8.png", "9.png"];
      pathList = data.mosaicTextures;
      texlist = (function() {
        var _i, _len, _results;
        _results = [];
        for (_i = 0, _len = pathList.length; _i < _len; _i++) {
          path = pathList[_i];
          _results.push(new THREE.ImageUtils.loadTexture('/img/resize_img/1/' + path));
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
      materialNumbers = {
        "img/resize_img/1/1.png": 0,
        "img/resize_img/1/2.png": 1,
        "img/resize_img/1/3.png": 2,
        "img/resize_img/1/4.png": 3,
        "img/resize_img/1/5.png": 4,
        "img/resize_img/1/6.png": 5,
        "img/resize_img/1/7.png": 6,
        "img/resize_img/1/8.png": 7,
        "img/resize_img/1/9.png": 8
      };
      row = 80;
      col = 60;
      sizeX = 1000 / col;
      sizeY = 1000 / row;
      sizeX = 10;
      sizeY = 10;
      geometry = new THREE.PlaneGeometry(sizeX, sizeY, 1, 1);
      pieces = [];
      pieces_tween = [];
      cnt = 0;
      _ref = data.mosaicPieces;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        piecedata = _ref[_i];
        piece = new THREE.Mesh(geometry, materials[materialNumbers[piecedata.resize_image_path]]);
        position = new THREE.Vector3(cnt - 1000, -500, 0);
        piece.position.copy(position);
        scene.add(piece);
        target = new THREE.Vector3(piecedata.x * sizeX - 500, 500 - piecedata.y * sizeY, 0);
        movetime = 300;
        delaytime = 100 + 10 * cnt;
        twn = new TWEEN.Tween(piece.position).to(target, movetime).delay(delaytime);
        pieces_tween.push(twn);
        cnt += 1;
      }
      projector = new THREE.Projector();
      $(renderer.domElement).bind('mousedown', function(e) {
        var mouseX, mouseY, obj, ray, vec;
        console.log("rendererclicked");
        mouseX = ((e.pageX - e.target.offsetParent.offsetLeft) / renderer.domElement.width) * 2 - 1;
        mouseY = ((e.pageY - e.target.offsetParent.offsetTop) / renderer.domElement.height) * 2 - 1;
        vec = new THREE.Vector3(mouseX, mouseY, 0);
        projector.unprojectVector(vec, camera);
        ray = new THREE.Raycaster(camera.position, vec.sub(camera.position).normalize());
        obj = ray.intersectObjects(scene.children, true);
        if (obj.length > 0) {
          return console.log("object clicked", obj[0].object.id);
        } else {
          return console.log("no clicked object");
        }
      });
      isTweenInitiaized = false;
      $('canvas').mouseup(function() {
        var _j, _len1;
        if (!isTweenInitiaized) {
          console.log("tweenset");
          for (_j = 0, _len1 = pieces_tween.length; _j < _len1; _j++) {
            twn = pieces_tween[_j];
            twn.start();
          }
          return isTweenInitiaized = true;
        }
      });
      $('canvas').keypress(function(e) {
        var controlMode;
        console.log(e.which);
        switch (e.which) {
          case 113:
            return controlMode = controlMode === "move" ? "none" : "move";
          case 119:
            return controlMode = controlMode === "zoom" ? "none" : "zoom";
          case 101:
            return controlMode = controlMode === "target" ? "none" : "target";
          case 97:
            controlMode = "reset";
            camera.position.set(0, 0, 1000);
            return camera.lookAt(THREE.Vector3(0, 0, 0));
          default:
            return controlMode = "none";
        }
      });
      anim = function() {
        requestAnimationFrame(anim);
        trackball.update();
        TWEEN.update();
        return renderer.render(scene, camera);
      };
      renderer.render(scene, camera);
      return anim();
    });
  });
});
