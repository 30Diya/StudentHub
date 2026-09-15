const boxes = document.querySelectorAll(".faq-box");

boxes.forEach(box => {
    box.addEventListener("click", () => {

        // Close other FAQ boxes
        boxes.forEach(item => {
            if (item !== box) {
                item.classList.remove("open");
                item.querySelector("button").textContent = "+";
            }
        });

        // Open current FAQ box
        box.classList.toggle("open");

        const button = box.querySelector("button");

        button.textContent =
            box.classList.contains("open") ? "−" : "+";
    });
});


// Search FAQ

const search = document.getElementById("search");

search.addEventListener("keyup", function () {

    const value = this.value.toLowerCase();

    boxes.forEach(box => {

        const text = box.innerText.toLowerCase();

        if (text.includes(value)) {
            box.style.display = "flex";
        } else {
            box.style.display = "none";
        }

    });

});