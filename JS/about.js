// ================= ABOUT PAGE =================

// Select all team members
const members = document.querySelectorAll(".member");


// ================= TEAM MEMBER CLICK =================

members.forEach(function (member) {

    member.addEventListener("click", function () {

        const name = member.querySelector("h3").textContent;
        const role = member.querySelector("p").textContent;

        alert(
            "Team Member\n\n" +
            "Name: " + name + "\n" +
            "Role: " + role
        );

    });

});


// ================= HOVER EFFECT =================

members.forEach(function (member) {

    member.addEventListener("mouseenter", function () {

        member.style.transform = "translateY(-6px)";
        member.style.transition = "0.3s";

    });

    member.addEventListener("mouseleave", function () {

        member.style.transform = "translateY(0)";

    });

});


// ================= PAGE LOAD ANIMATION =================

window.addEventListener("load", function () {

    members.forEach(function (member, index) {

        member.style.opacity = "0";

        setTimeout(function () {

            member.style.opacity = "1";
            member.style.transition = "opacity 0.5s ease";

        }, index * 200);

    });

});