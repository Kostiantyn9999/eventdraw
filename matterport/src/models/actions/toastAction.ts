import { TOAST_ACTIONS } from "../actionTypes";
import { IToast } from "types/toast";

export function addToast(payload: IToast) {
  return {
    type: TOAST_ACTIONS.TOAST_ADD,
    payload,
  };
}

export function removeToast(id: number) {
  return {
    type: TOAST_ACTIONS.TOAST_REMOVE,
    id,
  };
}
