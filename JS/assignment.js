// ================= ASSIGNMENT PAGE =================

const assignments = document.querySelectorAll(".assignment");

assignments.forEach(function (assignment) {

    assignment.addEventListener("click", function () {

        const title = assignment.querySelector("h3").textContent;
        const description = assignment.querySelector("p").textContent;
        const dueDate = assignment.querySelector("small").textContent;
        const status = assignment.querySelector("span").textContent;

        alert(
            "Assignment Details\n\n" +
            "Subject: " + title + "\n" +
            "Task: " + description + "\n" +
            dueDate + "\n" +
            "Status: " + status
        );

    });

});


// ================= ASSIGNMENT HOVER EFFECT =================

assignments.forEach(function (assignment) {

    assignment.addEventListener("mouseenter", function () {
        assignment.style.transform = "translateY(-3px)";
        assignment.style.transition = "0.3s";
    });

    assignment.addEventListener("mouseleave", function () {
        assignment.style.transform = "translateY(0)";
    });

});