function lightMode() {
    document.body.classList.remove("dark");
    localStorage.setItem("theme", "light");
}

function darkMode() {
    document.body.classList.add("dark");
    localStorage.setItem("theme", "dark");
}

if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
}