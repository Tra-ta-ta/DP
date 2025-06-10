import "./assets/main.css";

import { createApp } from "vue";
import App from "./App.vue";
import axios from "axios";
import { create } from "@/store/store";
import { createR } from "@/router/router";
import { fetchCurrentOrder, fetchCurrentUser } from "@/API/Api";

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.get("https://mineturic.ru/sanctum/csrf-cookie");

const { store } = create();
await fetchCurrentUser(store);
await fetchCurrentOrder(store);
const { router } = createR(store);

createApp(App).use(router).use(store).mount("#app");
