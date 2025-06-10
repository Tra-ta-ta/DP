<template>
  <button
    @click="toggleTheme"
    class="fixed top-4 right-4 z-50 px-4 py-2 rounded bg-wood-light dark:bg-wood-dark text-black dark:text-white shadow transition"
  >
    {{ theme === "dark" ? "Светлая тема" : "Тёмная тема" }}
  </button>
</template>

<script setup>
import { useTheme } from "@/toggleTheme";

const { theme, toggleTheme } = useTheme();
</script>
