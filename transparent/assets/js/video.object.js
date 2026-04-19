var VideoArt = function (parameter) {
  const main = this;

  this.id = parameter.id;
  this.group = parameter.group;
  this.position = parameter.position;
  this.rotation = parameter.rotation;
  this.url = parameter.url;

  main.fnInit = function () {
    main.video = document.createElement("video");
    main.video.src = this.url;

    main.video.loop = true;
    main.video.load();

    document.addEventListener("click", () => {
      main.video.play();
    })

    // main.video.addEventListener( "loadedmetadata", function (e) {
    //     var width = this.videoWidth,
    //         height = this.videoHeight;
    //         console.log(width, height);
    // }, false );

    main.videocanvas = document.createElement("canvas");
    main.videocanvas.width = 480;
    main.videocanvas.height = 204;

    main.videocanvasctx = main.videocanvas.getContext("2d");
    main.videocanvasctx.fillStyle = "#ff0000";
    main.videocanvasctx.fillRect(0, 0, 480, 204);

    main.videoTexture = new THREE.Texture(main.videocanvas);

    var movieMaterial = new THREE.MeshBasicMaterial({
      side: THREE.DoubleSide,
      overdraw: 0.5,
    });
    movieMaterial.map = main.videoTexture;

    var movieGeometry = new THREE.PlaneGeometry(240, 100, 4, 4);

    var movieMesh = new THREE.Mesh(movieGeometry, movieMaterial);
    movieMesh.position.copy(main.position);
    movieMesh.position.x += 6;
    movieMesh.rotation.x = main.rotation.x;
    movieMesh.rotation.y = main.rotation.y;
    movieMesh.rotation.z = main.rotation.z;

    this.plane = movieMesh;
    this.plane.userData = { id: main.id, path: main.url };
    this.group.add(movieMesh);
  };

  main.fnRender = function () {
    if (main.video.readyState === main.video.HAVE_ENOUGH_DATA) {
      main.videocanvasctx.drawImage(main.video, 0, 0);
      main.videoTexture.needsUpdate = true;
    }
  };

  main.fnInit();
};
