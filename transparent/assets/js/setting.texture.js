var SettingTextureEditor = function (parameter) {
  const main = this;

  this.scene = parameter;

  main.modellist;
  main.selectModels = [];

  main.fnInit = function () {
    // $.getJSON("./assets/data/texture.json")
    // .then((data) =>  {
    //     data.forEach(element => {
    //         var html = `<li><a href="#tab-${element.id}"><img src="${element.thumb}"><span>${element.name}</span></a></li>`;
    //         $("#tab-links").append(html);
    //         var textHtml = "";
    //         element.children.forEach((texture)=>{
    //             textHtml += `<div class="material-item"><img src="${texture.path}"></div>`;
    //         });
    //         var sectionHtml =   `<section id="tab-${element.id}">
    //                             <div class="tab-content">
    //                             ${textHtml}
    //                             </div>
    //                             </section>`;
    //         $(".tabs").append(sectionHtml);
    //     });
    //     const tabLinks = $('#tab-links li a');
    //     // Handle link clicks.
    //     tabLinks.click(function(event) {
    //         var $this = $(this);
    //         // Prevent default click behaviour.
    //         event.preventDefault();
    //         // Remove the active class from the active link and section.
    //         $('#tab-links a.active, section.active').removeClass('active');
    //         // Add the active class to the current link and corresponding section.
    //         $this.addClass('active');
    //         $($this.attr('href')).addClass('active');
    //     });
    //     $(".material-item").click(function(e) {
    //         console.log($(this).children("img"));
    //         var path = $(this).find("img").attr("src");
    //         // console.log(path);
    //         var texture = new THREE.TextureLoader().load( path );
    //         texture.needsUpdate= true;
    //         texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
    //         texture.offset.set( 0, 0 );
    //         texture.repeat.set(0.05, 0.05);
    //         console.log(main.activedModel);
    //         main.activedModel.data.traverse((child) => {
    //             if ( child.isMesh ) {
    //                 console.log(child);
    //                 // var material = new THREE.MeshStandardMaterial({
    //                 //     side: THREE.DoubleSide,
    //                 // });
    //                 child.material.color = undefined;
    //                 child.material.map = texture;
    //                 // material.map = texture;
    //                 // material.material = true;
    //                 // child.material = material;
    //                 child.material.needsUpdate = true;
    //             }
    //         });
    //     });
    // });
  };

  main.setModelList = function (list) {
    main.modellist = list;
  };

  main.setModelsInfo = function (list) {
    main.modelsInfo = list;
  };

  main.setActiveShape = function (model) {
    main.selectModels = [];

    main.activedModel = model;

    main.activedModel.data.traverse((child) => {
      if (child.isGroup) {
        if (main.modellist.includes(child.name)) {
          if (!main.selectModels.includes(child.name)) {
            main.selectModels.push(child.name);
          }
        }
      }
    });

    var selectedModelInfos = [];
    main.selectModels.forEach((shape) => {
      main.modelsInfo.forEach((oneModel) => {
        if (oneModel.name == shape) {
          selectedModelInfos.push(oneModel);
        }
      });
    });
    // var pE = document.getElementById("selected-model-list");
    // pE.innerHTML = "";
    // selectedModelInfos.forEach((selectedModel) => {
    //     var settingElement = document.createElement("div");
    //     settingElement.classList.add("propertiesView");
    //     var tableChtml = "";
    //     selectedModel.option.forEach((option) => {
    //         tableChtml += `<tr class="mesh-color-line">
    //             <td width="160">${option.name}</td>
    //             <td width="40"><input type="checkbox" class="check-gradiant"></td>
    //             <td width="100" class="color-group-pan" data-model="${option.model}">
    //                 <div class="color-pan col-pan"></div>
    //                 <div class="gradient-pan col-pan"></div>
    //             </td>
    //          </tr>`
    //     });

    //     var tableHtml = `<table>
    //         <tbody>
    //             ${tableChtml}
    //         </tbody>
    //     </table>`;

    //     var html = `<h3>${selectedModel.name}</h3>
    //     <input class="chk-all" type="checkbox" style="margin:0">All
    //     ${tableHtml}`;

    //     settingElement.innerHTML = html;
    //     pE.append(settingElement);
    // });

    $(".check-gradiant").change(function () {
      // console.log("chcked");
      if ($(this).is(":checked")) {
        $(this).addClass("active");

        $(this)
          .closest(".mesh-color-line")
          .find(".color-group-pan")
          .addClass("gradient");
      } else {
        $(this)
          .closest(".mesh-color-line")
          .find(".color-group-pan")
          .removeClass("gradient");
      }
    });

    $(".color-pan").colpick({
      colorScheme: "light",
      layout: "rgbhex",
      color: "ff8800",
      onSubmit: function (hsb, hex, rgb, el) {
        $(el).css("background-color", "#" + hex);
        $(el).colpickHide();

        // console.log($(el).closest('.color-group-pan').hasClass("gradient"));
        // check gradient state
        const state = $(el).closest(".color-group-pan").hasClass("gradient");

        if (state) {
          $(".gradient").removeClass("active");
          $(el).closest(".color-group-pan").addClass("active");

          $(el).closest(".color-group-pan").data("first", hex);
          main.setGradient();
        } else {
          main.setColor(rgb.r, rgb.g, rgb.b);
        }
      },
    });

    $(".gradient-pan").colpick({
      colorScheme: "light",
      layout: "rgbhex",
      color: "ff8800",
      onSubmit: function (hsb, hex, rgb, el) {
        $(el).css("background-color", "#" + hex);
        $(el).colpickHide();

        $(".gradient").removeClass("active");
        $(el).closest(".color-group-pan").addClass("active");
        // main.setColor(rgb.r, rgb.g, rgb.b);
        $(el).closest(".color-group-pan").data("second", hex);
        main.setGradient();
      },
    });

    $(".color-pan").click(function () {
      $(".color-pan").removeClass("active");
      $(this).addClass("active");
    });

    $("input.chk-all").click(function () {
      $(this).closest(".propertiesView").data("all", $(this).is(":checked"));
    });
  };

  main.setGradient = function () {
    var element = $(".gradient.active");

    const firstC = element.data("first");
    const secondC = element.data("second");

    if (firstC == undefined || secondC == undefined) return;

    const checkAll = element.closest(".propertiesView").data("all");
    var modelName = element.data("model");

    // return;
    if (checkAll) {
      this.scene.traverse((child) => {
        if (child.isMesh) {
          if (child.name == modelName) {
            child.geometry.computeBoundingBox();

            var uniforms = {
              color1: {
                value: new THREE.Color(parseInt("0x" + firstC)),
              },
              color2: {
                value: new THREE.Color(parseInt("0x" + secondC)),
              },
              bboxMin: {
                value: child.geometry.boundingBox.min,
              },
              bboxMax: {
                value: child.geometry.boundingBox.max,
              },
            };

            // Create material
            var material = new THREE.ShaderMaterial({
              uniforms: uniforms,
              vertexShader: `
                  uniform vec3 bboxMin;
                  uniform vec3 bboxMax;
              
                  varying vec2 vUv;

                  void main() {
                  vUv.y = (position.y - bboxMin.y) / (bboxMax.y - bboxMin.y);
                  gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0);
                  }
              `,
              fragmentShader: `
                uniform vec3 color1;
                uniform vec3 color2;
            
                varying vec2 vUv;
                
                void main() {
                
                gl_FragColor = vec4(mix(color1, color2, vUv.y), 1.0);
                }
            `,
              // wireframe: true,
              side: THREE.DoubleSide,
            });

            child.material = material;
            child.material.needsUpdate = true;
          }
        }
      });
    } else {
      main.activedModel.data.traverse((child) => {
        if (child.isMesh) {
          if (child.name == modelName) {
            child.geometry.computeBoundingBox();

            var uniforms = {
              color1: {
                value: new THREE.Color(parseInt("0x" + firstC)),
              },
              color2: {
                value: new THREE.Color(parseInt("0x" + secondC)),
              },
              bboxMin: {
                value: child.geometry.boundingBox.min,
              },
              bboxMax: {
                value: child.geometry.boundingBox.max,
              },
            };

            // Create material
            var material = new THREE.ShaderMaterial({
              uniforms: uniforms,
              vertexShader: `
                uniform vec3 bboxMin;
                uniform vec3 bboxMax;
            
                varying vec2 vUv;

                void main() {
                vUv.y = (position.y - bboxMin.y) / (bboxMax.y - bboxMin.y);
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0);
                }
              `,
              fragmentShader: `
                uniform vec3 color1;
                uniform vec3 color2;
            
                varying vec2 vUv;
                
                void main() {
                
                gl_FragColor = vec4(mix(color1, color2, vUv.y), 1.0);
                }
              `,
              // wireframe: true,
              side: THREE.DoubleSide,
            });

            child.material = material;
            child.material.needsUpdate = true;
          }
        }
      });
    }
  };

  main.setColor = function (r, g, b) {
    // console.log(r, g, b);

    $(".color-pan.active").css("background", `rgb(${r}, ${g}, ${b})`);

    // console.log($('.color-pan.active').data('model'));

    var modelName = $(".color-pan.active")
      .closest(".color-group-pan")
      .data("model");

    if ($(".color-pan.active").closest(".propertiesView").data("all")) {
      this.scene.traverse((child) => {
        if (child.isMesh) {
          if (child.name == modelName) {
            var material = new THREE.MeshStandardMaterial({
              color: new THREE.Color().setRGB(r / 256, g / 256, b / 256), // red
              side: THREE.DoubleSide,
            });

            // child.material.color =  new THREE.Color().setRGB(r/ 256, g / 256, b / 256);
            // child.material.map = texture;

            child.material = material;
            child.material.needsUpdate = true;
          }
        }
      });
    } else {
      main.activedModel.data.traverse((child) => {
        if (child.isMesh) {
          // console.log(child);

          if (child.name == modelName) {
            var material = new THREE.MeshStandardMaterial({
              color: new THREE.Color().setRGB(r / 256, g / 256, b / 256), // red
              side: THREE.DoubleSide,
            });

            // child.material.color =  new THREE.Color().setRGB(r/ 256, g / 256, b / 256);
            // child.material.map = texture;

            child.material = material;
            child.material.needsUpdate = true;
          }
        }
      });
    }
  };

  main.fnInit();
};
