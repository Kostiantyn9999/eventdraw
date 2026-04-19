import { IEvent, IFloor, ITemplate } from "types/floor";
import { IUser } from "types/user";
import { API } from "./API";

const url = "https://test.eventdraw.com.au/frontend/web/site/get-threed-json";
const eventUrl =
  "https://test.eventdraw.com.au/frontend/web/site/get-all-events";
const templateUrl =
  "https://test.eventdraw.com.au/frontend/web/site/get-all-templates";

export const authToken = async (token: string): Promise<IFloor> => {
  const formData = new FormData();
  formData.append("token", token);

  try {
    let d;

    let response = await fetch(url, {
      method: "POST",
      body: formData,
    });

    d = await response.json();

    if (response.ok) {
      return d;
    }
  } catch (err: any) {
    return Promise.reject(err.message ? err.message : token);
  }
};

export const getEvents = async (keyword: string): Promise<Array<IEvent>> => {
  return new Promise(async (resolve, reject) => {
    const formData = new FormData();

    try {
      let d;

      let response = await fetch(
        `${eventUrl}?search=${keyword}&t=${Date.now().toString(36)}`,
        {
          method: "POST",
          body: formData,
        }
      );

      d = await response.json();

      if (response.ok) {
        return resolve(d);
      }
    } catch (err: any) {
      return Promise.reject(err.message ? err.message : "");
    }
  });
};

export const getTemplates = async (
  keyword: string
): Promise<Array<ITemplate>> => {
  return new Promise(async (resolve, reject) => {
    try {
      const formData = new FormData();
      let d;

      let response = await fetch(
        `${templateUrl}?search=${keyword}&t=${Date.now().toString(36)}`,
        {
          method: "POST",
          body: formData,
        }
      );
      d = await response.json();

      if (response.ok) {
        return resolve(d);
      }
    } catch (err: any) {
      return Promise.reject(err.message ? err.message : "");
    }
  });
};

export const login = async (username: string, password: string) => {
  return API.post<IUser>("/api/login", {
    email: username,
    password: password,
  });
};
