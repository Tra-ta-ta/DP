<template>
  <ToggleThemeBtn />
  <div class="min-h-screen flex flex-col md:flex-row">
    <div
      class="hidden md:flex md:w-2/3 items-center justify-center bg-contain bg-center bg-opacity-90 bg-no-repeat bg-wood-light dark:bg-wood-dark"
      style="background-image: url('https://mineturic.ru/images/BGforKigin.jpg')"
    >
      <div class="max-w-xl w-full px-6">
        <div class="h-96"></div>
        <p class="text-white text-center mt-4 text-lg font-semibold">
          Добро пожаловать! Забронируйте номер с комфортом.
        </p>
      </div>
    </div>
    <div
      class="w-full md:w-1/3 flex items-center justify-center bg-gray-100 dark:bg-gray-500 p-6 md:p-0 min-h-screen"
    >
      <Form
        :validation-schema="schema"
        @submit="submitForm"
        class="w-full max-w-md bg-white dark:bg-gray-200 rounded shadow-lg p-8"
      >
        <h2 class="text-2xl font-bold mb-6 text-center text-black">
          Авторизация
        </h2>

        <div class="mb-4">
          <label class="block text-sm mb-1 text-black">Логин</label>
          <Field
            placeholder="Введите логин"
            name="login"
            type="text"
            class="w-full p-2 border rounded-lg"
          />
          <ErrorMessage name="login" class="text-sm text-red-500" />
        </div>

        <div class="mb-4">
          <label class="block text-sm mb-1 text-black">Пароль</label>
          <Field
            placeholder="Введите пароль"
            name="password"
            type="password"
            class="w-full p-2 border rounded-lg"
          />
          <ErrorMessage name="password" class="text-sm text-red-500" />
        </div>

        <div v-if="error" class="text-red-500 text-sm mb-4">
          {{ error }}
        </div>

        <button
          type="submit"
          class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 font-semibold transition"
        >
          Войти
        </button>
      </Form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { login } from "@/API/Api";
import { Form, ErrorMessage, Field } from "vee-validate";
import * as yup from "yup";
import ToggleThemeBtn from "@/components/ToggleThemeBtn.vue";
import { useRouter } from "vue-router";
import { useStore } from "vuex";

const error = ref("");
const router = useRouter();
const store = useStore();

const schema = yup.object({
  login: yup.string().required("Логин обязателен"),
  password: yup.string().required("Пароль обязателен"),
});

const submitForm = async (values) => {
  error.value = await login(values.login, values.password, store, router);
};
</script>
