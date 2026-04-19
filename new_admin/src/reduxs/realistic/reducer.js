import {
  GET_ALL_REALISTIC,
  GET_ALL_REALISTIC_SUCCESS,
  GET_ALL_REALISTIC_ERROR,
  GET_ALL_EVENT,
  GET_ALL_EVENT_SUCCESS,
  GET_ALL_EVENT_ERROR,
  GET_ALL_TEMPLATE,
  GET_ALL_TEMPLATE_SUCCESS,
  GET_ALL_TEMPLATE_ERROR,
  GET_REALISTIC_LIST,
  GET_REALISTIC_LIST_SUCCESS,
  GET_REALISTIC_LIST_ERROR,
  ADD_REALISTIC,
  ADD_REALISTIC_SUCCESS,
  ADD_REALISTIC_ERROR,
  GET_REALISTIC,
  GET_REALISTIC_SUCCESS,
  GET_REALISTIC_ERROR,
  EDIT_REALISTIC,
  EDIT_REALISTIC_SUCCESS,
  EDIT_REALISTIC_ERROR,
  DELETE_REALISTIC,
  DELETE_REALISTIC_SUCCESS,
  DELETE_REALISTIC_ERROR,
  RESET_REALISTIC,
} from "reduxs/actions";

const INIT_STATE = {
  realistics: null,
  events: null,
  templates: null,
  realisticList: null,
  realisticData: null,
  realisticId: null,
  success: false,
  loading: false,
  delLoading: false,
  error: null,
};

const realisticReducer = (state = INIT_STATE, action) => {
  switch (action.type) {
    case GET_ALL_REALISTIC:
      return {
        ...state,
        loading: true,
        error: null,
      };
    case GET_ALL_REALISTIC_SUCCESS:
      return {
        ...state,
        loading: false,
        realistics: action.payload.realistics,
        error: null,
      };
    case GET_ALL_REALISTIC_ERROR:
      return {
        ...state,
        loading: false,
        realistics: null,
        error: action.payload,
      };
    case GET_ALL_EVENT:
      return {
        ...state,
        loading: true,
        error: null,
      };
    case GET_ALL_EVENT_SUCCESS:
      return {
        ...state,
        loading: false,
        events: action.payload.events,
        error: null,
      };
    case GET_ALL_EVENT_ERROR:
      return {
        ...state,
        loading: false,
        events: null,
        error: action.payload,
      };
    case GET_ALL_TEMPLATE:
      return {
        ...state,
        loading: true,
        error: null,
      };
    case GET_ALL_TEMPLATE_SUCCESS:
      return {
        ...state,
        loading: false,
        templates: action.payload.templates,
        error: null,
      };
    case GET_ALL_TEMPLATE_ERROR:
      return {
        ...state,
        loading: false,
        templates: null,
        error: action.payload,
      };
    case GET_REALISTIC_LIST:
      return {
        ...state,
        loading: true,
        realisticData: null,
        realisticId: null,
        error: null,
      };
    case GET_REALISTIC_LIST_SUCCESS:
      return {
        ...state,
        loading: false,
        realisticList: action.payload.realisticList,
        error: null,
      };
    case GET_REALISTIC_LIST_ERROR:
      return {
        ...state,
        loading: false,
        realisticList: null,
        error: action.payload,
      };
    case ADD_REALISTIC:
      return { ...state, loading: true, error: null };
    case ADD_REALISTIC_SUCCESS:
      return {
        ...state,
        loading: false,
        success: action.payload,
        error: null,
      };
    case ADD_REALISTIC_ERROR:
      return {
        ...state,
        loading: false,
        success: false,
        error: action.payload,
      };
    case GET_REALISTIC:
      return { ...state, error: null };
    case GET_REALISTIC_SUCCESS:
      return {
        ...state,
        realisticData: action.payload,
        error: null,
      };
    case GET_REALISTIC_ERROR:
      return {
        ...state,
        realisticData: null,
        error: action.payload,
      };
    case EDIT_REALISTIC:
      return { ...state, loading: true, error: null };
    case EDIT_REALISTIC_SUCCESS:
      return {
        ...state,
        loading: false,
        success: action.payload,
        error: null,
      };
    case EDIT_REALISTIC_ERROR:
      return {
        ...state,
        loading: false,
        success: false,
        error: action.payload,
      };
    case DELETE_REALISTIC:
      return { ...state, delLoading: true, error: null };
    case DELETE_REALISTIC_SUCCESS:
      return {
        ...state,
        delLoading: false,
        success: action.payload,
        error: null,
      };
    case DELETE_REALISTIC_ERROR:
      return {
        ...state,
        delLoading: false,
        success: false,
        error: action.payload,
      };
    case RESET_REALISTIC:
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
export default realisticReducer;
