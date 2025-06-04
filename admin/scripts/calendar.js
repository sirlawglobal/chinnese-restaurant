// DOM Elements
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
let currentView = "month"; // Default view

let months = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];

// Calendar events (for demonstration)
let events = [
  {
    id: 1,
    title: "New Seasonal Dish Tasting",
    date: "2025-05-03",
    time: "10:30 AM - 12:30 PM",
    startTime: "10:30 AM",
    endTime: "12:30 PM",
    type: "new-dish",
    people: ["HC", "SC", "+3"],
  },
  {
    id: 2,
    title: "Weekly Team Check-in",
    date: "2025-05-04",
    time: "11:00 AM - 12:30 PM",
    startTime: "11:00 AM",
    endTime: "12:30 PM",
    type: "team-check",
  },
  {
    id: 3,
    title: "Inventory Audit",
    date: "2025-05-04",
    time: "10:30 AM - 12:30 PM",
    startTime: "10:30 AM",
    endTime: "12:30 PM",
    type: "inventory",
  },
  {
    id: 4,
    title: "Weekly Team Check-in",
    date: "2025-05-11",
    time: "11:30 AM - 12:30 PM",
    startTime: "11:30 AM",
    endTime: "12:30 PM",
    type: "team-check",
  },
  {
    id: 5,
    title: "Inventory Audit",
    date: "2025-05-11",
    time: "11:30 AM - 12:30 PM",
    startTime: "11:30 AM",
    endTime: "12:30 PM",
    type: "inventory",
  },
  {
    id: 6,
    title: "New Seasonal Dish Tasting",
    date: "2025-05-13",
    time: "11:30 AM - 12:30 PM",
    startTime: "11:30 AM",
    endTime: "12:30 PM",
    type: "new-dish",
    people: ["HC", "SC", "+3"],
  },
];

//   Function to parse the time string and update events
function updateEventTimes(event) {
  const parts = event.time.split(" - ");
  event.startTime = parts[0].trim();
  // event.endTime = parts[1].trim();
  return event;
}

// Update the initial events array
events = events.map(updateEventTimes);

// Event Listeners
addScheduleBtn.addEventListener("click", showNewScheduleForm);
closeNewScheduleBtn.addEventListener("click", hideNewScheduleForm);

// Today's date for the form
const today = new Date();
const defaultDateFormatted = `${today.getFullYear()}-${String(
  today.getMonth() + 1
).padStart(2, "0")}-${String(today.getDate()).padStart(2, "0")}`;
let selectedDate = defaultDateFormatted;

function populateYearSelector() {
  const startYear = currentYear - 10;
  const endYear = currentYear + 10;
  for (let i = startYear; i <= endYear; i++) {
    const option = document.createElement("option");
    option.value = i;
    option.textContent = i;
    if (i === currentYear) {
      option.selected = true;
    }
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
    currentDate = new Date(
      firstDayOfCurrentWeek.getFullYear(),
      firstDayOfCurrentWeek.getMonth(),
      firstDayOfCurrentWeek.getDate() - 7
    );
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
    currentDate = new Date(
      firstDayOfCurrentWeek.getFullYear(),
      firstDayOfCurrentWeek.getMonth(),
      firstDayOfCurrentWeek.getDate() + 7
    );
  } else if (currentView === "day") {
    currentDate.setDate(currentDate.getDate() + 1);
  } else if (currentView === "year") {
    currentYear++;
  }
  updateCalendarView(currentView);
}

// Function to generate the calendar grid for a given month and year (MODIFIED)
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

    if (
      currentYearToday === year &&
      currentMonthToday === month &&
      currentDateToday === i
    ) {
      dayCell.classList.add("today");
    }

    dayCell.addEventListener("click", (event) => {
      // Check if the click target is the day cell itself or the day number
      if (
        event.target === dayCell ||
        event.target.classList.contains("day-number")
      ) {
        const year = parseInt(dayCell.dataset.year);
        const month = parseInt(dayCell.dataset.month);
        const day = parseInt(dayCell.dataset.day);
        const newSelectedDate = new Date(year, month, day);

        // Format the date for the new schedule form
        const formattedDate = `${newSelectedDate.getFullYear()}-${String(
          newSelectedDate.getMonth() + 1
        ).padStart(2, "0")}-${String(newSelectedDate.getDate()).padStart(
          2,
          "0"
        )}`;
        const dateInput = document.getElementById("scheduleDate");
        dateInput.value = formattedDate;
        selectedDate = formattedDate;

        showNewScheduleForm(); // Open the new schedule form
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
      eventDiv.style.cursor = "pointer"; // Indicate event is clickable
      eventDiv.dataset.year = year;
      eventDiv.dataset.month = month;
      eventDiv.dataset.day = i;
      eventDiv.addEventListener("click", showDayViewForEvent); // New event listener
      eventDiv.innerHTML = `
  <div>${event.title}</div>
  <div class="event-time">${event.time}</div>
  ${
    event.people
      ? `<div class="event-people">${event.people
          .map((person) => `<div class="avatar">${person}</div>`)
          .join("")}</div>`
      : ""
  }
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
    console.log("Clicked Event Dataset:", eventDiv.dataset);
    const year = parseInt(eventDiv.dataset.year);
    const month = parseInt(eventDiv.dataset.month);
    const day = parseInt(eventDiv.dataset.day);

    if (!isNaN(year) && !isNaN(month) && !isNaN(day)) {
      const newSelectedDate = new Date(year, month, day);
      // console.log("Selected Date for Day View:", newSelectedDate);
      currentDate = newSelectedDate;
      updateCalendarView("day");
    } else {
      console.error(
        "Could not parse date from event dataset:",
        eventDiv.dataset
      );
    }
  } else {
    console.error("Clicked element is not within an event div.");
  }
}

function generateWeekView(date) {
  calendarGrid.classList.add("week-view");
  calendarGrid.classList.remove("year-view", "day-view", "month-view");
  calendarGrid.innerHTML = ""; // Clear the previous grid

  const startOfWeek = getStartOfWeek(date);
  const endOfWeek = new Date(
    startOfWeek.getFullYear(),
    startOfWeek.getMonth(),
    startOfWeek.getDate() + 6
  );

  const monthName = startOfWeek.toLocaleString("en-US", {
    month: "long",
  });
  calendarHeaderTitle.innerHTML = `${monthName}`;
  const startDateFormatted = `${startOfWeek.toLocaleString("en-US", {
    weekday: "short",
  })} ${startOfWeek.getDate()}`;
  const endDateFormatted = `${endOfWeek.toLocaleString("en-US", {
    weekday: "short",
  })} ${endOfWeek.getDate()}`;

  weekHeader.textContent = `${startDateFormatted} - ${endDateFormatted}`;
  calendarHeader.appendChild(weekHeader);

  const daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
  const headerRow = document.createElement("div");
  headerRow.classList.add("calendar-row", "day-headers");
  const emptyHeader = document.createElement("div"); // For the time column
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
      const currentDateOfWeek = new Date(
        startOfWeek.getFullYear(),
        startOfWeek.getMonth(),
        startOfWeek.getDate() + i
      );
      const dayCell = document.createElement("div");
      dayCell.classList.add("day-cell");
      const eventsOnThisHour = getEventsForDateTime(currentDateOfWeek, hour);
      eventsOnThisHour.forEach((event) =>
        addEventToCellInWeekView(dayCell, event)
      );
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
${
  event.people
    ? `<div class="event-people">${event.people
        .map((person) => `<div class="avatar">${person}</div>`)
        .join("")}</div>`
    : ""
}
`;
  cell.appendChild(eventDiv);
}

// Update the view button event listener to re-render if already active
viewButtons.forEach((button) => {
  button.addEventListener("click", (e) => {
    const view = e.target.dataset.view;
    if (view === currentView) {
      updateCalendarView(view); // Re-render the current view
    } else {
      updateCalendarView(view);
    }
  });
});

function generateYearView(year) {
  calendarHeaderTitle.innerHTML = `${year}`;
  weekHeader.textContent = "";
  calendarGrid.innerHTML = "";
  calendarGrid.classList.add("year-view");
  calendarGrid.classList.remove("day-view", "week-view", "month-view"); // Add a class for specific styling

  for (let month = 0; month < 12; month++) {
    const monthContainer = document.createElement("div");
    monthContainer.classList.add("month-container");
    generateMiniMonth(month, year, monthContainer);
    calendarGrid.appendChild(monthContainer);
  }
}

function generateMiniMonth(month, year, container) {
  container.innerHTML = ""; // Clear previous content

  const monthName = new Date(year, month, 1).toLocaleString("en-US", {
    month: "long",
  });
  const monthHeader = document.createElement("div");
  monthHeader.classList.add("mini-month-header");
  monthHeader.textContent = monthName;
  monthHeader.dataset.month = month; // Store month for navigation
  monthHeader.dataset.year = year; // Store year for navigation
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
    dayCell.dataset.month = month; // Store month for navigation
    dayCell.dataset.year = year; // Store year for navigation
    dayCell.dataset.day = i; // Store day for navigation
    dayCell.addEventListener("click", navigateToMonthView);

    const eventsOnThisDay = getEventsForDate(new Date(year, month, i));
    if (eventsOnThisDay.length > 0) {
      dayCell.classList.add("has-event"); // You can enhance this with more visual cues
    }
    miniCalendarGrid.appendChild(dayCell);
  }

  container.appendChild(miniCalendarGrid);
}

function navigateToMonthView(event) {
  console.log("====================================");
  console.log(event);
  console.log("====================================");
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
  const diff = date.getDate() - day + (day === 0 ? -6 : 1); // Adjust when day is Sunday
  return new Date(date.setDate(diff));
}

function getEventsForDate(date) {
  const year = date.getFullYear();
  const month = date.getMonth();
  const day = date.getDate();
  return events.filter((event) => {
    const eventDate = new Date(event.date);
    return (
      eventDate.getFullYear() === year &&
      eventDate.getMonth() === month &&
      eventDate.getDate() === day
    );
  });
}

function getEventsForDateTime(date) {
  const year = date.getFullYear();
  const month = date.getMonth();
  const day = date.getDate();
  return events.filter((event) => {
    const eventDate = new Date(event.date);
    return (
      eventDate.getFullYear() === year &&
      eventDate.getMonth() === month &&
      eventDate.getDate() === day
    );
  });
}

function addEventToCell(event) {
  const eventDiv = document.createElement("div");
  eventDiv.classList.add("event", event.type);
  eventDiv.innerHTML = `
<div>${event.title}</div>
<div class="event-time">${event.time}</div>
${
  event.people
    ? `<div class="event-people">${event.people
        .map((person) => `<div class="avatar">${person}</div>`)
        .join("")}</div>`
    : ""
}
`;

  const eventDate = new Date(event.date);
  const dayOfMonth = eventDate.getDate();
  console.log("Event Day:", dayOfMonth);

  const dayCells = calendarGrid.querySelectorAll(".calendar-day:not(.empty)");
  dayCells.forEach((cell) => {
    const dayNumberElement = cell.querySelector(".day-number");
    console.log("Day Number Element:", dayNumberElement);
    if (dayNumberElement) {
      console.log("Day Number Text:", dayNumberElement.textContent);
    }
    if (
      dayNumberElement &&
      parseInt(dayNumberElement.textContent) === dayOfMonth
    ) {
      cell.appendChild(eventDiv);
    }
  });
}

function updateCalendarView(view) {
  currentView = view;
  viewButtons.forEach((btn) => btn.classList.remove("active"));
  document
    .querySelector(`.view-btn[data-view="${view}"]`)
    .classList.add("active");

  if (currentView === "month") {
    generateMonthView(currentMonth, currentYear);
    monthSelector.value = currentMonth;
    yearSelector.value = currentYear;
  } else if (currentView === "week") {
    generateWeekView(currentDate);
    // You might want to update a header here to show the current week
  } else if (currentView === "day") {
    generateDayView(currentDate);
    // You might want to update a header here to show the current day
  } else if (currentView === "year") {
    generateYearView(currentYear);
    yearSelector.value = currentYear;
  }
}

function generateDayView(date) {
  calendarHeaderTitle.innerHTML = `${date.toDateString()}`;
  weekHeader.textContent = "";
  calendarGrid.classList.add("day-view");
  calendarGrid.classList.remove("year-view", "week-view", "month-view");
  calendarGrid.innerHTML = `<div class="calendar-day-header">Time</div><div class="day-view-column"></div>`;
  const dayViewColumn = calendarGrid.querySelector(".day-view-column");

  const eventsOnThisDay = getEventsForDateTime(date); // Get all events for the day

  for (let hour = 0; hour < 24; hour++) {
    const timeSlotContainer = document.createElement("div"); // Container for time label and events
    timeSlotContainer.classList.add("time-slot-container");

    const timeLabel = document.createElement("div");
    timeLabel.classList.add("time-slot-label");
    const formattedHour = String(hour).padStart(2, "0");
    timeLabel.textContent = `${formattedHour}:00`;
    timeSlotContainer.appendChild(timeLabel);

    const eventArea = document.createElement("div");
    eventArea.classList.add("event-area"); // Area to hold events for this hour
    timeSlotContainer.appendChild(eventArea);

    const eventsInThisHour = eventsOnThisDay.filter((event) => {
      const eventStartHour = parseInt(event.time.split(":")[0]);
      const eventEndHour = event.endTime
        ? parseInt(event.endTime.split(":")[0])
        : eventStartHour + 1; // Assume 1-hour duration if no end time
      return eventStartHour <= hour && hour < eventEndHour; // Check if the hour falls within the event's duration
    });

    if (eventsInThisHour.length > 0) {
      // Logic to calculate widths and positions of overlapping events
      const overlaps = findOverlappingEvents(eventsInThisHour);
      const positions = calculateEventPositions(eventsInThisHour, overlaps);

      eventsInThisHour.forEach((event, index) => {
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
<div class="event-details">${event.time} ${
    event.venue ? `(${event.venue})` : ""
  }</div>
${
  event.people
    ? `<div class="event-people">${event.people
        .map((person) => `<div class="avatar">${person}</div>`)
        .join("")}</div>`
    : ""
}
<div class="event-notes">${event.notes || ""}</div>
`;
  return eventDiv;
}

function findOverlappingEvents(events) {
  const overlaps = {};
  for (let i = 0; i < events.length; i++) {
    overlaps[events[i].id] =
      events.filter(
        (e) =>
          e.id !== events[i].id &&
          parseInt(e.time.split(":")[0]) ===
            parseInt(events[i].time.split(":")[0])
      ).length > 0;
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
      currentLeft = 0; // Reset for the next non-overlapping event (simplistic)
    }
  });

  return positions;
}

populateYearSelector();
updateCalendarView(currentView); // Initial call to set the default view
monthSelector.value = currentMonth;

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

// Function to show the new schedule form with sliding animation
function showNewScheduleForm() {
  container.classList.add("slide-left");
}

// Function to hide the new schedule form with sliding animation
function hideNewScheduleForm() {
  container.classList.remove("slide-left");
}

createScheduleBtn.addEventListener("click", (e) => {
  e.preventDefault();

  const timeParts = timeInput.value.split(" - "); // Assuming the input is also "startTime - endTime"
  const startTime = timeParts[0] ? timeParts[0].trim() : timeInput.value.trim();
  const endTime = timeParts[1] ? timeParts[1].trim() : endTimeInput.value;

  const newEvent = {
    title: titleInput.value,
    type: categoryInput.value,
    date: dateInput.value,
    time: timeInput.value,
    startTime: startTime,
    endTime: endTime,
    team: teamInput.value.split(",").map((item) => item.trim()),
    venue: venueInput.value,
    notes: notesInput.value,
    people: teamInput.value.split(",").map((item) => item.trim()),
  };

  events.push(newEvent);
  updateCalendarView(currentView);
  hideNewScheduleForm();
  resetForm();
  alert("New schedule created successfully!");
});

// Local Storage Handling
if (localStorage.getItem("restaurantSchedule")) {
  events = JSON.parse(localStorage.getItem("restaurantSchedule"));
  // Ensure existing stored events have startTime and endTime
  events = events.map((event) => {
    if (!event.startTime && event.time) {
      return updateEventTimes(event);
    }
    return event;
  });
  updateCalendarView(currentView);
}

function saveScheduleToLocalStorage() {
  localStorage.setItem("restaurantSchedule", JSON.stringify(events));
}

const originalPush = Array.prototype.push;
Array.prototype.push = function (...args) {
  const result = originalPush.apply(this, args);
  if (this === events) {
    // Ensure new events also have startTime and endTime before saving
    args.forEach((newEvent) => {
      if (!newEvent.startTime && newEvent.time) {
        updateEventTimes(newEvent);
      }
    });
    saveScheduleToLocalStorage();
  }
  return result;
};

function getCategoryClass(category) {
  const categoryLower = category.toLowerCase();
  if (categoryLower.includes("dish") || categoryLower.includes("tasting"))
    return "new-dish";
  if (categoryLower.includes("team") || categoryLower.includes("check"))
    return "team-check";
  if (categoryLower.includes("inventory") || categoryLower.includes("audit"))
    return "inventory";
  return "new-dish"; // Default
}

// Add event to calendar UI
function addEventToCalendar(event) {
  console.log("Added event to calendar:", event);

  // Get date from event
  const eventDate = new Date(event.date);
  const day = eventDate.getDate();

  // Find the calendar day cell with this day number
  const dayCells = document.querySelectorAll(".calendar-day");
  let targetCell = null;

  dayCells.forEach((cell) => {
    const dayNumber = cell.querySelector(".day-number").textContent;
    if (parseInt(dayNumber) === day) {
      targetCell = cell;
    }
  });

  if (targetCell) {
    // Create event element
    const eventElement = document.createElement("div");
    eventElement.className = `event ${event.type}`;

    // Create event content
    let eventContent = `
              <div>${event.title}</div>
              <div class="event-time">${event.time}</div>
          `;

    // Add people if applicable
    if (event.team) {
      eventContent += `
                  <div class="event-people">
                      <div class="avatar">HC</div>
                  </div>
              `;
    }

    eventElement.innerHTML = eventContent;

    // Add to target cell
    targetCell.appendChild(eventElement);
  }
}

// Reset the form fields
function resetForm() {
  document.getElementById("scheduleTitle").value = "";
  document.getElementById("scheduleCategory").value = "";
  document.getElementById("scheduleTime").value = "12:32";
  document.getElementById("scheduleTeam").value = "";
  document.getElementById("scheduleVenue").value = "";
  document.getElementById("scheduleNotes").value = "";

  selectedDate = defaultDateFormatted;
}
