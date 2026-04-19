export class SceneLoader {
	nodes = [];

	constructor(sdk) {
		this.sdk = sdk;
	}

	async load(sid, callback) {
		const nodesToStop = this.nodes.splice(0);

		for (const node of nodesToStop) {
			node.stop();
		}

		const scene = sidToScene.get(sid);
		if (!scene) {
			return;
		}

		const nodesToStart = await this.sdk.Scene.deserialize(JSON.stringify(scene));
		
		if (callback) {
			for (const node of nodesToStart.nodeIterator()) {
				callback(node);
			}
		}

		for (const node of nodesToStart.nodeIterator()) {
			node.start();
			this.nodes.push(node);
		}
	}

	loadModels(mss, chairM) {
		this.nodes.forEach((node) => {
			const componentIterator = node.componentIterator();

			for (const component of componentIterator) {
				if (component.componentType === "mp.shape") {
					const m = mss[component.inputs.type];
					component.inputs.model = m ? m.gltf.scene.clone() : null;
				} else if (component.componentType === "mp.chair") {
					component.inputs.model = chairM.scene.clone();
				}
			}
		});
	}
}

export const sidToScene = new Map();
