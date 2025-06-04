// Wait for DOM to load
document.addEventListener("DOMContentLoaded", function () {
  // Get all chart contexts first
  const revenueCtx = document.getElementById("revenueChart").getContext("2d");
  const categoryCtx = document.getElementById("categoryChart").getContext("2d");
  const ordersCtx = document.getElementById("ordersChart").getContext("2d");

  // Global configuration for horizontal-only gridlines
  Chart.defaults.scale.grid = {
    drawOnChartArea: true, // Allow gridlines on the chart
    drawTicks: false, // Hide tick-mark lines
    color: "rgba(0, 0, 0, 0.1)", // Light gray gridlines
    borderDash: [3, 3], // Dashed lines (optional)
    lineWidth: 1, // Thin lines
  };

  // Disable vertical gridlines specifically for all charts
  Chart.defaults.scales.category.grid = {
    display: false, // This kills vertical gridlines for category axes (x-axis)
  };
  Chart.defaults.plugins.legend.labels.usePointStyle = true;
  Chart.defaults.plugins.legend.labels.pointStyle = "rectRounded";

  new Chart(revenueCtx, {
    type: "line",
    data: {
      labels: ["Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
      datasets: [
        {
          label: "Income",
          data: [11000, 10800, 11500, 10200, 16300, 12000, 14500, 17500],
          borderColor: "#ff6600",
          backgroundColor: "rgba(255, 102, 0, 0.1)",
          tension: 0.4,
          pointRadius: 0, // Default state - no points visible
          pointHoverRadius: 6, // Point appears on hover
          pointBackgroundColor: "#ff6600",
          fill: false,
        },
        {
          label: "Expense",
          data: [5400, 5200, 5800, 5000, 6200, 5100, 6300, 7500],
          borderColor: "#000000",
          backgroundColor: "rgba(0, 0, 0, 0.1)",
          tension: 0.4,
          pointRadius: 0, // Default state - no points visible
          pointHoverRadius: 6, // Point appears on hover
          pointBackgroundColor: "#000000",
          fill: false,
        },
      ],
    },
    options: {
      responsive: true,
      interaction: {
        intersect: false,
        mode: "index",
      },
      plugins: {
        legend: {
          display: true,
          position: "top",
          align: "end",
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              return `${context.dataset.label}: ${context.formattedValue}`;
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => `${value / 1000}K`,
          },
        },
      },
    },
  });

  new Chart(categoryCtx, {
    type: "doughnut",
    data: {
      labels: ["Seafood", "Seafood", "Seafood", "Seafood"],
      datasets: [
        {
          data: [30, 25, 25, 20],
          backgroundColor: ["#FF6600", "#FFE8D0", "#000000", "#444444"],
          borderWidth: 0,
          hoverOffset: 10,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            generateLabels: (chart) => {
              const total = chart.data.datasets[0].data.reduce(
                (a, b) => a + b,
                0
              );
              return chart.data.labels.map((label, i) => ({
                text: `${label}: ${(
                  (chart.data.datasets[0].data[i] / total) *
                  100
                ).toFixed(1)}%`,
                fillStyle: chart.data.datasets[0].backgroundColor[i],
                hidden: false,
                lineWidth: 0,
                pointStyle: "rectRounded",
              }));
            },
          },
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              return `${context.label}: ${context.formattedValue}%`;
            },
          },
        },
      },
      cutout: "65%",
    },
  });

  const orders = [130, 125, 160, 185, 150, 135, 140]; // Data
  const highlightIndex = 3; // Thursday

  const backgroundColors = orders.map((_, i) =>
    i === highlightIndex ? "#FF6600" : "#FFE8D0"
  );

  new Chart(ordersCtx, {
    type: "bar",
    data: {
      labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
      datasets: [
        {
          label: "Orders",
          data: orders,
          backgroundColor: backgroundColors,
          borderRadius: 6,
          barThickness: 30,
        },
      ],
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          max: 200,
          ticks: {
            stepSize: 50,
          },
          grid: {
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
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.raw} orders`,
            title: (ctx) =>
              ctx[0].label === "Thu" ? "Thursday" : ctx[0].label,
          },
          backgroundColor: "#000",
          titleColor: "#fff",
          bodyColor: "#fff",
          bodyFont: {
            weight: "bold",
          },
        },
      },
    },
  });
});
