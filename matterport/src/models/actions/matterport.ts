import { IMatterport } from "types/matterport";
import {
  MATTERPORT_FRONT_ACTIONS,
  MATTERPORT_ADMIN_ACTIONS,
} from "../actionTypes";

// export const fetchMatteport = (id: number) => {
//   return {
//     type: MATTERPORT_FRONT_ACTIONS.MATTERPORT_FETCH_REQUESTED,
//     id,
//   };
// };

// export const fetchMatterportAll = () => {
//   return {
//     type: MATTERPORT_FRONT_ACTIONS.MATTERPORT_ALL_FETCH_REQUESTED,
//   };
// };

export type ISubmitMatterportType = {
  mat?: string;
  name?: string;
  minX?: number;
  maxX?: number;
  minY?: number;
  maxY?: number;
  baseElevation?: number;
  axis?: boolean;
};

// export const saveMatterport = (matterport: ISubmitMatterportType) => {
//   return {
//     type: MATTERPORT_ADMIN_ACTIONS.MATTERPORT_SAVE_REQUEST,
//     matterport,
//   };
// };

// export const updateMatterport = (
//   id: string,
//   matterport: ISubmitMatterportType
// ) => {
//   return {
//     type: MATTERPORT_ADMIN_ACTIONS.MATTERPORT_UPDATE_REQUEST,
//     id,
//     matterport,
//   };
// };

// export const removeMatterport = (id: number) => {
//   return {
//     type: MATTERPORT_ADMIN_ACTIONS.MATTERPORT_REMOVE_REQUEST,
//     id,
//   };
// };
