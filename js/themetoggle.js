const btn = document.getElementById("themeToggle");

let str = ThemeChecker();

if (str !== "light") {
  document.documentElement.classList.add("dark");
}

btn.onclick = () => {
  localStorage.setItem(
    "theme",
    localStorage.getItem("theme") === "light" ? "dark" : "light",
  );
  document.documentElement.classList.toggle("dark");
};

function ThemeChecker() {
  let obj = localStorage.getItem("theme");

  if (!obj) {
    localStorage.setItem("theme", "light");
    return "light";
  }
  return obj;
}

function eventListenerToggle() {
  let theme = ThemeChecker();

  document.documentElement.classList.toggle("dark", theme === "dark");
}

window.addEventListener("storage", (event) => {
  if (event.key === "theme") {
    eventListenerToggle();
  }
});
