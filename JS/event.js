// Featured Event Button

const featuredButton = document.querySelector(".button");

if (featuredButton) {
    featuredButton.addEventListener("click", function(e) {
        e.preventDefault();

        alert("Tech Fest 2025 details will be available soon!");
    });
}


// Event Details Buttons

const eventLinks = document.querySelectorAll(".event-info a");

eventLinks.forEach(link => {

    link.addEventListener("click", function(e) {

        e.preventDefault();

        const card = this.closest(".event-card");

        const eventName = card.querySelector("h3").textContent;

        alert("You selected: " + eventName);

    });

});


// Event Card Animation

const eventCards = document.querySelectorAll(".event-card");

eventCards.forEach((card, index) => {

    card.style.opacity = "0";

    setTimeout(() => {
        card.style.opacity = "1";
        card.style.transition = "0.5s";
    }, index * 200);

});