document.getElementById("paymentForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const amount = parseFloat(formData.get("amount"));

  if (amount > 50000) {
    document.getElementById("formStatus").innerHTML = "<p class='text-danger'>❌ You can't pay more than ₹50,000!</p>";
    return;
  }

  fetch("php/process_payment.php", {
    method: "POST",
    body: formData,
  })
    .then(res => res.text())
    .then(data => {
      document.getElementById("formStatus").innerHTML = data;
      this.reset();
      loadTransactions();
      loadSummary();
      loadCharts(); // Refresh analytics
    });
});

function loadTransactions() {
  fetch("php/transactions.php")
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById("transactionsTable");
      tbody.innerHTML = "";

      data.forEach((tx, i) => {
        const row = `
          <tr>
            <td>${i + 1}</td>
            <td>${tx.card_holder}</td>
            <td>${tx.card_number}</td>
            <td>₹${parseFloat(tx.amount).toFixed(2)}</td>
            <td>${tx.timestamp}</td>
          </tr>
        `;
        tbody.innerHTML += row;
      });
    });
}

function loadSummary() {
  fetch("php/summary.php")
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        document.getElementById("available-balance").innerText = `₹${data.availableBalance}`;
        document.getElementById("today-total").innerText = `₹${data.todayTotal}`;
        document.getElementById("max-transaction").innerText = `₹${data.maxTransaction}`;
        document.getElementById("userEmail").innerText = "👤 " + data.email;
      }
    });
}

// 🔍 Filters
document.getElementById("filterCard").addEventListener("input", filter);
document.getElementById("filterDate").addEventListener("change", filter);

function filter() {
  const cardVal = document.getElementById("filterCard").value.toLowerCase();
  const dateVal = document.getElementById("filterDate").value;

  const rows = document.querySelectorAll("#transactionsTable tr");
  rows.forEach(row => {
    const card = row.children[2].textContent.toLowerCase();
    const date = row.children[4].textContent.split(" ")[0];
    const match = card.includes(cardVal) && (dateVal === "" || date === dateVal);
    row.style.display = match ? "" : "none";
  });
}

// 📊 Load Charts for Analytics
function loadCharts() {
  fetch("php/summary.php")
    .then(res => res.json())
    .then(data => {
      if (data.status !== 'success') return;

      // LINE CHART
      const lineCtx = document.getElementById("lineChart").getContext("2d");
      const dates = data.daily.map(d => d.date);
      const totals = data.daily.map(d => d.total);

      if (window.lineChartInstance) window.lineChartInstance.destroy();
      window.lineChartInstance = new Chart(lineCtx, {
        type: 'line',
        data: {
          labels: dates,
          datasets: [{
            label: "Daily Payments",
            data: totals,
            fill: false,
            borderColor: 'blue',
            tension: 0.2
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false }
          }
        }
      });

      // PIE CHART
      const spent = parseFloat(data.total_paid);
      const remaining = 100000 - spent;

      const pieCtx = document.getElementById("pieChart").getContext("2d");
      if (window.pieChartInstance) window.pieChartInstance.destroy();
      window.pieChartInstance = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
          labels: ["Spent", "Available"],
          datasets: [{
            data: [spent, remaining],
            backgroundColor: ["#dc3545", "#28a745"]
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });
    });
}

// Show/Hide Analytics
function toggleAnalytics() {
  const section = document.getElementById("analyticsSection");
  section.style.display = section.style.display === "none" ? "block" : "none";

  if (section.style.display === "block") {
    loadCharts(); // Load when shown
  }
}

window.onload = function () {
  loadTransactions();
  loadSummary();
};
