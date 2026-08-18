// Check the saved theme when the Profile page opens

if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
} else {
    document.body.classList.remove("dark");
}