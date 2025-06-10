import { createWebHistory, createRouter } from "vue-router";

import Login from "@/pages/Login.vue";
import Welcome from "@/pages/Welcome.vue";
import Service from "@/pages/Service.vue";
import About from "@/pages/About.vue";
import Dashboard from "@/pages/Dashboard.vue";
import Booking from "@/pages/Booking.vue";
import Registration from "@/pages/Registration.vue";
import BookingAdmin from "@/pages/BookingAdmin.vue";
import ShowBooking from "@/pages/ShowBooking.vue";
import Reports from "@/pages/Reports.vue";
import Personal from "@/pages/Personal.vue";
import PersonalShow from "@/pages/PersonalShow.vue";
import CreatePersonal from "@/pages/CreatePersonal.vue";
import Rooms from "@/pages/Rooms.vue";
import ChatsNewMenager from "@/pages/ChatsNewMenager.vue";
import ServicePersonal from "@/pages/ServicePersonal.vue";
import RoomsPersonal from "@/pages/RoomsPersonal.vue";

const routes = [
  { path: "/", component: Welcome },
  { path: "/login", component: Login, meta: { guestOnly: true } },
  { path: "/registration", component: Registration, meta: { guestOnly: true } },
  { path: "/services", component: Service },
  { path: "/about", component: About },
  { path: "/service/:id", component: Service, meta: { roles: [3] } },
  { path: "/dashboard", component: Dashboard, meta: { roles: [1] } },
  { path: "/rooms", component: Rooms, meta: { roles: [2] } },
  {
    name: "booking",
    path: "/booking",
    component: Booking,
    meta: { roles: [1] },
  },
  { path: "/booking/menager", component: BookingAdmin, meta: { roles: [4] } },
  {
    name: "showBooking",
    path: "/booking/menager/:id",
    component: ShowBooking,
    meta: { roles: [4] },
  },
  { path: "/reports", component: Reports, meta: { roles: [2] } },
  { path: "/personals", component: Personal, meta: { roles: [2] } },
  {
    name: "showPersonal",
    path: "/personal/:id",
    component: PersonalShow,
    meta: { roles: [2] },
  },
  {
    name: "createPersonal",
    path: "/personal/create",
    component: CreatePersonal,
    meta: { roles: [2] },
  },
  { path: "/newChats", component: ChatsNewMenager, meta: { roles: [4] } },
  {path: "/servicePersonal", component: ServicePersonal, meta: {roles:[3]}},
  {path: "/roomsPersonal", component:RoomsPersonal, meta: {roles: [3]}}
];

export function createR(store) {
  const router = createRouter({
    history: createWebHistory(),
    routes,
  });

  router.beforeEach((to, from) => {
    const user = store.state.user;
    const isAuth = !!user;
    const role = user?.roles_idRole || 0;

    if (to.meta.guestOnly && isAuth) {
      return redirectByRole(role);
    }

    if (to.path === "/" && isAuth) {
      if (role !== 1) {
        return redirectByRole(role);
      }
    }

    if (to.meta.roles && to.meta.roles.length > 0) {
      if (!isAuth) {
        return { path: "/login" };
      }
      if (!to.meta.roles.includes(role)) {
        return redirectByRole(role);
      }
    }

    return true;
  });

  return { router };
}

function redirectByRole(role) {
  switch (role) {
    case 1:
      return { path: "/dashboard" };
    case 2:
      return { path: "/personals" };
    case 3:
      return { path: "/roomsPersonal" };
    case 4:
      return { path: "/booking/menager" };
    default:
      return { path: "/login" };
  }
}
