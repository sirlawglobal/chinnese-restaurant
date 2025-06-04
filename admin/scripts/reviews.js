document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("reviewChart").getContext("2d");
  const dropdownToggle = document.querySelector(
    ".filter-dropdown .dropdown-toggle"
  );
  const dropdownMenu = document.querySelector(
    ".filter-dropdown .dropdown-menu"
  );

  if (dropdownToggle && dropdownMenu) {
    dropdownToggle.addEventListener("click", function () {
      dropdownMenu.style.display =
        dropdownMenu.style.display === "block" ? "none" : "block";
    });

    window.addEventListener("click", function (event) {
      if (
        !event.target.matches(".dropdown-toggle") &&
        !event.target.closest(".dropdown-menu")
      ) {
        dropdownMenu.style.display = "none";
      }
    });
  }

  const data = {
    labels: [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ],
    datasets: [
      {
        label: "Positive Review",
        data: [115, 135, 125, 150, 160, 180, 155, 145, 174, 146, 152, 162],
        backgroundColor: "#ff8a65",
        barThickness: 20,
        categoryPercentage: 0.4,
        barPercentage: 0.8,
      },
      {
        label: "Negative Review",
        data: [75, 60, 65, 50, 40, 30, 35, 55, 73, 60, 70, 38],
        backgroundColor: "#212121",
        barThickness: 20,
        categoryPercentage: 0.4,
        barPercentage: 0.8,
      },
    ],
  };

  const chart = new Chart(ctx, {
    type: "bar",
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          max: 200,
          ticks: {
            stepSize: 50,
          },
          grid: {
            borderColor: "#ccc",
            borderDash: [2, 2],
            drawBorder: false,
          },
        },
        x: {
          grid: {
            display: false,
          },
        },
      },
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          mode: "index",
          intersect: false,
          callbacks: {
            title: (tooltipItems) => {
              return tooltipItems[0].label;
            },
            label: (tooltipItem) => {
              return `${tooltipItem.dataset.label}: ${tooltipItem.formattedValue}`;
            },
          },
          backgroundColor: "#ffffff",
          titleColor: "#000",
          bodyColor: "#000",
        },
        annotation: {
          annotations: [
            {
              type: "box",
              xScaleID: "x",
              yScaleID: "y",
              xMin: "Sep",
              xMax: "Sep",
              yMin: 0,
              yMax: 200,
              backgroundColor: "rgba(255, 138, 101, 0.2)",
              borderColor: "transparent",
            },
            {
              type: "label",
              xValue: "Sep",
              yValue: 174,
              xScaleID: "x",
              yScaleID: "y",
              content: "September 2025\nPositive: 174\nNegative: 73",
              backgroundColor: "rgba(0, 0, 0, 0.8)",
              color: "white",
              textAlign: "left",
              borderRadius: 5,
              padding: 8,
              font: {
                size: 10,
              },
              position: "top",
              yAdjust: -25,
            },
          ],
        },
      },
    },
  });
});
