<template>
  <div class="navigation-wrapper">
    <transition name="fade">
      <div v-if="isPanelVisible" class="overlay" @click.self="closePanel"></div>
    </transition>

    <div class="navigation-container">
      <button
        class="toggle-button"
        @mouseenter="isButtonHovered = true"
        @mouseleave="isButtonHovered = false"
        @click="togglePanel"
        :style="buttonStyle"
      >
        Навигация
      </button>
      <transition name="slide-fade">
        <div
          v-if="isPanelVisible"
          class="nav-panel"
          @mouseleave="isButtonHovered = false"
        >
          <button class="close-button" @click="closePanel">×</button>

          <nav>
            <ul v-if="$store.state.user == null">
              <li class="my-3">
                <RouterLink to="/login">Войти</RouterLink>
              </li>
              <li class="my-3">
                <RouterLink to="/registration">Зарегистрироваться</RouterLink>
              </li>
              <li class="my-3">
                <RouterLink to="/">Главная</RouterLink>
              </li>
              <li class="my-3">
                <RouterLink to="/services">Услуги</RouterLink>
              </li>
            </ul>
            <ul v-else-if="$store.state.user.roles_idRole == 1">
              <li class="my-3">
                <button @click="logoutHendler">Выйти</button>
              </li>
              <li class="my-3">
                <RouterLink to="/dashboard">Личный кабинет</RouterLink>
              </li>
              <li class="my-3">
                <RouterLink to="/services">Услуги</RouterLink>
              </li>
              <li class="my-3">
                <RouterLink to="/booking">Забронировать</RouterLink>
              </li>
              <li class="my-3">
                <RouterLink to="/">Главная</RouterLink>
              </li>
            </ul>
            <ul v-else-if="$store.state.user.roles_idRole == 2">
              <li class="my-3">
                <button @click="logoutHendler">Выйти</button>
              </li>
              <li class="my-3">
                <RouterLink to="/rooms">Номера</RouterLink>
              </li>
              <li class="my-3">
                <Report />
              </li>
              <li class="my-3">
                <RouterLink to="/personals">Управление персоналом</RouterLink>
              </li>
            </ul>
            <ul v-else-if="$store.state.user.roles_idRole == 3">
              <li class="my-3">
                <RouterLink to="/servicePersonal">Заказы на услуги</RouterLink>
              </li>
              <li class="my-3">
                <RouterLink to="/roomsPersonal">Статусы номеров</RouterLink>
              </li>
              <li class="my-3">
                <button @click="logoutHendler">Выйти</button>
              </li>
            </ul>
            <ul v-else-if="$store.state.user.roles_idRole == 4">
              <li class="my-3">
                <RouterLink to="/newChats">Новые обращения</RouterLink>
              </li>
              <li class="my-3">
                <RouterLink to="/booking/menager">Бронирования</RouterLink>
              </li>
              <li class="my-3">
                <button @click="logoutHendler">Выйти</button>
              </li>
            </ul>
          </nav>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { RouterLink, useRouter } from "vue-router";
import { logout } from "@/API/Api";
import Report from "./Report.vue";
import { useStore } from "vuex";

const store = useStore();
const router = useRouter();
const isPanelVisible = ref(false);
const isButtonHovered = ref(false);

const logoutHendler = async () => {
  await logout(store);
  router.push({ path: "/" });
};

watch(isPanelVisible, (newVal) => {
  if (newVal) {
    document.body.style.overflow = "hidden";
  } else {
    document.body.style.overflow = "auto";
  }
});

const buttonStyle = computed(() => ({
  transform: isButtonHovered.value ? "translateX(0)" : "translateX(70%)",
}));

const togglePanel = () => {
  isPanelVisible.value = !isPanelVisible.value;
};

const closePanel = () => {
  isPanelVisible.value = false;
};

const navigateTo = (route) => {
  console.log("Переход на:", route); // Замените на свою логику маршрутизации
  closePanel();
};
</script>

<style scoped>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(3px);
  z-index: 999;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s ease-in;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(100%) translateY(-50%);
  opacity: 0;
}

.navigation-container {
  position: fixed;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1000;
}

.toggle-button {
  position: relative;
  background: #2196f3;
  color: white;
  border: none;
  padding: 15px 25px;
  border-radius: 25px 0 0 25px;
  cursor: pointer;
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  transform: translateX(70%);
  box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
  z-index: 1001;
}
.dark .toggle-button {
  background: #2563eb;
}
.toggle-button:hover {
  transform: translateX(0);
}

.nav-panel {
  position: fixed;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  background: white;
  width: 250px;
  height: 70vh;
  box-shadow: -2px 0 15px rgba(0, 0, 0, 0.2);
  border-radius: 15px 0 0 15px;
  padding: 30px 20px;
  opacity: 1;
  z-index: 1001;
}
.dark .nav-panel {
  background: #1e293b;
  color: #f1f5f9;
}

.close-button {
  position: absolute;
  left: -20px;
  top: 50%;
  transform: translateY(-50%);
  background: #ff4444;
  color: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50% 0 0 50%;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: -2px 0 8px rgba(0, 0, 0, 0.2);
}
.dark .close-button {
  background: #dc2626;
}

.dark .close-button:hover {
  background: #ef4444;
}

.close-button:hover {
  background: #ff6666;
  transform: translateY(-50%) scale(1.1);
}

nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

nav li a {
  padding: 12px;
  display: block;
  color: #2c3e50;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s;
}
.dark nav li a {
  color: wheat;
}

nav li button {
  padding: 12px;
  display: block;
  color: #2c3e50;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s;
}
.dark nav li button {
  color: wheat;
}
nav li a:hover {
  background: #f0f4f8;
  transform: translateX(5px);
}
nav nav li button:hover {
  background: #f0f4f8;
  transform: translateX(5px);
}
.dark nav li button:hover {
  background: #76787a;
  transform: translateX(5px);
}
.dark nav li a:hover {
  background: #76787a;
  transform: translateX(5px);
}
</style>
