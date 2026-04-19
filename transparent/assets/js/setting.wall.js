var SettingWall = function (parameter1, parameter2, model) {
  const main = this;

  this.measKoeff = 39.37;
  this.wallHeight = 3 * this.measKoeff;
  this.texture = { type: 0, value: { r: 200, g: 200, b: 200 } };
  this.opacity = 1;

  this.engine = parameter1;
  this.group = parameter2;
  this.model = model;

  this.wallModels = [];
  this.doors = [];

  this.arts = [];

  this.activedWall = null;
  this.activedArt = null;

  this.onChangedWallTexture = $.Callbacks();
  this.onChangedWallHeight = $.Callbacks();
  this.onChangedWallOpacity = $.Callbacks();

  this.onArtImported = $.Callbacks();
  this.onArtMoved = $.Callbacks();
  this.onArtRotated = $.Callbacks();
  this.onArtRemoved = $.Callbacks();

  this.artDefaultHeight = 300;
  this.referenceCameraY = 152.45062472030136; // Reference camera Y position
  this.referenceDistance = 500; // Reference distance

  main.artRemove = (fileName) => {
    const subDir = main.engine.scenes.mode === "TOKEN" ? 'token.' + main.engine.scenes.token : 'shared.' + main.engine.scenes.shared;
    const formData = new FormData();
    formData.append('path', subDir + "/" + fileName);
    formData.append('type', "delete");

    fetch("upload.php", {
      method: "POST",
      body: formData,
    }).then(
      (response) => response.json()
    ).then((result) => {
    }).catch(
      (error) => console.log(error) // Handle the error response object
    );
  };
  
  main.setArtImage = function (type, fileName, putArt = false) {
    const subDir = main.engine.scenes.mode === "TOKEN" ? 'token.' + main.engine.scenes.token : 'shared.' + main.engine.scenes.shared;
    const path = "uploads/" + subDir + "/" + fileName;
    
    var cancelButton = document.createElement("div");
    cancelButton.innerHTML = 'X';
    cancelButton.style.position = 'absolute';
    cancelButton.style.top = '-8px'; // Adjust as needed
    cancelButton.style.right = '-8px'; // Adjust as needed
    cancelButton.style.backgroundColor = 'red';
    cancelButton.style.color = 'white';
    cancelButton.style.padding = '5px';
    cancelButton.style.cursor = 'pointer';
    cancelButton.style.borderRadius = '50%';
    cancelButton.style.fontSize = '10px';
    cancelButton.style.lineHeight = '10px';
    cancelButton.style.textAlign = 'center';
    cancelButton.style.width = '6px';
    cancelButton.style.height = '6px';
    cancelButton.style.display = 'flex';
    cancelButton.style.justifyContent = 'center';
    cancelButton.style.alignItems = 'center';
    cancelButton.style.fontFamily = 'sans-serif';

    if (type === 0) {
      if (putArt) {
        const id = "image-" + Util.guid();
        main.onArtImported.fire(0, id, path);
      }

      if ($(main.artTextureElement).find(".wall-color-element").length == 0) {
        $(main.artTextureElement).html("");
      }
      var artElement = document.createElement("div");
      artElement.append(cancelButton)
      artElement.classList.add("wall-color-element");
      artElement.style.cssText = `position: relative; background: url('${path}'); background-repeat: no-repeat; background-size: contain; background-position: center;`;
      main.artTextureElement.append(artElement);
      
      artElement.addEventListener("click", (e) => {
        if (e.target == cancelButton) {
          return
        }
        const newId = "image-" + Util.guid();
        main.onArtImported.fire(0, newId, path);
      });
      cancelButton.addEventListener('click', function() {
        main.arts.forEach((element) => {
          if (element.userData.path == path) {
            main.onArtRemoved.fire(element); 
          }
        });
        main.artRemove(fileName);
        artElement.remove();
        if ($(main.artTextureElement).find(".wall-color-element").length == 0) {
          $(main.artTextureElement).html("No Image");
        }
      });
    } else if (type === 1) {
      if (putArt) {
        const id = "image-" + Util.guid();
        main.onArtImported.fire(1, id, path);
      }

      if ($(main.videoTextureElement).find(".wall-color-element").length == 0) {
        $(main.videoTextureElement).html("");
      }
      var videoElement = document.createElement("div");
      videoElement.style.position = "relative";
      videoElement.classList.add("wall-color-element");
      videoElement.innerHTML = `<video style="width:100%; height:100%" preload="true" autoplay="true"><source src="${path}" /></video>`;
      videoElement.append(cancelButton);
      main.videoTextureElement.append(videoElement);

      videoElement.addEventListener("click", (e) => {
        if (e.target == cancelButton) {
          return
        }
        const newId = "video-" + Util.guid();
        main.onArtImported.fire(1, newId, path);
      });
      cancelButton.addEventListener('click', function (e) {
        main.arts.forEach((element) => {
          if (element.userData.path == path) {
            main.onArtRemoved.fire(element); 
          }
        });
        main.artRemove(fileName);
        videoElement.remove();
        if ($(main.videoTextureElement).find(".wall-color-element").length == 0) {
          $(main.videoTextureElement).html("No Video");
        }
      });
    }
  }

  main.initArtLoad = function () {
    const subDir = main.engine.scenes.mode === "TOKEN" ? 'token.' + main.engine.scenes.token : 'shared.' + main.engine.scenes.shared;
    const formData = new FormData();
    formData.append('path', subDir + "/");
    formData.append('type', "getfile");

    fetch("upload.php", {
      method: "POST",
      body: formData,
    }).then(
      (response) => response.json()
    ).then((result) => {
      for (let i = 0; i < result.image.length; i++) {
        main.setArtImage(0, result.image[i]);
      }
      for (let i = 0; i < result.media.length; i++) {
        main.setArtImage(1, result.media[i]);
      }
    }).catch(
      (error) => console.log(error) // Handle the error response object
    );
  }

  main.fnInit = function () {
    $("#btn-setting-wall").click(() => {
      $(".sidebar").css("width", "0");
      document.getElementById("setting-wall").style.width = "250px";

      this.engine.settingPosSize.unSelectedModels();
    });

    $("#btn-close-wall").click(() => {
      document.getElementById("setting-wall").style.width = "0";
    });

    this.parentElement = document.getElementById("wall-color-setting");
    this.parentTextureElement = document.getElementById("wall-texture-setting");

    this.videoTextureElement = document.getElementById("wall-video-texture-setting");
    this.artTextureElement = document.getElementById("wall-art-texture-setting");

    main.initArtLoad();

    $.getJSON("./assets/data/color.json").then((data) => {
      data.forEach((element) => {
        var colorElement = document.createElement("div");
        colorElement.classList.add("wall-color-element");

        colorElement.style.cssText = `background: rgb(${element.r}, ${element.g}, ${element.b})`;
        this.parentElement.append(colorElement);

        colorElement.addEventListener("click", () => {
          main.onChangedWallTexture.fire(0, {
            r: element.r,
            g: element.g,
            b: element.b,
          });
        });
      });
    });

    $("#wall-picker")
      .colpick({
        layout: "hex",
        submit: 0,
        colorScheme: "light",
        onChange: function (hsb, hex, rgb) {
          $("#wall-picker")
            .val(hex)
            .css("border-color", "#" + hex);
          main.onChangedWallTexture.fire(0, { r: rgb.r, g: rgb.g, b: rgb.b });
        },
      })
      .keyup(function () {
        $(this).colpickSetColor(this.value);
      });

    $("#btn-reset-Wall").click(() => {
      main.onChangedWallTexture.fire(0, { r: 214, g: 214, b: 214 });
    });

    $.getJSON("./assets/data/wall-texture.json").then((data) => {
      data.maps.forEach((element) => {
        var colorElement = document.createElement("div");
        colorElement.classList.add("wall-color-element");
        colorElement.style.cssText = `background: url('assets/${element.path}'); background-size: contain;`;
        this.parentTextureElement.append(colorElement);

        colorElement.addEventListener("click", () => {
          main.onChangedWallTexture.fire(1, element.path);
        });
      });
    });

    var handlesSlider = document.getElementById("slider-wall-opacity");
    noUiSlider.create(handlesSlider, {
      start: [0],
      range: {
        min: [0],
        max: [1],
      },
      connect: true,
    });

    handlesSlider.noUiSlider.set(0.15);

    handlesSlider.noUiSlider.on("update", function () {
      const opaicty = handlesSlider.noUiSlider.get();
      main.onChangedWallOpacity.fire(opaicty);
    });

    $("#btn-reset-wall-opacity").click(() => {
      handlesSlider.noUiSlider.set(0.15);
    });

    $("#ipt-set-height").on("input", (e) => {
      var depth = e.target.value;

      main.onChangedWallHeight.fire(depth * main.measKoeff);
    });

    const input = document.getElementById("upload-art");

    // This will upload the file after having read it
    const upload = (files) => {
      const formData = new FormData();
      for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
      }
      const subDir = main.engine.scenes.mode === "TOKEN" ? 'token.' + main.engine.scenes.token : 'shared.' + main.engine.scenes.shared;
      formData.append('sub_dir', subDir);

      fetch("upload.php", {
        method: "POST",
        body: formData,
      }).then(
        (response) => response.json()
      ).then((result) => {
        $(input).val("");
        for (let i = 0; i < result.image.length; i++) {
          main.setArtImage(0, result.image[i]);
        }
        for (let i = 0; i < result.media.length; i++) {
          main.setArtImage(1, result.media[i]);
        }
      }).catch(
        (error) => console.log(error) // Handle the error response object
      );
    };

    const onSelectFile = () => upload(input.files);

    // Add a listener on your input
    // It will be triggered when a file will be selected
    input.addEventListener("change", onSelectFile, false);

    $("#art-width").on("input", (e) => {
      var width = $("#art-width").val();

      this.activedArt.geometry.computeBoundingBox();
      var height =
        this.activedArt.geometry.boundingBox.max.x -
        this.activedArt.geometry.boundingBox.min.x;

      this.activedArt.scale.x = width / height;

      if ($("#art-aspect-ratio").is(":checked")) {
        this.activedArt.scale.y = width / height;
        var oheight =
          this.activedArt.geometry.boundingBox.max.y -
          this.activedArt.geometry.boundingBox.min.y;
        var calch = (oheight * width) / height;
        
        $("#art-height").val(calch.toFixed(2));
      }
    });

    $("#art-height").on("input", (e) => {
      var height = $("#art-height").val();

      this.activedArt.geometry.computeBoundingBox();
      var oheight =
        this.activedArt.geometry.boundingBox.max.y -
        this.activedArt.geometry.boundingBox.min.y;

      this.activedArt.scale.y = height / oheight;
      
      if ($("#art-aspect-ratio").is(":checked")) {
        this.activedArt.scale.x = height / oheight;
        var calcH =
          this.activedArt.geometry.boundingBox.max.x -
          this.activedArt.geometry.boundingBox.min.x;
        $("#art-width").val(((calcH * height) / oheight).toFixed(2));
      }
    });

    $("#art-rotation").on("input", (e) => {
      var rotation = $("#art-rotation").val();

      const id = this.activedArt.userData.id;
      this.onArtRotated.fire(id, (Math.PI * rotation) / 180);
    });

    $("#art-base").on("input", (e) => {
      var positionY = $("#art-base").val();
      this.activedArt.position.y = positionY;
    });

    $("#btn-art-remove").click((e) => {
      this.onArtRemoved.fire(this.activedArt);
      $("#art-setting").addClass("dismiss").removeClass("selected").hide();
    });

    main.fireOnChangedWallHeight(main.fnChangeWallHeight);
    main.fireOnChangedWallOpacity(main.fnChangedWallOpacity);
    main.fireOnChangedWallTexture(main.fnChangeTexture);
    main.fireOnImportedArt(main.fnImportedArt);
    main.fireOnMovedArt(main._fnMovedArt);
    main.fireOnRotatedArt(main._fnRotationArt);
    main.fireOnRemovedArt(main._fnRemoveArt);
  };

  main.setGroup = function (group) {
    this.group = group;
  };

  main.fnAddDoor = function (door) {
    main.doors.push(door);
  };

  main.fnSetWallGroups = function (model) {
    this.wallModels.push(model);
  };

  main.fnUpdate = function () {
    
  };

  main.createHoleAndDoor = function (textureUpdate = true) {
    main.wallModels.forEach((wallGroup) => {
      main.doors.forEach((door1) => {
        if (wallGroup.checkOverlap(door1)) {
          wallGroup.addWallObjectMesh(door1);
        }
      });

      wallGroup.createHole();
    });
  };

  //https://www.geeksforgeeks.org/find-two-rectangles-overlap/
  main.intersection = function (a, b) {
    if (a.min.x >= b.max.x || b.min.x >= a.max.x) return false;

    // If one rectangle is above other
    if (a.min.z >= b.max.z || b.min.z >= a.max.z) return false;

    return true;
  };

  main.checkWall = function (raycaster) {
    var wallEdgePlanes = this.model.floorplan.wallEdgePlanes();

    var intersects = raycaster.intersectObjects(wallEdgePlanes, false);

    if (intersects.length > 0) {
      this.activedWall = intersects[0];
      return true;
    }

    var models = [];
    this.wallModels.forEach((model) => {
      model.traverse((child) => {
        models.push(child);
      });
    });

    var intersects1 = raycaster.intersectObjects(models, false);

    if (intersects1.length > 0) {
      this.activedWall = intersects1[0];
      return true;
    }

    this.activedWall = null;
    return false;
  };

  main.checkArt = function (raycaster) {
    var intersects = raycaster.intersectObjects(this.arts, false);

    if (intersects.length > 0) {
      this.activedArt = intersects[0].object;

      if ($("#art-setting").hasClass("dismiss")) {
        $("#art-setting").removeClass("dismiss").addClass("selected").show();
      }

      // $(".sidebar").css("width", "0px");

      this.activedArt.geometry.computeBoundingBox();

      var width =
        this.activedArt.geometry.boundingBox.max.x -
        this.activedArt.geometry.boundingBox.min.x;
      var oheight =
        this.activedArt.geometry.boundingBox.max.y -
        this.activedArt.geometry.boundingBox.min.y;

      $("#art-width").val((width * this.activedArt.scale.x).toFixed(2));
      $("#art-height").val((oheight * this.activedArt.scale.y).toFixed(2));
      $("#art-base").val(this.activedArt.position.y);

      return true;
    }

    if ($(".notification-container").hasClass("selected")) {
      $(".notification-container").removeClass("selected").addClass("dismiss");
    }

    this.activedArt = null;
    return false;
  };

  main.moveArt = function (raycaster, cx, cy) {
    main.checkWall(raycaster);
    if (this.activedWall && this.activedArt) {
      const x = this.activedWall.point.x + cx / 2;
      const y = this.activedArt.position.y;
      const z = this.activedWall.point.z + cy / 2;
      
      this.onArtMoved.fire(
        this.activedArt.userData.id,
        new THREE.Vector3(x, y, z)
      );

      var angle = 0;
      if (this.activedWall.object.geometry.type == "Geometry") {
        var normal2 = new THREE.Vector2();
        var normal3 = this.activedWall.object.geometry.faces[0].normal;
        normal2.x = normal3.x;
        normal2.y = normal3.z;

        var refVec = new THREE.Vector2(0, 1.0);

        angle = Util.angle(refVec.x, refVec.y, normal2.x, normal2.y);
        // this.rotation.y = angle;
      } else {
        var normal2 = new THREE.Vector2();
        var normal3 = this.activedWall.face.normal;
        normal2.x = normal3.x;
        normal2.y = normal3.z;

        var refVec = new THREE.Vector2(0, 1.0);

        angle = Util.angle(refVec.x, refVec.y, normal2.x, normal2.y);
      }

      const id = this.activedArt.userData.id;
      this.onArtRotated.fire(id, angle);

      // this.activedArt.translateZ(1);

      if (this.activedArt) return true;
      else return false;
    }

    if (this.activedArt) return true;
    else return false;
  };

  main.fireOnChangedWallHeight = (callback) => {
    this.onChangedWallHeight.add(callback);
  };

  main.fireOnChangedWallOpacity = (callback) => {
    this.onChangedWallOpacity.add(callback);
  };

  main.fireOnChangedWallTexture = (callback) => {
    this.onChangedWallTexture.add(callback);
  };

  main.fireOnImportedArt = (callback) => {
    this.onArtImported.add(callback);
  };

  main.fireOnMovedArt = (callback) => {
    this.onArtMoved.add(callback);
  };

  main.fireOnRotatedArt = (callback) => {
    this.onArtRotated.add(callback);
  };

  main.fireOnRemovedArt = (callback) => {
    this.onArtRemoved.add(callback);
  };

  main.fnChangeWallHeight = (depth) => {
    main.wallModels.forEach((wallGroup) => {
      wallGroup.setHeight(depth);
    });

    main.model.floorplan.walls.forEach((wall) => {
      wall.setHeight(depth);
      wall.fireRedraw();
    });

    this.wallHeight = depth;
  };

  main.fnChangedWallOpacity = (opacity) => {
    main.wallModels.forEach((wallGroup) => {
      wallGroup.children.forEach((wall) => {
        if (wall.isMesh) {
          wall.material.opacity = opacity;
          wall.material.needsUpdate = true;
        }
      });
    });

    main.group.traverse((wall) => {
      if (wall.isMesh) {
        wall.material.opacity = opacity;
        wall.material.needsUpdate = true;
      }
    });

    this.opacity = opacity;
  };

  main.fnChangeTexture = (type, value) => {
    if (type === 0) {
      const { r, g, b } = value;
      main.fnChangeTextureRGB(r, g, b);
    } else if (type === 1) {
      main.fnChangeTextureImage(value);
    }

    main.texture = { type, value };
  };

  main.fnChangeTextureRGB = function (r, g, b) {
    main.wallModels.forEach((wallGroup) => {
      wallGroup.children.forEach((wall) => {
        if (wall.isMesh) {
          wall.material.map = null;
          wall.material.color = new THREE.Color().setRGB(
            r / 256,
            g / 256,
            b / 256
          );
          wall.material.needsUpdate = true;
        }
      });
    });

    main.model.floorplan.walls.forEach((wall) => {
      wall.setColor(new THREE.Color().setRGB(r / 256, g / 256, b / 256));
      wall.fireRedraw();
    });
  };

  main.fnChangeTextureImage = function (path) {
    const TEXTURE_SIZE = 300;

    var texture = new THREE.TextureLoader().load("assets/" + path);
    texture.needsUpdate = true;
    texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
    texture.offset.set(0, 0);

    main.wallModels.forEach((wallGroup) => {
      wallGroup.children.forEach((wall) => {
        if (wall.type == "Mesh") {
          wall.geometry.computeBoundingBox();

          var max = wall.geometry.boundingBox.max;
          var min = wall.geometry.boundingBox.min;

          var height = max.y - min.y;
          var width = max.x - min.x;

          texture.repeat.set(width / TEXTURE_SIZE, height / TEXTURE_SIZE);

          wall.material.map = texture;
          wall.material.color = new THREE.Color().setRGB(
            255 / 256,
            255 / 256,
            255 / 256
          );
          wall.material.needsUpdate = true;
        }
      });
    });

    main.model.floorplan.walls.forEach((wall) => {
      wall.setColor(new THREE.Color(0xffffff));
      wall.setTexture(texture);
      wall.fireRedraw();
    });
  };

  main.fnImportedArt = (type, id, path) => {
    if (type === 0) {
      main._fnInsertArtElement(id, path);
    } else if (type === 1) {
      main._fnCreateVideoPlane(id, path);
    }
  };

  main._fnInsertArtElement = function (id, path) {
    const img = new Image();
    img.src = path;

    img.onload = function() {
      const width = img.naturalWidth;
      const height = img.naturalHeight;
      const aspectRatio = width / height;

      const heightFactor = main.engine.camera.position.y / main.referenceCameraY;
      const distance = main.referenceDistance * heightFactor / 2;
      const cameraPosition = new THREE.Vector3();
      main.engine.camera.getWorldPosition(cameraPosition);
      const cameraDirection = new THREE.Vector3();
      main.engine.camera.getWorldDirection(cameraDirection);
      cameraDirection.multiplyScalar(distance)
      const cameraRotationY = Math.atan2(cameraDirection.x, cameraDirection.z);
      
      var position = new THREE.Vector3(
        main.engine.width / 2 + cameraPosition.x + cameraDirection.x,
        main.artDefaultHeight / 2,
        main.engine.height / 2 +  + cameraPosition.z + cameraDirection.z
      );

      var texture = new THREE.TextureLoader().load(path);
      var planeGeometry = new THREE.PlaneGeometry(main.artDefaultHeight * aspectRatio, main.artDefaultHeight);
      var planeMaterial = new THREE.MeshBasicMaterial({
        map: texture,
        side: THREE.DoubleSide,
      });

      var plane = new THREE.Mesh(planeGeometry, planeMaterial);
      plane.position.copy(position);
      plane.rotation.y = cameraRotationY;
      plane.userData = { id, path };

      main.group.add(plane);
      main.arts.push(plane);
    };
  };

  main._fnCreateVideoPlane = function (id, path) {
    const videoSource = document.createElement('video');
    videoSource.src = path;
    videoSource.controls = true;

    videoSource.onloadedmetadata = function() {
      const width = videoSource.videoWidth;
      const height = videoSource.videoHeight;
      const aspectRatio = width / height;

      const heightFactor = main.engine.camera.position.y / main.referenceCameraY;
      const distance = main.referenceDistance * heightFactor / 2;
      const cameraPosition = new THREE.Vector3();
      main.engine.camera.getWorldPosition(cameraPosition);
      const cameraDirection = new THREE.Vector3();
      main.engine.camera.getWorldDirection(cameraDirection);
      cameraDirection.multiplyScalar(distance)
      const cameraRotationY = Math.atan2(cameraDirection.x, cameraDirection.z);
      
      var position = new THREE.Vector3(
        main.engine.width / 2 + cameraPosition.x + cameraDirection.x,
        main.artDefaultHeight / 2,
        main.engine.height / 2 +  + cameraPosition.z + cameraDirection.z
      );
        
      const video = document.createElement('video');
      video.src = path;
      video.loop = true;
      video.muted = true;
      video.play();

      const videoTexture = new THREE.VideoTexture(video);
      videoTexture.minFilter = THREE.LinearFilter;
      videoTexture.magFilter = THREE.LinearFilter;

      const videoMaterial = new THREE.MeshBasicMaterial({ map: videoTexture, side: THREE.DoubleSide });
      const geometry = new THREE.PlaneGeometry(main.artDefaultHeight * aspectRatio, main.artDefaultHeight); // Adjust the size as needed
      const videoMesh = new THREE.Mesh(geometry, videoMaterial);
      videoMesh.position.copy(position);
      videoMesh.rotation.y = cameraRotationY;
      videoMesh.userData = { id, path };

      main.group.add(videoMesh);
      main.arts.push(videoMesh);
    };
  };

  main._fnMovedArt = (id, position) => {
    this.arts.forEach((element) => {
      if (element.userData.id == id) {
        element.position.copy(position);
      }
    });
  };

  main._fnRotationArt = (id, rotation) => {
    this.arts.forEach((element) => {
      if (element.userData.id == id) {
        element.rotation.y = rotation;
      }
    });
  };

  main._fnRemoveArt = (art) => {
    this.arts = this.arts.filter((d) => d.userData.id != art.userData.id);
    this.group.remove(art);
  };

  main.fnInit();
};