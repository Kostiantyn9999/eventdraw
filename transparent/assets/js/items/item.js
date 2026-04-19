const Item = function (geometry, material, position) {
  THREE.Mesh.call(this, geometry, material);

  const main = this;

  main.init = function () {
    this.geometry.computeBoundingBox();

    this.halfSize = this.objectHalfSize();
  };

  main.objectHalfSize = function () {
    const objectBox = new THREE.Box3();
    objectBox.setFromObject(this);
    return objectBox.max.clone().sub(objectBox.min).divideScalar(2);
  };

  main.init();
};

Item.prototype = Object.create(THREE.Mesh.prototype);
