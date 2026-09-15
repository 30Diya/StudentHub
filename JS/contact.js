// ================= CONTACT FORM =================

const contactForm = document.querySelector(".contact-form form");

if (contactForm) {

    contactForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const subject = document.getElementById("subject").value.trim();
        const message = document.getElementById("message").value.trim();

        // Check if all fields are filled
        if (name === "" || email === "" || subject === "" || message === "") {

            alert("Please fill in all the fields.");

            return;
        }

        // Show success message
        alert(
            "Message sent successfully!\n\n" +
            "Thank you, " + name + ".\n" +
            "Our StudentHub team will get back to you soon."
        );

        // Clear the form
        contactForm.reset();

    });

}



// ================= CONTACT INFORMATION =================

const infoCards = document.querySelectorAll(".info-card");

infoCards.forEach(function (card) {

    card.addEventListener("click", function () {

        const title = card.querySelector("h3").textContent;
        const information = card.querySelector("p").textContent;

        alert(title + ":\n" + information);

    });

});



// ================= HELP CARDS =================

const helpLinks = document.querySelectorAll(".help-card a");

helpLinks.forEach(function (link) {

    link.addEventListener("click", function (event) {

        event.preventDefault();

        const card = link.closest(".help-card");
        const title = card.querySelector("h3").textContent;

        alert(
            "You selected: " + title +
            "\n\nPlease use the contact form above to send us your question."
        );

    });

});