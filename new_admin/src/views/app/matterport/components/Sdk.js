export function makeSdk(elementId) {
    return new Sdk(elementId);
}

class Sdk {
    elementId = null;
    callback = null;
    sdk = null;

    constructor(elementId) {
        this.elementId = elementId;
    }

    init(applicationKey, setSdk) {
        const that = this;
        const checkIframe = function () {
            const iframe = document.getElementById(that.elementId);
            const iWindow = iframe?.contentWindow;
            if (iWindow != null && iWindow.MP_SDK) {
                clearInterval(intervalId);

                iWindow.MP_SDK.connect(iWindow, applicationKey).then((sdk) => {
                    that.sdk = sdk;
                    if (that.callback) {
                        that.callback(sdk, setSdk);
                    }
                });
            }
        };

        const intervalId = setInterval(checkIframe, 1000);
    }

    onChanged(callback) {
        this.callback = callback;
    }
}
