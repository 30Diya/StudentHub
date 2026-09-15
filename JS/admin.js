// ================= ADMIN PAGE =================

// Edit buttons
const editButtons = document.querySelectorAll("button");

editButtons.forEach(function (button) {

    button.addEventListener("click", function () {

        const row = button.closest("tr");
        const cells = row.querySelectorAll("td");

        if (cells.length === 0) {
            return;
        }

        // Users table
        if (cells.length === 4) {

            const name = cells[0].textContent;
            const email = cells[1].textContent;
            const role = cells[2].textContent;

            alert(
                "Edit User\n\n" +
                "Name: " + name + "\n" +
                "Email: " + email + "\n" +
                "Role: " + role
            );

        }

        // Events table
        else if (cells.length === 3) {

            const title = cells[0].textContent;
            const date = cells[1].textContent;

            alert(
                "Edit Event\n\n" +
                "Title: " + title + "\n" +
                "Date: " + date
            );

        }

    });

});


// ================= SIDEBAR =================

const sidebarLinks = document.querySelectorAll(".sidebar a");

sidebarLinks.forEach(function (link) {

    link.addEventListener("click", function (event) {

        event.preventDefault();

        const section = link.textContent;

        alert("You selected: " + section);

    });

});


// ================= TABLE ROW HOVER =================

const tableRows = document.querySelectorAll("table tr");

tableRows.forEach(function (row) {

    row.addEventListener("mouseenter", function () {

        row.style.backgroundColor = "#f1f5f7";

    });

    row.addEventListener("mouseleave", function () {

        row.style.backgroundColor = "";

    });

});