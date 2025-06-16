document.addEventListener("DOMContentLoaded", function () {
  // === SUPPLY LINE CHART ===
  const supplyCtx = document.getElementById("supplyChart").getContext("2d");
  const weeklyTotalElement = document.getElementById("weeklyTotal");
  const dateRangeLabel = document.getElementById("dateRangeLabel");

  // Utility: Get current week range string
  function getCurrentWeekRange() {
    const today = new Date();
    const dayOfWeek = today.getDay(); // 0 (Sun) - 6 (Sat)
    const start = new Date(today);
    start.setDate(today.getDate() - dayOfWeek);
    const end = new Date(start);
    end.setDate(start.getDate() + 6);

    const options = { month: 'short', day: 'numeric' };
    return `${start.toLocaleDateString('en-US', options)} - ${end.toLocaleDateString('en-US', options)}`;
  }

  // Set the "This Week" label dynamically
  if (dateRangeLabel) {
    dateRangeLabel.childNodes[0].nodeValue = getCurrentWeekRange();
  }

  // Load supply line chart
  fetch("../../../BackEnd/controller/inventory/fetch_inventory.php?chart=supply")
    .then(response => response.json())
    .then(result => {
      console.log("Supply chart data loaded:", result);

      const totalProductsThisWeek = result.data.reduce((sum, val) => sum + val, 0);
      if (weeklyTotalElement) {
        weeklyTotalElement.textContent = totalProductsThisWeek.toLocaleString();
      }

      const supplyData = {
        labels: result.labels,
        datasets: [
          {
            label: "Products",
            data: result.data,
            fill: false,
            borderColor: "#FF8A65",
            tension: 0.4,
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

      new Chart(supplyCtx, {
        type: "line",
        data: supplyData,
        options: {
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 80,
                color: "#757575",
              },
              grid: {
                drawBorder: false,
                color: "#EEEEEE",
              },
            },
            x: {
              ticks: {
                color: "#757575",
              },
              grid: {
                display: false,
              },
            },
          },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: "rgba(0, 0, 0, 0.8)",
              titleColor: "#FFFFFF",
              bodyColor: "#FFFFFF",
              borderColor: "#FF8A65",
              borderWidth: 1,
              callbacks: {
                label: context => `${context.dataset.label}: ${context.formattedValue} Products`,
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
    })
    .catch(error => {
      console.error("Error loading supply chart data:", error);
    });

  // === STOCK BAR CHART ===
  const stockCtx = document.getElementById("stockChart").getContext("2d");
  const totalProductsElement = document.getElementById("totalProducts");
  const inStock1Element = document.getElementById("inStock1");
  const inStock2Element = document.getElementById("inStock2");
  const inStock3Element = document.getElementById("inStock3");

  fetch("../../../BackEnd/controller/inventory/fetch_inventory.php?chart=stock")
    .then(response => response.json())
    .then(data => {
      console.log("Stock chart data loaded:", data);

      const inStock1Count = data.available;
      const inStock2Count = data.low;
      const inStock3Count = data.out;
      const totalProducts = inStock1Count + inStock2Count + inStock3Count;

      const numberOfBars = 30;
      const percentage1 = totalProducts > 0 ? inStock1Count / totalProducts : 0;
      const percentage2 = totalProducts > 0 ? inStock2Count / totalProducts : 0;

      let bars1 = Math.round(percentage1 * numberOfBars);
      let bars2 = Math.round(percentage2 * numberOfBars);

      if (bars1 + bars2 > numberOfBars) {
        const scale = numberOfBars / (bars1 + bars2);
        bars1 = Math.floor(bars1 * scale);
        bars2 = Math.floor(bars2 * scale);
      }

      const bars3 = numberOfBars - bars1 - bars2;

      console.log({ bars1, bars2, bars3, totalProducts });

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
            barThickness: 15,
            borderRadius: 10,
            categoryPercentage: 1,
            barPercentage: 1,
          },
        ],
      };

      new Chart(stockCtx, {
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
            legend: { display: false },
            tooltip: { enabled: false },
          },
          responsive: true,
          maintainAspectRatio: false,
        },
      });

      totalProductsElement.textContent = totalProducts.toLocaleString();
      inStock1Element.textContent = inStock1Count.toLocaleString();
      inStock2Element.textContent = inStock2Count.toLocaleString();
      inStock3Element.textContent = inStock3Count.toLocaleString();
    })
    .catch(error => {
      console.error("Error loading stock chart data:", error);
    });
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