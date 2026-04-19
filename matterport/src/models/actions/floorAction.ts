import { FLOOR_ACTION } from "../actionTypes";

export function receiveFloor(floor) {
  return {
    type: FLOOR_ACTION.RECEIVE_FLOOR,
    floor,
  };
}
