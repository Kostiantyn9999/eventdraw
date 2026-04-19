import { FLOOR_ACTION } from "../actionTypes";

const initialState = null;

export default function floor(state = initialState, action = "") {
  switch (action.type) {
    case FLOOR_ACTION.RECEIVE_FLOOR:
      return action.floor;
    default:
      return state;
  }
}
