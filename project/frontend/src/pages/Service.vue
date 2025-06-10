<template>
  <ToggleThemeBtn />
  <div
    class="min-h-screen bg-cover bg-center relative"
    style="background-image: url('')"
  >
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    <div
      class="relative z-10 flex justify-center items-center min-h-screen px-4"
    >
      <div
        class="bg-white text-black w-full max-w-3xl p-8 rounded-xl shadow-2xl"
      >
        <h2 class="text-3xl font-bold mb-8 text-center">Наши услуги</h2>

        <div class="space-y-6">
          <div
            v-for="service in services.services"
            :key="service.id"
            class="flex flex-col my-2 border-l-4 border-gray-400 pl-4"
          >
            <h3 class="text-xl font-semibold mb-1">{{ service.name }}</h3>
            <p class="text-gray-700 text-sm">{{ service.discriprion }}</p>
            <button
              v-if="$store.state.room"
              @click="
                createOrder(
                  $store.state.room,
                  service.id,
                  $store.state.user.id
                )
              "
              class="bg-green-500 w-36 hover:bg-green-700 self-end text-white rounded-lg p-2"
            >
              Заказать в номер
            </button>
          </div>
          <div v-if="status" class="text-green-500 text-sm">
            {{ status }}
          </div>
          <div v-if="error" class="text-red-500 text-sm">
            {{ error }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { getServices } from "@/API/Api";
import { onMounted, ref } from "vue";
import { useStore } from "vuex";
import { fetchCurrentRoom, createOrderOnService } from "../API/Api";
import ToggleThemeBtn from "@/components/ToggleThemeBtn.vue";
const store = useStore();
const services = ref([]);
const error = ref();
const status = ref();
const createOrder = async (idRoom, idService, idUser) => {
  await createOrderOnService(idRoom, idService, idUser).then((response) => {
    status.value = response.data.status;
    error.value = '';
  },(Status) => {
    error.value = Status.response.data.status;
    status.value = '';
  });
  
}
const loadServices = async () => {
  services.value = await getServices();
};
onMounted(() => {
  loadServices();
  fetchCurrentRoom(store);
});
</script>
