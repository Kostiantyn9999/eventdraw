var Utils = function () {
  const main = this;

  main.guid = function () {
    var tS4 = function () {
      return Math.floor((1 + Math.random()) * 0x10000)
        .toString(16)
        .substring(1);
    };

    return (
      tS4() +
      tS4() +
      "-" +
      tS4() +
      "-" +
      tS4() +
      "-" +
      tS4() +
      "-" +
      tS4() +
      tS4() +
      tS4()
    );
  };

  main.angle2pi = function (x1, y1, x2, y2) {
    var tTheta = main.angle(x1, y1, x2, y2);
    if (tTheta < 0) {
      tTheta += 2 * Math.PI;
    }
    return tTheta;
  };

  main.angle = function (x1, y1, x2, y2) {
    var tDot = x1 * x2 + y1 * y2;
    var tDet = x1 * y2 - y1 * x2;
    var tAngle = -Math.atan2(tDet, tDot);
    return tAngle;
  };

  main.map = function (array, func) {
    var tResult = [];

    array.forEach((element) => {
      tResult.push(func(element));
    });

    return tResult;
  };

  main.cycle = function (arr, shift) {
    var tReturn = arr.slice(0);
    for (var tI = 0; tI < shift; tI++) {
      var tmp = tReturn.shift();
      tReturn.push(tmp);
    }
    return tReturn;
  };

  main.removeIf = function (array, func) {
    var tResult = [];
    array.forEach((element) => {
      if (!func(element)) {
        tResult.push(element);
      }
    });
    return tResult;
  };

  main.isClockwise = function (points) {
    // make positive
    let tSubX = Math.min(
      0,
      Math.min.apply(
        null,
        main.map(points, function (p) {
          return p.x;
        })
      )
    );
    let tSubY = Math.min(
      0,
      Math.min.apply(
        null,
        main.map(points, function (p) {
          return p.x;
        })
      )
    );

    var tNewPoints = main.map(points, function (p) {
      return {
        x: p.x - tSubX,
        y: p.y - tSubY,
      };
    });

    // determine CW/CCW, based on:
    // http://stackoverflow.com/questions/1165647
    var tSum = 0;
    for (var tI = 0; tI < tNewPoints.length; tI++) {
      var tC1 = tNewPoints[tI];
      var tC2;
      if (tI == tNewPoints.length - 1) {
        tC2 = tNewPoints[0];
      } else {
        tC2 = tNewPoints[tI + 1];
      }
      tSum += (tC2.x - tC1.x) * (tC2.y + tC1.y);
    }
    return tSum >= 0;
  };

  main.distance = function (x1, y1, x2, y2) {
    return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
  };

  main.removeValue = function (array, value) {
    for (var tI = array.length - 1; tI >= 0; tI--) {
      if (array[tI] === value) {
        array.splice(tI, 1);
      }
    }
  };

  main.guid = () => {
    var tS4 = function () {
      return Math.floor((1 + Math.random()) * 0x10000)
        .toString(16)
        .substring(1);
    };

    return (
      tS4() +
      tS4() +
      "-" +
      tS4() +
      "-" +
      tS4() +
      "-" +
      tS4() +
      "-" +
      tS4() +
      tS4() +
      tS4()
    );
  };

  /** distance between line and point */
  main.pointDistanceFromLine = function (x, y, x1, y1, x2, y2) {
    var tPoint = this.closestPointOnLine(x, y, x1, y1, x2, y2);
    var tDx = x - tPoint.x;
    var tDy = y - tPoint.y;
    return Math.sqrt(tDx * tDx + tDy * tDy);
  };

  main.closestPointOnLine = function (x, y, x1, y1, x2, y2) {
    // Inspired by: http://stackoverflow.com/a/6853926
    var tA = x - x1;
    var tB = y - y1;
    var tC = x2 - x1;
    var tD = y2 - y1;

    var tDot = tA * tC + tB * tD;
    var tLenSq = tC * tC + tD * tD;
    var tParam = tDot / tLenSq;

    var tXx, tYy;

    if (tParam < 0 || (x1 == x2 && y1 == y2)) {
      tXx = x1;
      tYy = y1;
    } else if (tParam > 1) {
      tXx = x2;
      tYy = y2;
    } else {
      tXx = x1 + tParam * tC;
      tYy = y1 + tParam * tD;
    }

    return {
      x: tXx,
      y: tYy,
    };
  };
};

var Util = new Utils();
