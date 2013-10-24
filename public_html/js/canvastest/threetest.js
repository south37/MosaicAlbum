// Generated by CoffeeScript 1.6.3
$(function() {
  return window.addEventListener("DOMContentLoaded", function() {
    var mosaicImagePath, selectedImagePath;
    $('#showMosaic').tooltip({
      placement: 'top',
      title: 'くりっくしてね',
      triger: 'hover'
    });
    $('#modal1 .modal-header').empty().append("members:xx,oo").append('<button id="closeModal" class="btn">x</button>');
    $('#modal1 .modal-body').empty();
    $('#modal1 .modal-footer').empty().append('右クリックで保存できます ').append('<button id="fb_share" class="btni btn-primary">facebookでshare</button>');
    mosaicImagePath = "";
    selectedImagePath = "";
    $('#showMosaic').click(function() {
      $('#modal1 .modal-body').empty().append("<img src=" + mosaicImagePath + " alt='modaicImg'></img>");
      $('#modal1').modal('toggle');
      return console.log(mosaicImagePath);
    });
    $('#showSelect').click(function() {
      $('#modal1 .modal-body').empty().append("<img src=" + selectedImagePath + " alt='selectedImg'></img>");
      $('#modal1').modal('toggle');
      return console.log(selectedImagePath);
    });
    $('#closeModal').click(function() {
      return $('#modal1').modal('toggle');
    });
    $('#fb_share').click(function() {
      return alert("shareしたよ");
    });
    return $.getJSON("/common/mosaic_viewer/ajax_list", function(data) {
      var anim, aspect, camera, cameraPosition, cnt, col, delaytime, directioalLight, farClip, fbIconGeometry, fbIconMaterials, fbIconMaterials_, fbIconTexList, fbUserIdList, fbUserInfoList, fov, height, info, isTweenInitiaized, lookTarget, material, mosaicPieceGeometry, mosaicPieceMap, mosaicPieceMaterials, mosaicPieceMaterials_, mosaicPiecePathList, mosaicPieceTexList, movetime, nearClip, path, piece, piecedata, pieces, pieces_tween, position, projector, renderer, row, scene, sizeX, sizeY, target, tex, texInfo, tmpTex, trackball, twn, userInfo, userPosList, width, _i, _j, _k, _l, _len, _len1, _len2, _len3, _ref, _ref1;
      console.log(data);
      mosaicImagePath = data.mosaicImage;
      width = window.innerWidth;
      height = window.innerHeight - 100;
      width = $('#container').innerWidth();
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
      fbUserInfoList = data.userInfo;
      fbUserIdList = (function() {
        var _i, _len, _results;
        _results = [];
        for (_i = 0, _len = fbUserInfoList.length; _i < _len; _i++) {
          info = fbUserInfoList[_i];
          _results.push(info.userID);
        }
        return _results;
      })();
      fbIconTexList = (function() {
        var _i, _len, _results;
        _results = [];
        for (_i = 0, _len = fbUserInfoList.length; _i < _len; _i++) {
          info = fbUserInfoList[_i];
          _results.push(new THREE.ImageUtils.loadTexture(info.iconPath));
        }
        return _results;
      })();
      fbIconMaterials = (function() {
        var _i, _len, _results;
        _results = [];
        for (_i = 0, _len = fbIconTexList.length; _i < _len; _i++) {
          tex = fbIconTexList[_i];
          _results.push(new THREE.MeshBasicMaterial({
            map: tex,
            side: THREE.DoubleSide
          }));
        }
        return _results;
      })();
      fbIconMaterials_ = {};
      for (_i = 0, _len = fbUserInfoList.length; _i < _len; _i++) {
        userInfo = fbUserInfoList[_i];
        tmpTex = new THREE.ImageUtils.loadTexture(userInfo.iconPath);
        fbIconMaterials_[userInfo.userID] = new THREE.MeshBasicMaterial({
          map: tmpTex,
          side: THREE.DoubleSide
        });
      }
      mosaicPiecePathList = data.mosaicTextures;
      mosaicPieceTexList = (function() {
        var _j, _len1, _results;
        _results = [];
        for (_j = 0, _len1 = mosaicPiecePathList.length; _j < _len1; _j++) {
          path = mosaicPiecePathList[_j];
          _results.push(new THREE.ImageUtils.loadTexture(path));
        }
        return _results;
      })();
      mosaicPieceMaterials = (function() {
        var _j, _len1, _results;
        _results = [];
        for (_j = 0, _len1 = mosaicPieceTexList.length; _j < _len1; _j++) {
          tex = mosaicPieceTexList[_j];
          _results.push(new THREE.MeshBasicMaterial({
            map: tex,
            side: THREE.DoubleSide
          }));
        }
        return _results;
      })();
      mosaicPieceMaterials_ = {};
      _ref = data.mosaicPieceMap;
      for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
        texInfo = _ref[_j];
        tmpTex = new THREE.ImageUtils.loadTexture(texInfo.path);
        mosaicPieceMaterials_[texInfo.image_id] = new THREE.MeshBasicMaterial({
          map: tmpTex,
          side: THREE.DoubleSide
        });
      }
      mosaicPieceMap = {
        "118": 0,
        "119": 1,
        "120": 2,
        "121": 3,
        "122": 4,
        "123": 5,
        "124": 6,
        "125": 7,
        "126": 8
      };
      row = 80;
      col = 60;
      sizeX = 1000 / col;
      sizeY = 1000 / row;
      sizeX = 100;
      sizeY = 100;
      fbIconGeometry = new THREE.PlaneGeometry(sizeX, sizeY, 1, 1);
      userPosList = {};
      sizeX = 10;
      sizeY = 10;
      mosaicPieceGeometry = new THREE.PlaneGeometry(sizeX, sizeY, 1, 1);
      pieces = [];
      pieces_tween = [];
      cnt = 0;
      for (_k = 0, _len2 = fbIconMaterials.length; _k < _len2; _k++) {
        material = fbIconMaterials[_k];
        piece = new THREE.Mesh(fbIconGeometry, material);
        position = new THREE.Vector3(100 * cnt, -300, 100);
        piece.position.copy(position);
        scene.add(piece);
        userPosList[fbUserIdList[cnt]] = position;
        cnt += 1;
      }
      console.log(userPosList);
      cnt = 0;
      _ref1 = data.mosaicPieces;
      for (_l = 0, _len3 = _ref1.length; _l < _len3; _l++) {
        piecedata = _ref1[_l];
        piece = new THREE.Mesh(mosaicPieceGeometry, mosaicPieceMaterials[mosaicPieceMap[piecedata.image_id]]);
        piece.position.copy(userPosList[piecedata.user_id]);
        piece.fb_image_id = piecedata.fb_image_id;
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
        var mouseX2D, mouseX3D, mouseY2D, mouseY3D, obj, ray, tmp_id, vec;
        console.log("rendererclicked");
        mouseX2D = e.clientX - e.target.clientLeft;
        mouseY2D = e.clientY - e.target.clientTop;
        mouseX3D = (mouseX2D / e.target.width) * 2 - 1;
        mouseY3D = (mouseY2D / e.target.height) * -2 + 1;
        vec = new THREE.Vector3(mouseX3D, mouseY3D, -1);
        projector.unprojectVector(vec, camera);
        ray = new THREE.Raycaster(camera.position, vec.sub(camera.position).normalize());
        obj = ray.intersectObjects(scene.children, true);
        if (obj.length > 0) {
          tmp_id = obj[0].object.fb_image_id;
          path = '/common/mosaic_viewer/ajax_fb_image/' + tmp_id;
          return $.getJSON(path, function(data) {
            console.log(data);
            return selectedImagePath = data.fb_image_path;
          });
        } else {
          return console.log("no clicked object");
        }
      });
      isTweenInitiaized = false;
      $('canvas').mouseup(function() {
        var _len4, _m;
        if (!isTweenInitiaized) {
          console.log("tweenset");
          for (_m = 0, _len4 = pieces_tween.length; _m < _len4; _m++) {
            twn = pieces_tween[_m];
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
      $(window).bind('resize', function() {
        console.log("window resize");
        width = $('#container').innerWidth();
        height = window.innerHeight - 100;
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
