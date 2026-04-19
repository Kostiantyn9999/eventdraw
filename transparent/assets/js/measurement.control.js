var MeasurementControl = function (scene, camera, width, height) {
	const main = this;

	main.scene = scene;
	main.camera = camera;
	main.width = width;
	main.height = height;
	main.raycaster = new THREE.Raycaster();
	main.mouse = new THREE.Vector2();
	main.isDrag = false;
	main.isMouseDown = false;
	main.selectedMeasurementId = null;
	main.startDraw = true;
	main.measurementCount = 0;
	main.textElevation = 0;

	main.mode = false;

	main.lines = {}; // To store the lines
    main.points = {}; // To store points
	main.markers = {};
	main.distances = {};
	main.previewLine = null; // Store the current preview line
	main.previewDistance = null; // Store the current preview distance
	
	main.setTextElevation = function (elevation) {
		main.textElevation = parseFloat(elevation);
		for (let key in main.distances) {
			if (main.distances.hasOwnProperty(key)) {
				let models = main.distances[key];
                models.forEach(model => {
                    if (model && model.defaultPosition) { // Check if model is not undefined
                        model.position.copy(model.defaultPosition)
                        model.position.y += main.textElevation
                    }
                });
			}
		}
	}

	main.measureDistance = function (point1, point2, isPreview = false) {
		const formatter = new Intl.NumberFormat('en-US', {
			style: 'decimal',
			maximumFractionDigits: 2,
			minimumFractionDigits: 2,
		});
		const distance = point1.distanceTo(point2) / 40;
		const distLabel = main.createTextSprite(`${formatter.format(distance.toFixed(2))} m`, isPreview);
		distLabel.measurementId = main.measurementCount;

		const midpoint = new THREE.Vector3().addVectors(point1, point2).multiplyScalar(0.5);
		distLabel.defaultPosition = midpoint;
		distLabel.position.copy(midpoint);
		distLabel.position.y += this.textElevation;
        
		if (isPreview) {
			if (!main.previewDistance != null) {
				main.scene.remove(main.previewDistance);
			}
			main.previewDistance = distLabel;
			main.scene.add(distLabel);
		} else {
			main.distances[main.measurementCount].push(distLabel);
			main.scene.add(distLabel);
		}
    }

    main.createTextSprite = function(text, isPreview) {
		const canvas = document.createElement('canvas');
		const ctx = canvas.getContext("2d");
		// Define the rectangle dimensions
		var rectX = 10;
		var rectY = 20;
		var rectWidth = 200;
		var rectHeight = 50;

		// Draw a white rectangle
		ctx.fillStyle = isPreview ? "#ff0000" : "#0000ff";
		ctx.fillRect(rectX, rectY, rectWidth, rectHeight);

		// Set text properties
		ctx.fillStyle = "white";
		ctx.font = "28px Arial";
		ctx.textAlign = "center";    // Align text horizontally center
		ctx.textBaseline = "middle"; // Align text vertically middle

		// Calculate the center of the rectangle
		var centerX = rectX + rectWidth / 2;
		var centerY = rectY + rectHeight / 2;

		// Draw the text at the center of the rectangle
		ctx.fillText(text, centerX, centerY);

        const texture = new THREE.CanvasTexture(canvas);
        const spriteMaterial = new THREE.SpriteMaterial({ map: texture });
        const sprite = new THREE.Sprite(spriteMaterial);
        sprite.scale.set(100, 50, 1)
        return sprite;
    }

	main.addSphereMarker = function (point) {
		const geometry = new THREE.SphereGeometry(15, 32, 16); 
		const material = new THREE.MeshBasicMaterial({ color: 0x0000ff }); 
		const sphere = new THREE.Mesh(geometry, material);
		sphere.measurementId = main.measurementCount;
		sphere.position.copy(point);
		sphere.scale.set(0.2, 0.2, 0.2);
		main.scene.add(sphere);
        main.markers[main.measurementCount].push(sphere);
	}

	main.addLine = function (point1, point2) {
		const direction = new THREE.Vector3().subVectors(point2, point1);
		const orientation = new THREE.Matrix4();
		orientation.lookAt(point1, point2, new THREE.Object3D().up);
		orientation.multiply(new THREE.Matrix4().set(1, 0, 0, 0, 0, 0, -1, 0, 0, 1, 0, 0, 0, 0, 0, 1));
		const lineGeometry = new THREE.CylinderGeometry(1.5, 1.5, direction.length(), 5, 5);
		const line = new THREE.Mesh(lineGeometry, new THREE.MeshBasicMaterial({ color: 0x0000ff }));
		line.applyMatrix4(orientation);

		line.position.x = (point1.x + point2.x) / 2;
		line.position.y = (point1.y + point2.y) / 2;
		line.position.z = (point1.z + point2.z) / 2;
		line.measurementId = main.measurementCount;
		
		main.lines[main.measurementCount].push(line); // Store the line
        main.scene.add(line);
	}
	
	main.updatePreviewLine = function (startPoint, endPoint) {
		if (!main.previewLine != null) {
			main.scene.remove(main.previewLine);
		}
		const direction = new THREE.Vector3().subVectors(endPoint, startPoint);
		const orientation = new THREE.Matrix4();
		orientation.lookAt(startPoint, endPoint, new THREE.Object3D().up);
		orientation.multiply(new THREE.Matrix4().set(1, 0, 0, 0, 0, 0, -1, 0, 0, 1, 0, 0, 0, 0, 0, 1));
		const lineGeometry = new THREE.CylinderGeometry(1.5, 1.5, direction.length(), 5, 5);
		const line = new THREE.Mesh(lineGeometry, new THREE.MeshBasicMaterial({ color: 0xff0000 }));
		line.applyMatrix4(orientation);

		line.position.x = (startPoint.x + endPoint.x) / 2;
		line.position.y = (startPoint.y + endPoint.y) / 2;
		line.position.z = (startPoint.z + endPoint.z) / 2;

		main.previewLine = line;
		main.scene.add(line);
		main.measureDistance(startPoint, endPoint, true);
    }

	main.clearPreviewLine = function () {
		if (!main.previewLine != null) {
			main.scene.remove(main.previewLine);
			main.previewLine = null;
		}
		if (!main.previewDistance != null) {
			main.scene.remove(main.previewDistance);
			main.previewDistance = null;
		}
	}

	main.selectLines = function (measurementId, out = false) {
		try {
			const lines = main.lines[measurementId];
			for (let i = 0; i < lines.length; i++) {
				lines[i].material.color.set(out ? 0x0000ff : 0xffff00); 
			}
			const markers = main.markers[measurementId];
			for (let i = 0; i < markers.length; i++) {
				markers[i].material.color.set(out ? 0x0000ff : 0xffff00); 
			}
		} catch (error) {console.log(error)}
	}

	main.clearLines = function (measurementId) {
		try {
			const lines = main.lines[measurementId];
			for (let i = 0; i < lines.length; i++) {
				main.scene.remove(lines[i]);
			}
			delete main.lines[measurementId];

			delete main.points[measurementId];

			const markers = main.markers[measurementId];
			for (let i = 0; i < markers.length; i++) {
				main.scene.remove(markers[i]);
			}
			delete main.markers[measurementId];

			const distances = main.distances[measurementId];
			for (let i = 0; i < distances.length; i++) {
				main.scene.remove(distances[i]);
			}
			delete main.markers[measurementId];
		} catch (error) {console.log(error)}
	}
	
	main.onMouseDown = function (event) {
		main.isMouseDown = true;
	}
	
	main.onMouseMove = function (event) {
		main.mouse.x = (event.clientX / main.width) * 2 - 1;
      	main.mouse.y = -(event.clientY / main.height) * 2 + 1;
		main.raycaster.setFromCamera(main.mouse, main.camera);
		if (main.isMouseDown) {
			main.isDrag = true;
		}
		const intersects = main.raycaster.intersectObjects(main.scene.children, true);
		if (intersects.length > 0 && main.points[main.measurementCount] && main.points[main.measurementCount].length > 0) {
			main.updatePreviewLine(main.points[main.measurementCount][main.points[main.measurementCount].length - 1], intersects[0].point);
		}
		
		if (intersects.length > 0 && intersects[0].object.measurementId !== undefined && !main.points[main.measurementCount]) {
			main.selectLines(intersects[0].object.measurementId);
			main.selectedMeasurementId = intersects[0].object.measurementId;
		} else if (main.selectedMeasurementId != null) {
			main.selectLines(main.selectedMeasurementId, true);
			main.selectedMeasurementId = null;
		}
	}

	main.initArray = function () {
		if (main.startDraw) {
			main.points[main.measurementCount] = [];
			main.markers[main.measurementCount] = [];
			main.lines[main.measurementCount] = [];
			main.distances[main.measurementCount] = [];
			main.startDraw = false;
		}
	}
	
	main.onMouseUp = function (event) {
		if (!main.isDrag && event.button == 0) {
			main.mouse.x = (event.clientX / main.width) * 2 - 1;
			main.mouse.y = -(event.clientY / main.height) * 2 + 1;
			main.raycaster.setFromCamera(main.mouse, main.camera);

			const intersects = main.raycaster.intersectObjects(main.scene.children, true);
			
			if (intersects.length > 0) {
				main.initArray();
				main.addSphereMarker(intersects[0].point); // Add a circle at the clicked point
				const tempPoints = main.points[main.measurementCount];
				tempPoints.push(intersects[0].point);
				if (tempPoints.length > 1) { // Need at least two tempPoints to draw a line
					const lastIndex = tempPoints.length - 1;
					main.addLine(tempPoints[lastIndex - 1], tempPoints[lastIndex]);
					main.measureDistance(tempPoints[lastIndex - 1], tempPoints[lastIndex]);
					main.clearPreviewLine();
				}
			}
		}
		if (main.points[main.measurementCount] && main.points[main.measurementCount].length > 0 && !main.isDrag && event.button == 2) {
			main.clearPreviewLine();
			main.startDraw = true;
			main.measurementCount += 1;
		}

		if (
			!main.points[main.measurementCount] &&
			!main.isDrag &&
			event.button == 2 &&
			main.selectedMeasurementId != null
		) {
			main.clearLines(main.selectedMeasurementId);
			main.selectedMeasurementId = null;
		}
		main.isMouseDown = false;
		main.isDrag = false;
	}

	main.toggleAllModelsVisibility = function (modelData) {
		for (let key in modelData) {
			if (main.hasOwnProperty(key)) {
				let group = main[key]; // e.g., main.lines, main.markers, etc.
				// Now iterate over each key in the group
				for (let subKey in group) {
					if (group.hasOwnProperty(subKey)) {
						let models = group[subKey]; // Array of models

						// Toggle visibility for each model in the array
						models.forEach(model => {
							if (model) { // Check if model is not undefined
								model.visible = !model.visible; // Toggle visibility
							}
						});
					}
				}
			}
		}
	}
	
	main.fnInit = function () {
		$("#mode-selection #measurement-button").click(function () {
			if ($(this).hasClass("active")) {
				$(this).removeClass("active");
				main.mode = false;
			} else {
				$(this).addClass("active");
				main.mode = true;
			}
			main.toggleAllModelsVisibility({
				lines: main.lines,
				markers: main.markers,
				distances: main.distances,
			})
			if (main.previewLine) {
				main.previewLine.visible = !main.previewLine.visible;
			}
			if (main.previewDistance) {
				main.previewDistance.visible = !main.previewDistance.visible;
			}
		});
	};

	main.fnInit();
};
