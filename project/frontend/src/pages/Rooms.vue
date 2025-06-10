<template>
  <div class="min-h-screen bg-gray-50 text-gray-800 px-6 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-2xl font-semibold mb-6 text-center">Номера</h1>
      
      <div class="flex flex-wrap justify-between mb-4 gap-2">
        <div>
          <AddRoom @roomAdd="RoomAdded" />
        </div>
        <div class="flex flex-wrap gap-2">
          <input
            v-model="sortInput"
            type="text"
            placeholder="Поиск по номеру"
            class="border border-gray-300 rounded-md p-2 text-sm shadow-sm"
          />

          <select
            v-model="sortKey"
            class="border border-gray-300 rounded-md p-2 bg-white text-sm shadow-sm"
          >
            <option value="number">По номеру</option>
            <option value="type">По типу</option>
            <option value="status">По статусу</option>
          </select>

          <select
            v-model="selectedType"
            class="border border-gray-300 rounded-md p-2 bg-white text-sm shadow-sm"
          >
            <option value="">Все типы</option>
            <option value="Стандарт">Стандарт</option>
            <option value="Делюкс">Делюкс</option>
            <option value="Королевский люкс">Королевский люкс</option>
          </select>
        </div>
      </div>

      <div v-if="sortedRooms.length" class="space-y-4">
        <div
          v-for="room in paginatedRooms"
          :key="room.id"
          class="p-4 bg-white rounded-md shadow-sm hover:shadow-md transition"
        >
          <div class="flex justify-between items-center">
            <div>
              <p class="font-semibold text-lg">Номер: {{ room.number }}</p>
              <p class="text-sm text-gray-500">Тип: {{ room.types.typeRoom }}</p>
              <p class="text-sm text-gray-500">Статус: {{ room.statusRoom }}</p>
            </div>
            <div>
              <button
                @click="deleteRooms(room.id)"
                v-if="room.statusRoom != 'Занят'"
                class="text-red-600 hover:text-red-800"
              >
                Удалить
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-center items-center mt-6 space-x-4">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-4 py-2 bg-blue-500 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-600 transition-colors"
          >
            Назад
          </button>
          <span class="text-sm text-gray-600">
            Страница {{ currentPage }} из {{ totalPages }}
          </span>
          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-4 py-2 bg-blue-500 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-600 transition-colors"
          >
            Вперед
          </button>
        </div>
      </div>

      <div v-else class="text-center text-gray-400 mt-20">Нет номеров</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { getRooms, deleteRoom } from "@/API/Api";
import AddRoom from "@/components/AddRoom.vue";

const rooms = ref([]);
const sortKey = ref("number");
const sortInput = ref("");
const selectedType = ref(""); // 🔍 фильтрация по типу
const currentPage = ref(1);
const itemsPerPage = 10;

const RoomAdded = (room) => {
  rooms.value = [...rooms.value, room];
  currentPage.value = 1;
};

const renderRooms = async () => {
  rooms.value = await getRooms();
};

onMounted(() => {
  renderRooms();
});

const deleteRooms = (id) => {
  if (confirm("Вы точно хотите удалить номер?")) {
    rooms.value = rooms.value.filter((room) => room.id !== id);
    deleteRoom(id);
  }
};

const sortedRooms = computed(() => {
  const input = sortInput.value?.trim().toLowerCase() || "";
  let filteredRooms = [...rooms.value];

  // ✅ Фильтрация по типу
  if (selectedType.value) {
    filteredRooms = filteredRooms.filter(
      (room) => room.types.typeRoom === selectedType.value
    );
  }

  // 🔎 Поиск по номеру
  if (input) {
    const terms = input.split(/\s+/);
    filteredRooms = filteredRooms.filter((room) =>
      terms.every((term) => room.number.toString().includes(term))
    );
  }

  // 📊 Сортировка
  return filteredRooms.sort((a, b) => {
    if (sortKey.value === "type") {
      return Number(a.types.id) - Number(b.types.id);
    } else if (sortKey.value === "number") {
      return Number(a.number) - Number(b.number);
    } else if (sortKey.value === "status") {
      return a.statusRoom.localeCompare(b.statusRoom);
    }
    return 0;
  });
});

const totalPages = computed(() =>
  Math.ceil(sortedRooms.value.length / itemsPerPage)
);

const paginatedRooms = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return sortedRooms.value.slice(start, end);
});

watch([sortInput, sortKey, selectedType], () => {
  currentPage.value = 1;
});

watch(totalPages, (newTotal) => {
  if (currentPage.value > newTotal && newTotal > 0) {
    currentPage.value = newTotal;
  }
});
</script>
