<template>
  <ToggleThemeBtn />
  <div
    class="min-h-screen bg-cover bg-center relative"
    style="
      background-image: url('https://mineturic.ru/images/bgForRegistr.jpg');
    "
  >
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    <div
      class="relative z-10 flex items-center justify-center min-h-screen px-4"
    >
      <div
        class="bg-white bg-opacity-80 text-black p-8 rounded-lg shadow-lg w-full max-w-md dark:bg-black"
      >
        <h2 class="text-2xl font-bold mb-6 text-center dark:text-white">
          Регистрация
        </h2>
        <Form
          :validation-schema="schema"
          @submit="submitForm"
          class="space-y-4"
        >
          <div>
            <label class="block mb-1 dark:text-white">Имя</label>
            <Field
              name="name"
              type="text"
              class="w-full p-2 border rounded-lg"
            />
            <ErrorMessage name="name" class="text-sm text-red-500" />
          </div>
          <div>
            <label class="block mb-1 dark:text-white">Фамилия</label>
            <Field
              name="surname"
              type="text"
              class="w-full p-2 border rounded-lg"
            />
            <ErrorMessage name="surname" class="text-sm text-red-500" />
          </div>
          <div>
            <label class="block mb-1 dark:text-white">Отчество</label>
            <Field
              name="thirdname"
              type="text"
              class="w-full p-2 border rounded-lg"
            />
            <ErrorMessage name="thirdname" class="text-sm text-red-500" />
          </div>
          <div>
            <label class="block mb-1 dark:text-white">Номер телефона</label>
            <Field name="phone" v-slot="{ field }">
              <input
                v-bind="field"
                v-maska="'+7 (###) ###-##-##'"
                placeholder="+7 (___) ___-__-__"
                class="w-full p-2 border rounded-lg"
                type="tel"
              />
            </Field>
            <ErrorMessage name="phone" class="text-sm text-red-500" />
          </div>
          <div>
            <label class="block mb-1 dark:text-white">Логин</label>
            <Field
              name="login"
              type="text"
              class="w-full p-2 border rounded-lg"
            />
            <ErrorMessage name="login" class="text-sm text-red-500" />
          </div>
          <div>
            <label class="block mb-1 dark:text-white">Пароль</label>
            <Field
              name="password"
              type="password"
              class="w-full p-2 border rounded-lg"
            />
            <ErrorMessage name="password" class="text-sm text-red-500" />
          </div>
          <div v-if="error" class="text-red-500 text-sm">
            {{ error }}
          </div>
          <button
            type="submit"
            class="w-full bg-blue-500 text-black py-2 rounded hover:bg-blue-200 font-semibold transition dark:text-white dark:bg-wood-dark"
          >
            Зарегистрироваться
          </button>
        </Form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { registration } from "@/API/Api";
import ToggleThemeBtn from "@/components/ToggleThemeBtn.vue";
import { Form, ErrorMessage, Field } from "vee-validate";
import * as yup from "yup";
import { vMaska } from "maska/vue";
import { ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";

const store = useStore();
const router = useRouter();
const error = ref("");
const schema = yup.object({
  name: yup.string().required("Имя обязательно"),
  surname: yup.string().required("Фамилия обязательна"),
  thirdname: yup.string().required("Отчество обязательно"),
  phone: yup
    .string()
    .required("Телефон обязателен")
    .matches(
      /^\+7\s?\(\d{3}\)\s?\d{3}-\d{2}-\d{2}$/,
      "Введите номер в формате +7 (999) 123-45-67"
    ),
  login: yup.string().required("Логин обязателен"),
  password: yup
    .string()
    .required("Пароль обязателен")
    .min(6, "Минимум 6 символов"),
});
const submitForm = async (values, { resetForm }) => {
  console.log("Данные формы:", values);

  try {
    const response = await registration(values, store);
    router.push('/');
  } catch (err) {
    if (err.response) {
      error.value = err.response.data;
    } else if (err.response && err.response.data && err.response.data.message) {
      error.value = err.response.data.message;
    } else {
      error.value = "Произошла неизвестная ошибка";
    }
  }
};
</script>
