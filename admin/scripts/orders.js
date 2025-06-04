document.addEventListener("DOMContentLoaded", function () {
  // Get all chart contexts first
  const revenueCtx = document.getElementById("overviewChart").getContext("2d");

  new Chart(revenueCtx, {
    type: "line",
    data: {
      labels: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
      datasets: [
        {
          label: "Income",
          data: [210, 200, 140, 301, 259, 230, 350, 380],
          borderColor: "#ff6600",
          backgroundColor: "rgba(255, 102, 0, 0.1)",
          tension: 0.4,
          pointRadius: 0,
          pointHoverRadius: 6,
          pointBackgroundColor: "#ff6600",
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
          display: false,
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
            callback: (value) => value,
          },
        },
      },
    },
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // Get the chart context
  const categoryCtx = document
    .getElementById("order_typeChart")
    .getContext("2d");
  // Chart data
  const chartData = [75, 60, 65];

  // Calculate total
  const totalValue = chartData.reduce((sum, value) => sum + value, 0);

  // Update the center text with the total
  document.getElementById("totalValue").textContent = totalValue;

  // Define the plugin correctly
  const verticalLineLegendPlugin = {
    id: "verticalLineLegend",
    afterRender: function (chart) {
      const legendContainer = document.getElementById("chart-legend");
      if (!legendContainer) return;

      const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);

      legendContainer.innerHTML = chart.data.labels
        .map((label, i) => {
          const color = chart.data.datasets[0].backgroundColor[i];
          const value = chart.data.datasets[0].data[i];
          const percentage = Math.round(value);

          return `
                <div class="legend-item">
                  <span class="legend-line" style="background: ${color};"></span>
                  <div class="legend-text">
                    <span class="legend-label">${label}</span>
                    <span class="legend-value">${percentage}</span>
                  </div>
                </div>
              `;
        })
        .join("");
    },
  };

  // Register the plugin globally
  Chart.register(verticalLineLegendPlugin);

  // Create the doughnut chart
  new Chart(categoryCtx, {
    type: "doughnut",
    data: {
      labels: ["Dine-In", "Takeaway", "Order"],
      datasets: [
        {
          data: chartData,
          backgroundColor: ["#FF6C1F", "#FDCEA2", "#000000"],
          borderWidth: 0,
          hoverOffset: 5,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false, // Disable default legend
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((context.raw / total) * 100);
              return `${context.label}: ${percentage}%`;
            },
          },
        },
      },
      cutout: "65%",
    },
  });
});
