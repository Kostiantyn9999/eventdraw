var Wall = function (start, end) {
  const main = this;

  this.measKoeff = 39.37;
  this.thickness = 5;
  this.height = 3 * this.measKoeff;
  this.opacity = 0.15;
  this.color = new THREE.Color(0, 0, 0);
  this.texture = null;

  // start Corner and end Corner
  this.start = start;
  this.end = end;

  // Front Edge and Back Edge
  main.frontEdge = null;
  main.backEdge = null;

  main.items = [];

  main.init = function () {};

  main.getStart = function () {
    return main.start;
  };

  main.getEnd = function () {
    return main.end;
  };

  main.setHeight = function (height) {
    this.height = height;
  };

  main.setColor = function (color) {
    main.color = color;
  };

  main.setTexture = function (texture) {
    main.texture = texture;
  };

  main.fireRedraw = function () {
    if (this.frontEdge) {
      this.frontEdge.redrawCallbacks.fire();
    }
    if (this.backEdge) {
      this.backEdge.redrawCallbacks.fire();
    }
  };
  main.init();
};
