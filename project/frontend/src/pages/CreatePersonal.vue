<template>
  <div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Добавить сотрудника</h2>

    <Form :validation-schema="schema" @submit="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
        <Field name="name" type="text" class="w-full p-2 border rounded-lg" />
        <ErrorMessage name="name" class="text-sm text-red-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >Фамилия</label
        >
        <Field
          name="surname"
          type="text"
          class="w-full p-2 border rounded-lg"
        />
        <ErrorMessage name="surname" class="text-sm text-red-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >Отчество</label
        >
        <Field
          name="thirdname"
          type="text"
          class="w-full p-2 border rounded-lg"
        />
        <ErrorMessage name="thirdname" class="text-sm text-red-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >Телефон</label
        >
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
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >Логин</label
        >
        <Field name="login" type="text" class="w-full p-2 border rounded-lg" />
        <ErrorMessage name="login" class="text-sm text-red-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >Пароль</label
        >
        <Field
          name="password"
          type="password"
          class="w-full p-2 border rounded-lg"
        />
        <ErrorMessage name="password" class="text-sm text-red-500" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Роль</label>
        <Field name="role" as="select" class="w-full p-2 border rounded-lg">
          <option value="">Выберите роль</option>
          <option value="4">Менеджер</option>
          <option value="3">Персонал</option>
        </Field>
        <ErrorMessage name="role" class="text-sm text-red-500" />
      </div>
      <div v-if="error" class="text-red-400 text-sm">
        {{ error }}
      </div>

      <div>
        <button
          type="submit"
          class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600"
        >
          Добавить сотрудника
        </button>
      </div>
    </Form>

    <div v-if="successMessage" class="mt-4 text-green-600">
      {{ successMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { vMaska } from "maska/vue";
import { createUser } from "@/API/Api";

const error = ref("");
const successMessage = ref("");

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
  role: yup.string().required("Роль обязательна"),
});

const handleSubmit = async (values, { resetForm }) => {
  error.value = "";
  successMessage.value = "";

  try {
    const response = await createUser(values);

    if (response) {
      successMessage.value = "Сотрудник успешно добавлен!";
      resetForm();
    } else {
      error.value = "Что-то пошло не так. Попробуйте снова.";
    }
  } catch (err) {
    if (err.response && err.response.data && typeof err.response.data === "string") {
      error.value = err.response.data;
    } else{
      error.value = err.response.data.message;
    }
  }
};
</script>
