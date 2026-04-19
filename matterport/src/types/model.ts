export type IModel = {
  id: number;
  category: number;
  shapeType: string;
  model: string;
  elevate: number;
  height: number;
  default: Record<"height" | "elevation", string>;
};
