import { IEvent } from "./floor";

export type IShapeTypesObject = {
  position: { x: number; y: number; z: number };
  scale: { x: number; y: number; z: number };
};

export type IMatterportComponent = {
  type: string;
  input: { value: IShapeTypesObject[]; url: string };
};

export type IMatterportElement = {
  name: string;
  components: IMatterportComponent;
};

export type IMatterport = {
  id: number;
  name: string;
  mat: string;
  baseElevation: number;
  axis: boolean;
  maxX: number;
  maxY: number;
  minX: number;
  minY: number;
  creation_date: string;
  order: number;
  width: number;
  height: number;
  admin: string;
  event: Array<IEvent>;
};
