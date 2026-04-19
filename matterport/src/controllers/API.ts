import axios from "axios";
import history from "src/navigation/history";
import routes from "src/navigation/routes";

// const user = JSON.parse(localStorage.getItem("user"));
// console.log("API", user.access_token);
declare module "axios" {
  export interface AxiosInstance {
    request<T = any>(config: AxiosRequestConfig): Promise<T>;
    get<T = any>(url: string, config?: AxiosRequestConfig): Promise<T>;
    delete<T = any>(url: string, config?: AxiosRequestConfig): Promise<T>;
    head<T = any>(url: string, config?: AxiosRequestConfig): Promise<T>;
    post<T = any>(
      url: string,
      data?: any,
      config?: AxiosRequestConfig
    ): Promise<T>;
    put<T = any>(
      url: string,
      data?: any,
      config?: AxiosRequestConfig
    ): Promise<T>;
    patch<T = any>(
      url: string,
      data?: any,
      config?: AxiosRequestConfig
    ): Promise<T>;
  }
}

export const API = (() => {
  return axios.create({
    baseURL: "https://flooriing.com/event_api/public/",
    headers: {
      Accept: "application/json",
      // Authorization: `Bearer ${user ? user.access_token : ""}`,
      "Content-Type": "application/json",
    },
  });
})();

API.interceptors.request.use(
  (config) => {
    if (localStorage.getItem("user")) {
      try {
        const user = JSON.parse(localStorage.getItem("user"));
        config.headers.Authorization = `Bearer ${
          user ? user.access_token : ""
        }`;
      } catch (e) {}
    }

    return config;
  },
  (error) => Promise.reject(error)
);

API.interceptors.response.use(
  (response) => Promise.resolve(response.data),
  (err) => {
    if (err.status === 401) {
      history.push(routes.SIGN_IN);
    }

    return Promise.reject(err);
  }
);

// API.interceptors.response.use(undefined, (err) => {
//   const error = err.response;
//   if (error.status === 401) {
//     history.push(routes.SIGN_IN);
//   }

//   throw err.response;
// });
