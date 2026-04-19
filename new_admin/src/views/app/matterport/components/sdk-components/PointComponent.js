class PointComponent {
    root = null;

    centerPointer = null;

    inputs = {
        x: 0,
        y: 0,
        overlay: true,
        elevation: 0,
    };

    onInit() {
        const THREE = this.context.three;

        this.root = new THREE.Object3D();
        this.outputs.objectRoot = this.root;
        this.outputs.collider = this.root;

        this.makeCenterPointer();
        this.updatePosition();
    }

    makeCenterPointer() {
        const THREE = this.context.three;

        const pointGeometry = new THREE.SphereBufferGeometry(0.5, 32, 32);
        const pointMaterial = new THREE.MeshBasicMaterial({ color: 0x00ff00 });

        this.centerPointer = new THREE.Mesh(pointGeometry, pointMaterial);

        this.root.add(this.centerPointer);
    }

    onInputsUpdated(oldInputs) {
        this.root.visible = this.inputs.overlay;
        if (oldInputs.x !== this.inputs.x) {
            this.updatePosition();
        }

        if (oldInputs.y !== this.inputs.y) {
            this.updatePosition();
        }

        if (oldInputs.elevation !== this.inputs.elevation) {
            this.updatePosition();
        }
    }

    updatePosition() {
        if (this.centerPointer) {
            this.centerPointer.position.x = this.inputs.x;
            this.centerPointer.position.y = this.inputs.elevation;
            this.centerPointer.position.z = this.inputs.y;
        }
    }
}

export const pointType = "mp.point";

export const makePoint = function () {
    return new PointComponent();
};
