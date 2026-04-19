import { all, call, fork, put, takeEvery } from "redux-saga/effects";
import { GET_ALL_USER, GET_USER_LIST, ADD_USER, GET_USER, EDIT_USER, DELETE_USER } from "../actions";
import {
  getAllUserSuccess,
  getAllUserError,
  getUserList,
  getUserListSuccess,
  getUserListError,
  addUserSuccess,
  addUserError,
  getUserSuccess,
  getUserError,
  editUserSuccess,
  editUserError,
  deleteUserSuccess,
  deleteUserError,
} from "./action";
import { toaster, parseMessage, handleResponseErrorMessage } from "helpers";
import UserService from "services/UserService";

export function* watchgetAllUser() {
  yield takeEvery(GET_ALL_USER, getAllUser);
}

const getAllUserAsync = async () => {
  return UserService.getAllUser();
};

function* getAllUser() {
  try {
    const response = yield call(getAllUserAsync);
    if (response.data) {
      yield put(getAllUserSuccess(response.data.data));
    } else {
      toaster("", response.data.message);
      yield put(getAllUserError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getAllUserError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetUserList() {
  yield takeEvery(GET_USER_LIST, getUserListAc);
}

const getUserListAsync = async () => {
  return UserService.getAllUser();
};

function* getUserListAc({ payload }) {
  try {
    const response = yield call(getUserListAsync);

    if (response.data) {
      yield put(getUserListSuccess(response.data));
    } else {
      toaster("", response.data.message);
      yield put(getUserListError(response.data.message));
    }
  } catch (error) {
    const errMessage = handleResponseErrorMessage(error);
    yield put(getUserListError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchAddUser() {
  yield takeEvery(ADD_USER, addUser);
}

const addUserAsync = async (data) => {
  return UserService.addUser(data);
};

function* addUser({ payload }) {
  const { navigate } = payload;
  try {
    const response = yield call(addUserAsync, payload.userData);
    if (!response.data.error) {
      toaster("success", response.data.message);
      yield put(addUserSuccess(true));
      navigate(`${process.env.PUBLIC_URL}/user`);
    } else {
      toaster("", response.data.message);
      yield put(addUserError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(addUserError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchGetUser() {
  yield takeEvery(GET_USER, getUser);
}

const getUserAsync = async (id) => {
  return UserService.getUser(id);
};

function* getUser({ payload }) {
  try {
    const response = yield call(getUserAsync, payload.userId);
    if (response.data) {
      yield put(getUserSuccess(response.data));
    } else {
      toaster("", response.data.message);
      yield put(getUserError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(getUserError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchEditUser() {
  yield takeEvery(EDIT_USER, editUser);
}

const editUserAsync = async (data, id) => {
  return UserService.editUser(data, id);
};

function* editUser({ payload }) {
  const { navigate } = payload;
  try {
    const response = yield call(editUserAsync, payload.userData, payload.userId);
    if (response.data.state) {
      toaster("success", "Successfully Updated!");
      yield put(editUserSuccess(response.data.state));
      navigate(`${process.env.PUBLIC_URL}/user`);
    } else {
      toaster("", "Failed!");
      yield put(editUserError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(editUserError(errMessage));
    toaster("error", errMessage);
  }
}

export function* watchDeleteUser() {
  yield takeEvery(DELETE_USER, deleteUser);
}

const deleteUserAsync = async (id) => {
  return UserService.deleteUser(id);
};

function* deleteUser({ payload }) {
  try {
    const response = yield call(deleteUserAsync, payload.userId);
    if (response.data) {
      toaster("success", "Successfully Deleted!");
      yield put(deleteUserSuccess(true));
      yield put(getUserList());
    } else {
      toaster("", response.data.message);
      yield put(deleteUserError(response.data.message));
    }
  } catch (error) {
    const errMessage = parseMessage(handleResponseErrorMessage(error));
    yield put(deleteUserError(errMessage));
    toaster("error", errMessage);
  }
}

export default function* rootSaga() {
  yield all([
    fork(watchgetAllUser),
    fork(watchGetUserList),
    fork(watchAddUser),
    fork(watchGetUser),
    fork(watchEditUser),
    fork(watchDeleteUser),
  ]);
}
