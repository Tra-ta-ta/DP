<template>
  <div>
    <button
      @click="show = true"
      class="border border-gray-300 rounded-md p-2 mr-2 text-sm shadow-sm"
    >
      Добавить номер
    </button>

    <teleport to="body">
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      >
        <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-lg">
          <h2 class="text-xl font-semibold mb-4">Добавление номера</h2>

          <form @submit.prevent="submit">
            <div class="mb-4">
              <label class="block text-sm text-gray-600 mb-1">Номер</label>
              <input
                type="number"
                v-model.number="roomNumber"
                class="w-full border rounded-lg p-2"
                required
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm text-gray-600 mb-1">Тип номера</label>
              <select
                v-model="roomType"
                class="w-full border rounded-lg p-2"
                required
              >
                <option value="" disabled selected>Выберите тип</option>
                <option
                  v-for="type in roomTypes.types"
                  :key="type.id"
                  :value="type.id"
                >
                  {{ type.typeRoom }}
                </option>
              </select>
            </div>
            <div class="flex justify-between items-center mt-6">
              <button
                type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-500"
              >
                Сохранить
              </button>
              <button
                @click="show = false"
                type="button"
                class="text-gray-500 hover:underline"
              >
                Отмена
              </button>
            </div>
          </form>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { createRoom, getTypeRooms } from "@/API/Api";
import { onMounted, ref } from "vue";

const show = ref(false);
const roomNumber = ref(null);
const roomType = ref("");

const roomTypes = ref();
const emit = defineEmits("roomAdd");
onMounted(async () => {
  roomTypes.value = await getTypeRooms();
});
const submit = async () => {
  if (!roomNumber.value || !roomType.value) {
    alert("Заполните все поля");
    return;
  }

  const data = {
    number: roomNumber.value,
    type: roomType.value,
  };
  const room = await createRoom(roomType.value, roomNumber.value);
  emit("roomAdd", room);

  roomNumber.value = null;
  roomType.value = "";
  show.value = false;
};
</script>
