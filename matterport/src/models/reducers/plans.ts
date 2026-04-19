import { MATTERPORT_FRONT_ACTIONS } from "../actionTypes";

const initialState = null;

export default function plans(state = initialState, action = "") {
  switch (action.type) {
    // case types.RECEIVE_PLANS:
    //   return action.plans;
    case MATTERPORT_FRONT_ACTIONS.MATTERPORT_ALL_FETCH_SUCCESSED:
      return action.matterports;
    case MATTERPORT_FRONT_ACTIONS.MATTERPORT_FETCH_SUCCESSED:
      return { ...state, [action.matterport.id]: action.matterport };
    default:
      return state;
  }
}
