export const GET_ALL_SHAPE = "GET_ALL_SHAPE";
export const GET_ALL_SHAPE_SUCCESS = "GET_ALL_SHAPE_SUCCESS";
export const GET_ALL_SHAPE_ERROR = "GET_ALL_SHAPE_ERROR";
export const GET_SHAPE_LIST = "GET_SHAPE_LIST";
export const GET_SHAPE_LIST_SUCCESS = "GET_SHAPE_LIST_SUCCESS";
export const GET_SHAPE_LIST_ERROR = "GET_SHAPE_LIST_ERROR";
export const GET_CATEGORY_LIST = "GET_CATEGORY_LIST";
export const GET_CATEGORY_LIST_SUCCESS = "GET_CATEGORY_LIST_SUCCESS";
export const GET_CATEGORY_LIST_ERROR = "GET_CATEGORY_LIST_ERROR";
export const ADD_SHAPE = "ADD_SHAPE";
export const ADD_SHAPE_SUCCESS = "ADD_SHAPE_SUCCESS";
export const ADD_SHAPE_ERROR = "ADD_SHAPE_ERROR";
export const GET_SHAPE = "GET_SHAPE";
export const GET_SHAPE_SUCCESS = "GET_SHAPE_SUCCESS";
export const GET_SHAPE_ERROR = "GET_SHAPE_ERROR";
export const EDIT_SHAPE = "EDIT_SHAPE";
export const EDIT_SHAPE_SUCCESS = "EDIT_SHAPE_SUCCESS";
export const EDIT_SHAPE_ERROR = "EDIT_SHAPE_ERROR";
export const DELETE_SHAPE = "DELETE_SHAPE";
export const DELETE_SHAPE_SUCCESS = "DELETE_SHAPE_SUCCESS";
export const DELETE_SHAPE_ERROR = "DELETE_SHAPE_ERROR";
export const RESET_SHAPE = "RESET_SHAPE";

export const getAllShape = () => ({
  type: GET_ALL_SHAPE,
  payload: {},
});

export const getAllShapeSuccess = (shapes) => ({
  type: GET_ALL_SHAPE_SUCCESS,
  payload: shapes,
});

export const getAllShapeError = (error) => ({
  type: GET_ALL_SHAPE_ERROR,
  payload: error,
});

export const getShapeList = () => ({
  type: GET_SHAPE_LIST,
  payload: {},
});

export const getShapeListSuccess = (shapeList) => ({
  type: GET_SHAPE_LIST_SUCCESS,
  payload: { shapeList },
});

export const getShapeListError = (error) => ({
  type: GET_SHAPE_LIST_ERROR,
  payload: error,
});

export const getCategoryList = () => ({
  type: GET_CATEGORY_LIST,
  payload: {},
});

export const getCategoryListSuccess = (categoryList) => ({
  type: GET_CATEGORY_LIST_SUCCESS,
  payload: { categoryList },
});

export const getCategoryListError = (error) => ({
  type: GET_CATEGORY_LIST_ERROR,
  payload: error,
});

export const addShape = (shapeData, navigate) => ({
  type: ADD_SHAPE,
  payload: { shapeData, navigate },
});

export const addShapeSuccess = (success) => ({
  type: ADD_SHAPE_SUCCESS,
  payload: success,
});

export const addShapeError = (error) => ({
  type: ADD_SHAPE_ERROR,
  payload: error,
});

export const getShape = (shapeId) => ({
  type: GET_SHAPE,
  payload: { shapeId },
});

export const getShapeSuccess = (shapeData) => ({
  type: GET_SHAPE_SUCCESS,
  payload: shapeData,
});

export const getShapeError = (error) => ({
  type: GET_SHAPE_ERROR,
  payload: error,
});

export const editShape = (shapeId, shapeData, navigate) => ({
  type: EDIT_SHAPE,
  payload: { shapeId, shapeData, navigate },
});

export const editShapeSuccess = (success) => ({
  type: EDIT_SHAPE_SUCCESS,
  payload: success,
});

export const editShapeError = (error) => ({
  type: EDIT_SHAPE_ERROR,
  payload: error,
});

export const deleteShape = (shapeId) => ({
  type: DELETE_SHAPE,
  payload: { shapeId },
});

export const deleteShapeSuccess = (success) => ({
  type: DELETE_SHAPE_SUCCESS,
  payload: success,
});

export const deleteShapeError = (error) => ({
  type: DELETE_SHAPE_ERROR,
  payload: error,
});

export const resetShape = () => ({
  type: RESET_SHAPE,
  payload: {},
});
