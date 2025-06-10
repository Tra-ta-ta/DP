<template>
  <ToggleThemeBtn />
  <div class="min-h-screen flex flex-col">
    <div
      class="bg-no-repeat bg-cover text-white text-2xl font-bold flex-1"
      style="
        background-image: url('https://mineturic.ru/images/bgForWelcome.jpg');
      "
    >
      <div class="flex justify-center items-center h-96">
        <div class="p-6 font-mono font-bold text-4xl text-shadow-lg">
          Добро пожаловать в нашу гостиницу
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
        <div
          v-for="type in types.types"
          :key="type.id"
          class="bg-cover bg-center bg-no-repeat relative h-96 rounded overflow-hidden shadow"
          :style="{
            backgroundImage: `url('https://mineturic.ru/images/${type.image}')`,
          }"
        >
          <div
            class="absolute inset-0 p-4 flex flex-col justify-between bg-black/40 dark:bg-black/60"
          >
            <div
              class="bg-white/90 dark:bg-wood-dark/90 text-black dark:text-white rounded-xl p-4"
            >
              <h3 class="text-xl font-semibold">{{ type.typeRoom }} - {{type.price}} р. за ночь</h3>
              <p class="text-sm mt-2">{{ type.description }}</p>
            </div>
            <button
              @click="goBooking(type.id)"
              class="mt-4 py-2 px-4 bg-wood-light dark:bg-white text-black rounded hover:bg-yellow-100 dark:hover:bg-gray-200 self-center transition"
            >
              Перейти к брони
            </button>
          </div>
        </div>
      </div>

      <Chat />
    </div>

    <Footer />
  </div>
</template>

<script setup>
import Footer from "@/components/Footer.vue";
import Chat from "@/components/Chat.vue";
import { getTypeRooms } from "@/API/Api";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import ToggleThemeBtn from "@/components/ToggleThemeBtn.vue";

const router = useRouter();
const types = ref([]);

const goBooking = (id) => {
  router.push({ name: "booking", query: { type: id } });
};

const loadTypeRooms = async () => {
  types.value = await getTypeRooms();
};

onMounted(() => {
  loadTypeRooms();
});
</script>

<style scoped>
.text-shadow-lg {
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
}
</style>
