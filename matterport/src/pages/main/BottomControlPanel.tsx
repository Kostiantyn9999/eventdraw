import React, { useContext, useRef, useState } from "react";
import {
  Box,
  Button,
  Heading,
  Layer,
  Text,
  TextArea,
  TextInput,
} from "grommet";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faFileImage,
  faFilePdf,
  faImage,
  faShare,
  faShareAlt,
} from "@fortawesome/free-solid-svg-icons";
import RowWithTitle from "src/components/RowWithTitle";

import { exportJPG, exportPDF, exportPNG } from "./utils";
import { EventDrawContext } from "src/components/context";
import { AppContext } from "src/AppContext";
import { saveMatterportFile } from "src/controllers/matterport";
import { connect } from "react-redux";
import { bindActionCreators, Dispatch } from "redux";
import { RootState } from "src/models/store";
import { addToast } from "src/models/actions/toastAction";
import { UniqueStringId } from 'unique-string-generator';

type Props = {
  mat: string;
};
type IFileType = "JPG" | "PNG" | "PDF";

const SettingButton = ({
  tip,
  onClick,
  children,
}: {
  tip: string;
  onClick?: () => void;
  children: JSX.Element | JSX.Element[];
}) => {
  return (
    <Button
      tip={{
        plain: true,
        dropProps: { align: { bottom: "top" } },
        content: (
          <Box
            pad="xxsmall"
            margin="xsmall"
            elevation="small"
            // background="#000000"
            background="grey"
            round="xsmall"
            overflow="hidden"
            align="center"
          >
            <Text color="#ffffff" size="small">
              {tip}
            </Text>
          </Box>
        ),
      }}
      onClick={onClick}
    >
      <Box
        width="30px"
        height="30px"
        // background="#000000"
        background="grey"
        round="xsmall"
        pad="xsmall"
        justify="center"
      >
        {children}
      </Box>
    </Button>
  );
};

const BottomControlPanel = ({
  mat,
  addToast,
}: Props & ReturnType<typeof mapDispatchToProps>) => {
  const textRef = useRef<HTMLTextAreaElement>(null);

  const { data } = useContext(EventDrawContext);
  const { sdk, scene } = useContext(AppContext);

  const [mode, setMode] = useState<IFileType | null>(null);
  // const [tempShareNumber, setTempShareNumber] = useState<number | null>(null);
  const [copyString, setCopyString] = useState<string | null>(null);
  const [filename, setFileName] = useState<string>("");

  const open = (m: IFileType) => {
    setMode(m);
  };

  const onClose = () => {
    setMode(null);
  };

  const onCloseIFrameModal = () => {
    setCopyString(null);
  };

  const exportMatterport = () => {
    switch (mode) {
      case "JPG":
        exportJPG(filename, sdk.sdk.Renderer);
        break;
      case "PNG":
        exportPNG(filename, sdk.sdk.Renderer);
        break;
      case "PDF":
        exportPDF(filename, sdk.sdk.Renderer);
        break;
      default:
        break;
    }
  };

  const renderModal = () => {
    return (
      <Layer position="center" onClickOutside={onClose} onEsc={onClose}>
        <Box pad="medium" gap="small" width="medium">
          <Heading level={3} margin="none">
            Save As
          </Heading>
          <Box>
            <RowWithTitle title="FileName:">
              <Box flex>
                <TextInput
                  value={filename}
                  onChange={(e) => setFileName(e.target.value)}
                />
              </Box>
            </RowWithTitle>
          </Box>
          <Box align="center" justify="end" direction="row" gap="small">
            <Button secondary label="Cancel" onClick={onClose} />
            <Button label="Save" primary onClick={exportMatterport} />
          </Box>
        </Box>
      </Layer>
    );
  };

  const importSpace = () => {};

  const shareSpace = async () => {
    const d = await scene.serialize();

    const newD = JSON.stringify({
      matterport: d,
      availableModels: scene.availableModels,
    });

    let blob = new Blob([newD], { type: "text/plain" });

    // console.log(data)
    const response = await saveMatterportFile(
      new File([blob], UniqueStringId())
    );

    if (response && response.url) {
      setCopyString(
        `<iframe title="EventDraw" width=800 height=600 src="${window.location.origin}${window.location.pathname}?shared=${response.url.slice(8)}&building=${mat}" title="description"/>`
      );
    }

    
  };

  const downloadSpace = async () => {
    const linkElement = document.createElement("a");
    const d = await scene.serialize();

    const newD = JSON.stringify({
      matterport: d,
      availableModels: scene.availableModels,
    });
    const file = new Blob([newD], { type: "application/json" });
    linkElement.href = URL.createObjectURL(file);
    linkElement.download = "data.eventdraw";
    linkElement.click();
  };

  const shareUrl = async () => {
    const d = await scene.serialize();

    const newD = JSON.stringify({
      matterport: d,
      availableModels: scene.availableModels,
    });

    var blob = new Blob([newD], { type: "text/plain" });

    const response = await saveMatterportFile(
      new File([blob], UniqueStringId())
    );
    
    if (response && response.url) {
      setCopyString(
        `${window.location.origin}${window.location.pathname}?shared=${response.url.slice(8)}&building=${mat}`
      );
    }
  };

  const copyIframeLink = () => {
    if (textRef.current) {
      textRef.current.select();
      const res = document.execCommand("copy");

      if (res) {
        addToast({
          content: {
            header: "Notification",
            message: "Code Snipe is copied!",
          },
        });

        setCopyString(null);
      }
    }
  };

  const renderIframeShareSpace = () => {
    return (
      <Layer
        position="center"
        onClickOutside={onCloseIFrameModal}
        onEsc={onCloseIFrameModal}
      >
        <Box pad="medium" gap="small" width="large">
          <Heading level={3} margin="none">
            Please copy this code snippet in html
          </Heading>
          <Box>
            <TextArea ref={textRef} value={copyString ?? ""} />
          </Box>
          <Box align="center" justify="end" direction="row" gap="small">
            <Button secondary label="Copy" onClick={copyIframeLink} />
          </Box>
        </Box>
      </Layer>
    );
  };

  return (
    <>
      <Layer modal={false} plain position="bottom-right">
        <Box pad={{ right: "small", bottom: "small" }}>
          <Box direction="row" align="center" gap="15px">
            <SettingButton
              tip="Export PNG"
              onClick={() => {
                open("PNG");
              }}
            >
              <FontAwesomeIcon
                icon={faFileImage as any}
                size="lg"
                color="#ffffff"
              />
            </SettingButton>
            <SettingButton
              tip="Export JPG"
              onClick={() => {
                open("JPG");
              }}
            >
              <FontAwesomeIcon
                icon={faImage as any}
                size="lg"
                color="#ffffff"
              />
            </SettingButton>
            <SettingButton
              tip="Export PDF"
              onClick={() => {
                open("PDF");
              }}
            >
              <FontAwesomeIcon
                icon={faFilePdf as any}
                size="lg"
                color="#ffffff"
              />
            </SettingButton>
            <SettingButton tip="Share Plan" onClick={shareUrl}>
              <FontAwesomeIcon
                icon={faShareAlt as any}
                size="lg"
                color="#ffffff"
              />
            </SettingButton>
            <SettingButton tip="Export Iframe" onClick={shareSpace}>
              <FontAwesomeIcon
                icon={faShare as any}
                size="lg"
                color="#ffffff"
              />
            </SettingButton>
          </Box>
        </Box>
      </Layer>
      {mode && renderModal()}
      {copyString && renderIframeShareSpace()}
    </>
  );
};

const mapStateToProps = (state: RootState) => ({
  // plans: state.plans,
});

const mapDispatchToProps = (dispatch: Dispatch) => {
  return bindActionCreators(
    {
      addToast,
    },
    dispatch
  );
};

export default connect(mapStateToProps, mapDispatchToProps)(BottomControlPanel);
