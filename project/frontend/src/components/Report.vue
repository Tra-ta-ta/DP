<template>
  <div>
    <a @click="showModal = true"> Сформировать отчёт </a>
    <teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-[1020] flex items-center justify-center bg-black bg-opacity-50"
      >
        <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-xl">
          <h2 class="text-xl font-semibold mb-4">Выбор периода отчёта</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm text-gray-600">От</label>
              <input
                type="date"
                v-model="dateFrom"
                class="w-full mt-1 border rounded-lg p-2"
              />
            </div>

            <div>
              <label class="block text-sm text-gray-600">До</label>
              <input
                type="date"
                v-model="dateTo"
                class="w-full mt-1 border rounded-lg p-2"
              />
            </div>
          </div>

          <div class="mt-6 flex justify-between">
            <button
              class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600"
              @click="generateReport('Orders')"
            >
              Отчёт о завершённых бронях
            </button>
            <button
              class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600"
              @click="generateReport('ServiceOrder')"
            >
              Отчёт о заказов услуг
            </button>
          </div>

          <button
            @click="showModal = false"
            class="mt-4 text-sm text-gray-500 hover:underline"
          >
            Отмена
          </button>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { getOrdersonServiceReport, getOrdersReport } from "@/API/Api";
import { ref } from "vue";

const showModal = ref(false);
const dateFrom = ref("");
const dateTo = ref("");

const generateReport = (type) => {
  if (!dateFrom.value || !dateTo.value) {
    alert("Выберите обе даты.");
    return;
  }
  if (type === "Orders") {
    getOrdersReport(dateFrom.value, dateTo.value);
  }
  if (type === "ServiceOrder") {
    getOrdersonServiceReport(dateFrom.value, dateTo.value);
  }
  console.log(
    `Генерация ${type.toUpperCase()} отчёта с ${dateFrom.value} по ${
      dateTo.value
    }`
  );
  showModal.value = false;
};
</script>
<style scoped>
a {
  padding: 12px;
  display: block;
  color: #2c3e50;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s;
}
.dark a {
  color: wheat;
}
.dark a:hover {
  cursor: pointer;
  background: #76787a;
  transform: translateX(5px);
}
a:hover {
  cursor: pointer;
  background: #f0f4f8;
  transform: translateX(5px);
}
</style>
