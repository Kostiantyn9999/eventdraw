import React from "react";

type Props = {
  onForward: () => void;
  onBack: () => void;
  onLeft: () => void;
  onRight: () => void;
  onLeftRotate: () => void;
  onRightRotate: () => void;
  onZoomIn: () => void;
  onZoomOut: () => void;
};

const MovingControl = ({
  onForward,
  onBack,
  onLeft,
  onRight,
  onLeftRotate,
  onRightRotate,
  onZoomIn,
  onZoomOut,
}: Props) => {
  const useTag = '<use xlink:href="#orbitBackground_0_Layer0_0_FILL" />';
  const useTagArrow =
    '<use xlink:href="#orbitDirectionSign_0_Layer0_0_1_STROKES" />';
  const useTagZoomIn = '<use xlink:href="#ZoomInButton_0_Layer1_0_FILL" />';
  const useTagZoomIn1 =
    '<use xlink:href="#ZoomInButton_0_Layer0_0_1_STROKES" />';
  const useTagZoomOut = '<use xlink:href="#ZoomOutButton_0_Layer1_0_FILL" />';
  const useTagZoomOut1 =
    '<use xlink:href="#ZoomOutButton_0_Layer0_0_1_STROKES" />';
  const useTagLeft = '<use xlink:href="#TurnLeftButton_0_Layer1_0_FILL" />';
  const useTagLeft1 = '<use xlink:href="#TurnLeftButton_0_Layer0_0_FILL" />';
  const useTagRight = '<use xlink:href="#TurnRightButton_0_Layer1_0_FILL" />';
  const useTagRight1 = '<use xlink:href="#TurnRightButton_0_Layer0_0_FILL" />';
  const useTagReset = '<use xlink:href="#CenterButton_0_Layer1_0_FILL" />';
  const useTagReset1 = '<use xlink:href="#CenterButton_0_Layer0_0_FILL" />';

  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      version="1.1"
      preserveAspectRatio="none"
      x="0px"
      y="0px"
      width="59"
      height="116"
      viewBox="0 0 59 116"
    >
      <defs>
        <g id="orbitBackground_0_Layer0_0_FILL">
          <path
            fill="#FFFFFF"
            fillOpacity="0.6980392156862745"
            stroke="none"
            d="
          M 47.65 14.25
          Q 45.8419921875 10.4919921875 42.65 7.3 41.84375 6.49375 41 5.75 34.2068359375 0 25 0 14.65 0 7.3 7.3 4.6798828125 9.9380859375 3 12.95 2.6498046875 13.6041015625 2.35 14.25 0 19.139453125 0 25 0 29.687890625 1.5 33.75 3.3064453125 38.6564453125 7.3 42.65 14.65 50 25 50 35.35 50 42.65 42.65 46.6708984375 38.6564453125 48.5 33.75 50 29.687890625 50 25 50 19.144921875 47.65 14.25 Z"
          ></path>
        </g>
        <g id="ZoomOutButton_0_Layer1_0_FILL">
          <path
            fill="#FFFFFF"
            fillOpacity="0.6980392156862745"
            stroke="none"
            d="
          M 18.1 0
          L 0.05 0 0.05 18.1 18.1 18.1 18.1 0 Z"
          ></path>
        </g>
        <g id="ZoomInButton_0_Layer1_0_FILL">
          <path
            fill="#FFFFFF"
            fillOpacity="0.6980392156862745"
            stroke="none"
            d="
          M 18 0
          L 0 0 0 18 18 18 18 0 Z"
          ></path>
        </g>
        <g id="TurnLeftButton_0_Layer1_0_FILL">
          <path
            fill="#FFFFFF"
            fillOpacity="0.6980392156862745"
            stroke="none"
            d="
          M 18 18
          L 18 0 0 0 0 18 18 18 Z"
          ></path>
        </g>
        <g id="TurnLeftButton_0_Layer0_0_FILL">
          <path
            fill="#333333"
            stroke="none"
            d="
          M 13.9 12
          L 11.4 11.6
          Q 10.9 11.5 10.6 11.4 9.9 11.15 9.7 11.05 9.05 10.75 8.5 10.35 8.3 10.25 8.05 10 7.9 9.9 7.6 9.65
          L 9.9 8.25
          Q 10 8.2 10 8.15
          L 9.9 8.1
          Q 3.2 3.8 3.1 3.75
          L 3.35 12.15 5.6 10.8
          Q 6.6 11.9 7.55 12.45 8.6 13.1 9.3 13.35
          L 10.6 13.75
          Q 10.75 13.8 11.45 13.95
          L 12.6 14.15
          Q 13.85 14.3 14.7 14.25
          L 14.6 12.1
          Q 14.6 12 14.45 12
          L 13.9 12 Z"
          ></path>
        </g>
        <g id="TurnRightButton_0_Layer1_0_FILL">
          <path
            fill="#FFFFFF"
            fillOpacity="0.6980392156862745"
            stroke="none"
            d="
          M 18 18
          L 18 0 0 0 0 18 18 18 Z"
          ></path>
        </g>
        <g id="TurnRightButton_0_Layer0_0_FILL">
          <path
            fill="#333333"
            stroke="none"
            d="
          M 14.45 12.15
          L 14.7 3.75
          Q 14.6 3.8 7.9 8.1
          L 7.8 8.15
          Q 7.8 8.2 7.9 8.25
          L 10.2 9.65
          Q 9.9 9.9 9.75 10 9.5 10.25 9.3 10.35 8.75 10.75 8.1 11.05 7.9 11.15 7.2 11.4 6.9 11.5 6.4 11.6
          L 3.9 12 3.35 12
          Q 3.2 12 3.2 12.1
          L 3.1 14.25
          Q 3.95 14.3 5.2 14.15
          L 6.35 13.95
          Q 7.05 13.8 7.2 13.75
          L 8.5 13.35
          Q 9.2 13.1 10.25 12.45 11.2 11.9 12.2 10.8
          L 14.45 12.15 Z"
          ></path>
        </g>
        <g id="CenterButton_0_Layer1_0_FILL">
          <path
            fill="#FFFFFF"
            fillOpacity="0.6980392156862745"
            stroke="none"
            d="
          M 9 -9
          L -9 -9 -9 9 9 9 9 -9 Z"
          ></path>
        </g>
        <g id="CenterButton_0_Layer0_0_FILL">
          <path
            fill="#333333"
            fillOpacity="0.6980392156862745"
            stroke="none"
            d="
          M -3 7.5
          L -7.25 7.5 -7.25 3 -9 3 -9 9 -3 9 -3 7.5
          M -9 -3
          L -7.35 -3 -7.35 -7.4 -3 -7.4 -3 -9 -9 -9 -9 -3
          M 9 3
          L 7.25 3 7.25 7.5 3 7.5 3 9 9 9 9 3
          M 3 -7.4
          L 7.4 -7.4 7.4 -3 9 -3 9 -9 3 -9 3 -7.4 Z"
          ></path>
        </g>
        <g
          id="orbitDirectionSign_0_Layer0_0_1_STROKES"
          style={{ cursor: "pointer" }}
        >
          <rect
            width="20"
            height="20"
            x="-8"
            y="-8"
            fill="#FFFFFF"
            opacity="0"
          ></rect>
          <path
            stroke="#333333"
            strokeWidth="4"
            strokeLinejoin="miter"
            strokeLinecap="butt"
            strokeMiterlimit="3"
            fill="none"
            d="M 0 8 L 0 0 8 0"
          ></path>
        </g>
        <path
          id="ZoomOutButton_0_Layer0_0_1_STROKES"
          style={{ cursor: "pointer" }}
          stroke="#333333"
          strokeWidth="2.5"
          strokeLinejoin="miter"
          strokeLinecap="butt"
          strokeMiterlimit="10"
          fill="none"
          d="M 14.35 8.95 L 3.95 8.95"
        ></path>
        <path
          id="ZoomInButton_0_Layer0_0_1_STROKES"
          style={{ cursor: "pointer" }}
          stroke="#333333"
          strokeWidth="2.5"
          strokeLinejoin="miter"
          strokeLinecap="butt"
          strokeMiterlimit="10"
          fill="none"
          d="M 9.25 4.05 L 9.25 8.95 14.1 8.95 M 9.25 13.5 L 9.25 8.95 4.3 8.95"
        ></path>
      </defs>
      <g transform="matrix(1, 0, 0, 1, 0, 0)">
        <g transform="matrix( 1, 0, 0, 1, 4.75,0) ">
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTag }}
          />
        </g>
        <g
          id="orbit-down"
          transform="matrix( -0.70709228515625, -0.70709228515625, 0.70709228515625, -0.70709228515625, 29.75,30.65) "
          onClick={onBack}
        >
          <g transform="matrix( 1, 0, 0, 1, -8.6,-8.6) ">
            <g
              transform="matrix( 1, 0, 0, 1, 0,0) "
              dangerouslySetInnerHTML={{ __html: useTagArrow }}
            />
          </g>
        </g>
        <g
          id="orbit-left"
          transform="matrix( 0.70709228515625, -0.70709228515625, 0.70709228515625, 0.70709228515625, 24.1,25) "
          onClick={onLeft}
        >
          <g transform="matrix( 1, 0, 0, 1, -8.6,-8.6) ">
            <g
              transform="matrix( 1, 0, 0, 1, 0,0) "
              dangerouslySetInnerHTML={{ __html: useTagArrow }}
            />
          </g>
        </g>
        <g
          id="orbit-up"
          transform="matrix( 0.70709228515625, 0.70709228515625, -0.70709228515625, 0.70709228515625, 29.75,19.35) "
          onClick={onForward}
        >
          <g transform="matrix( 1, 0, 0, 1, -8.6,-8.6) ">
            <g
              transform="matrix( 1, 0, 0, 1, 0,0) "
              dangerouslySetInnerHTML={{ __html: useTagArrow }}
            />
          </g>
        </g>
        <g
          id="orbit-right"
          transform="matrix( -0.70709228515625, 0.70709228515625, -0.70709228515625, -0.70709228515625, 35.4,25) "
          onClick={onRight}
        >
          <g transform="matrix( 1, 0, 0, 1, -8.6,-8.6) ">
            <g
              transform="matrix( 1, 0, 0, 1, 0,0) "
              dangerouslySetInnerHTML={{ __html: useTagArrow }}
            />
          </g>
        </g>
        <g
          id="orbit-zoom-in"
          transform="matrix( 1, 0, 0, 1, 20.2,58.35) "
          onClick={onZoomIn}
        >
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagZoomIn }}
          />
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagZoomIn1 }}
          />
        </g>
        <g
          id="orbit-zoom-out"
          transform="matrix( 0.997222900390625, 0, 0, 0.994476318359375, 20.15,77.6) "
          onClick={onZoomOut}
        >
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagZoomOut }}
          />
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagZoomOut1 }}
          />
        </g>
        <g
          id="orbit-rot-left"
          transform="matrix( 1, 0, 0, 1, 0,66.6) "
          onClick={onLeftRotate}
        >
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagLeft }}
          />
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagLeft1 }}
          />
        </g>
        <g
          id="orbit-rot-right"
          transform="matrix( 1, 0, 0, 1, 40.45,65.6) "
          onClick={onRightRotate}
        >
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagRight }}
          />
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagRight1 }}
          />
        </g>
        <g id="orbit-reset" transform="matrix( 1, 0, 0, 1, 29.25,106.45) ">
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagReset }}
          />
          <g
            transform="matrix( 1, 0, 0, 1, 0,0) "
            dangerouslySetInnerHTML={{ __html: useTagReset1 }}
          />
        </g>
      </g>
    </svg>
  );
};

export default MovingControl;
