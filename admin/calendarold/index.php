<?php


require_once __DIR__ . '/../../BackEnd/config/init.php';
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login_page.php');
//     exit;
//}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Overview</title>
    <link rel="stylesheet" href="../assets/styles/general.css" />
    <link rel="stylesheet" href="../assets/styles/panels.css" />
    <link rel="stylesheet" href="../assets/styles/calendar.css" />
  </head>
  <body class="flex">
    <main>
      <div class="content">
        <div class="container" id="container">
          <!-- Schedule Details View -->
          <div class="schedule-details" id="scheduleDetails">
            <div class="sidebar">
              <h2>Schedule Details</h2>

              <div class="schedule-item">
                <div class="schedule-title">Weekly Specials Review</div>
                <div class="menu-updates-tag">Menu Updates</div>

                <div class="schedule-details-row">
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M8 2V5"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M16 2V5"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M3.5 9.09H20.5"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                  Apr 7, 2025
                </div>

                <div class="schedule-details-row">
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M12 8V13"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M12 22C7.17 22 3.25 18.08 3.25 13.25C3.25 8.42 7.17 4.5 12 4.5C16.83 4.5 20.75 8.42 20.75 13.25"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M9 2H15"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                  3:00 PM - 4:00 PM
                </div>

                <div class="schedule-details-row">
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M12 12V15"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M7.5 10.5H16.5"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M7 18V7.8C7 6.11984 7 5.27976 7.32698 4.63803C7.6146 4.07354 8.07354 3.6146 8.63803 3.32698C9.27976 3 10.1198 3 11.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V12.2C21 13.8802 21 14.7202 20.673 15.362C20.3854 15.9265 19.9265 16.3854 19.362 16.673C18.7202 17 17.8802 17 16.2 17H7Z"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M7 17V19.5C7 20.6046 6.10457 21.5 5 21.5V21.5C3.89543 21.5 3 20.6046 3 19.5V17H7Z"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                  Kitchen
                </div>

                <div class="team-section">
                  <div class="team-title">Team</div>
                  <div class="team-members">
                    <div class="team-member">
                      <div class="avatar">HC</div>
                      Head Chef
                    </div>
                    <div class="team-member">
                      <div class="avatar">SC</div>
                      Sous Chef
                    </div>
                  </div>
                  <div class="menu-team">
                    <div class="avatar">MD</div>
                    Menu Development Team
                    <div class="team-count">+3</div>
                  </div>
                </div>

                <div class="notes-section">
                  <div class="notes-title">Notes</div>
                  <div class="notes-content">
                    Finalize weekly specials and update menu options for coming
                    weeks.
                  </div>
                </div>
              </div>

              <div class="schedule-item">
                <div class="schedule-title">Weekly Specials Review</div>
                <div class="menu-updates-tag">Menu Updates</div>

                <div class="schedule-details-row">
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M8 2V5"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M16 2V5"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M3.5 9.09H20.5"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                  Apr 7, 2025
                </div>

                <div class="schedule-details-row">
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M12 8V13"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M12 22C7.17 22 3.25 18.08 3.25 13.25C3.25 8.42 7.17 4.5 12 4.5C16.83 4.5 20.75 8.42 20.75 13.25"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M9 2H15"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-miterlimit="10"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                  3:00 PM - 4:00 PM
                </div>

                <div class="schedule-details-row">
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M12 12V15"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M7.5 10.5H16.5"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M7 18V7.8C7 6.11984 7 5.27976 7.32698 4.63803C7.6146 4.07354 8.07354 3.6146 8.63803 3.32698C9.27976 3 10.1198 3 11.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V12.2C21 13.8802 21 14.7202 20.673 15.362C20.3854 15.9265 19.9265 16.3854 19.362 16.673C18.7202 17 17.8802 17 16.2 17H7Z"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                    <path
                      d="M7 17V19.5C7 20.6046 6.10457 21.5 5 21.5V21.5C3.89543 21.5 3 20.6046 3 19.5V17H7Z"
                      stroke="currentColor"
                      stroke-width="1.5"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    />
                  </svg>
                  Kitchen
                </div>
              </div>
            </div>
            <div class="inner-content card">
              <div class="calendar-header">
                <div class="calendar-controls">
                  <div class="view-selector">
                    <select id="monthSelector">
                      <option value="0">January</option>
                      <option value="1">February</option>
                      <option value="2">March</option>
                      <option value="3">April</option>
                      <option value="4">May</option>
                      <option value="5">June</option>
                      <option value="6">July</option>
                      <option value="7">August</option>
                      <option value="8">September</option>
                      <option value="9">October</option>
                      <option value="10">November</option>
                      <option value="11">December</option>
                    </select>
                    <select id="yearSelector"></select>
                  </div>
                  <div class="view-buttons">
                    <button class="view-btn active" data-view="month">
                      Month
                    </button>
                    <button class="view-btn" data-view="week">Week</button>
                    <button class="view-btn" data-view="day">Day</button>
                    <button class="view-btn" data-view="year">Year</button>
                  </div>

                  <button id="addScheduleBtn" class="submit-btn">
                    + Add Schedule
                  </button>
                </div>
                <div class="calendar-filter">
                  <div class="filter-tag meetings">
                    <span>Meetings</span>
                    <span class="filter-count">6</span>
                  </div>
                  <div class="filter-tag menu">
                    <span>Menu Updates</span>
                    <span class="filter-count">4</span>
                  </div>
                  <div class="filter-tag inventory">
                    <span>Inventory Checks</span>
                    <span class="filter-count">5</span>
                  </div>
                  <div class="filter-tag events">
                    <span>Events</span>
                    <span class="filter-count">5</span>
                  </div>
                </div>
              </div>

              <div
                class="calendar-grid-header flex align-center justify-between"
              >
                <button class="navigate" id="prevViewBtn"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.9254 4.55806C13.1915 4.80214 13.1915 5.19786 12.9254 5.44194L8.4375 9.55806C8.17138 9.80214 8.17138 10.1979 8.4375 10.4419L12.9254 14.5581C13.1915 14.8021 13.1915 15.1979 12.9254 15.4419C12.6593 15.686 12.2278 15.686 11.9617 15.4419L7.47378 11.3258C6.67541 10.5936 6.67541 9.40641 7.47378 8.67418L11.9617 4.55806C12.2278 4.31398 12.6593 4.31398 12.9254 4.55806Z" fill="#1C1C1C"/></svg></button>
                <div class="calendar-day-header" id="calendarDayHeader">
                  <h1 id="calendarTitle">May 2025</h1>
                  <p class="week-header"></p>
                </div>
                <button class="navigate" id="nextViewBtn"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.07459 15.4419C6.80847 15.1979 6.80847 14.8021 7.07459 14.5581L11.5625 10.4419C11.8286 10.1979 11.8286 9.80214 11.5625 9.55806L7.07459 5.44194C6.80847 5.19786 6.80847 4.80214 7.07459 4.55806C7.34072 4.31398 7.77219 4.31398 8.03831 4.55806L12.5262 8.67418C13.3246 9.40641 13.3246 10.5936 12.5262 11.3258L8.03831 15.4419C7.77219 15.686 7.34072 15.686 7.07459 15.4419Z" fill="#1C1C1C"/></svg></button>
              </div>

              <div class="calendar-grid" id="calendarGrid"></div>
            </div>
          </div>

          <!-- New Schedule Form -->
          <div class="new-schedule card" id="newSchedule">
            <div class="form-header">
              <h2 class="form-title">New Schedule</h2>
              <button class="close-btn" id="closeNewSchedule">&times;</button>
            </div>

            <div class="form-group">
              <label class="form-label">Title</label>
              <input type="text" class="form-control" id="scheduleTitle" />
            </div>

            <div class="form-group">
              <label class="form-label">Category</label>
              <input type="text" class="form-control" id="scheduleCategory" />
            </div>

            <div class="form-group">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" id="scheduleDate" />
            </div>

            <div class="form-group">
              <label class="form-label">Time</label>
              <input
                type="time"
                class="form-control"
                id="scheduleTime"
                value="12:30"
              />
              <input
                type="time"
                class="form-control"
                id="scheduleEndTime"
                value=""
              />
            </div>

            <div class="form-group">
              <label class="form-label">Team</label>
              <input type="text" class="form-control" id="scheduleTeam" />
            </div>

            <div class="form-group">
              <label class="form-label">Venue</label>
              <input type="text" class="form-control" id="scheduleVenue" />
            </div>

            <div class="form-group">
              <label class="form-label">Notes</label>
              <textarea
                class="form-control"
                id="scheduleNotes"
                rows="3"
              ></textarea>
            </div>

            <button class="submit-btn" id="createScheduleBtn">Create</button>
          </div>
        </div>
      </div>
    </main>
    <script src="../scripts/components.js"></script>
    <script src="../scripts/calendar.js"></script>
  </body>
</html>
