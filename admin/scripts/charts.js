

document.addEventListener("DOMContentLoaded", async function () {
  // Get all chart contexts
  const revenueCtx = document.getElementById("revenueChart").getContext("2d");
  const categoryCtx = document.getElementById("categoryChart").getContext("2d");
  const ordersCtx = document.getElementById("ordersChart").getContext("2d");

  // Global chart configuration
  Chart.defaults.scale.grid = {
    drawOnChartArea: true,
    drawTicks: false,
    color: "rgba(0, 0, 0, 0.1)",
    borderDash: [3, 3],
    lineWidth: 1,
  };
  Chart.defaults.scales.category.grid = { display: false };
  Chart.defaults.plugins.legend.labels.usePointStyle = true;
  Chart.defaults.plugins.legend.labels.pointStyle = "rectRounded";

  // Fetch data from APIs
  try {
    // Fetch revenue data
    const revenueResponse = await fetch('../dashboard/get_revenue_data.php');
    const revenueData = await revenueResponse.json();
    
    // Fetch category data
    const categoryResponse = await fetch('../dashboard/get_category_data.php');
    const categoryData = await categoryResponse.json();
    
    // Fetch orders data
    const ordersResponse = await fetch('../dashboard/get_orders_data.php');
    const ordersData = await ordersResponse.json();

    // Initialize charts with dynamic data
    initRevenueChart(revenueCtx, revenueData);
    initCategoryChart(categoryCtx, categoryData);
    initOrdersChart(ordersCtx, ordersData);
  } catch (error) {
    console.error('Error loading chart data:', error);
  }
});

function initRevenueChart(ctx, data) {
  new Chart(ctx, {
    type: "line",
    data: {
      labels: data.labels.reverse(), // Reverse to show oldest to newest
      datasets: [
        {
          label: "Income",
          data: data.revenue,
          borderColor: "#ff6600",
          backgroundColor: "rgba(255, 102, 0, 0.1)",
          tension: 0.4,
          pointRadius: 0,
          pointHoverRadius: 6,
          pointBackgroundColor: "#ff6600",
          fill: true,
        },
        {
          label: "Expense",
          data: data.expense,
          borderColor: "#000000",
          backgroundColor: "rgba(0, 0, 0, 0.1)",
          tension: 0.4,
          pointRadius: 0,
          pointHoverRadius: 6,
          pointBackgroundColor: "#000000",
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      interaction: { intersect: false, mode: "index" },
      plugins: {
        legend: { display: true, position: "top", align: "end" },
        tooltip: {
          callbacks: {
            label: function (context) {
              return `${context.dataset.label}: $${context.raw.toFixed(2)}`;
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => `$${(value / 1000).toFixed(1)}K`,
          },
        },
      },
    },
  });
}

function initCategoryChart(ctx, data) {
  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: data.labels,
      datasets: [
        {
          data: data.data,
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
              const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
              return chart.data.labels.map((label, i) => ({
                text: `${label}: ${((chart.data.datasets[0].data[i] / total) * 100).toFixed(1)}%`,
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
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((context.raw / total) * 100).toFixed(1);
              return `${context.label}: ${percentage}% (${context.raw} items)`;
            },
          },
        },
      },
      cutout: "65%",
    },
  });
}

function initOrdersChart(ctx, data) {
  // Find index of current day to highlight
  const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  const today = new Date().getDay(); // 0 (Sunday) to 6 (Saturday)
  const highlightIndex = today;
  
  const backgroundColors = data.data.map((_, i) => 
    i === highlightIndex ? "#FF6600" : "#FFE8D0"
  );

  // Shorten day names for display
  const shortLabels = data.labels.map(label => label.substring(0, 3));

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: shortLabels,
      datasets: [
        {
          label: "Orders",
          data: data.data,
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
          ticks: { stepSize: Math.ceil(Math.max(...data.data) / 5) },
          grid: { drawBorder: false },
        },
        x: { grid: { display: false } },
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.raw} orders`,
            title: (ctx) => data.labels[ctx.dataIndex],
          },
          backgroundColor: "#000",
          titleColor: "#fff",
          bodyColor: "#fff",
          bodyFont: { weight: "bold" },
        },
      },
    },
  });
}