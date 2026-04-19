export type IUser = {
  name: string;
} | null;

export type IAuth = {
  auth: boolean;
  user: IUser;
};