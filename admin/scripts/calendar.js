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
let events = [];

// Fetch from backend
fetch('/chinnese-restaurant/admin/calendar/schedules.php')
  .then(res => res.json())
  .then(data => {
    events = data.map(event => ({
      ...event,
      
   type: getCategoryClass(event.category_slug),
      time: `${event.start_time} - ${event.end_time}`,
      startTime: event.start_time,
      endTime: event.end_time,
      people: JSON.parse(event.team || "[]")
    }));
    updateCalendarView(currentView);
  });


//   Function to parse the time string and update events
function updateEventTimes(event) {
  const parts = event.time.split(" - ");
  event.startTime = parts[0].trim();
  // event.endTime = parts[1].trim();
  return event;
}




const API_URL = '/chinnese-restaurant/admin/calendar/schedules.php'; // adjust path if needed

// Fetch schedules from backend

function formatDate(dateStr) {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateStr).toLocaleDateString(undefined, options);
}



function renderSidebar(events) {
  const sidebar = document.querySelector('.sidebar');
  if (!sidebar) {
    console.warn('Sidebar not found');
    return;
  }

  sidebar.innerHTML = '<h2>Schedule Details</h2>';

  if (!events.length) {
    sidebar.innerHTML += `<div class="empty-state">No schedules found.</div>`;
    return;
  }

  events.forEach(event => {
    const teamList = (event.people || []).map(name => `
      <div class="team-member">
        <div class="avatar">${name}</div>
        ${name}
      </div>`).join('');

    const scheduleHTML = `
      <div class="schedule-item">
        <div class="schedule-title">${event.title}</div>
       
        <div class="menu-updates-tag ${event.type}">${event.category}</div>
        <div class="schedule-details-row">📅 ${formatDate(event.date)}</div>
        <div class="schedule-details-row">🕒 ${event.startTime} - ${event.endTime}</div>
        <div class="schedule-details-row">📍 ${event.venue || "No venue"}</div>
        <div class="team-section">
          <div class="team-title">Team</div>
          <div class="team-members">${teamList}</div>
        </div>
        <div class="notes-section">
          <div class="notes-title">Notes</div>
          <div class="notes-content">${event.notes || "No notes available"}</div>
        </div>
      </div>
    `;

    sidebar.insertAdjacentHTML('beforeend', scheduleHTML);
  });
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

function generateMonthView(month, year, eventsData = events) {
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
      if (
        event.target === dayCell ||
        event.target.classList.contains("day-number")
      ) {
        const year = parseInt(dayCell.dataset.year);
        const month = parseInt(dayCell.dataset.month);
        const day = parseInt(dayCell.dataset.day);
        const newSelectedDate = new Date(year, month, day);
        const formattedDate = `${newSelectedDate.getFullYear()}-${String(
          newSelectedDate.getMonth() + 1
        ).padStart(2, "0")}-${String(newSelectedDate.getDate()).padStart(2, "0")}`;

        document.getElementById("scheduleDate").value = formattedDate;
        selectedDate = formattedDate;
        showNewScheduleForm();
      }
    });

    const dayNumber = document.createElement("div");
    dayNumber.classList.add("day-number");
    dayNumber.textContent = i;
    dayCell.appendChild(dayNumber);

    const eventsOnThisDay = getEventsForDate(new Date(year, month, i), eventsData);
    eventsOnThisDay.forEach((event) => {
      if (!event || !event.title) return;

      const eventDiv = document.createElement("div");
      eventDiv.classList.add("event", event.type);
      eventDiv.style.cursor = "pointer";
      eventDiv.dataset.year = year;
      eventDiv.dataset.month = month;
      eventDiv.dataset.day = i;

      eventDiv.innerHTML = `
        <div class="event-title">${event.title}</div>
        <div class="event-time">${event.time}</div>
        <div class="event-actions">
          <button class="edit-event" data-id="${event.id}">✏️</button>
          <button class="delete-event" data-id="${event.id}">🗑️</button>
        </div>
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

  // ✅ Activate edit/delete buttons after DOM is updated
  setTimeout(() => {
    document.querySelectorAll(".edit-event").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const id = btn.dataset.id;
        const event = events.find((ev) => ev.id == id);
        if (event) openEditForm(event);
      });
    });

    document.querySelectorAll(".delete-event").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const id = btn.dataset.id;
        if (confirm("Are you sure you want to delete this event?")) {
          deleteEventById(id);
        }
      });
    });
  }, 0);
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

function generateWeekView(date, eventsData = events) {
  calendarHeaderTitle.innerHTML = `Week of ${date.toDateString()}`;
  weekHeader.textContent = "";
  calendarGrid.innerHTML = "";
  calendarGrid.classList.add("week-view");
  calendarGrid.classList.remove("day-view", "month-view", "year-view");

  const startOfWeek = new Date(date);
  startOfWeek.setDate(date.getDate() - date.getDay()); // Sunday

  for (let i = 0; i < 7; i++) {
    const currentDate = new Date(startOfWeek);
    currentDate.setDate(startOfWeek.getDate() + i);

    const dayColumn = document.createElement("div");
    dayColumn.classList.add("week-day-column");

    const header = document.createElement("div");
    header.classList.add("week-day-header");
    header.innerText = currentDate.toDateString();
    dayColumn.appendChild(header);

    const eventsForDay = getEventsForDate(currentDate, eventsData);

    eventsForDay.forEach(event => {
      const eventDiv = document.createElement("div");
      eventDiv.classList.add("event", event.type);
      eventDiv.innerHTML = `
        <div>${event.title}</div>
        <div class="event-time">${event.time}</div>
      `;
      dayColumn.appendChild(eventDiv);
    });

    calendarGrid.appendChild(dayColumn);
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

function generateYearView(year, eventsData = events) {
  calendarHeaderTitle.innerHTML = `${year}`;
  weekHeader.textContent = "";
  calendarGrid.innerHTML = "";
  calendarGrid.classList.add("year-view");
  calendarGrid.classList.remove("day-view", "week-view", "month-view");

  for (let month = 0; month < 12; month++) {
    const monthContainer = document.createElement("div");
    monthContainer.classList.add("month-container");
    generateMiniMonth(month, year, monthContainer, eventsData); // ✅ pass it here
    calendarGrid.appendChild(monthContainer);
  }
}

function generateMiniMonth(month, year, container, eventsData = events) {
  const monthName = months[month];
  const title = document.createElement("div");
  title.classList.add("mini-month-title");
  title.textContent = monthName;
  container.appendChild(title);

  const daysGrid = document.createElement("div");
  daysGrid.classList.add("mini-month-grid");

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  for (let i = 0; i < firstDay; i++) {
    const empty = document.createElement("div");
    empty.classList.add("mini-day", "empty");
    daysGrid.appendChild(empty);
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const cell = document.createElement("div");
    cell.classList.add("mini-day");
    const cellDate = new Date(year, month, day);
    const eventsOnDay = getEventsForDate(cellDate, eventsData);
    if (eventsOnDay.length) cell.classList.add("has-event");
    cell.textContent = day;
    daysGrid.appendChild(cell);
  }

  container.appendChild(daysGrid);
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

function getEventsForDate(date, data = events) {
  return data.filter((event) => {
    const eventDate = new Date(event.date);
    return (
      eventDate.getFullYear() === date.getFullYear() &&
      eventDate.getMonth() === date.getMonth() &&
      eventDate.getDate() === date.getDate()
    );
  });
}

function getEventsForDateTime(date, data = events) {
  return getEventsForDate(date, data);
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

function updateCalendarView(view, data = events) {
  currentView = view;

  // Update active button styles
  viewButtons.forEach((btn) => btn.classList.remove("active"));
  const activeBtn = document.querySelector(`.view-btn[data-view="${view}"]`);
  if (activeBtn) activeBtn.classList.add("active");

  // Load the view with filtered data
  if (currentView === "month") {
    generateMonthView(currentMonth, currentYear, data);  // pass data
    monthSelector.value = currentMonth;
    yearSelector.value = currentYear;
  } else if (currentView === "week") {
    generateWeekView(currentDate, data);
  } else if (currentView === "day") {
    generateDayView(currentDate, data);
  } else if (currentView === "year") {
    generateYearView(currentYear, data);
    yearSelector.value = currentYear;
  }
}


function generateDayView(date, eventsData = events) {
  calendarHeaderTitle.innerHTML = `${date.toDateString()}`;
  weekHeader.textContent = "";
  calendarGrid.classList.add("day-view");
  calendarGrid.classList.remove("year-view", "week-view", "month-view");
  calendarGrid.innerHTML = `<div class="calendar-day-header">Time</div><div class="day-view-column"></div>`;
  const dayViewColumn = calendarGrid.querySelector(".day-view-column");

  const eventsOnThisDay = getEventsForDateTime(date, eventsData); // ✅ now passes filtered data

  for (let hour = 0; hour < 24; hour++) {
    const timeSlotContainer = document.createElement("div");
    timeSlotContainer.classList.add("time-slot-container");

    const timeLabel = document.createElement("div");
    timeLabel.classList.add("time-slot-label");
    timeLabel.textContent = `${String(hour).padStart(2, "0")}:00`;
    timeSlotContainer.appendChild(timeLabel);

    const eventArea = document.createElement("div");
    eventArea.classList.add("event-area");
    timeSlotContainer.appendChild(eventArea);

    const eventsInThisHour = eventsOnThisDay.filter((event) => {
      const eventStartHour = parseInt(event.startTime.split(":")[0]);
      const eventEndHour = event.endTime
        ? parseInt(event.endTime.split(":")[0])
        : eventStartHour + 1;
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

// createScheduleBtn.addEventListener("click", (e) => {
//   e.preventDefault();
//   try {
//     const timeParts = timeInput.value.split(" - ");
//     const startTime = timeParts[0] ? timeParts[0].trim() : timeInput.value.trim();
//     const endTime = timeParts[1] ? timeParts[1].trim() : endTimeInput.value;
//     const newEvent = {
//       title: titleInput.value,
//       type: categoryInput.value,
//       date: dateInput.value,
//       time: timeInput.value,
//       startTime: startTime,
//       endTime: endTime,
//       team: teamInput.value.split(",").map((item) => item.trim()),
//       venue: venueInput.value,
//       notes: notesInput.value,
//       people: teamInput.value.split(",").map((item) => item.trim()),
//     };
//     events.push(newEvent);
//     updateCalendarView(currentView);
//     hideNewScheduleForm();
//     resetForm();
//     alert("New schedule created successfully!");
//   } catch (error) {
//     console.error("Error creating schedule:", error);
//     alert("Error saving schedule. Please try again.");
//   }
// });
createScheduleBtn.addEventListener("click", (e) => {
  e.preventDefault();
  console.log("Form submitted, processing new event...");
  try {
    console.log("Input values:", {
      title: titleInput.value,
      date: dateInput.value,
      time: timeInput.value,
    });
    const timeParts = timeInput.value.split(" - ");
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
    console.log("New event created:", newEvent);
    events.push(newEvent);
    console.log("Events array updated:", events);
    updateCalendarView(currentView);
    hideNewScheduleForm();
    resetForm();
    alert("New schedule created successfully!");
  } catch (error) {
    console.error("Error in createScheduleBtn:", error);
    alert("Error saving schedule. Please try again.");
  }
});

function getCategoryClass(slug) {
  return slug || "new-dish"; // fallback to default if missing
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

function fetchSchedulesFromServer() {
  console.log("Fetching schedules from API...");

  fetch(API_URL)
    .then(response => response.json())
    .then(data => {
      console.log("API data received:", data);
      events = data.map(event => ({
        
  id: event.id, // ✅ Add this
  ...event,
  type: getCategoryClass(event.category_slug),
  category: event.category_name,
  time: `${event.start_time} - ${event.end_time}`,
  startTime: event.start_time,
  endTime: event.end_time,
  people: JSON.parse(event.team || "[]"),
  venue: event.venue,
  notes: event.notes,
  date: event.date,
}));
console.log("Event IDs:", events.map(e => e.id));

      updateCalendarView(currentView);
      renderSidebar(events);

      updateFilterCounts(events); // ✅ Add this line!
    })
    .catch(error => {
      console.error("Failed to fetch schedules:", error);
      alert("Failed to load schedules from server.");
    });
}


document.addEventListener("DOMContentLoaded", function () {
function renderSidebar(events) {
  const sidebar = document.querySelector('.sidebar');
  if (!sidebar) {
    console.warn('Sidebar element not found.');
    return;
  }

  console.log("Rendering events:", events);

  sidebar.innerHTML = '<h2>Schedule Details</h2>';

  if (!events.length) {
    sidebar.innerHTML += '<div class="empty-state">No schedules found.</div>';
    return;
  }

  events.forEach(event => {
    const teamList = (event.people || []).map(name => `
      <div class="team-member">
        <div class="avatar">${name}</div>
        ${name}
      </div>`).join('');

    const scheduleHTML = `
      <div class="schedule-item">
        <div class="schedule-title">${event.title}</div>
       <div class="menu-updates-tag ${event.type}">${event.category}</div>
        <div class="schedule-details-row">📅 ${formatDate(event.date)}</div>
        <div class="schedule-details-row">🕒 ${event.startTime} - ${event.endTime}</div>
        <div class="schedule-details-row">📍 ${event.venue || "No venue"}</div>
        <div class="team-section">
          <div class="team-title">Team</div>
          <div class="team-members">${teamList}</div>
        </div>
        <div class="notes-section">
          <div class="notes-title">Notes</div>
          <div class="notes-content">${event.notes || "No notes available"}</div>
        </div>
      </div>
    `;

    sidebar.insertAdjacentHTML('beforeend', scheduleHTML);
  });
}

});




document.addEventListener("DOMContentLoaded", () => {
  const filterTags = document.querySelectorAll(".filter-tag");

  filterTags.forEach(tag => {
    tag.addEventListener("click", () => {
      const selectedCategory = tag.dataset.category;

      // Highlight selected
      filterTags.forEach(t => t.classList.remove("active"));
      tag.classList.add("active");

      // Filter and update
      const filteredEvents = events.filter(e => getCategoryClass(e.category_slug) === selectedCategory);
      updateCalendarView(currentView, filteredEvents); // pass filtered
      renderSidebar(filteredEvents);                   // update sidebar
    });
  });
});



function updateFilterCounts(eventsData = events) {
  const countMap = {};

  // Count events by category slug (via getCategoryClass)
  eventsData.forEach(event => {
    const categorySlug = getCategoryClass(event.category_slug); // normalize
    countMap[categorySlug] = (countMap[categorySlug] || 0) + 1;
  });

  // Update each filter-tag's .filter-count
  const filterTags = document.querySelectorAll(".filter-tag");
  filterTags.forEach(tag => {
    const category = tag.dataset.category;
    const countSpan = tag.querySelector(".filter-count");
    const count = countMap[category] || 0;
    if (countSpan) {
      countSpan.textContent = count;
    }
  });
}




function deleteEventById(id) {
  fetch(API_URL, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `id=${encodeURIComponent(id)}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        // ✅ Remove event from array
        events = events.filter(ev => ev.id != id);
        // ✅ Update calendar and sidebar
        // updateCalendarView(currentView);
        // renderSidebar(events);

        fetchSchedulesFromServer();
        updateFilterCounts(events);
        alert("Event deleted successfully.");
      } else {
        console.error("Delete failed:", data.message);
        alert("Failed to delete event.");
      }
    })
    .catch(err => {
      console.error("Delete error:", err);
      alert("Something went wrong while deleting the event.");
    });
}

// function openEditForm(event) {
//   showNewScheduleForm(); // show the form

//   document.getElementById("scheduleId").value = event.id || '';
//   document.getElementById("scheduleTitle").value = event.title || '';
//   document.getElementById("scheduleDate").value = event.date || '';
//   document.getElementById("scheduleTime").value = event.startTime || '';
//   document.getElementById("scheduleEndTime").value = event.endTime || '';
//   document.getElementById("scheduleVenue").value = event.venue || '';
//   document.getElementById("scheduleNotes").value = event.notes || '';
//   document.getElementById("scheduleTeam").value = (event.people || []).join(', ');

//   if (document.getElementById("scheduleCategory")) {
//     document.getElementById("scheduleCategory").value = event.category_id || '';
//   }

//   document.getElementById("createScheduleBtn").textContent = "Update";
// }



// function saveSchedule() {
//   const id = document.getElementById("scheduleId").value;
//   const title = document.getElementById("scheduleTitle").value;
//   //const category_id = document.getElementById("scheduleCategory").value;

//   const category_id = parseInt(document.getElementById("scheduleCategory").value, 10);

//   const date = document.getElementById("scheduleDate").value;
//   const startTime = document.getElementById("scheduleTime").value;
//   const endTime = document.getElementById("scheduleEndTime").value;
//   const team = document.getElementById("scheduleTeam").value;
//   const venue = document.getElementById("scheduleVenue").value;
//   const notes = document.getElementById("scheduleNotes").value;

//   if (!title || !category_id || !date || !startTime || !endTime) {
//     alert("Please fill in all required fields.");
//     return;
//   }

// const payload = {
//   id: id || 0,
//   title,
//   category_id: parseInt(category_id, 10), // ✅ fix here
//   date,
//   startTime,
//   endTime,
//   venue,
//   notes,
//   team: team.split(",").map(t => t.trim())
// };



// console.log("Payload being sent:", payload);

//   fetch(API_URL, {
//     method: "POST",
//     headers: { "Content-Type": "application/json" },
//     body: JSON.stringify(payload)
//   })
//     //.then(res => res.json())
//     .then(async (res) => {
//   const text = await res.text();
//   try {
//     const json = JSON.parse(text);
//     return json;
//   } catch (e) {
//     console.error("❌ Invalid JSON from server:", text);
//     throw new Error("Invalid response. Fix your PHP output.");
//   }
// })
//     .then(data => {
//       if (data.status === "success") {
//         alert(id ? "Schedule updated!" : "Schedule created!");
//         fetchSchedulesFromServer(); // refresh UI
//         hideNewScheduleForm();
//         resetForm();
//       } else {
//         alert("Save failed: " + data.message);
//       }
//     })
//     .catch(err => {
//        console.error("Error saving:", err); 
   
//       alert("Something went wrong while saving.");
//     });
// }

function openEditForm(event) {
  showNewScheduleForm();
  document.getElementById("scheduleId").value = event.id || '';
  document.getElementById("scheduleTitle").value = event.title || '';
  document.getElementById("scheduleDate").value = event.date || '';
  document.getElementById("scheduleTime").value = event.startTime || '';
  document.getElementById("scheduleEndTime").value = event.endTime || '';
  document.getElementById("scheduleVenue").value = event.venue || '';
  document.getElementById("scheduleNotes").value = event.notes || '';
  document.getElementById("scheduleTeam").value = (event.people || []).join(', ');
  const categorySelect = document.getElementById("scheduleCategory");
  if (categorySelect) {
    categorySelect.value = event.category_id || event.category || ''; // Use category_id from event
  }
  document.getElementById("createScheduleBtn").textContent = "Update";
}



function saveSchedule() {
  const id = document.getElementById("scheduleId").value;
  const title = document.getElementById("scheduleTitle").value;
  const category_id = parseInt(document.getElementById("scheduleCategory").value, 10);
  const date = document.getElementById("scheduleDate").value;
  const startTime = document.getElementById("scheduleTime").value;
  const endTime = document.getElementById("scheduleEndTime").value;
  const team = document.getElementById("scheduleTeam").value;
  const venue = document.getElementById("scheduleVenue").value;
  const notes = document.getElementById("scheduleNotes").value;

  if (!title || !category_id || !date || !startTime || !endTime) {
    alert("Please fill in all required fields.");
    return;
  }

  const payload = {
    id: id ? parseInt(id, 10) : 0,
    title,
    category_id,
    date,
    startTime,
    endTime,
    venue,
    notes,
    team: team ? team.split(",").map(t => t.trim()) : []
  };

  console.log("Payload being sent:", payload);
  console.log("Request URL:", API_URL);

  fetch(API_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  })
    .then(res => {
      console.log("Response received:", {
        status: res.status,
        ok: res.ok,
        url: res.url
      });
      if (!res.ok) {
        throw new Error(`HTTP error! Status: ${res.status} ${res.statusText}`);
      }
      return res.text();
    })
    .then(text => {
      console.log("Raw response:", text);
      if (!text) {
        throw new Error("Empty response from server");
      }
      try {
        const data = JSON.parse(text);
        console.log("Parsed JSON:", data);
        return data;
      } catch (e) {
        console.error("JSON parse error:", e.message, "Raw response:", text);
        throw new Error(`Invalid JSON response: ${text}`);
      }
    })
    .then(data => {
      console.log("Data received:", data);
      if (typeof data !== "object" || data === null) {
        throw new Error("Response is not a valid JSON object");
      }
      if (data.status === "success") {
        alert(id ? "Schedule updated successfully!" : "Schedule created successfully!");
        fetchSchedulesFromServer();
        hideNewScheduleForm();
        resetForm();
      } else {
        alert("Save failed: " + (data.message || "Unknown error"));
      }
    })
    .catch(err => {
      console.error("Fetch error:", err);
      alert("Request failed: " + err.message);
    });
}
// ✅ Reset form function – keep this
// function resetForm() {
//   const form = document.getElementById("newSchedule");
//   if (form) form.reset();
//   document.getElementById("scheduleId").value = "";
//   document.getElementById("createScheduleBtn").textContent = "Create";
// }


function resetForm() {
  document.getElementById("scheduleId").value = "";
  document.getElementById("scheduleTitle").value = "";
  document.getElementById("scheduleCategory").value = "";
  document.getElementById("scheduleDate").value = "";
  document.getElementById("scheduleTime").value = "12:30";
  document.getElementById("scheduleEndTime").value = "";
  document.getElementById("scheduleTeam").value = "";
  document.getElementById("scheduleVenue").value = "";
  document.getElementById("scheduleNotes").value = "";
  document.getElementById("createScheduleBtn").textContent = "Create";
}






// ✅ Only ONE DOMContentLoaded block below – delete the others
document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ DOM fully loaded 🚀");

  // 🔄 Safely rebind the createSchedule button (prevents duplicate submit)
  const oldCreateBtn = document.getElementById("createScheduleBtn");
  if (oldCreateBtn) {
    const newCreateBtn = oldCreateBtn.cloneNode(true);
    oldCreateBtn.parentNode.replaceChild(newCreateBtn, oldCreateBtn);

    newCreateBtn.addEventListener("click", (e) => {
      e.preventDefault();
      saveSchedule();
    });
  }

  // 🚀 Load schedules from backend
  fetchSchedulesFromServer();

  // 🏷️ Setup filter tag click handlers
  const filterTags = document.querySelectorAll(".filter-tag");
  filterTags.forEach(tag => {
    tag.addEventListener("click", () => {
      const selectedCategory = tag.dataset.category;

      // Toggle active filter styling
      filterTags.forEach(t => t.classList.remove("active"));
      tag.classList.add("active");

      // Filter the events
      const filteredEvents = events.filter(e =>
        getCategoryClass(e.category_slug) === selectedCategory
      );
      updateCalendarView(currentView, filteredEvents);
      renderSidebar(filteredEvents);
    });
  });
});

