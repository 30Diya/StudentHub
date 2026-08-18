// Check saved theme when page opens
if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
}


// Light Mode
function lightMode() {
    document.body.classList.remove("dark");

    // Save light mode
    localStorage.setItem("theme", "light");
}


// Dark Mode
function darkMode() {
    document.body.classList.add("dark");

    // Save dark mode
    localStorage.setItem("theme", "dark");
}