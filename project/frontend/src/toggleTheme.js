import { ref, watchEffect } from "vue";

const theme = ref(localStorage.getItem("theme") || "light");

export function useTheme() {
  watchEffect(() => {
    const html = document.documentElement;
    if (theme.value === "dark") {
      html.classList.add("dark");
    } else {
      html.classList.remove("dark");
    }
    localStorage.setItem("theme", theme.value);
  });

  return {
    theme,
    toggleTheme: () => {
      theme.value = theme.value === "dark" ? "light" : "dark";
    },
  };
}
