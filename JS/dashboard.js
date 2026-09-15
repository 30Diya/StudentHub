// ===============================
// STUDENTHUB DASHBOARD JAVASCRIPT
// ===============================

document.addEventListener("DOMContentLoaded", function () {

    // --------------------------------
    // Welcome message
    // --------------------------------

    setTimeout(function () {
        alert("Welcome back to StudentHub! Have a productive day.");
    }, 500);


    // --------------------------------
    // Statistics Cards
    // --------------------------------

    const cards = document.querySelectorAll(".card");

    cards.forEach(function (card) {

        card.addEventListener("click", function () {

            const title = card.querySelector("span").textContent;
            const number = card.querySelector("h2").textContent;

            alert(title + ": " + number);
        });

    });


    // --------------------------------
    // Progress Bar Animation
    // --------------------------------

    const progressBar = document.querySelector(".bar span");

    if (progressBar) {

        progressBar.style.width = "0%";

        setTimeout(function () {
            progressBar.style.width = "78%";
            progressBar.style.transition = "width 1.5s ease";
        }, 300);
    }


    // --------------------------------
    // Progress Circle Animation
    // --------------------------------

    const circle = document.querySelector(".circle");

    if (circle) {

        circle.style.background =
            "conic-gradient(#3a7563 0%, #e1e5df 0%)";

        setTimeout(function () {

            circle.style.background =
                "conic-gradient(#3a7563 78%, #e1e5df 78%)";

            circle.style.transition = "1.5s";

        }, 300);
    }


    // --------------------------------
    // Achievement Click
    // --------------------------------

    const achievements =
        document.querySelectorAll(".achievement");

    achievements.forEach(function (achievement) {

        achievement.addEventListener("click", function () {

            const achievementName =
                achievement.querySelector("h3").textContent;

            const description =
                achievement.querySelector("p").textContent;

            alert(
                "Achievement Unlocked!\n\n" +
                achievementName +
                "\n" +
                description
            );

        });

    });


    // --------------------------------
    // View All Achievements
    // --------------------------------

    const viewAll =
        document.querySelector(".section-heading a");

    if (viewAll) {

        viewAll.addEventListener("click", function (event) {

            event.preventDefault();

            alert(
                "You have earned 4 achievements:\n\n" +
                "🏆 Top Performer\n" +
                "⭐ Assignment Master\n" +
                "🎯 Goal Achiever\n" +
                "🚀 Fast Learner"
            );

        });

    }


    // --------------------------------
    // FAQ Button
    // --------------------------------

    const faqButton =
        document.querySelector(".faq-banner a");

    if (faqButton) {

        faqButton.addEventListener("click", function () {

            alert("Opening StudentHub FAQ...");

        });

    }


    // --------------------------------
    // Recent Activity Click
    // --------------------------------

    const activities =
        document.querySelectorAll(".activity-item");

    activities.forEach(function (activity) {

        activity.addEventListener("click", function () {

            const activityName =
                activity.querySelector("strong").textContent;

            alert("Activity: " + activityName);

        });

    });

});