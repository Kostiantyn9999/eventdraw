import { all, call, fork, put, takeEvery } from "redux-saga/effects";
import { GET_ALL_SHAPE, GET_SHAPE_LIST, GET_CATEGORY_LIST, ADD_SHAPE, GET_SHAPE, EDIT_SHAPE, DELETE_SHAPE } from "../actions";
import {
  getAllShapeSuccess,
  getAllShapeError,
  getShapeList,
  getShapeListSuccess,
  getShapeListError,
  getCategoryListSuccess,
  getCategoryListError,
  addShapeSuccess,
  addShapeError,
  getShapeSuccess,
  getShapeError,
  editShapeSuccess,
  editShapeError,
  deleteShapeSuccess,
  deleteShapeError,
} from "./action";
import { toaster, parseMessage, handleResponseErrorMessage } from "helpers";
import ShapeService from "services/ShapeService";
import { categoryList } from "configs/constants";
const categoryHash = {};
categoryList.forEach((cat) => {
  categoryHash[cat.id] = cat.name;
});

export function* watchgetAllShape() {
  yield takeEvery(GET_ALL_SHAPE, getAllShape);
}

const getAllShapeAsync = async () => {
  return ShapeService.getAllShape();
};

function* getAllShape() {
  try {
    const response = yield call(getAllShapeAsync);
    if (response.data) {
      yield put(getAllShapeSuccess(response.data.data));
    } else {
      toaster("", response.data.message);
      yield put(getAllShapeError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getAllShapeError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetShapeList() {
  yield takeEvery(GET_SHAPE_LIST, getShapeListAc);
}

const getShapeListAsync = async () => {
  return ShapeService.getAllShape();
};

function* getShapeListAc({ payload }) {
  try {
    const response = yield call(getShapeListAsync);
    if (response.data) {
      const list = [];
      response.data.forEach((item) => {
        list.push({ ...item, category: categoryHash[item.category] });
      });
      yield put(getShapeListSuccess(list));
    } else {
      toaster("", "Something went any wrong");
      yield put(getShapeListError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getShapeListError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetCategoryList() {
  yield takeEvery(GET_CATEGORY_LIST, getCategoryListAc);
}

const getCategoryListAsync = async () => {
  return ShapeService.getAllCategory();
};

function* getCategoryListAc({ payload }) {
  try {
    const response = yield call(getCategoryListAsync);

    if (response.data.success) {
      yield put(getCategoryListSuccess(response.data.data));
    } else {
      toaster("", response.data.message);
      yield put(getCategoryListError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getCategoryListError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchAddShape() {
  yield takeEvery(ADD_SHAPE, addShape);
}

const addShapeAsync = async (data) => {
  return ShapeService.addShape(data);
};

function* addShape({ payload }) {
  const { navigate } = payload;
  try {
    const response = yield call(addShapeAsync, payload.shapeData);
    if (!response.data.error) {
      toaster("success", response.data.message);
      yield put(addShapeSuccess(true));
      navigate(`${process.env.PUBLIC_URL}/shape`);
    } else {
      toaster("", response.data.error);
      yield put(addShapeError(response.data.error));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(addShapeError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetShape() {
  yield takeEvery(GET_SHAPE, getShape);
}

const getShapeAsync = async (id) => {
  return ShapeService.getShape(id);
};

function* getShape({ payload }) {
  try {
    const response = yield call(getShapeAsync, payload.shapeId);
    if (!response.data.error) {
      yield put(getShapeSuccess(response.data));
    } else {
      toaster("", response.data.error);
      yield put(getShapeError(response.data.error));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(getShapeError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchEditShape() {
  yield takeEvery(EDIT_SHAPE, editShape);
}

const editShapeAsync = async (data, id) => {
  return ShapeService.editShape(data, id);
};

function* editShape({ payload }) {
  const { navigate } = payload;
  try {
    const response = yield call(editShapeAsync, payload.shapeData, payload.shapeId);
    if (!response.data.error) {
      toaster("success", "Successfully Updated");
      yield put(editShapeSuccess(true));
      navigate(`${process.env.PUBLIC_URL}/shape`);
    } else {
      toaster("", response.data.error);
      yield put(editSShapeError(response.data.error));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(editShapeError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchDeleteShape() {
  yield takeEvery(DELETE_SHAPE, deleteShape);
}

const deleteShapeAsync = async (id) => {
  return ShapeService.deleteShape(id);
};

function* deleteShape({ payload }) {
  try {
    const response = yield call(deleteShapeAsync, payload.shapeId);
    if (!response.data.error) {
      toaster("success", "Successfully Deleted");
      yield put(deleteShapeSuccess(true));
      yield put(getShapeList());
    } else {
      toaster("", response.data.error);
      yield put(deleteShapeError(response.data.error));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(deleteShapeError(errMessage));
    toaster("error", errMessage);
  }
}

export default function* rootSaga() {
  yield all([
    fork(watchgetAllShape),
    fork(watchGetShapeList),
    fork(watchGetCategoryList),
    fork(watchAddShape),
    fork(watchGetShape),
    fork(watchEditShape),
    fork(watchDeleteShape),
  ]);
}
