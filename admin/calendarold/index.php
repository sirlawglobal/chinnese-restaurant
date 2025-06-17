<?php 
require_once '../../BackEnd/config/init.php';
UserSession::requireLogin();
UserSession::requireRole(['admin','staff','super_admin']);
$first_name = UserSession::getFirstName();
$userRole = UserSession::get('role');
$profilePicture = UserSession::getProfilePicture();
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
    
  <style>
    .menu {
  background-color: #f5a623;
  color: #fff;
}
.inventory {
  background-color:rgb(141, 52, 168);
 color: #fff; 
}
.meetings {
  background-color: #4285f4;
  color: #fff;
}
.events {
  background-color: #fbbc05;
  color: #fff;
}

  </style>
  <main>
      <div class="content">
        <div class="container" id="container">
          <!-- Schedule Details View -->
          <div class="schedule-details" id="scheduleDetails">
        <div class="sidebar">
  <h2>Schedule Details</h2>
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
                <div class="filter-tag all active" data-category="all">
  <span>All</span>
</div>

  <div class="filter-tag meetings" data-category="meetings">
    <span>Meetings</span><span class="filter-count">6</span>
  </div>
  <div class="filter-tag menu" data-category="menu">
    <span>Menu Updates</span><span class="filter-count">4</span>
  </div>
  <div class="filter-tag inventory" data-category="inventory">
    <span>Inventory Checks</span><span class="filter-count">5</span>
  </div>
  <div class="filter-tag events" data-category="events">
    <span>Events</span><span class="filter-count">5</span>
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
            <input type="hidden" id="scheduleId">
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
<select id="scheduleCategory" class="form-control" require>
  <option value="">Select Category</option>
  <option value="1">Meetings</option>
  <option value="2">Menu Updates</option>
  <option value="3">Inventory Checks</option>
  <option value="4">Events</option>
</select>
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

    <script>
       const username = '<?php echo addslashes($first_name); ?>';
      const userRole = '<?php echo addslashes($userRole); ?>';
      const profilePicture = '<?php echo addslashes($profilePicture); ?>';
    </script>
    <script src="../scripts/components.js"></script>
    <script src="../scripts/calendar.js"></script>
  </body>
</html>
