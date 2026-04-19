import {
  GET_ALL_MATTERPORT,
  GET_ALL_MATTERPORT_SUCCESS,
  GET_ALL_MATTERPORT_ERROR,
  GET_MATTERPORT_LIST,
  GET_MATTERPORT_LIST_SUCCESS,
  GET_MATTERPORT_LIST_ERROR,
  ADD_MATTERPORT,
  ADD_MATTERPORT_SUCCESS,
  ADD_MATTERPORT_ERROR,
  GET_MATTERPORT,
  GET_MATTERPORT_SUCCESS,
  GET_MATTERPORT_ERROR,
  EDIT_MATTERPORT,
  EDIT_MATTERPORT_SUCCESS,
  EDIT_MATTERPORT_ERROR,
  DELETE_MATTERPORT,
  DELETE_MATTERPORT_SUCCESS,
  DELETE_MATTERPORT_ERROR,
  RESET_MATTERPORT,
} from "reduxs/actions";

const INIT_STATE = {
  matterports: null,
  matterportList: null,
  matterportData: null,
  matterportId: null,
  success: false,
  loading: false,
  delLoading: false,
  error: null,
};

const matterportReducer = (state = INIT_STATE, action) => {
  switch (action.type) {
    case GET_ALL_MATTERPORT:
      return {
        ...state,
        error: null,
      };
    case GET_ALL_MATTERPORT_SUCCESS:
      return {
        ...state,
        matterports: action.payload,
        error: null,
      };
    case GET_ALL_MATTERPORT_ERROR:
      return {
        ...state,
        matterports: null,
        error: action.payload,
      };
    case GET_MATTERPORT_LIST:
      return {
        ...state,
        loading: true,
        matterportData: null,
        matterportId: null,
        error: null,
      };
    case GET_MATTERPORT_LIST_SUCCESS:
      return {
        ...state,
        loading: false,
        matterportList: action.payload.matterportList,
        error: null,
      };
    case GET_MATTERPORT_LIST_ERROR:
      return {
        ...state,
        loading: false,
        matterportList: null,
        error: action.payload,
      };
    case ADD_MATTERPORT:
      return { ...state, loading: true, error: null };
    case ADD_MATTERPORT_SUCCESS:
      return {
        ...state,
        loading: false,
        success: action.payload,
        error: null,
      };
    case ADD_MATTERPORT_ERROR:
      return {
        ...state,
        loading: false,
        success: false,
        error: action.payload,
      };
    case GET_MATTERPORT:
      return { ...state, loading: true, error: null };
    case GET_MATTERPORT_SUCCESS:
      return {
        ...state,
        loading: false,
        matterportData: action.payload,
        error: null,
      };
    case GET_MATTERPORT_ERROR:
      return {
        ...state,
        loading: false,
        matterportData: null,
        error: action.payload,
      };
    case EDIT_MATTERPORT:
      return { ...state, loading: true, error: null };
    case EDIT_MATTERPORT_SUCCESS:
      return {
        ...state,
        loading: false,
        success: action.payload,
        error: null,
      };
    case EDIT_MATTERPORT_ERROR:
      return {
        ...state,
        loading: false,
        success: false,
        error: action.payload,
      };
    case DELETE_MATTERPORT:
      return { ...state, delLoading: true, error: null };
    case DELETE_MATTERPORT_SUCCESS:
      return {
        ...state,
        delLoading: false,
        success: action.payload,
        error: null,
      };
    case DELETE_MATTERPORT_ERROR:
      return {
        ...state,
        delLoading: false,
        success: false,
        error: action.payload,
      };
    case RESET_MATTERPORT:
      return {
        ...state,
        loading: false,
        delLoading: false,
        success: false,
        error: null,
      };
    default:
      return { ...state };
  }
};
export default matterportReducer;
