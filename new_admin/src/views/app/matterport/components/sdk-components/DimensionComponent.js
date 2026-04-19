// import { roundToNearestTarget } from "helpers/util";

class DimensionComponent {
    root = null;
    rotationPivot = null;
    box = null;
    leftTopPivot = null;
    leftBottomPivot = null;
    rightTopPivot = null;
    rightBottomPivot = null;
    xline = null;
    yline = null;
    boxMaterial = null;

    inputs = {
        xMin: 0,
        xMax: 1,
        yMin: 0,
        yMax: 1,
        axis: false,
        elevation: 0,
        rotation: 0,
        comparedBackImage: null,
        overlay: true,
        startPosition: null,
    };

    onInit() {
        const THREE = this.context.three;

        this.root = new THREE.Object3D();

        this.outputs.objectRoot = this.root;
        this.outputs.collider = this.root;

        this.rotationPivot = new THREE.Object3D();
        this.root.add(this.rotationPivot);

        this.boxMaterial = new THREE.MeshBasicMaterial({
            color: 0xff0000,
            depthWrite: false,
            transparent: true,
            side: THREE.DoubleSide,
            blending: THREE.AdditiveBlending,
            opacity: 1,
        });

        this.makeDimension();
    }

    makeDimension() {
        const THREE = this.context.three;

        if (this.box) {
            this.rotationPivot.remove(this.box);
            this.box.material.dispose();
            this.box.geometry.dispose();
            this.box = null;
        }

        if (this.leftTopPivot) {
            this.rotationPivot.remove(this.leftTopPivot);
            this.leftTopPivot.material.dispose();
            this.leftTopPivot.geometry.dispose();
            this.leftTopPivot = null;
        }

        if (this.leftBottomPivot) {
            this.rotationPivot.remove(this.leftBottomPivot);
            this.leftBottomPivot.material.dispose();
            this.leftBottomPivot.geometry.dispose();
            this.leftBottomPivot = null;
        }

        if (this.rightTopPivot) {
            this.rotationPivot.remove(this.rightTopPivot);
            this.rightTopPivot.material.dispose();
            this.rightTopPivot.geometry.dispose();
            this.rightTopPivot = null;
        }

        if (this.rightBottomPivot) {
            this.rotationPivot.remove(this.rightBottomPivot);
            this.rightBottomPivot.material.dispose();
            this.rightBottomPivot.geometry.dispose();
            this.rightBottomPivot = null;
        }

        if (this.xline) {
            this.rotationPivot.remove(this.xline);
            this.xline.material.dispose();
            this.xline.geometry.dispose();
            this.xline = null;
        }

        if (this.yline) {
            this.rotationPivot.remove(this.yline);
            this.yline.material.dispose();
            this.yline.geometry.dispose();
            this.yline = null;
        }

        const boxGeometry = new THREE.BoxGeometry(
            this.inputs.xMax - this.inputs.xMin,
            0.2,
            this.inputs.yMax - this.inputs.yMin
        );

        this.box = new THREE.Mesh(
            boxGeometry,
            this.boxMaterial || new THREE.MeshBasicMaterial()
        );
    
        this.box.position.set(
            (this.inputs.xMin + this.inputs.xMax) / 2,
            this.inputs.elevation,
            (this.inputs.yMin + this.inputs.yMax) / 2
        );

        const pivotGeometry = new THREE.BoxGeometry(1, 0.4, 1);
        const pivotMaterial = new THREE.MeshBasicMaterial({
            color: 0x0000ff,
            depthWrite: false,
            transparent: true,
            side: THREE.DoubleSide,
            blending: THREE.AdditiveBlending,
            opacity: 1,
        });

        this.leftTopPivot = new THREE.Mesh(pivotGeometry, pivotMaterial);
        this.leftTopPivot.position.set(this.inputs.xMin, this.inputs.elevation, this.inputs.yMin);
        this.leftBottomPivot = new THREE.Mesh(pivotGeometry, pivotMaterial);
        this.leftBottomPivot.position.set(this.inputs.xMin, this.inputs.elevation, this.inputs.yMax);
        this.rightTopPivot = new THREE.Mesh(pivotGeometry, pivotMaterial);
        this.rightTopPivot.position.set(this.inputs.xMax, this.inputs.elevation, this.inputs.yMax);
        this.rightBottomPivot = new THREE.Mesh(pivotGeometry, pivotMaterial);
        this.rightBottomPivot.position.set(this.inputs.xMax, this.inputs.elevation, this.inputs.yMin);

        const xLineGeometry = new THREE.BoxGeometry(this.inputs.xMax - this.inputs.xMin, 1, 0.2);

        const xLineMaterial = new THREE.MeshBasicMaterial({
            color: 0x00ff00,
            depthWrite: false,
            transparent: true,
            side: THREE.FrontSide,
            blending: THREE.AdditiveBlending,
            opacity: 1,
        });

        this.xline = new THREE.Mesh(xLineGeometry, xLineMaterial);
        this.xline.position.y = 1;
        this.xline.position
            .addVectors(
                new THREE.Vector3(this.inputs.xMin, 0, this.inputs.yMin),
                new THREE.Vector3(this.inputs.xMax, 0, this.inputs.yMin + 0.2)
            )
            .divideScalar(2);

        const yLineGeometry = new THREE.BoxGeometry(0.2, 1, this.inputs.yMax - this.inputs.yMin);

        const yLineMaterial = new THREE.MeshBasicMaterial({
            color: 0x00ff00,
            depthWrite: false,
            transparent: true,
            side: THREE.DoubleSide,
            blending: THREE.AdditiveBlending,
            opacity: 1,
        });

        this.yline = new THREE.Mesh(yLineGeometry, yLineMaterial);
        this.yline.position.y = 1;
        this.yline.position
            .addVectors(
                new THREE.Vector3(this.inputs.xMin, 0, this.inputs.yMin),
                new THREE.Vector3(this.inputs.xMin + 0.2, 0, this.inputs.yMax)
            )
            .divideScalar(2);
        this.rotationPivot.add(this.box);
        this.rotationPivot.add(this.leftTopPivot);
        this.rotationPivot.add(this.leftBottomPivot);
        this.rotationPivot.add(this.rightTopPivot);
        this.rotationPivot.add(this.rightBottomPivot);
        // this.rotationPivot.add(this.xline);
        // this.rotationPivot.add(this.yline);

        this.fixRotation();
    }

    onInputsUpdated(oldInputs) {
        this.root.visible = this.inputs.overlay;
        if (oldInputs.comparedBackImage != this.inputs.comparedBackImage) {
            if (this.boxMaterial && this.inputs.comparedBackImage) {
                const THREE = this.context.three;
                const texture = new THREE.Texture(this.inputs.comparedBackImage.image);
                texture.encoding = THREE.sRGBEncoding;
                texture.needsUpdate = true;

                this.boxMaterial.color = new THREE.Color(0xffffff);
                this.boxMaterial.map = texture;
                this.boxMaterial.opacity = 0.7;
                this.boxMaterial.side = THREE.FrontSide;
            }
            if (this.boxMaterial && !this.inputs.comparedBackImage) {
                this.boxMaterial = new THREE.MeshBasicMaterial({
                    color: 0xff0000,
                    depthWrite: false,
                    transparent: true,
                    side: THREE.DoubleSide,
                    blending: THREE.AdditiveBlending,
                    opacity: 1,
                });
            }
            this.makeDimension();
        }

        if (
            oldInputs.xMin !== this.inputs.xMin ||
            oldInputs.xMax !== this.inputs.xMax ||
            oldInputs.yMin !== this.inputs.yMin ||
            oldInputs.yMax !== this.inputs.yMax ||
            oldInputs.axis !== this.inputs.axis ||
            oldInputs.elevation !== this.inputs.elevation
        ) {
            this.fixRotation();
            this.makeDimension();

            return;
        }

        if (oldInputs.rotation !== this.inputs.rotation || oldInputs.startPosition != this.inputs.startPosition) {
            this.fixRotation();
        }
    }

    fixRotation() {
        const centerX = (this.inputs.xMax - this.inputs.xMin) / 2 + this.inputs.xMin;
        const centerY = (this.inputs.yMax - this.inputs.yMin) / 2 + this.inputs.yMin;

        this.rotationPivot.position.x = -centerX;
        this.rotationPivot.position.z = -centerY;
        
        const initRotation = 0;
        // if (this.inputs.startPosition && this.inputs.startPosition.rotation) {
        //     initRotation = roundToNearestTarget(this.inputs.startPosition.rotation.y);
        // }
        this.root.rotation.y = (-Math.abs(initRotation) + this.inputs.rotation) * Math.PI / 180;
        this.root.position.x = centerX;
        this.root.position.z = centerY;
    };
}

export const dimensionType = "mp.dimension";

export const makeDimension = function () {
    return new DimensionComponent();
};
