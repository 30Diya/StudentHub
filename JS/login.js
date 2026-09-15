// Keep saved dark mode

if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
}


// Login form

const form = document.querySelector("form");

form.addEventListener("submit", function(event) {

    event.preventDefault();

    // Get email and password

    const email = document.querySelector('input[type="email"]').value;
    const password = document.querySelector('input[type="password"]').value;


    // Check if fields are filled

    if (email !== "" && password !== "") {

        alert("Login Successful! Welcome to StudentHub 🎉");

        // Go to Home Page

        window.location.href = "home.html";

    } else {

        alert("Please enter your email and password.");

    }

});