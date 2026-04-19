import Req from "interceptors/TokenInterceptor";

const UserService = (function () {
  function _getAllUser() {
    return Req.get(`${process.env.REACT_APP_API_URL_1}/api/users`);
  }
  function _getUser(id) {
    return Req.get(`${process.env.REACT_APP_API_URL_1}/api/users/${id}`);
  }
  function _addUser(data) {
    return Req.post(`${process.env.REACT_APP_API_URL_1}/api/users`, data);
  }
  function _editUser(data, id) {
    return Req.post(`${process.env.REACT_APP_API_URL_1}/api/users/${id}`, data);
  }
  function _deleteUser(id) {
    return Req.delete(`${process.env.REACT_APP_API_URL_1}/api/users/${id}`);
  }
  return {
    getAllUser: _getAllUser,
    getUser: _getUser,
    addUser: _addUser,
    editUser: _editUser,
    deleteUser: _deleteUser,
  };
})();
export default UserService;
