const WallItem = function (geometry, material, position) {
  Item.call(this, geometry, material, position);
};

WallItem.prototype = Object.create(Item.prototype);
