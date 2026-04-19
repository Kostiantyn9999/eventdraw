var commandManager = new CommandManager();

document.addEventListener("DOMContentLoaded", function () {  

  window.addEventListener('keydown', (event) => {
    // Check if the Control key is pressed along with 'z' or 'y'
    if (event.ctrlKey) {
      // For most browsers, event.key gives 'z' or 'y'
      if (event.key.toLowerCase() === 'z') {
        event.preventDefault();
        commandManager.undo();
      } else if (event.key.toLowerCase() === 'y') {
        event.preventDefault();
        commandManager.redo();
      }
    }
  });

  $('#cmd-undo').click(function () {
    commandManager.undo();
  });

  $('#cmd-redo').click(function () {
    commandManager.redo();
  });

  $.getJSON("./assets/data/scenes.json").then(async (data) => {
    const models = new ShapePosition();
    const settingBackground = new SettingBackground();
    settingBackground.fireOnBackground(models.onChangedBackground);

    const modelStore = new ShapeModels(models);
    const planViewer = new PlanViewer({
      container: "container",
      scenes: models,
      store: modelStore,
    });
    planViewer.fireBackground(settingBackground.newOnBackground);
    new CanvasExport(settingBackground);
  });

  $("#full-screen-button").click(function () {
    var fullscreenElement =
      document.fullscreenElement ||
      document.mozFullScreenElement ||
      document.webkitFullscreenElement ||
      document.msFullscreenElement;
    if (fullscreenElement == null) {
      launchIntoFullscreen(document.documentElement);
      $("#full-screen-button").attr("title", "Exit full screen");
      $("#full-screen-button").addClass("compress");
    } else {
      exitFullscreen();
      $("#full-screen-button").attr("title", "Enter full screen");
      $("#full-screen-button").removeClass("compress");
    }
  });

  // $("#share-icon-button").click(function () {

  //   fetch("upload_scene.php", {
  //     method: "POST",
  //   })
  //     .then((response) => response.json())
  //     .then((success) => { console.log(success)});
  //   // var parent = document.createElement("div");
  //   // parent.innerHTML = `<input id='share_link' type='text' value='${window.location.href}' />`;

  //   // swal({
  //   //   title: "Share Plan via link",
  //   //   content: parent,
  //   //   buttons: {
  //   //     showCancelButton: false,
  //   //     confirmButtonText: "Copy",
  //   //     reverseButtons: false,
  //   //   },
  //   // }).then((clicked) => {
  //   //   if (clicked) {
  //   //     const copyText = document.querySelector("#share_link");
  //   //     copyText.select();
  //   //     document.execCommand("copy");

  //   //     swal("Copied!");
  //   //   }
  //   // });
  // });

  let hoverTimeout = null;
  $(document)
    .on("mouseover", ".square-button[data-title]", function (e) {
      if (
        !$("#tutorial").hasClass("show") ||
        ($("#tutorial").attr("topic") !== "tour2" &&
          $("#tutorial").attr("topic") !== "360" &&
          $("#tutorial").attr("topic") !== "2D")
      ) {
        $(".square-button[data-title].hover").removeClass("hover");
        $(this).addClass("hover");
        clearTimeout(hoverTimeout);
        hoverTimeout = setTimeout(function () {
          $(".square-button[data-title].hover").removeClass("hover");
        }, 3000);
      }
    })
    .on("mouseout", ".square-button[data-title]", function (e) {
      $(this).removeClass("hover");
    });
});

function launchIntoFullscreen(element) {
  if (element.requestFullscreen) {
    element.requestFullscreen();
  } else if (element.mozRequestFullScreen) {
    element.mozRequestFullScreen();
  } else if (element.webkitRequestFullscreen) {
    element.webkitRequestFullscreen();
  } else if (element.msRequestFullscreen) {
    element.msRequestFullscreen();
  }
}

function exitFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) {
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) {
    document.msExitFullscreen();
  }
}
