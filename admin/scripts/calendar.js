const container = document.getElementById("container");
const scheduleDetails = document.getElementById("scheduleDetails");
const newSchedule = document.getElementById("newSchedule");
const addScheduleBtn = document.getElementById("addScheduleBtn");
const closeNewScheduleBtn = document.getElementById("closeNewSchedule");
const newScheduleForm = document.getElementById("newSchedule");
const createScheduleBtn = document.getElementById("createScheduleBtn");
const titleInput = document.getElementById("scheduleTitle");
const categoryInput = document.getElementById("scheduleCategory");
const dateInput = document.getElementById("scheduleDate");
const timeInput = document.getElementById("scheduleTime");
const endTimeInput = document.getElementById("scheduleEndTime");
const teamInput = document.getElementById("scheduleTeam");
const venueInput = document.getElementById("scheduleVenue");
const notesInput = document.getElementById("scheduleNotes");
const calendarHeader = document.getElementById("calendarDayHeader");
const calendarHeaderTitle = document.getElementById("calendarTitle");
const weekHeader = document.querySelector(".week-header");
const monthSelector = document.getElementById("monthSelector");
const yearSelector = document.getElementById("yearSelector");
const calendarGrid = document.getElementById("calendarGrid");
const viewButtons = document.querySelectorAll(".view-btn");
const prevViewBtn = document.getElementById("prevViewBtn");
const nextViewBtn = document.getElementById("nextViewBtn");

prevViewBtn.addEventListener("click", navigatePrevious);
nextViewBtn.addEventListener("click", navigateNext);

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();
let currentView = "month";
let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
let events = [];

const today = new Date();
const defaultDateFormatted = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, "0")}-${String(today.getDate()).padStart(2, "0")}`;
let selectedDate = defaultDateFormatted;

async function fetchEvents() {
  
    const response = await fetch('../calendar/get_events.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    });
    const result = await response.json();
    if (result.data_type === 'events') {
        events = result.data;
        updateCalendarView(currentView);
    } else {
        alert(result.message);
    }
}

function populateYearSelector() {
    const startYear = currentYear - 10;
    const endYear = currentYear + 10;
    for (let i = startYear; i <= endYear; i++) {
        const option = document.createElement("option");
        option.value = i;
        option.textContent = i;
        if (i === currentYear) option.selected = true;
        yearSelector.appendChild(option);
    }
}

function navigatePrevious() {
    if (currentView === "month") {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
    } else if (currentView === "week") {
        const firstDayOfCurrentWeek = getStartOfWeek(currentDate);
        currentDate = new Date(firstDayOfCurrentWeek.getFullYear(), firstDayOfCurrentWeek.getMonth(), firstDayOfCurrentWeek.getDate() - 7);
    } else if (currentView === "day") {
        currentDate.setDate(currentDate.getDate() - 1);
    } else if (currentView === "year") {
        currentYear--;
    }
    updateCalendarView(currentView);
}

function navigateNext() {
    if (currentView === "month") {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
    } else if (currentView === "week") {
        const firstDayOfCurrentWeek = getStartOfWeek(currentDate);
        currentDate = new Date(firstDayOfCurrentWeek.getFullYear(), firstDayOfCurrentWeek.getMonth(), firstDayOfCurrentWeek.getDate() + 7);
    } else if (currentView === "day") {
        currentDate.setDate(currentDate.getDate() + 1);
    } else if (currentView === "year") {
        currentYear++;
    }
    updateCalendarView(currentView);
}

function generateMonthView(month, year) {
    calendarHeaderTitle.innerHTML = `${months[month]} ${year}`;
    weekHeader.textContent = "";
    calendarGrid.classList.add("month-view");
    calendarGrid.classList.remove("year-view", "week-view", "day-view");
    calendarGrid.innerHTML = `
        <div class="calendar-day-header">Sun</div>
        <div class="calendar-day-header">Mon</div>
        <div class="calendar-day-header">Tue</div>
        <div class="calendar-day-header">Wed</div>
        <div class="calendar-day-header">Thu</div>
        <div class="calendar-day-header">Fri</div>
        <div class="calendar-day-header">Sat</div>
    `;

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const daysInMonth = lastDayOfMonth.getDate();
    const startingDay = firstDayOfMonth.getDay();

    const today = new Date();
    const currentMonthToday = today.getMonth();
    const currentYearToday = today.getFullYear();
    const currentDateToday = today.getDate();

    for (let i = 0; i < startingDay; i++) {
        const emptyCell = document.createElement("div");
        emptyCell.classList.add("calendar-day", "empty");
        calendarGrid.appendChild(emptyCell);
    }

    for (let i = 1; i <= daysInMonth; i++) {
        const dayCell = document.createElement("div");
        dayCell.classList.add("calendar-day");
        dayCell.dataset.year = year;
        dayCell.dataset.month = month;
        dayCell.dataset.day = i;
        dayCell.style.cursor = "pointer";

        if (currentYearToday === year && currentMonthToday === month && currentDateToday === i) {
            dayCell.classList.add("today");
        }

        dayCell.addEventListener("click", (event) => {
            if (event.target === dayCell || event.target.classList.contains("day-number")) {
                const year = parseInt(dayCell.dataset.year);
                const month = parseInt(dayCell.dataset.month);
                const day = parseInt(dayCell.dataset.day);
                const newSelectedDate = new Date(year, month, day);
                const formattedDate = `${newSelectedDate.getFullYear()}-${String(newSelectedDate.getMonth() + 1).padStart(2, "0")}-${String(newSelectedDate.getDate()).padStart(2, "0")}`;
                dateInput.value = formattedDate;
                selectedDate = formattedDate;
                showNewScheduleForm();
            }
        });

        const dayNumber = document.createElement("div");
        dayNumber.classList.add("day-number");
        dayNumber.textContent = i;
        dayCell.appendChild(dayNumber);

        const eventsOnThisDay = getEventsForDate(new Date(year, month, i));
        eventsOnThisDay.forEach((event) => {
            const eventDiv = document.createElement("div");
            eventDiv.classList.add("event", event.type);
            eventDiv.style.cursor = "pointer";
            eventDiv.dataset.year = year;
            eventDiv.dataset.month = month;
            eventDiv.dataset.day = i;
            eventDiv.dataset.eventId = event.id;
            eventDiv.addEventListener("click", showDayViewForEvent);
            eventDiv.innerHTML = `
                <div>${event.title}</div>
                <div class="event-time">${event.time}</div>
                ${event.team ? `<div class="event-people">${event.team.map(person => `<div class="avatar">${person}</div>`).join("")}</div>` : ""}
            `;
            dayCell.appendChild(eventDiv);
        });

        calendarGrid.appendChild(dayCell);
    }
}

function showDayViewForEvent(event) {
    event.stopPropagation();
    const eventDiv = event.target.closest(".event");
    if (eventDiv) {
        const year = parseInt(eventDiv.dataset.year);
        const month = parseInt(eventDiv.dataset.month);
        const day = parseInt(eventDiv.dataset.day);
        if (!isNaN(year) && !isNaN(month) && !isNaN(day)) {
            currentDate = new Date(year, month, day);
            updateCalendarView("day");
        }
    }
}

function generateWeekView(date) {
    calendarGrid.classList.add("week-view");
    calendarGrid.classList.remove("year-view", "day-view", "month-view");
    calendarGrid.innerHTML = "";

    const startOfWeek = getStartOfWeek(date);
    const endOfWeek = new Date(startOfWeek.getFullYear(), startOfWeek.getMonth(), startOfWeek.getDate() + 6);

    const monthName = startOfWeek.toLocaleString("en-US", { month: "long" });
    calendarHeaderTitle.innerHTML = `${monthName}`;
    const startDateFormatted = `${startOfWeek.toLocaleString("en-US", { weekday: "short" })} ${startOfWeek.getDate()}`;
    const endDateFormatted = `${endOfWeek.toLocaleString("en-US", { weekday: "short" })} ${endOfWeek.getDate()}`;
    weekHeader.textContent = `${startDateFormatted} - ${endDateFormatted}`;
    calendarHeader.appendChild(weekHeader);

    const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    const headerRow = document.createElement("div");
    headerRow.classList.add("calendar-row", "day-headers");
    const emptyHeader = document.createElement("div");
    emptyHeader.classList.add("time-header");
    headerRow.appendChild(emptyHeader);
    daysOfWeek.forEach((day) => {
        const dayHeader = document.createElement("div");
        dayHeader.classList.add("day-header-cell");
        dayHeader.textContent = day;
        headerRow.appendChild(dayHeader);
    });
    calendarGrid.appendChild(headerRow);

    for (let hour = 0; hour < 24; hour++) {
        const timeRow = document.createElement("div");
        timeRow.classList.add("calendar-row");
        const timeCell = document.createElement("div");
        timeCell.classList.add("time-slot");
        const formattedHour = String(hour).padStart(2, "0");
        timeCell.textContent = `${formattedHour}:00`;
        timeRow.appendChild(timeCell);

        for (let i = 0; i < 7; i++) {
            const currentDateOfWeek = new Date(startOfWeek.getFullYear(), startOfWeek.getMonth(), startOfWeek.getDate() + i);
            const dayCell = document.createElement("div");
            dayCell.classList.add("day-cell");
            const eventsOnThisHour = getEventsForDateTime(currentDateOfWeek, hour);
            eventsOnThisHour.forEach((event) => addEventToCellInWeekView(dayCell, event));
            timeRow.appendChild(dayCell);
        }
        calendarGrid.appendChild(timeRow);
    }
}

function addEventToCellInWeekView(cell, event) {
    const eventDiv = document.createElement("div");
    eventDiv.classList.add("event", event.type, "week-event");
    eventDiv.innerHTML = `
        <div class="event-title">${event.title}</div>
        <div class="event-time">${event.time}</div>
        ${event.team ? `<div class="event-people">${event.team.map(person => `<div class="avatar">${person}</div>`).join("")}</div>` : ""}
    `;
    cell.appendChild(eventDiv);
}

viewButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
        updateCalendarView(e.target.dataset.view);
    });
});

function generateYearView(year) {
    calendarHeaderTitle.innerHTML = `${year}`;
    weekHeader.textContent = "";
    calendarGrid.innerHTML = "";
    calendarGrid.classList.add("year-view");
    calendarGrid.classList.remove("day-view", "week-view", "month-view");

    for (let month = 0; month < 12; month++) {
        const monthContainer = document.createElement("div");
        monthContainer.classList.add("month-container");
        generateMiniMonth(month, year, monthContainer);
        calendarGrid.appendChild(monthContainer);
    }
}

function generateMiniMonth(month, year, container) {
    container.innerHTML = "";
    const monthName = new Date(year, month, 1).toLocaleString("en-US", { month: "long" });
    const monthHeader = document.createElement("div");
    monthHeader.classList.add("mini-month-header");
    monthHeader.textContent = monthName;
    monthHeader.dataset.month = month;
    monthHeader.dataset.year = year;
    monthHeader.addEventListener("click", navigateToMonthView);
    container.appendChild(monthHeader);

    const miniCalendarGrid = document.createElement("div");
    miniCalendarGrid.classList.add("mini-calendar-grid");

    const dayHeaders = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    dayHeaders.forEach((header) => {
        const headerCell = document.createElement("div");
        headerCell.classList.add("mini-day-header");
        headerCell.textContent = header;
        miniCalendarGrid.appendChild(headerCell);
    });

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const daysInMonth = lastDayOfMonth.getDate();
    const startingDay = firstDayOfMonth.getDay();

    for (let i = 0; i < startingDay; i++) {
        const emptyCell = document.createElement("div");
        emptyCell.classList.add("mini-day", "empty");
        miniCalendarGrid.appendChild(emptyCell);
    }

    for (let i = 1; i <= daysInMonth; i++) {
        const dayCell = document.createElement("div");
        dayCell.classList.add("mini-day");
        dayCell.textContent = i;
        dayCell.dataset.month = month;
        dayCell.dataset.year = year;
        dayCell.dataset.day = i;
        dayCell.addEventListener("click", navigateToMonthView);

        const eventsOnThisDay = getEventsForDate(new Date(year, month, i));
        if (eventsOnThisDay.length > 0) {
            dayCell.classList.add("has-event");
        }
        miniCalendarGrid.appendChild(dayCell);
    }

    container.appendChild(miniCalendarGrid);
}

function navigateToMonthView(event) {
    const month = parseInt(event.target.dataset.month);
    const year = parseInt(event.target.dataset.year);
    currentMonth = month;
    currentYear = year;
    monthSelector.value = currentMonth;
    yearSelector.value = currentYear;
    updateCalendarView("month");
}

function getStartOfWeek(date) {
    const day = date.getDay();
    const diff = date.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(date.setDate(diff));
}

function getEventsForDate(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const day = date.getDate();
    return events.filter((event) => {
        const eventDate = new Date(event.event_date);
        return (
            eventDate.getFullYear() === year &&
            eventDate.getMonth() === month &&
            eventDate.getDate() === day
        );
    });
}

function getEventsForDateTime(date, hour) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const day = date.getDate();
    return events.filter((event) => {
        const eventDate = new Date(event.event_date);
        const eventStartHour = parseInt(event.start_time.split(":")[0]);
        return (
            eventDate.getFullYear() === year &&
            eventDate.getMonth() === month &&
            eventDate.getDate() === day &&
            eventStartHour === hour
        );
    });
}

function generateDayView(date) {
    calendarHeaderTitle.innerHTML = `${date.toDateString()}`;
    weekHeader.textContent = "";
    calendarGrid.classList.add("day-view");
    calendarGrid.classList.remove("year-view", "week-view", "month-view");
    calendarGrid.innerHTML = `<div class="calendar-day-header">Time</div><div class="day-view-column"></div>`;
    const dayViewColumn = calendarGrid.querySelector(".day-view-column");

    const eventsOnThisDay = getEventsForDate(date);

    for (let hour = 0; hour < 24; hour++) {
        const timeSlotContainer = document.createElement("div");
        timeSlotContainer.classList.add("time-slot-container");

        const timeLabel = document.createElement("div");
        timeLabel.classList.add("time-slot-label");
        const formattedHour = String(hour).padStart(2, "0");
        timeLabel.textContent = `${formattedHour}:00`;
        timeSlotContainer.appendChild(timeLabel);

        const eventArea = document.createElement("div");
        eventArea.classList.add("event-area");
        timeSlotContainer.appendChild(eventArea);

        const eventsInThisHour = eventsOnThisDay.filter((event) => {
            const eventStartHour = parseInt(event.start_time.split(":")[0]);
            const eventEndHour = event.end_time ? parseInt(event.end_time.split(":")[0]) : eventStartHour + 1;
            return eventStartHour <= hour && hour < eventEndHour;
        });

        if (eventsInThisHour.length > 0) {
            const overlaps = findOverlappingEvents(eventsInThisHour);
            const positions = calculateEventPositions(eventsInThisHour, overlaps);

            eventsInThisHour.forEach((event) => {
                const eventDiv = addEventToDayView(event);
                eventDiv.style.width = `${positions[event.id].width}%`;
                eventDiv.style.left = `${positions[event.id].left}%`;
                eventArea.appendChild(eventDiv);
            });
        }

        dayViewColumn.appendChild(timeSlotContainer);
    }
}

function addEventToDayView(event) {
    const eventDiv = document.createElement("div");
    eventDiv.classList.add("event", event.type, "day-event");
    eventDiv.innerHTML = `
        <div class="event-title">${event.title}</div>
        <div class="event-details">${event.time} ${event.venue ? `(${event.venue})` : ""}</div>
        ${event.team ? `<div class="event-people">${event.team.map(person => `<div class="avatar">${person}</div>`).join("")}</div>` : ""}
        <div class="event-notes">${event.notes || ""}</div>
    `;
    return eventDiv;
}

function findOverlappingEvents(events) {
    const overlaps = {};
    for (let i = 0; i < events.length; i++) {
        overlaps[events[i].id] = events.filter((e) => e.id !== events[i].id && parseInt(e.start_time.split(":")[0]) === parseInt(events[i].start_time.split(":")[0])).length > 0;
    }
    return overlaps;
}

function calculateEventPositions(events, overlaps) {
    const positions = {};
    const numOverlapping = events.filter((e) => overlaps[e.id]).length;
    const baseWidth = numOverlapping > 0 ? 100 / (numOverlapping + 1) : 100;
    let currentLeft = 0;

    events.forEach((event) => {
        positions[event.id] = { width: baseWidth, left: currentLeft };
        if (overlaps[event.id]) {
            currentLeft += baseWidth;
        } else {
            currentLeft = 0;
        }
    });

    return positions;
}

function showNewScheduleForm() {
    container.classList.add("slide-left");
}

function hideNewScheduleForm() {
    container.classList.remove("slide-left");
}

createScheduleBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const timeParts = timeInput.value.split(" - ");
    const startTime = timeParts[0]?.trim() ?? timeInput.value.trim();
    const endTime = timeParts[1]?.trim() ?? endTimeInput.value;

    const eventData = {
        title: titleInput.value,
        type: categoryInput.value,
        date: dateInput.value,
        startTime,
        endTime,
        team: teamInput.value.split(",").map(item => item.trim()).filter(item => item),
        venue: venueInput.value,
        notes: notesInput.value
    };

    const response = await fetch('../calendar/create_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(eventData)
    });

    const result = await response.json();
    if (result.data_type === 'event_created') {
        await fetchEvents();
        hideNewScheduleForm();
        resetForm();
        alert(result.message);
    } else {
        alert(result.message);
    }
});

function resetForm() {
    titleInput.value = "";
    categoryInput.value = "";
    timeInput.value = "";
    endTimeInput.value = "";
    teamInput.value = "";
    venueInput.value = "";
    notesInput.value = "";
    dateInput.value = defaultDateFormatted;
    selectedDate = defaultDateFormatted;
}

addScheduleBtn.addEventListener("click", showNewScheduleForm);
closeNewScheduleBtn.addEventListener("click", hideNewScheduleForm);
monthSelector.addEventListener("change", (e) => {
    currentMonth = parseInt(e.target.value);
    updateCalendarView(currentView);
});
yearSelector.addEventListener("change", (e) => {
    currentYear = parseInt(e.target.value);
    updateCalendarView(currentView);
});
viewButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
        updateCalendarView(e.target.dataset.view);
    });
});

fetchEvents();
populateYearSelector();
updateCalendarView(currentView);
monthSelector.value = currentMonth;



// edit.......
eventDiv.innerHTML += `<button onclick="deleteEvent(${event.id})">Delete</button>`;
async function deleteEvent(id) {   
    const response = await fetch('../calendar/delete_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    });
    const result = await response.json();
    if (result.data_type === 'event_deleted') {
        await fetchEvents();
    }
    alert(result.message);
}