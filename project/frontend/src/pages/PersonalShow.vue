<template>
  <div
    v-if="personal"
    class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg"
  >
    <div class="text-lg font-semibold text-gray-700" v-if="personal.message">
      {{ personal.message }}
    </div>
    <div v-else>
      <div class="flex items-center space-x-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">
            {{ personal[0].name }} {{ personal[0].surname }}
            {{ personal[0].thirdname }}
          </h2>
          <p class="text-gray-500">{{ personal[0].roles.value }}</p>
          <p class="text-sm text-gray-600">Телефон: {{ personal[0].phone }}</p>
        </div>
      </div>

      <div class="mt-6 border-t pt-4">
        <h3 class="text-lg font-semibold text-gray-800">Действия</h3>
        <div class="flex flex-col items-center gap-3">
          <button
            @click="alertManager"
            class="text-left w-60 rounded-lg bg-blue-500 p-2 shadow-lg hover:bg-blue-300"
          >
            Изменить роль на менеджера
          </button>
          <button
            @click="alertStaff"
            class="text-left w-60 rounded-lg bg-blue-500 p-2 shadow-lg hover:bg-blue-300"
          >
            Изменить роль на персонал
          </button>
          <button
            @click="alertDelete"
            class="text-left w-60 rounded-lg bg-red-500 p-2 shadow-lg hover:bg-red-300"
          >
            Удалить из системы
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { deleteUser, getPersonalUser, updateUser } from "@/API/Api";
import ToggleThemeBtn from "@/components/ToggleThemeBtn.vue";
import echo from "@/echo";
import { onMounted, onUnmounted, ref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();

const getPersonal = async (id) => {
  personal.value = await getPersonalUser(id);
};
onMounted(() => {
  getPersonal(route.params.id);
  echo.private(`Personal.${route.params.id}`).listen("RoleChange", (e) => {
    personal.value[0].roles.value = e.role;
    personal.value[0].roles.id = e.roleId;
    personal.value[0].roles_idRole = e.roleId;
  });
});
onUnmounted(() => {
  echo.leave(`Personal.${route.params.id}`);
});
const personal = ref();

const alertManager = () => {
  alert("Роль будет изменена на менеджера");
  updateUser(route.params.id, 4);
};

const alertStaff = () => {
  alert("Роль будет изменена на персонал");
  updateUser(route.params.id, 3);
};

const alertDelete = () => {
  const confirmed = confirm(
    "Вы уверены, что хотите удалить сотрудника из системы?"
  );
  if (confirmed) {
    deleteUser(route.params.id);
  }
};
</script>
