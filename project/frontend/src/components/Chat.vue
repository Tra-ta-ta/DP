<template>
  <div>
    <button
      @click="toggleChat"
      class="fixed bottom-5 right-5 w-16 h-16 bg-green-500 rounded-full shadow z-50 flex items-center justify-center text-white text-2xl"
    >
      💬
    </button>

    <div
      v-if="isOpen"
      class="fixed bottom-24 right-5 w-[36rem] max-w-[95vw] h-[32rem] max-h-[80vh] bg-white border border-gray-300 rounded-lg shadow-lg flex flex-col z-50"
    >
      <div class="flex justify-between items-center bg-gray-200 p-3 rounded-t-lg">
        <span class="font-semibold text-gray-800">Чат с нами</span>
        <button @click="toggleChat" class="text-gray-600 hover:text-black">✕</button>
      </div>

      <div
        ref="messageContainer"
        class="flex-1 p-3 overflow-y-auto text-sm space-y-2 bg-gray-50"
      >
        <div
          v-for="msg in messages"
          :key="msg.id"
          :class="[
            'px-3 py-2 rounded shadow-sm w-fit max-w-[80%]',
            'text-gray-800 border border-gray-200',
            msg.user.id === $store.state.user.id
              ? 'bg-blue-100 self-end ml-auto text-right'
              : 'bg-white self-start mr-auto text-left',
          ]"
        >
          <div class="text-left border-b-2 border-gray-400 mb-1">
            <div class="flex justify-between items-center gap-2">
              <span class="font-semibold">{{ msg.user.name }}</span>
              <span class="text-xs text-gray-500 whitespace-nowrap">{{ formatTime(msg.created_at) }}</span>
            </div>
          </div>
          <div class="py-2 text-left whitespace-pre-wrap">{{ msg.messageText }}</div>
        </div>
      </div>

      <form
        v-if="chatId"
        @submit.prevent="send"
        class="transition w-full sm:w-auto p-3 flex gap-2 border-t border-gray-200 bg-white"
      >
        <input
          v-model="newMessage"
          type="text"
          placeholder="Введите сообщение..."
          class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-300 text-gray-800"
        />
        <button
          type="submit"
          class="px-4 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600 transition w-full sm:w-auto"
        >
          Отправить
        </button>
      </form>

      <div v-else class="text-black p-4 text-base">
        Войдите для того чтобы написать сообщение
      </div>
    </div>
  </div>
</template>

<script setup>
import { fetchChat, getMessages, sendMessage } from "@/API/Api";
import echo from "@/echo";
import { ref, nextTick } from "vue";

const isOpen = ref(false);
const newMessage = ref("");
const messages = ref([]);
const chatId = ref(null);
const messageContainer = ref(null);

const scrollToBottom = () => {
  if (messageContainer.value) {
    messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
  } else {
    console.warn("messageContainer не найден");
  }
};

const formatTime = (isoString) => {
  const date = new Date(isoString);
  return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
};

const toggleChat = async () => {
  if (!isOpen.value) {
    chatId.value = await fetchChat();

    if (chatId.value) {
      echo.private(`Chat.${chatId.value}`).listen("MessageSend", (e) => {
        messages.value.push(e.message);
        scrollToBottom();
      });

      const response = await getMessages(chatId.value);
      messages.value = response;
      isOpen.value = true;
      await nextTick();
      scrollToBottom();
    }

    isOpen.value = true;
  } else {
    echo.leave(`Chat.${chatId.value}`);
    isOpen.value = false;
  }
};

const send = async () => {
  if (newMessage.value.trim() !== "") {
    const content = newMessage.value;
    newMessage.value = "";
    await sendMessage(chatId.value, content);
  }
};
</script>
