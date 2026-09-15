// ================= ACHIEVEMENT PAGE =================

const achievements = document.querySelectorAll(".achievement");

achievements.forEach(function (achievement) {

    // Click an achievement to show its details
    achievement.addEventListener("click", function () {

        const title = achievement.querySelector("h3").textContent;
        const description = achievement.querySelector("p").textContent;
        const date = achievement.querySelector("small").textContent;

        alert(
            "Achievement Details\n\n" +
            "Achievement: " + title + "\n" +
            "Description: " + description + "\n" +
            "Date: " + date
        );

    });

    // Hover effect
    achievement.addEventListener("mouseenter", function () {

        achievement.style.transform = "translateY(-5px)";
        achievement.style.transition = "0.3s";

    });

    achievement.addEventListener("mouseleave", function () {

        achievement.style.transform = "translateY(0)";

    });

});


// ================= PAGE LOAD EFFECT =================

window.addEventListener("load", function () {

    achievements.forEach(function (achievement, index) {

        achievement.style.opacity = "0";

        setTimeout(function () {

            achievement.style.opacity = "1";
            achievement.style.transition = "opacity 0.5s ease";

        }, index * 200);

    });

});