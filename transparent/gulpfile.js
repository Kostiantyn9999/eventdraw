const gulp = require("gulp");
const minify = require("gulp-minify");
const concat = require("gulp-concat");
const rename = require("gulp-rename");
const cleanCss = require("gulp-clean-css");
const uglify = require("gulp-uglify");
const merge = require("merge-stream");
const browserSync = require("browser-sync").create();

// JavaScript compression task
gulp.task("compress-js", function () {
  return gulp
    .src([
      "assets/js/CommandManager.js",
      "assets/js/core/jquery.min.js",
      "assets/js/core/jquery.ui.js",
      "assets/js/three/three.min.js",
      "assets/js/three/ThreeBSP.js",     
      "assets/js/three/CSS3DRender.js",
      "assets/js/three/DDSLoader.js",
      "assets/js/three/DRACOLoader.js",
      "assets/js/three/GLTFExporter.js",
      "assets/js/three/GLTFLoader.js",
      "assets/js/three/FirstPersonControls.js",
      "assets/js/three/OrbitControls.js",
      "assets/js/three/OBJExporter.js",
      "assets/js/three/spritetext.js",
      "assets/js/three/ColladaExporter.js",
      "assets/js/objects/horizontalModel.js",
      "assets/js/objects/wallModel.js",
      "assets/js/objects/eventModel.js",
      "assets/js/objects/table.js",
      "assets/js/objects/emptyModel.js",
      "assets/js/objects/building.js",
      "assets/js/core/tweenLife.min.js",
      "assets/js/core/jspdf.min.js",
      "assets/js/core/sweetalert.min.js",
      "assets/js/core/nouislider.min.js",
      "assets/js/core/jstree.min.js",
      "assets/js/core/colorpicker.js",
      "assets/js/core/xncolorpicker.min.js",
      "assets/plugins/colpick/js/colpick.js",
      "assets/js/Util.js",
      "assets/js/floorplaner.js",
      "assets/js/items/item.js",
      "assets/js/items/wall_item.js",
      "assets/js/threes/edge.js",
      "assets/js/threes/plan.js",
      "assets/js/threes/manager.js",
      "assets/js/threes/scene.js",
      "assets/js/models/halfedge.js",
      "assets/js/models/wall.js",
      "assets/js/threes/light.js",
      "assets/js/models/corner.js",
      "assets/js/models/floorplan.js",
      "assets/js/models/model.js",
      "assets/js/threes/main.js",
      "assets/js/threes/app.js",
      "assets/js/cursor.js",
      "assets/js/video.object.js",
      "assets/js/setting.text.js",
      "assets/js/camera.control.js",
      "assets/js/light.control.js",
      "assets/js/measurement.control.js",
      "assets/js/shape.position.js",
      "assets/js/shape.store.js",
      "assets/js/shape.control.js",
      "assets/js/canvas.export.js",
      "assets/js/setting.background.js",
      "assets/js/setting.floor.js",
      "assets/js/setting.panorama.js",
      "assets/js/setting.wall.js",
      "assets/js/setting.texture.js",
      "assets/js/setting.size.js",
      "assets/js/setting.environment.js",
      "assets/js/engine.js",
      "assets/js/main.js",
    ])
    .pipe(concat("bundle.js"))
    .pipe(uglify().on("error", function (e) {
        console.log(e);
    }))
    .pipe(gulp.dest("dist/js"))
    .pipe(browserSync.stream());
});

// CSS compression task
gulp.task("compress-css", function () {
  return gulp
    .src([
      "assets/css/jquery.ui.css",
      "assets/css/nouislider.min.css",
      "assets/plugins/colpick/css/colpick.css",
      "assets/css/jsTree/default/style.min.css",
      "assets/css/style.css",
      "assets/css/control.css",
    ])
    .pipe(concat("stylesheet.css"))
    .pipe(gulp.dest("dist/css"))
    .pipe(browserSync.stream());
});

gulp.task("copyToServer", function () {
  const indexHtml = gulp
    .src("index.html")
    .pipe(gulp.dest("C:/WorkSpace/eventdraw/test"))
    .pipe(browserSync.stream());

  const bundleJs = gulp
    .src("dist/js/bundle.js")
    .pipe(gulp.dest("C:/WorkSpace/eventdraw/test/assets/js"))
    .pipe(browserSync.stream());

  const stylesheetCss = gulp
    .src("dist/css/stylesheet.css")
    .pipe(gulp.dest("C:/WorkSpace/eventdraw/test/assets/css"))
    .pipe(browserSync.stream());

  // Merge all streams into one
  return merge(indexHtml, bundleJs, stylesheetCss);
});

// Default task
gulp.task("default", gulp.series("compress-css", "compress-js", "copyToServer"))