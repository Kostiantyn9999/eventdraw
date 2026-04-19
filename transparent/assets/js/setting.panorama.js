var SettingPanorama = function (parameter) {
  const main = this;

  main.spotPoint = [];
  main.spotGroup = new THREE.Group();
  main.scene = parameter;

  main.init = function () {
    main.scene.add(main.spotGroup);

    $("#btn-add-points").click(() => {
      // $("mySidebar").style({});
      document.getElementById("add-points").style.width = "250px";
    });

    $("#btn-close-add-points").click(() => {
      document.getElementById("add-points").style.width = "0";
    });

    $("#btn-add-points-apply").click(() => {
      // document.getElementById("add-points").style.width = "0";
      const x = $("#add-x-point").val();
      const y = $("#add-y-point").val();
      const z = $("#add-z-point").val();

      // console.log("click", x, y, z);

      const html = `<li>
                              <span>X</span><span>${x}</span>
                              <span>Y</span><span>${y}</span>
                              <span>Z</span><span>${z}</span>
                              <span class="badge">10</span>
                          </li>`;

      const geometry = new THREE.RingGeometry(20, 30, 32);
      const material = new THREE.MeshBasicMaterial({
        color: 0xff0000,
        transparent: true,
        opacity: 0.8,
        side: THREE.DoubleSide,
      });
      mesh = new THREE.Mesh(geometry, material);
      mesh.rotation.x = Math.PI / 2;
      mesh.position.set(x, y, z);

      main.spotGroup.add(mesh);

      $("#spot-point-lists").append(html);
    });

    $("#add-x-point").change(() => {});

    $("#add-y-point").change(() => {});

    $("#add-z-point").change(() => {});
  };

  main.init();
};
