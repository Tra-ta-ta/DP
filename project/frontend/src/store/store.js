
import { createStore } from "vuex";

export function create() {
  const store = createStore({
    state: {
      isAuth: false,
      user: null,
      room: null,
      order: null,
    },
    mutations: {
      login(state, user) {
        state.isAuth = true;
        state.user = user;
      },
      setRoom(state, roomId) {
        state.room = roomId;
      },
      setOrder(state, orderId) {
        state.order = orderId;
      },
      logout(state) {
        state.user = null;
        state.isAuth = false;
        state.room = null;
        state.order = null;
      },
    },
  });
  return { store };
}
