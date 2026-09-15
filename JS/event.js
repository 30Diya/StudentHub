// ================= EVENT PAGE =================

let allEvents = [];
let filteredEvents = [];

let currentPage = 1;

const eventsPerPage = 6;


// ================= GET HTML ELEMENTS =================

const eventGrid = document.getElementById("eventGrid");

const searchInput = document.getElementById("search");

const categoryFilter = document.getElementById("categoryFilter");

const yearFilter = document.getElementById("yearFilter");

const courseFilter = document.getElementById("courseFilter");

const sortSelect = document.getElementById("sort");

const pagination = document.getElementById("pagination");

const resultMessage = document.getElementById("resultMessage");


// ================= FETCH JSON =================

fetch("../JSON/events.json")

    .then(function(response) {

        if (!response.ok) {
            throw new Error("Could not load events.json");
        }

        return response.json();

    })

    .then(function(data) {

        // Store events from JSON

        allEvents = data.events;

        filteredEvents = [...allEvents];

        // Display events

        displayEvents();

    })

    .catch(function(error) {

        console.error("Error loading events:", error);

        eventGrid.innerHTML = `
            <div class="no-results">
                <h3>Unable to load events</h3>
                <p>Please check your events.json file.</p>
            </div>
        `;

    });


// ================= DISPLAY EVENTS =================

function displayEvents() {

    eventGrid.innerHTML = "";

    const totalEvents = filteredEvents.length;


    // ================= NO RESULTS =================

    if (totalEvents === 0) {

        resultMessage.textContent = "No events found.";

        eventGrid.innerHTML = `
            <div class="no-results">

                <h3>No Events Found</h3>

                <p>
                    Try changing your search or filter options.
                </p>

            </div>
        `;

        pagination.innerHTML = "";

        return;
    }


    // ================= RESULT COUNT =================

    resultMessage.textContent =
        totalEvents + " event(s) found";


    // ================= TOTAL PAGES =================

    const totalPages = Math.ceil(
        totalEvents / eventsPerPage
    );


    // If current page is greater than total pages

    if (currentPage > totalPages) {

        currentPage = totalPages;

    }


    // ================= START / END =================

    const start =
        (currentPage - 1) * eventsPerPage;

    const end =
        start + eventsPerPage;


    const eventsToDisplay =
        filteredEvents.slice(start, end);


    // ================= CREATE CARDS =================

    eventsToDisplay.forEach(function(event, index) {

        const eventCard =
            document.createElement("div");

        eventCard.className = "event-card";


        eventCard.innerHTML = `

            <div class="image-box">

                <img
                    src="${event.image}"
                    alt="${event.title}">

                <div class="date">

                    <strong>
                        ${event.day}
                    </strong>

                    <span>
                        ${event.month}
                    </span>

                </div>

            </div>


            <div class="event-info">

                <span class="category">
                    ${event.category}
                </span>

                <h3>
                    ${event.title}
                </h3>

                <p>
                    ${event.description}
                </p>

                <div class="event-meta">

                    <span>
                        📅 ${event.date}
                    </span>

                    <span>
                        🎓 ${event.course}
                    </span>

                    <span>
                        📍 ${event.location}
                    </span>

                </div>

                <a href="#">
                    View Details →
                </a>

            </div>

        `;


        eventGrid.appendChild(eventCard);


        // ================= DETAILS BUTTON =================

        const detailsButton =
            eventCard.querySelector("a");


        detailsButton.addEventListener(
            "click",
            function(e) {

                e.preventDefault();

                alert(

                    "Event Details\n\n" +

                    "Event: " +
                    event.title +

                    "\nCategory: " +
                    event.category +

                    "\nDate: " +
                    event.date +

                    "\nYear: " +
                    event.year +

                    "\nCourse: " +
                    event.course +

                    "\nLocation: " +
                    event.location +

                    "\n\n" +

                    event.description

                );

            }
        );


        // ================= ANIMATION =================

        eventCard.style.opacity = "0";

        setTimeout(function() {

            eventCard.style.opacity = "1";

            eventCard.style.transition =
                "opacity 0.5s ease";

        }, index * 100);

    });


    // ================= PAGINATION =================

    createPagination(totalPages);

}


// ================= SEARCH =================

searchInput.addEventListener(
    "input",
    function() {

        currentPage = 1;

        applyFilters();

    }
);


// ================= CATEGORY FILTER =================

categoryFilter.addEventListener(
    "change",
    function() {

        currentPage = 1;

        applyFilters();

    }
);


// ================= YEAR FILTER =================

yearFilter.addEventListener(
    "change",
    function() {

        currentPage = 1;

        applyFilters();

    }
);


// ================= COURSE FILTER =================

courseFilter.addEventListener(
    "change",
    function() {

        currentPage = 1;

        applyFilters();

    }
);


// ================= SORT =================

sortSelect.addEventListener(
    "change",
    function() {

        currentPage = 1;

        applyFilters();

    }
);


// ================= APPLY FILTERS =================

function applyFilters() {

    const searchValue =
        searchInput.value
            .toLowerCase()
            .trim();


    const categoryValue =
        categoryFilter.value;


    const yearValue =
        yearFilter.value;


    const courseValue =
        courseFilter.value;


    // ================= FILTER =================

    filteredEvents =
        allEvents.filter(function(event) {

            const matchesSearch =

                event.title
                    .toLowerCase()
                    .includes(searchValue)

                ||

                event.description
                    .toLowerCase()
                    .includes(searchValue)

                ||

                event.category
                    .toLowerCase()
                    .includes(searchValue)

                ||

                event.course
                    .toLowerCase()
                    .includes(searchValue);


            const matchesCategory =

                categoryValue === "all"

                ||

                event.category === categoryValue;


            const matchesYear =

                yearValue === "all"

                ||

                event.year === yearValue;


            const matchesCourse =

                courseValue === "all"

                ||

                event.course === courseValue;


            return (

                matchesSearch &&

                matchesCategory &&

                matchesYear &&

                matchesCourse

            );

        });


    // ================= SORT =================

    const sortValue =
        sortSelect.value;


    if (sortValue === "title-asc") {

        filteredEvents.sort(function(a, b) {

            return a.title.localeCompare(b.title);

        });

    }


    else if (sortValue === "title-desc") {

        filteredEvents.sort(function(a, b) {

            return b.title.localeCompare(a.title);

        });

    }


    else if (sortValue === "date-asc") {

        filteredEvents.sort(function(a, b) {

            return new Date(a.date) -
                   new Date(b.date);

        });

    }


    else if (sortValue === "date-desc") {

        filteredEvents.sort(function(a, b) {

            return new Date(b.date) -
                   new Date(a.date);

        });

    }


    // ================= DISPLAY =================

    displayEvents();

}


// ================= PAGINATION =================

function createPagination(totalPages) {

    pagination.innerHTML = "";


    // Don't show pagination for one page

    if (totalPages <= 1) {

        return;

    }


    // ================= PREVIOUS =================

    const previousButton =
        document.createElement("button");

    previousButton.textContent =
        "Previous";


    previousButton.disabled =
        currentPage === 1;


    previousButton.addEventListener(
        "click",
        function() {

            if (currentPage > 1) {

                currentPage--;

                displayEvents();

                window.scrollTo({
                    top: 500,
                    behavior: "smooth"
                });

            }

        }
    );


    pagination.appendChild(previousButton);


    // ================= PAGE NUMBERS =================

    for (
        let i = 1;
        i <= totalPages;
        i++
    ) {

        const pageButton =
            document.createElement("button");

        pageButton.textContent = i;


        if (i === currentPage) {

            pageButton.classList.add("active");

        }


        pageButton.addEventListener(
            "click",
            function() {

                currentPage = i;

                displayEvents();

                window.scrollTo({
                    top: 500,
                    behavior: "smooth"
                });

            }
        );


        pagination.appendChild(pageButton);

    }


    // ================= NEXT =================

    const nextButton =
        document.createElement("button");

    nextButton.textContent =
        "Next";


    nextButton.disabled =
        currentPage === totalPages;


    nextButton.addEventListener(
        "click",
        function() {

            if (currentPage < totalPages) {

                currentPage++;

                displayEvents();

                window.scrollTo({
                    top: 500,
                    behavior: "smooth"
                });

            }

        }
    );


    pagination.appendChild(nextButton);

}


// ================= FEATURED EVENT =================

const featuredButton =
    document.querySelector(".button");


if (featuredButton) {

    featuredButton.addEventListener(
        "click",
        function(e) {

            e.preventDefault();

            alert(

                "Featured Event\n\n" +

                "Tech Fest 2025\n" +

                "Date: 20 August 2025\n" +

                "Location: Main Auditorium\n\n" +

                "Explore technology, innovation and exciting ideas with students from across the campus."

            );

        }
    );

}