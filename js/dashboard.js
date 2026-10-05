/**
 * dashboard.js
 * KPI period dropdown + Chart.js chart for the Dashboard page.
 * All values are injected inline from PHP via window.dashboardData.
 * No tooltips anywhere — the chart is read from the axis + total chip.
 */
(function () {
  const data = window.dashboardData || {};
  const salesData = data.monthlySalesTrend ?? Array(12).fill(0);
  const periods = data.periods || {};
  const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

  const peso = (n) => '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  /* ── KPI period dropdown (drives Sales + Real Revenue together) ── */
  function swapText(el, text) {
    if (!el || el.textContent === text) return;
    el.classList.add('is-swapping');
    setTimeout(() => {
      el.textContent = text;
      el.classList.remove('is-swapping');
    }, 120);
  }

  function applyPeriodValue(value) {
    const p = periods[value];
    if (!p) return;

    const picker = document.getElementById('salesPeriodPicker');
    const toggle = document.getElementById('salesPeriodToggle');
    const label = toggle ? toggle.querySelector('.period-dropdown-label') : null;
    const items = Array.from(document.querySelectorAll('.period-dropdown-item'));

    if (label) {
      label.textContent = value === 'today' ? 'Today' : value === 'month' ? 'This Month' : 'This Year';
    }

    items.forEach((item) => {
      const isSelected = item.dataset.value === value;
      item.classList.toggle('is-selected', isSelected);
      item.setAttribute('aria-selected', String(isSelected));
    });

    if (picker) {
      picker.classList.remove('is-open');
      const toggleButton = picker.querySelector('.period-dropdown-toggle');
      if (toggleButton) {
        toggleButton.setAttribute('aria-expanded', 'false');
      }
    }

    swapText(document.getElementById('salesValue'), p.sales);
    swapText(document.getElementById('salesSub'), p.sub);
    swapText(document.getElementById('revenueValue'), p.revenue);
    swapText(document.getElementById('revenueSub'), p.revSub);
    swapText(document.getElementById('transactionsLabel'), p.transactionsLabel);
    swapText(document.getElementById('transactionsValue'), p.transactions);
  }

  const picker = document.getElementById('salesPeriodPicker');
  const toggle = document.getElementById('salesPeriodToggle');
  if (picker && toggle) {
    toggle.addEventListener('click', function (event) {
      event.stopPropagation();
      const isOpen = picker.classList.contains('is-open');
      picker.classList.toggle('is-open', !isOpen);
      toggle.setAttribute('aria-expanded', String(!isOpen));
    });

    document.addEventListener('click', function (event) {
      if (!picker.contains(event.target)) {
        picker.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });

    picker.querySelectorAll('.period-dropdown-item').forEach((item) => {
      item.addEventListener('click', function () {
        applyPeriodValue(this.dataset.value);
      });
    });
  }

  /* ── Chart ── */
  function getGradient(ctx, chartArea) {
    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
    gradient.addColorStop(0, 'rgba(220, 38, 38, .15)');
    gradient.addColorStop(1, 'rgba(220, 38, 38, .85)');
    return gradient;
  }

  let width, height, gradient;

  const canvas = document.getElementById('salesChart');
  if (!canvas) return;

  const chartCtx = canvas.getContext('2d');

  const chart = new Chart(chartCtx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Net Sales',
        data: salesData,
        backgroundColor: function (context) {
          const chart = context.chart;
          const { ctx, chartArea } = chart;
          if (!chartArea) return 'rgba(220,38,38,.7)';
          if (width !== chart.width || height !== chart.height) {
            gradient = getGradient(ctx, chartArea);
            width    = chart.width;
            height   = chart.height;
          }
          return gradient;
        },
        borderColor:          'rgba(220, 38, 38, 0)',
        borderWidth:           0,
        borderRadius:          7,
        borderSkipped:         'bottom',
        barPercentage:         0.58,
        categoryPercentage:    0.72,
        maxBarThickness:       46
      }]
    },
    options: {
      responsive:          true,
      maintainAspectRatio: false,
      animation:           { duration: 900, easing: 'easeInOutQuart' },
      plugins: {
        legend:  { display: false },
        tooltip: { enabled: false }   /* no tooltips — by explicit user request */
      },
      scales: {
        y: {
          beginAtZero: true,
          grace:       '8%',
          grid:   { color: 'rgba(0,0,0,.045)', drawBorder: false },
          border: { display: false },
          ticks:  {
            color:  '#64748b',
            font:   { size: 11, family: 'Inter' },
            padding: 6,
            callback: (v) => v >= 1000 ? '₱' + (v / 1000).toFixed(0) + 'k' : '₱' + v,
          }
        },
        x: {
          grid:   { display: false },
          border: { display: false },
          ticks:  { color: '#64748b', font: { size: 11, family: 'Inter' } },
        }
      }
    }
  });

  /* ── Chart range dropdown (6M / 12M) ── */
  const totalChip = document.getElementById('chartTotal');
  const rangeSelect = document.getElementById('chartRange');
  if (rangeSelect) {
    rangeSelect.addEventListener('change', function () {
      const months = parseInt(this.value, 10) || 12;
      const slice = salesData.slice(12 - months);
      const sliceLabels = labels.slice(12 - months);

      chart.data.labels = sliceLabels;
      chart.data.datasets[0].data = slice;
      chart.update();

      if (totalChip) {
        totalChip.textContent = (months === 12 ? 'YTD ' : months + 'M ') + peso(slice.reduce((a, b) => a + b, 0));
      }
    });
  }
})();
