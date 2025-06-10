<template>
  <div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center">Список чатов</h1>

    <div v-if="loading" class="text-center text-gray-500">Загрузка...</div>

    <ul v-else class="space-y-4">
      <div v-for="chat in paginatedChats" :key="chat.id">
        <li
          v-if="chat.status === 'Новый'"
          @click="selectChat(chat)"
          class="cursor-pointer rounded-lg border border-gray-300 p-4 hover:bg-indigo-50 transition"
        >
          <h2 class="text-xl font-semibold mb-1">Чат #{{ chat.id }}</h2>
          <p class="text-gray-600 mb-1">
            Статус: <span class="font-medium">{{ chat.status }}</span>
          </p>
        </li>
      </div>
    </ul>

    <div class="mt-6 flex justify-center space-x-3">
      <button
        @click="currentPage--"
        :disabled="currentPage === 1"
        class="px-4 py-2 rounded bg-gray-300 disabled:opacity-50"
      >
        Назад
      </button>

      <span class="px-4 py-2">Страница {{ currentPage }} из {{ totalPages }}</span>

      <button
        @click="currentPage++"
        :disabled="currentPage === totalPages"
        class="px-4 py-2 rounded bg-gray-300 disabled:opacity-50"
      >
        Вперед
      </button>
    </div>

    <transition name="fade">
      <div
        v-if="selectedChat"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="closeModal"
      >
        <div
          class="bg-white rounded-lg shadow-lg max-w-lg w-full max-h-[80vh] overflow-hidden flex flex-col p-6 relative"
        >
          <button
            @click="closeModal"
            class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold"
            aria-label="Close modal"
          >
            &times;
          </button>

          <h3 class="text-2xl font-semibold mb-3">Детали чата #{{ selectedChat.id }}</h3>

          <p class="font-medium mb-1">Статус: {{ selectedChat.status }}</p>

          <div class="mb-4">
            <p class="font-medium mb-2">Участники:</p>
            <ul class="list-disc list-inside space-y-1 text-gray-700">
              <li v-for="user in selectedChat.users" :key="user.id">
                {{ user.name }} {{ user.thirdname }}
              </li>
            </ul>
          </div>

          <div
            class="flex-1 overflow-y-auto mb-4 space-y-3 p-3 bg-gray-50 rounded border border-gray-200"
            ref="messagesContainer"
          >
            <div
              v-for="message in selectedChat.messages"
              :key="message.id"
              :class="[
                'max-w-[70%] p-3 rounded-lg shadow flex flex-col',
                message.users_idUser === currentUserId
                  ? 'bg-indigo-500 text-white self-end ml-auto'
                  : 'bg-white text-gray-900 self-start mr-auto',
              ]"
            >
              <div class="text-sm mb-1">
                <span class="font-medium">{{ formatTime(message.created_at) }}</span>
              </div>
              <div class="whitespace-pre-wrap text-sm">{{ message.messageText }}</div>
            </div>
          </div>

          <form @submit.prevent="sendMessage" class="flex space-x-3">
            <input
              v-model="newMessage"
              type="text"
              placeholder="Введите сообщение..."
              class="flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
              required
            />
            <button
              type="submit"
              :disabled="sending || !newMessage.trim()"
              class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Отправить
            </button>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, onUnmounted } from "vue";
import { getMessages, getNewChats, sendMessageMenager } from "@/API/Api";
import echo from "@/echo";
import { useStore } from "vuex";

const store = useStore();
const chats = ref([]);
const loading = ref(true);
const selectedChat = ref(null);
const currentPage = ref(1);
const perPage = 5;
const newMessage = ref("");
const messagesContainer = ref(null);
const currentUserId = store.state.user.id;
const sending = ref(false); // Блокировка отправки
let lastChannel = null;

const formatTime = (isoString) => {
  const date = new Date(isoString);
  return date.toLocaleTimeString("ru-RU", { hour: "2-digit", minute: "2-digit" });
};

const filteredChats = computed(() => {
  return chats.value.filter((chat) => chat.status === "Новый");
});

const paginatedChats = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return filteredChats.value.slice(start, start + perPage);
});

const totalPages = computed(() => {
  return Math.ceil(filteredChats.value.length / perPage);
});

const fetchChats = async () => {
  try {
    chats.value = await getNewChats();
  } catch (e) {
    console.error("Ошибка при получении чатов:", e);
  } finally {
    loading.value = false;
  }
};

const selectChat = async (chat) => {
  if (lastChannel) {
    echo.leave(lastChannel);
  }

  selectedChat.value = { ...chat, messages: [] };
  lastChannel = `Chat.${chat.id}`;

  try {
    const loadedMessages = await getMessages(chat.id);
    selectedChat.value.messages = loadedMessages;
  } catch (e) {
    console.error("Ошибка загрузки сообщений:", e);
  }

  echo.private(lastChannel).listen("MessageSend", (e) => {
    if (selectedChat.value && selectedChat.value.id === e.idChat) {
      selectedChat.value.messages.push(e.message);
      scrollToBottom();
    }
  });

  scrollToBottom();
};

const closeModal = () => {
  if (lastChannel) {
    echo.leave(lastChannel);
    lastChannel = null;
  }
  selectedChat.value = null;
  newMessage.value = "";
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || sending.value) return;

  sending.value = true;

  try {
    await sendMessageMenager(selectedChat.value.id, newMessage.value);
    newMessage.value = "";
    scrollToBottom();
  } catch (e) {
    console.error("Ошибка отправки сообщения:", e);
  } finally {
    sending.value = false;
  }
};

const scrollToBottom = () => {
  nextTick(() => {
    const container = messagesContainer.value;
    if (container) {
      container.scrollTop = container.scrollHeight;
    }
  });
};

onMounted(async () => {
  await fetchChats();

  echo.private("ChatsMenager").listen("ChatChange", (e) => {
    const index = chats.value.findIndex((chat) => chat.id === e.chat.id);
    if (index !== -1) {
      chats.value[index] = { ...chats.value[index], ...e.chat };
    } else {
      chats.value.unshift(e.chat);
    }
  });
});

onUnmounted(() => {
  echo.leave("ChatsMenager");
  if (lastChannel) echo.leave(lastChannel);
});
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
