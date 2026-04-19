export type ILayout = {
  width: number;
  height: number;
};

export type ISetting = {
  unit: number;
};

export type IGroupItem = {
  id: string;
  type: "group" | "shape";
  children: IGroupItem[];
};

export type IDimension = {
  width: number;
  height: number;
  depth: number;
};

export type IShape = {
  id: string;
  name: string;
  number: string;
  show_table_number: number;
  text: string;
  position: { x: number | string; y: number | string; z: number | string };
  rotation: { x: number | string; y: number | string; z: number | string };
  dimension: IDimension;
  elevate: number;
  height: number;
  chairs?: Array<{
    id: string;
    position: { x: number | string; y: number | string; z: number | string };
    rotation: { x: number | string; y: number | string; z: number | string };
  }>;
  fill_color: string;
  points?: Array<{ x: number | string; y: number | string }>;
};

export type IFloor = {
  layout: ILayout;
  setting: ISetting;
  groups: IGroupItem[];
  shapes: IShape[];
  imageInformation?: any
};

export type IMatterportDimension = {
  x: { min: number; max: number };
  z: { min: number; max: number };
  baseElevation: number;
  axis: boolean;
  rotation: number;
};

export type IEvent = {
  id: string;
  name: string;
  eventName: string;
};

export type ITemplate = {
  id: string;
  templateActive: string;
  templateName: string;
};
