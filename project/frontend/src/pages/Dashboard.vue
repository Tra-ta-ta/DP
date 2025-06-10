<template>
  <ToggleThemeBtn />
  <div
    class="min-h-screen bg-white dark:bg-gray-700 text-black flex flex-col items-center py-12 px-4"
  >
    <h1 class="text-4xl font-bold mb-10 text-center dark:text-gray-200">
      Профиль жильца
    </h1>
    <div class="w-full max-w-6xl bg-gray-100 p-8 rounded-xl shadow-md mb-12">
      <h2 class="text-2xl font-semibold mb-4">Личная информация</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p>
          <span class="font-semibold">ФИО:</span> {{ $store.state.user.name }}
          {{ $store.state.user.surname }} {{ $store.state.user.thirdname }}
        </p>
      </div>
    </div>

    <div class="w-full max-w-6xl grid grid-cols-1 md:grid-cols-2 gap-8">
      <div class="bg-gray-100 p-6 rounded-xl shadow-md">
        <h3 class="text-xl font-semibold mb-4 text-center">История броней</h3>
        <ul class="space-y-3">
          <li
            v-for="booking in paginatedBookings"
            :key="booking.id"
            class="border-b pb-2"
          >
            <p class="font-medium">Номер брони - {{ booking.id }}</p>
            <p class="font-medium">Статус '{{ booking.status }}''</p>
            <p class="text-sm text-gray-600">
              {{ booking.onDate }} – {{ booking.endDate }}
            </p>
          </li>
        </ul>
        <div class="flex justify-center mt-4 space-x-2">
          <button
            @click="prevBookingPage"
            :disabled="bookingPage === 1"
            class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50"
          >
            Назад
          </button>
          <button
            @click="nextBookingPage"
            :disabled="bookingPage >= totalBookingPages"
            class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50"
          >
            Вперёд
          </button>
        </div>
      </div>

      <div class="bg-gray-100 p-6 rounded-xl shadow-md">
        <h3 class="text-xl font-semibold mb-4 text-center">История услуг</h3>
        <ul class="space-y-3">
          <li
            v-for="service in paginatedServices"
            :key="service.id"
            class="border-b pb-2"
          >
            <p class="font-medium">{{ service.service.name }}</p>
            <p class="font-medium">Статус: {{ service.status }}</p>
            <p class="text-sm text-gray-600">Дата: {{ new Date(service.created_at).toLocaleDateString('ru-RU') }}</p>
          </li>
        </ul>
        <div class="flex justify-center mt-4 space-x-2">
          <button
            @click="prevServicePage"
            :disabled="servicePage === 1"
            class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50"
          >
            Назад
          </button>
          <button
            @click="nextServicePage"
            :disabled="servicePage >= totalServicePages"
            class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50"
          >
            Вперёд
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { getOrders, getServicesUser } from "@/API/Api";
import ToggleThemeBtn from "@/components/ToggleThemeBtn.vue";
import { useStore } from "vuex";
import echo from "@/echo";
const bookings = ref([]);
const loadOrders = async () => {
  bookings.value = await getOrders();
};
const loadServices = async () => {
  services.value = await getServicesUser();
};
onMounted(() => {
  loadOrders();
  loadServices();
  joinBroadcast();
});
onUnmounted(() => {
  logoutBroadcast();
});
const store = useStore();
const joinBroadcast = () => {
  echo
    .private(`Order.${store.state.order}`)
    .listen("StatusOrderChange", (e) => {
      const order = bookings.value.find(
        (booking) => booking.id === store.state.order
      );
      order.status = e.status;
    });
};
const logoutBroadcast = () => {
  echo.leave(`Order.${store.state.order}`);
};
const services = ref([]);

const bookingPage = ref(1);
const bookingsPerPage = 3;
const totalBookingPages = computed(() =>
  Math.ceil(bookings.value.length / bookingsPerPage)
);
const paginatedBookings = computed(() =>
  bookings.value.slice(
    (bookingPage.value - 1) * bookingsPerPage,
    bookingPage.value * bookingsPerPage
  )
);

function nextBookingPage() {
  if (bookingPage.value < totalBookingPages.value) bookingPage.value++;
}
function prevBookingPage() {
  if (bookingPage.value > 1) bookingPage.value--;
}

const servicePage = ref(1);
const servicesPerPage = 2;
const totalServicePages = computed(() =>
  Math.ceil(services.value.length / servicesPerPage)
);
const paginatedServices = computed(() =>
  services.value.slice(
    (servicePage.value - 1) * servicesPerPage,
    servicePage.value * servicesPerPage
  )
);

function nextServicePage() {
  if (servicePage.value < totalServicePages.value) servicePage.value++;
}
function prevServicePage() {
  if (servicePage.value > 1) servicePage.value--;
}
</script>
