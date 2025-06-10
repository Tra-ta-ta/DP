<template>
  <div class="min-h-screen bg-gray-50 text-gray-800 px-6 py-8">
    <div v-if="order" class="max-w-3xl mx-auto">
      <h1 class="text-2xl font-semibold mb-6 text-center">
        Детали заказа №{{ order.id }}
      </h1>
      <div class="bg-white rounded-md shadow-md p-6 space-y-4">
        <div>
          <h2 class="text-lg font-semibold mb-2">Информация о заказе</h2>
          <p>
            <span class="text-gray-500">Дата создания:</span>
            {{ formatDate(order.created_at) }}
          </p>
          <p>
            <span class="text-gray-500">Дата приезда:</span>
            {{ formatDate(order.onDate) }}
            <span class="text-gray-500">Дата отъезда:</span>
            {{ formatDate(order.endDate) }}
          </p>
          <p>
            <span class="text-gray-500">Статус: </span>
            <span :class="statusClasses(order.status)">
              {{ order.status }}
            </span>
          </p>
        </div>
        <div>
          <h2 class="text-lg font-semibold mb-2">Клиент</h2>
          <p><span class="text-gray-500">Имя:</span> {{ order.user.name }}</p>
          <p>
            <span class="text-gray-500">Телефон:</span> {{ order.user.phone }}
          </p>
        </div>
        <div
          v-if="order.status === 'Новый'"
          class="text-right pt-4 border-t border-gray-200"
        >
          <select v-model="selectRoom">
            <option v-for="room in rooms" :key="room.id" :value="room.id">
              {{ room.number }}
            </option>
          </select>
          <button
            @click="acceptOrderHendler(order.id, selectRoom)"
            class="px-3"
          >
            Принять
          </button>
          <button @click="cencelOrder(order.id)" class="px-3">Отменить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  acceptOrder,
  cencelOrder,
  getFullOrder,
  getRoomsOrders,
} from "@/API/Api";
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";

const acceptOrderHendler = async (id, number) => {
  order.value = await acceptOrder(id, number);
};
const route = useRoute();
onMounted(async () => {
  order.value = await getFullOrder(route.params.id);
  rooms.value = await getRoomsOrders(order.value.typeRoom_idTypeRoom);
});

const selectRoom = ref();
const order = ref(null);
const rooms = ref(null);
const formatDate = (date) => {
  return new Date(date).toLocaleDateString("ru-RU");
};

const statusClasses = (status) => {
  switch (status) {
    case "Завершен":
      return "text-green-600 font-medium";
    case "В обработке":
      return "text-yellow-600 font-medium";
    case "Отменен":
      return "text-red-500 font-medium";
    default:
      return "text-gray-500";
  }
};
</script>
