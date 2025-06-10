<template>
  <div class="min-h-screen bg-gray-50 text-gray-800 px-6 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-2xl font-semibold mb-6 text-center">Список заказов</h1>
      <div class="flex justify-end mb-4">
        <select
          v-model="sortKey"
          @change="sortOrders"
          class="border border-gray-300 rounded-md p-2 bg-white text-sm shadow-sm"
        >
          <option value="date">По дате</option>
          <option value="status">По статусу</option>
        </select>
      </div>
      <div v-if="sortedOrders.length" class="space-y-4">
        <div
          v-for="order in sortedOrders"
          :key="order.id"
          class="p-4 bg-white rounded-md shadow-sm hover:shadow-md transition"
        >
          <div class="flex justify-between items-center">
            <div>
              <p class="font-semibold text-lg">Заказ №{{ order.id }}</p>
              <p class="text-sm text-gray-500">
                Дата: {{ formatDate(order.onDate) }}
              </p>
            </div>
            <div>
              <button @click="goToOrder(order.id)">Подробнее</button>
            </div>
            <div class="text-sm text-gray-600">
              <span :class="statusClasses(order.status)">
                {{ order.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-center text-gray-400 mt-20">Нет заказов</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { getOrders } from "@/API/Api";
import { useRouter } from "vue-router";

const router = useRouter();
const goToOrder = (idOrder) => {
  router.push({ name: "showBooking", params: { id: idOrder } });
};
const renderOrders = async () => {
  orders.value = await getOrders();
};
onMounted(() => {
  renderOrders();
});
const orders = ref([]);

const sortKey = ref("date");

const sortedOrders = computed(() => {
  return [...orders.value].sort((a, b) => {
    if (sortKey.value === "date") {
      return new Date(b.onDate) - new Date(a.onDate);
    } else if (sortKey.value === "status") {
      return a.status.localeCompare(b.status);
    }
    return 0;
  });
});

const sortOrders = () => {};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("ru-RU");
};

const statusClasses = (status) => {
  switch (status) {
    case "Выполнен":
      return "text-green-600";
    case "В процессе":
      return "text-yellow-600";
    case "Отменен":
      return "text-red-500";
    case "Новый":
      return "text-green-600";
    default:
      return "text-gray-500";
  }
};
</script>
