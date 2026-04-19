import { initComponents } from "./sdk-components/index";

export const makeScene = (sdk) => {
    return new Scene(sdk);
};

class Scene {
    loaded = null;
    _objects = [];
    availableModels = [];
    preparedD = null;
    spy = null;

    constructor(sdk) {
        this.loaded = false;
        this.sdk = sdk;
        this.setup = this.setup.bind(this);
        sdk.onChanged(this.setup);
    }

    async setup(sdk, setSdk) {
        sdk.Scene.configure(function (renderer, three, effectComposer) {
            window.THREE = three;
            window.effectComposer = effectComposer;
        });

        await initComponents(sdk);
        setSdk(sdk);
        this.loaded = true;
    }

    // Fake API to serialize function
    * nodeIterator() {
        for (const node of this._objects) {
            yield node;
        }
    }

    bindings() {
        return [];
    }

    pathIterator() {
        return [];
    }

    async serialize() {
        return await this.sdk.sdk.Scene.serialize(this);
    }

    async deserialize(serialized, callback) {
        const nodesToStop = this._objects.splice(0);
        for (const node of nodesToStop) {
            node.stop();
        }

        const nodesToStart = await this.sdk.sdk.Scene.deserialize(serialized);

        if (callback) {
            for (const node of nodesToStart.nodeIterator()) {
                callback(node);
            }
        }

        for (const node of nodesToStart.nodeIterator()) {
            node.start();
            this._objects.push(node);
        }
    }

    loadModels(mss, chairM) {
        this._objects.forEach((node) => {
            const componentIterator = node.componentIterator();

            for (const component of componentIterator) {
                if (component.componentType === "mp.shape") {
                    (component.inputs).model = component.inputs.type;
                } else if (component.componentType === "mp.chair") {
                    component.inputs.model = "table_chair";
                }
            }
        });
    }

    getObjects() {
        return this._objects;
    }

    setAvailableModels(availableModels) {
        this.availableModels = availableModels;
    }

    setSavedData(data) {
        this.preparedD = data;
    }

    setClickSpy(spy) {
        this.spy = spy;
    }
}
