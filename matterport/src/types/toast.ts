export type IToastType = "";

export interface IToast {
  id?: number;
  content?: {
    header: string;
    message: string;
  };
  type?: IToastType;
}
