import { createStore, applyMiddleware, compose, Store } from "redux";
import createSagaMiddleware from "redux-saga";
// import logger from 'redux-logger'
import thunk from "redux-thunk";

import rootSaga from "./sagas";
import rootReducer from "./rootReducer";

declare global {
  interface Window {
    __REDUX_DEVTOOLS_EXTENSION_COMPOSE__?: typeof compose;
  }
}

// ! Saga
const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;
const sagaMiddleware = createSagaMiddleware();
const middleware = [thunk, sagaMiddleware];

const configureStore = (initialState?: any): Store => {
  let enhancer = composeEnhancers(applyMiddleware(...middleware));
  return createStore(rootReducer, initialState, enhancer);
};

const store = configureStore();

sagaMiddleware.run(rootSaga);

// ? Redux
// const composeEnhancers =
//   (process.env.NODE_ENV === "development" &&
//     typeof window !== "undefined" &&
//     window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__) ||
//   compose;
// const middleware = [thunk];
// const configureStore = (initialState?: any): Store => {
//   let enhancer = composeEnhancers(applyMiddleware(...middleware));
//   return createStore(rootReducer, initialState, enhancer);
// };
// const store = configureStore();

export type RootState = ReturnType<typeof rootReducer>;
export type AppDispatch = typeof store.dispatch;
export type AppState = typeof store.getState;

export default store;
