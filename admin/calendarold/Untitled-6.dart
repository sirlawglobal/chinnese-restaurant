dayCell.addEventListener("click", (event) => {
  const year = parseInt(dayCell.dataset.year);
  const month = parseInt(dayCell.dataset.month);
  const day = parseInt(dayCell.dataset.day);
  const formattedDate = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

  const filtered = events.filter(e => e.event_date === formattedDate);

  const dateInput = document.getElementById("scheduleDate");
  if (dateInput) dateInput.value = formattedDate;

  selectedDate = formattedDate;

  if (filtered.length > 0) {
    renderSidebarSchedule(filtered);  // Display events in sidebar
  } else {
    showNewScheduleForm();  // Open the form if no events exist
  }
});
