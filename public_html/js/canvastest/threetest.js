// Generated by CoffeeScript 1.6.3
$(function() {
  return window.addEventListener("DOMContentLoaded", function() {
    /*
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
    */

    var ajaxpath, mosaicImagePath, originalImagePath, selectedImagePath;
    $('#modal1 .modal-header').empty().append("画像の詳細  ").append('<button id="closeModal" class="btn">x</button>');
    $('#modal1 .modal-body').empty();
    $('#modal1 .modal-footer').empty().append('右クリックで保存できます ');
    $("#link_howToUse").hide();
    mosaicImagePath = "";
    selectedImagePath = "";
    originalImagePath = "";
    ajaxpath = "";
    $('#showMosaic').click(function() {
      $('#modal1 .modal-body').empty().append("<img src=" + mosaicImagePath + " alt='modaicImg'></img>");
      $('#modal1').modal('toggle');
      return console.log(mosaicImagePath);
    });
    $('#showSelect').click(function() {
      console.log("selected click:", ajaxpath);
      return $.getJSON(ajaxpath, function(ajaxdata) {
        console.log(ajaxdata);
        selectedImagePath = ajaxdata.fb_image_path;
        $("#selectedThumnail").attr("opacity", 1.0);
        $('#modal1 .modal-body').empty().append("<img src=" + selectedImagePath + " alt='selectedImg'></img>");
        $('#modal1').modal('toggle');
        return console.log(selectedImagePath);
      });
    });
    $('#showOriginal').click(function() {
      $('#modal1 .modal-body').empty().append("<img src=" + originalImagePath + " alt='originalImg'></img>");
      $('#modal1').modal('toggle');
      return console.log(originalImagePath);
    });
    $('#closeModal').click(function() {
      return $('#modal1').modal('toggle');
    });
    $('#fb_share').click(function() {
      return alert("shareしたよ");
    });
    return $.getJSON("/common/mosaic_viewer/ajax/list", function(data) {
      var anim, aspect, camera, cameraPosition, cnt, directioalLight, farClip, fbIconGeometry, fbIconMaterials, fov, height, imgpath, isTweenInitiaized, key, lookTarget, mosaicHeight, mosaicLeft, mosaicLeftPct, mosaicPieceGeometry, mosaicPieceMaterials, mosaicRight, mosaicRightPct, mosaicWidth, moveTImeMax, moveTime, moveTimeMin, nearClip, offsetTime, offsetTimeMax, piece, piecedata, position, projector, renderer, scene, sizeX, sizeY, target, tmpTex, trackball, tweenList, twn_target, twn_zoom, userNum, userPosList, userPosMax, userPosMin, val, width, zoomVector, zoompos, _i, _len, _ref, _ref1, _ref2;
      console.log(data);
      mosaicImagePath = data.mosaicInfo.mosaicPath;
      originalImagePath = data.mosaicInfo.originalPath;
      height = window.innerHeight - 150;
      width = $('#canvasField').innerWidth();
      renderer = new THREE.WebGLRenderer();
      renderer.setSize(width, height);
      $('#forCanvas').append(renderer.domElement);
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
      fbIconMaterials = {};
      console.log(data.userInfo);
      _ref = data.userInfo;
      for (key in _ref) {
        val = _ref[key];
        imgpath = '/' + val;
        console.log(imgpath);
        tmpTex = new THREE.ImageUtils.loadTexture(imgpath);
        fbIconMaterials[key] = new THREE.MeshBasicMaterial({
          map: tmpTex,
          side: THREE.DoubleSide
        });
      }
      mosaicPieceMaterials = {};
      _ref1 = data.mosaicPieceMap;
      for (key in _ref1) {
        val = _ref1[key];
        tmpTex = new THREE.ImageUtils.loadTexture('/' + val);
        mosaicPieceMaterials[key] = new THREE.MeshBasicMaterial({
          map: tmpTex,
          side: THREE.DoubleSide
        });
      }
      sizeX = 100;
      sizeY = 100;
      fbIconGeometry = new THREE.PlaneGeometry(sizeX, sizeY, 1, 1);
      userPosList = {};
      sizeX = 20;
      sizeY = 20;
      mosaicPieceGeometry = new THREE.PlaneGeometry(sizeX, sizeY, 1, 1);
      tweenList = [];
      userNum = data.mosaicInfo.userNum;
      userPosMin = new THREE.Vector3(-width * 0.6, -height * 0.9, 200);
      userPosMax = new THREE.Vector3(width * 0.6, -height * 0.9, 200);
      cnt = 0;
      for (key in fbIconMaterials) {
        val = fbIconMaterials[key];
        piece = new THREE.Mesh(fbIconGeometry, val);
        position = new THREE.Vector3().copy(userPosMin).lerp(userPosMax, (cnt + 1) / (userNum + 1));
        piece.position.copy(position);
        scene.add(piece);
        userPosList[key] = position;
        cnt += 1;
      }
      console.log(userPosList);
      mosaicLeftPct = -0.5;
      mosaicRightPct = 0.5;
      mosaicWidth = sizeX * data.mosaicInfo.splitX;
      mosaicHeight = sizeY * data.mosaicInfo.splitY;
      mosaicLeft = -mosaicWidth / 2;
      mosaicRight = mosaicWidth / 2;
      zoomVector = new THREE.Vector3(0, 0, 1000);
      moveTimeMin = 300;
      moveTImeMax = 600;
      offsetTimeMax = 5000;
      cnt = 0;
      _ref2 = data.mosaicPieces;
      for (_i = 0, _len = _ref2.length; _i < _len; _i++) {
        piecedata = _ref2[_i];
        piece = new THREE.Mesh(mosaicPieceGeometry, mosaicPieceMaterials[piecedata.image_id]);
        piece.position.copy(userPosList[piecedata.user_id]);
        piece.fb_image_id = piecedata.fb_image_id;
        scene.add(piece);
        target = new THREE.Vector3(piecedata.x * sizeX + mosaicLeft, height - piecedata.y * sizeY, 0);
        zoompos = new THREE.Vector3().copy(piece.position).lerp(target, 0.1).lerp(zoomVector, 0.95 * Math.random());
        moveTime = moveTimeMin + Math.floor(Math.random() * (moveTImeMax - moveTimeMin));
        offsetTime = 100 + 10 * Math.floor(Math.random() * offsetTimeMax);
        twn_zoom = new TWEEN.Tween(piece.position).to(zoompos, moveTime * 5).easing(TWEEN.Easing.Quadratic.Out).delay(offsetTime);
        twn_target = new TWEEN.Tween(piece.position).to(target, moveTime * 5).easing(TWEEN.Easing.Quadratic.In);
        twn_zoom.chain(twn_target);
        tweenList.push(twn_zoom);
        cnt += 1;
      }
      projector = new THREE.Projector();
      $(renderer.domElement).bind('mousedown', function(e) {
        var mouseX2D, mouseX3D, mouseY2D, mouseY3D, obj, ray, tmp_id, vec;
        mouseX2D = e.clientX - e.target.offsetLeft;
        mouseY2D = e.clientY - e.target.offsetTop;
        mouseX3D = (mouseX2D / e.target.width) * 2 - 1;
        mouseY3D = (mouseY2D / e.target.height) * -2 + 1;
        console.log("click:", mouseX3D, ":", mouseY3D);
        vec = new THREE.Vector3(mouseX3D, mouseY3D, -1);
        projector.unprojectVector(vec, camera);
        ray = new THREE.Raycaster(camera.position, vec.sub(camera.position).normalize());
        obj = ray.intersectObjects(scene.children, true);
        if (obj.length > 0) {
          console.log(obj[0].object.material.map.image.outerHTML);
          $("#selectedThumnail").attr("src", $(obj[0].object.material.map.image.outerHTML).attr("src"));
          $("#selectedThumnail").attr("opacity", 0.5);
          tmp_id = obj[0].object.fb_image_id;
          return ajaxpath = '/common/mosaic_viewer//ajax/fb_image/' + tmp_id;
        } else {
          return console.log("no object");
        }
      });
      isTweenInitiaized = false;
      $('canvas').mouseup(function() {
        var twn, _j, _len1;
        if (!isTweenInitiaized) {
          for (_j = 0, _len1 = tweenList.length; _j < _len1; _j++) {
            twn = tweenList[_j];
            twn.start();
          }
          return isTweenInitiaized = true;
        }
      });
      $(window).bind('resize', function() {
        width = $('#canvasField').innerWidth();
        height = window.innerHeight - 150;
        renderer.setSize(width, height);
        camera.aspect = width / height;
        return camera.updateProjectionMatrix();
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
