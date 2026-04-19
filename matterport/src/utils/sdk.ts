import {MpSdk, ShowcaseBundleWindow} from "bundle/sdk"; // const SDK_VERSION = '3.2';
// const SDK_VERSION = '3.2';

export const GetSDK = (
  elementId: string,
  applicationKey: string,
  sdk_version: string
): Promise<MpSdk> => {
  return new Promise((resolve, reject) => {
    const checkIframe = async () => {
      var iframe = document.getElementById(elementId);

      if (iframe !== undefined) {
        const iWindow = (<HTMLIFrameElement>iframe)
          .contentWindow as ShowcaseBundleWindow;
        if (iWindow != null && iWindow.MP_SDK) {
          clearInterval(intervalId);

          const sdk = await iWindow.MP_SDK.connect(iWindow, {
            applicationKey: applicationKey,
          });
          
          resolve(sdk);
        }
      }
    };

    const intervalId = setInterval(checkIframe, 100);
  });
};
