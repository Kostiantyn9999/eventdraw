import React, { ChangeEvent, useRef } from "react";

import { Box, Button } from "grommet";

type Props = {
  onChange: (image: HTMLImageElement) => void;
};

export const loadAsyncFile = (file: File): Promise<string | null> => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.addEventListener("load", (event) => {
      resolve(event.target ? (event.target.result as string) : null);
    });
    reader.readAsDataURL(file);
  });
};

export const loadAsyncImage = (url: string): Promise<HTMLImageElement> => {
  return new Promise((resolve, reject) => {
    const image = new Image();
    image.onload = () => {
      resolve(image);
    };
    image.src = url;
  });
};

const UploadButton = ({ onChange }: Props) => {
  const fileInputRef = useRef<HTMLInputElement | null>(null);

  const clickSave = () => {
    if (fileInputRef.current) {
      fileInputRef.current.click();
    }
  };

  const loadFile = async (file: File) => {
    const loadedFile = await loadAsyncFile(file);

    if (loadedFile) {
      const convertedImage = await loadAsyncImage(loadedFile);
      onChange(convertedImage);
    }
  };

  const pickFile = (event: ChangeEvent<HTMLInputElement>) => {
    if (event.target.files && event.target.files.length > 0) {
      loadFile(event.target.files[0]);
    }
  };

  return (
    <Box flex>
      <Button label="Upload Image" primary onClick={clickSave} />
      <input
        ref={fileInputRef}
        type="file"
        style={{ display: "none" }}
        onChange={pickFile}
      />
    </Box>
  );
};

export default UploadButton;
