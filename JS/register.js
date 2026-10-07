// StudentHub Registration Form JavaScript

const form = document.querySelector("form");

form.addEventListener("submit", function (event) {

    const password = document.querySelector(
        'input[name="password"]'
    ).value;

    const confirmPassword = document.querySelector(
        'input[name="confirm_password"]'
    ).value;

    const mobile = document.querySelector(
        'input[name="mobile"]'
    ).value;

    // Check password
    if (password.length < 6) {

        event.preventDefault();

        alert("Password must contain at least 6 characters.");

        return;
    }


    // Check confirm password
    if (password !== confirmPassword) {

        event.preventDefault();

        alert("Password and Confirm Password do not match.");

        return;
    }


    // Check mobile number
    if (!/^[0-9]{10}$/.test(mobile)) {

        event.preventDefault();

        alert("Mobile number must contain exactly 10 digits.");

        return;
    }

});