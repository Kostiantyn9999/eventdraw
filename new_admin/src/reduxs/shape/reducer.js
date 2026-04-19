import {
  GET_ALL_SHAPE,
  GET_ALL_SHAPE_SUCCESS,
  GET_ALL_SHAPE_ERROR,
  GET_SHAPE_LIST,
  GET_SHAPE_LIST_SUCCESS,
  GET_SHAPE_LIST_ERROR,
  GET_CATEGORY_LIST,
  GET_CATEGORY_LIST_SUCCESS,
  GET_CATEGORY_LIST_ERROR,
  ADD_SHAPE,
  ADD_SHAPE_SUCCESS,
  ADD_SHAPE_ERROR,
  GET_SHAPE,
  GET_SHAPE_SUCCESS,
  GET_SHAPE_ERROR,
  EDIT_SHAPE,
  EDIT_SHAPE_SUCCESS,
  EDIT_SHAPE_ERROR,
  DELETE_SHAPE,
  DELETE_SHAPE_SUCCESS,
  DELETE_SHAPE_ERROR,
  RESET_SHAPE,
} from "reduxs/actions";

const INIT_STATE = {
  shapes: null,
  shapeList: null,
  categoryList: null,
  shapeData: null,
  shapeId: null,
  success: false,
  loading: false,
  delLoading: false,
  error: null,
};

const shapeReducer = (state = INIT_STATE, action) => {
  switch (action.type) {
    case GET_ALL_SHAPE:
      return {
        ...state,
        error: null,
      };
    case GET_ALL_SHAPE_SUCCESS:
      return {
        ...state,
        shapes: action.payload,
        error: null,
      };
    case GET_ALL_SHAPE_ERROR:
      return {
        ...state,
        shapes: null,
        error: action.payload,
      };
    case GET_SHAPE_LIST:
      return {
        ...state,
        loading: true,
        shapeData: null,
        shapeId: null,
        error: null,
      };
    case GET_SHAPE_LIST_SUCCESS:
      return {
        ...state,
        loading: false,
        shapeList: action.payload.shapeList,
        error: null,
      };
    case GET_SHAPE_LIST_ERROR:
      return {
        ...state,
        loading: false,
        shapeList: null,
        error: action.payload,
      };
    case GET_CATEGORY_LIST:
      return {
        ...state,
        loading: true,
        categoryList: null,
        error: null,
      };
    case GET_CATEGORY_LIST_SUCCESS:
      return {
        ...state,
        loading: false,
        categoryList: action.payload.categoryList,
        error: null,
      };
    case GET_CATEGORY_LIST_ERROR:
      return {
        ...state,
        loading: false,
        categoryList: null,
        error: action.payload,
      };
    case ADD_SHAPE:
      return { ...state, loading: true, error: null };
    case ADD_SHAPE_SUCCESS:
      return {
        ...state,
        loading: false,
        success: action.payload,
        error: null,
      };
    case ADD_SHAPE_ERROR:
      return {
        ...state,
        loading: false,
        success: false,
        error: action.payload,
      };
    case GET_SHAPE:
      return { ...state, error: null };
    case GET_SHAPE_SUCCESS:
      return {
        ...state,
        shapeData: action.payload,
        error: null,
      };
    case GET_SHAPE_ERROR:
      return {
        ...state,
        shapeData: null,
        error: action.payload,
      };
    case EDIT_SHAPE:
      return { ...state, loading: true, error: null };
    case EDIT_SHAPE_SUCCESS:
      return {
        ...state,
        loading: false,
        success: action.payload,
        error: null,
      };
    case EDIT_SHAPE_ERROR:
      return {
        ...state,
        loading: false,
        success: false,
        error: action.payload,
      };
    case DELETE_SHAPE:
      return { ...state, delLoading: true, error: null };
    case DELETE_SHAPE_SUCCESS:
      return {
        ...state,
        delLoading: false,
        success: action.payload,
        error: null,
      };
    case DELETE_SHAPE_ERROR:
      return {
        ...state,
        delLoading: false,
        success: false,
        error: action.payload,
      };
    case RESET_SHAPE:
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
export default shapeReducer;
