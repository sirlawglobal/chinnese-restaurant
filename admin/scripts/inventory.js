document.addEventListener("DOMContentLoaded", function () {
  const supplyCtx = document.getElementById("supplyChart").getContext("2d");

  const supplyData = {
    labels: ["Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
    datasets: [
      {
        label: "Products",
        data: [180, 210, 190, 160, 270, 200, 240, 220], // Example data - replace with your actual data
        fill: false,
        borderColor: "#FF8A65",
        tension: 0.4, // Adjust for curve smoothness
        pointRadius: 5,
        pointBackgroundColor: "#FFFFFF",
        pointBorderColor: "#FF8A65",
        pointBorderWidth: 2,
        pointHoverRadius: 7,
        pointHoverBackgroundColor: "#FFFFFF",
        pointHoverBorderColor: "#FF8A65",
        pointHoverBorderWidth: 2,
      },
    ],
  };

  const supplyChart = new Chart(supplyCtx, {
    type: "line",
    data: supplyData,
    options: {
      scales: {
        y: {
          beginAtZero: true,
          max: 320, // Based on the image
          ticks: {
            stepSize: 80, // Based on the image
            color: "#757575", // Light gray color for ticks
          },
          grid: {
            drawBorder: false,
            color: "#EEEEEE", // Very light gray grid lines
          },
        },
        x: {
          ticks: {
            color: "#757575", // Light gray color for ticks
          },
          grid: {
            display: false, // No vertical grid lines in the image
          },
        },
      },
      plugins: {
        legend: {
          display: false, // No legend in the image
        },
        tooltip: {
          backgroundColor: "rgba(0, 0, 0, 0.8)",
          titleColor: "#FFFFFF",
          bodyColor: "#FFFFFF",
          borderColor: "#FF8A65",
          borderWidth: 1,
          callbacks: {
            label: function (context) {
              return `${context.dataset.label}: ${context.formattedValue} Products`;
            },
          },
          intersect: false,
          position: "nearest",
          caretPadding: 5,
          xAlign: "center",
          yAlign: "bottom",
        },
      },
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        intersect: false,
        mode: "nearest",
      },
    },
  });
});
document.addEventListener("DOMContentLoaded", function () {
  const stockCtx = document.getElementById("stockChart").getContext("2d");
  const totalProductsElement = document.getElementById("totalProducts");
  const inStock1Element = document.getElementById("inStock1");
  const inStock2Element = document.getElementById("inStock2");
  const inStock3Element = document.getElementById("inStock3");

  const totalProducts = 205;
  const inStock1Count = 120;
  const inStock2Count = 55;
  const inStock3Count = 30;
  const numberOfBars = 30;

  const percentage1 = inStock1Count / totalProducts;
  const percentage2 = inStock2Count / totalProducts;
  const percentage3 = inStock3Count / totalProducts;

  const bars1 = Math.round(percentage1 * numberOfBars);
  const bars2 = Math.round(percentage2 * numberOfBars);
  const bars3 = numberOfBars - bars1 - bars2; // Ensure we have exactly 30 bars

  const stockData = {
    labels: Array.from({ length: numberOfBars }, (_, i) => `Bar ${i + 1}`),
    datasets: [
      {
        label: "Stock Status",
        data: Array(numberOfBars).fill(1),
        backgroundColor: [
          ...Array(bars1).fill("#FF8A65"),
          ...Array(bars2).fill("#FFD5C2"),
          ...Array(bars3).fill("#212121"),
        ],
        borderWidth: 0,
        barThickness: 15, // Adjust thickness as needed
        borderRadius: 10, // Add rounded corners
        categoryPercentage: 1,
        barPercentage: 1,
      },
    ],
  };

  const stockChart = new Chart(stockCtx, {
    type: "bar",
    data: stockData,
    options: {
      indexAxis: "x",
      scales: {
        x: {
          display: false,
          stacked: true,
        },
        y: {
          display: false,
          stacked: true,
        },
      },
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          enabled: false,
        },
      },
      responsive: true,
      maintainAspectRatio: false,
    },
  });

  totalProductsElement.textContent = totalProducts;
  inStock1Element.textContent = inStock1Count;
  inStock2Element.textContent = inStock2Count;
  inStock3Element.textContent = inStock3Count;
});

document.addEventListener("DOMContentLoaded", function () {
  const addProductBtn = document.getElementById("addProduct");
  const closeModalButton = document.getElementById("close-modal");
  const modalContainer = document.querySelector(".modal-container");

  addProductBtn.addEventListener("click", () => {
    modalContainer.classList.add("show")
  })

  if (closeModalButton && modalContainer) {
    closeModalButton.addEventListener("click", () => {
      modalContainer.classList.remove("show");
    });
  }

  const form = document.querySelector("form");
  // if (form) {
  //   form.addEventListener("submit", (event) => {
  //     event.preventDefault();
  //     alert("Product added (simulated)!");
  //     modalContainer.classList.remove("show");
  //   });
  // }
});

document.addEventListener("DOMContentLoaded", function () {
  
  const actionButtons = document.querySelectorAll(".action button");
  const modal = document.getElementById("action-modal");
  const modalTitle = modal.querySelector(".modal-title");
  const modalItemName = modal.querySelector("#modal-item-name");
  const modalForm = modal.querySelector("#modal-form");
  const modalCloseButton = document.getElementById("modal-close-button");

  let currentRow; // To keep track of the row being updated

  actionButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      currentRow = button.closest("tr");
      const itemName = currentRow.querySelector("td:nth-child(2)").textContent;
      const actionType = button.textContent;

      modalTitle.textContent = `${actionType} for ${itemName}`;
      modalItemName.value = itemName;

      // Position the modal below the button
      const buttonRect = button.getBoundingClientRect();
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      modal.style.top = buttonRect.bottom + scrollTop + 5 + "px";
      modal.style.left = (buttonRect.left - 230) + "px";
      modal.style.display = "block";
    });
  });

  modalCloseButton.addEventListener("click", () => {
    modal.style.display = "none";
  });

  modalForm.addEventListener("submit", (e) => {
    e.preventDefault();
    if (currentRow) {
      const itemName = currentRow.querySelector("td:nth-child(2)").textContent;
      const newStock = modal.querySelector("#modal-new-stock").value;
      alert(`Row for "${itemName}" updated with stock: ${newStock}`);
      modal.style.display = "none";
      // In a real application, you would update the table data here
    }
  });

  // Close modal when clicking outside
  window.addEventListener("click", (event) => {
    if (
      modal.style.display === "block" &&
      !modal.contains(event.target) &&
      !Array.from(actionButtons).includes(event.target)
    ) {
      modal.style.display = "none";
    }
  });

})