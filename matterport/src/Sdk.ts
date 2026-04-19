import { MpSdk } from "bundle/sdk";
import { ISdk } from "./interfaces";

const interfaceVersion = "3.5";

export function makeSdk(elementId: string): ISdk {
  return new Sdk(elementId);
}

class Sdk implements ISdk {
  private elementId: string;
  private callback: (sdk: any) => void;
  private sdk: MpSdk;

  constructor(elementId: string) {
    this.elementId = elementId;
  }

  public init(applicationKey: string): void {
    const that = this;
    const checkIframe = function () {
      const iframe = document.getElementById(that.elementId);
      if (iframe && (iframe as any).contentWindow.MP_SDK) {
        clearInterval(intervalId);

        (iframe as any).contentWindow.MP_SDK.connect(
          iframe,
          applicationKey,
          interfaceVersion
        ).then((sdk: MpSdk) => {
          that.sdk = sdk;
          if (that.callback) {
            that.callback(sdk);
          }
        });
      }
    };

    const intervalId = setInterval(checkIframe, 100);
  }

  onChanged(callback: (sdk: any) => void): void {
    this.callback = callback;
  }
}
