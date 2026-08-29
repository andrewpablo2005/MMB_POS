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

  const periodSelect = document.getElementById('salesPeriod');
  if (periodSelect) {
    periodSelect.addEventListener('change', function () {
      const p = periods[this.value];
      if (!p) return;
      swapText(document.getElementById('salesValue'), p.sales);
      swapText(document.getElementById('salesSub'), p.sub);
      swapText(document.getElementById('revenueValue'), p.revenue);
      swapText(document.getElementById('revenueSub'), p.revSub);
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
