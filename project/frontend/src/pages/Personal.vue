<template>
  <div class="min-h-screen bg-gray-50 text-gray-800 px-6 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-2xl font-semibold mb-6 text-center">Персонал гостиницы</h1>

      <div class="flex flex-col md:flex-row md:justify-between mb-4 space-y-3 md:space-y-0">
        <button
          @click="goToCreatePersonal"
          class="border border-gray-300 rounded-md p-2 text-sm shadow-sm"
        >
          Добавить сотрудника
        </button>

        <div class="flex flex-col md:flex-row md:items-center md:space-x-2">
          <input
            v-model="sortInput"
            @input="sortPersonal"
            type="text"
            placeholder="Поиск по ФИО"
            class="border border-gray-300 rounded-md p-2 text-sm shadow-sm"
          />

          <select
            v-model="sortKey"
            @change="sortPersonal"
            class="border border-gray-300 rounded-md p-2 bg-white text-sm shadow-sm"
          >
            <option value="role">По роли</option>
            <option value="name">По имени</option>
          </select>

          <select
            v-model="roleFilter"
            @change="sortPersonal"
            class="border border-gray-300 rounded-md p-2 bg-white text-sm shadow-sm"
          >
            <option value="">Все роли</option>
            <option value="Менеджер">Менеджер</option>
            <option value="Персонал">Персонал</option>
          </select>
        </div>
      </div>

      <div v-if="sortedPersonals.length" class="space-y-4">
        <div
          v-for="personal in paginatedPersonals"
          :key="personal.id"
          class="p-4 bg-white rounded-md shadow-sm hover:shadow-md transition"
        >
          <div class="flex justify-between items-center">
            <div>
              <p class="font-semibold text-lg">
                ФИО: {{ personal.name }} {{ personal.surname }} {{ personal.thirdname }}
              </p>
              <p class="text-sm text-gray-500">
                Роль: {{ personal.roles.value }}
              </p>
            </div>
            <div>
              <button
                @click="goToPersonal(personal.id)"
                class="text-blue-600 hover:text-blue-800"
              >
                Подробнее
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

      <div v-else class="text-center text-gray-400 mt-20">Нет сотрудников</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import { getPersonal } from "@/API/Api";

const router = useRouter();

const personals = ref([]);
const sortKey = ref("name");
const sortInput = ref("");
const roleFilter = ref("");
const currentPage = ref(1);
const itemsPerPage = 10;

const renderPersonals = async () => {
  personals.value = await getPersonal();
};

onMounted(() => {
  renderPersonals();
});

const goToCreatePersonal = () => {
  router.push({ name: "createPersonal" });
};

const goToPersonal = (idPersonal) => {
  router.push({ name: "showPersonal", params: { id: idPersonal } });
};

const sortedPersonals = computed(() => {
  const input = sortInput.value?.trim().toLowerCase() || "";
  const role = roleFilter.value;

  let filtered = personals.value;

  if (role) {
    filtered = filtered.filter((p) => p.roles.value === role);
  }

  if (input) {
    const terms = input.split(/\s+/);
    filtered = filtered.filter((person) => {
      const nameParts = [
        person.surname.toLowerCase(),
        person.name.toLowerCase(),
        person.thirdname.toLowerCase(),
      ];
      return terms.every((term) =>
        nameParts.some((part) => part.includes(term))
      );
    });
  }

  return filtered.sort((a, b) => {
    if (sortKey.value === "role") {
      return a.roles.value.localeCompare(b.roles.value);
    } else if (sortKey.value === "name") {
      const aFullName = `${a.surname} ${a.name} ${a.thirdname}`;
      const bFullName = `${b.surname} ${b.name} ${b.thirdname}`;
      return aFullName.localeCompare(bFullName);
    }
    return 0;
  });
});

const totalPages = computed(() =>
  Math.ceil(sortedPersonals.value.length / itemsPerPage)
);

const paginatedPersonals = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return sortedPersonals.value.slice(start, start + itemsPerPage);
});

watch([sortInput, sortKey, roleFilter], () => {
  currentPage.value = 1;
});

watch(totalPages, (newTotal) => {
  if (currentPage.value > newTotal && newTotal > 0) {
    currentPage.value = newTotal;
  }
});
</script>
