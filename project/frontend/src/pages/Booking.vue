<template>
  <ToggleThemeBtn />
  <div
    class="min-h-screen bg-cover bg-center relative"
    style="background-image: url('http://localhost:8000/images/BGforKigin.jpg')"
  >
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    <div
      class="relative z-10 flex justify-center items-center min-h-screen px-4"
    >
      <div
        class="bg-white dark:bg-black bg-opacity-80 text-black p-8 rounded-lg shadow-lg w-full max-w-4xl"
      >
        <h2 class="text-3xl font-bold mb-6 text-center dark:text-white">
          Бронирование номера
        </h2>

        <form
          @submit.prevent="submitBooking"
          class="grid grid-cols-1 md:grid-cols-2 gap-6"
        >
          <div class="md:col-span-2">
            <label class="block mb-1 dark:text-white">Сообщение</label>
            <textarea
              v-model="form.message"
              class="w-full h-24 px-4 py-2 bg-gray-300 text-black rounded resize-none focus:outline-none focus:ring-2 focus:ring-gray-300"
              placeholder="Введите комментарий к бронированию"
            ></textarea>
          </div>

          <div>
            <label class="block mb-1 dark:text-white">Тип номера</label>
            <select
              v-model="form.roomType"
              class="w-full px-4 py-2 bg-gray-300 text-black rounded focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
              <option
                v-for="type in types.types"
                :key="type.id"
                :value="type.id"
              >
                {{ type.typeRoom }}
              </option>
            </select>
          </div>
          <div>
            <label class="block mb-1 dark:text-white">Дата начала</label>
            <input
              v-model="form.startDate"
              type="date"
              class="w-full px-4 py-2 bg-gray-300 text-black rounded focus:outline-none focus:ring-2 focus:ring-gray-300"
            />
          </div>
          <div>
            <label class="block mb-1 dark:text-white">Дата окончания</label>
            <input
              v-model="form.endDate"
              type="date"
              class="w-full px-4 py-2 bg-gray-300 text-black rounded focus:outline-none focus:ring-2 focus:ring-gray-300"
            />
          </div>
          <div class="md:col-span-2 text-red-400 text-sm" v-if="error">
            {{ error }}
          </div>
          <div class="md:col-span-2 text-green-400 text-sm" v-if="status">
            {{ status }}
          </div>
          <div class="md:col-span-2">
            <button
              type="submit"
              class="w-full bg-blue-600 font-bold dark:bg-wood-dark dark:text-white text-black py-2 rounded hover:bg-gray-200 transition"
            >
              Забронировать
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { getTypeRooms, createBooking } from "@/API/Api";
import ToggleThemeBtn from "@/components/ToggleThemeBtn.vue";
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

const store = useStore();
const route = useRoute();
const form = ref({
  message: "",
  roomType: "",
  startDate: "",
  endDate: "",
});

const types = ref([]);
const error = ref("");
const status = ref("");

const loadTypeRooms = async () => {
  types.value = await getTypeRooms();
};
onMounted(() => {
  loadTypeRooms();
  form.value.roomType = route.query.type ?? null;
});
const createOrder = async () => {
  await createBooking(
    form.value.message,
    form.value.startDate,
    form.value.endDate,
    form.value.roomType,
    store.state.user.id
  ).then(
    (response) => {
      error.value = "";
      status.value = response.status;
      store.commit("setOrder", response.order);
    },
    (status) => {
      status.value = "";
      error.value = status.response.data.error;
      if(status.response.data.message){
        error.value = status.response.data.message;
      }
    }
  );
};
const submitBooking = () => {
  error.value = "";

  if (!form.value.roomType || !form.value.startDate || !form.value.endDate) {
    error.value = "Пожалуйста, заполните все поля.";
    return;
  }

  if (form.value.startDate > form.value.endDate) {
    error.value = "Дата начала не может быть позже даты окончания.";
    return;
  }
  createOrder();
};
</script>
