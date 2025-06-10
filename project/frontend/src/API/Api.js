import { saveAs } from "file-saver";
import axios from "axios";
import echo from "@/echo";

const API_BASE_URL = "https://mineturic.ru/api";

const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  withCredentials: true,
});

export const getMessages = async (chatId) => {
  const response = await axios.get(
    `${API_BASE_URL}/getMessages?chatId=${chatId}`
  );
  return response.data;
};

export const login = async (login, password, store, router) => {
  try {
    const response = await axios.post(`${API_BASE_URL}/login`, {
      login,
      password,
    });
    store.commit("login", response.data);
    router.push("/");
  } catch (error) {
    return error.response.data.message;
  }
};

export const fetchCurrentUser = async (store) => {
  try {
    const response = await axiosInstance.get("/user");
    store.commit("login", response.data);
    return response.data;
  } catch {
    store.commit("logout");
    return null;
  }
};

export const fetchCurrentRoom = async (store) => {
  try {
    const response = await axiosInstance.get("/userRoom");
    store.commit("setRoom", response.data);
    return response.data;
  } catch {
    return null;
  }
};

export const fetchCurrentOrder = async (store) => {
  try {
    const response = await axiosInstance.get("/userOrder");
    store.commit("setOrder", response.data);
    return response.data;
  } catch {
    return null;
  }
};

export const fetchChat = async () => {
  try {
    const response = await axiosInstance.get("/joinChat");
    return response.data;
  } catch {
    return false;
  }
};

export const getNewChats = async () => {
  const response = await axiosInstance.get("/newChats");
  return response.data;
};

export const sendMessage = async (idChat, message) => {
  const response = await axios.post(
    `${API_BASE_URL}/sendMessage`,
    { idChat, message },
    { headers: { "X-Socket-ID": echo.socketId() } }
  );
  return response.data;
};

export const sendMessageMenager = async (idChat, message) => {
  const response = await axios.post(
    `${API_BASE_URL}/sendMessageMenager`,
    { idChat, message },
    { headers: { "X-Socket-ID": echo.socketId() } }
  );
  return response.data;
};

export const registration = async (values, store) => {
  try {
    const response = await axios.post(`${API_BASE_URL}/registration`, values);
    store.commit("login", response.data);
    return response.data;
  } catch (error) {
    throw error;
  }
};

export const logout = async (store) => {
  const response = await axios.post(`${API_BASE_URL}/logout`);
  store.commit("logout");
  return response.data;
};

export const getTypeRooms = async () => {
  const response = await axios.get(`${API_BASE_URL}/getTypeRooms`);
  return response.data;
};

export const getServices = async () => {
  const response = await axios.get(`${API_BASE_URL}/getServices`);
  return response.data;
};

export const getPersonal = async () => {
  const response = await axios.get(`${API_BASE_URL}/personal`);
  return response.data;
};

export const getPersonalUser = async (id) => {
  const response = await axios.get(`${API_BASE_URL}/personal`, {
    params: { id },
  });
  return response.data;
};

export const deleteUser = async (id) => {
  const response = await axios.delete(`${API_BASE_URL}/personal/${id}`);
  return response.data;
};

export const updateUser = async (id, role) => {
  const response = await axios.put(`${API_BASE_URL}/personal/${id}`, {
    idRole: role,
  });
  return response.data;
};

export const createUser = async (values) => {

    const response = await axios.post(`${API_BASE_URL}/personal`, values);
    return response.data;

};

export const createBooking = async (
  message,
  onDate,
  endDate,
  idTypeRoom,
  idUser
) => {
  const response = await axios.post(`${API_BASE_URL}/order`, {
    message,
    onDate,
    endDate,
    idTypeRoom,
    idUser,
  });
  return response.data;
};

export const getOrders = async () => {
  const response = await axios.get(`${API_BASE_URL}/order`);
  return response.data;
};
export const getOrdersOnService = async () => {
  const response = await axios.get(`${API_BASE_URL}/orderService`);
  return response.data;
};
export const acceptOrderOnService = async (id) => {
  const response = await axios.put(`${API_BASE_URL}/orderService/${id}`, {
    status: "Выполнено",
  });
  return response.data;
};
export const getFullOrder = async (id) => {
  const response = await axios.get(`${API_BASE_URL}/orderInfo`, {
    params: { id },
  });
  return response.data;
};

export const getServicesUser = async () => {
  const response = await axios.get(`${API_BASE_URL}/service`);
  return response.data;
};

export const createOrderOnService = async (idRoom, idService, idUser) => {
  return await axios.post(`${API_BASE_URL}/orderService`, {
    idRoom,
    idUser,
    idService,
  });
};

export const acceptOrder = async (id, number) => {
  const response = await axios.put(`${API_BASE_URL}/order/${id}`, {
    id,
    status: "В процессе",
    room: number,
  });
  return response.data;
};

export const cencelOrder = async (id) => {
  const response = await axios.put(`${API_BASE_URL}/order/${id}`, {
    id,
    status: "Отменен",
  });
  return response.data;
};

export const getRoomsOrders = async (id) => {
  const response = await axios.get(`${API_BASE_URL}/room`, {
    params: { type: id },
  });
  return response.data;
};

export const getRooms = async () => {
  const response = await axios.get(`${API_BASE_URL}/room?busy=true`);
  return response.data;
};

export const deleteRoom = async (id) => {
  const response = await axios.delete(`${API_BASE_URL}/room/${id}`);
  return response.data;
};

export const createRoom = async (type, number) => {
  try {
    const response = await axios.post(`${API_BASE_URL}/room`, { type, number });
    return response.data;
  } catch (error) {
    alert(error.response.data.message);
  }
};

export const getOrdersReport = async (dateFrom, dateTo) => {
  try {
    const response = await axios.get(
      `${API_BASE_URL}/getReportOrders?dateFrom=${dateFrom}&dateTo=${dateTo}`,
      { responseType: "blob" }
    );
    const blob = new Blob([response.data], {
      type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    });
    saveAs(blob, `reportOrder_from${dateFrom}_to${dateTo}.xlsx`);
  } catch (error) {
    alert("Не удалось загрузить отчёт");
  }
};

export const getOrdersonServiceReport = async (dateFrom, dateTo) => {
  try {
    const response = await axios.get(
      `${API_BASE_URL}/getReportServiceOrders?dateFrom=${dateFrom}&dateTo=${dateTo}`,
      { responseType: "blob" }
    );
    const blob = new Blob([response.data], {
      type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    });
    saveAs(blob, `reportOrderOnServices_from${dateFrom}_to${dateTo}.xlsx`);
  } catch (error) {
    alert("Не удалось загрузить отчёт");
  }
};
