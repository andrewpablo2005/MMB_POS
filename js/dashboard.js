/**
 * dashboard.js
 * KPI period toggle + Chart.js chart for the Dashboard page.
 * All values are injected inline from PHP via window.dashboardData.
 */
(function () {
  const data = window.dashboardData || {};
  const salesData = data.monthlySalesTrend ?? Array(12).fill(0);
  const periods = data.periods || {};
  const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

  const peso = (n) => '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  /* ── KPI period toggle (drives Sales + Real Revenue together) ── */
  function swapText(el, text) {
    if (!el || el.textContent === text) return;
    el.classList.add('is-swapping');
    setTimeout(() => {
      el.textContent = text;
      el.classList.remove('is-swapping');
    }, 120);
  }

  document.querySelectorAll('.kpi-toggle[role="group"]:not(.kpi-toggle--sm) button')
    .forEach((btn) => {
      btn.addEventListener('click', function () {
        const group = this.closest('.kpi-toggle');
        group.querySelectorAll('button').forEach((b) => b.classList.remove('active'));
        this.classList.add('active');

        const p = periods[this.dataset.period];
        if (!p) return;
        swapText(document.getElementById('salesValue'), p.sales);
        swapText(document.getElementById('salesSub'), p.sub);
        swapText(document.getElementById('revenueValue'), p.revenue);
        swapText(document.getElementById('revenueSub'), p.revSub);
      });
    });

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
        maxBarThickness:       46,
        hoverBackgroundColor: 'rgba(220, 38, 38, .95)',
        hoverBorderRadius:     7
      }]
    },
    options: {
      responsive:          true,
      maintainAspectRatio: false,
      animation:           { duration: 900, easing: 'easeInOutQuart' },
      interaction:         { mode: 'index', intersect: false },
      onHover: (e, els, ch) => {
        ch.canvas.style.cursor = els.length ? 'pointer' : 'default';
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, .92)',
          titleColor:      '#fff',
          bodyColor:       '#e2e8f0',
          titleFont:       { size: 12, weight: '600', family: 'Inter' },
          bodyFont:        { size: 13, weight: '700', family: 'Inter' },
          padding:          12,
          cornerRadius:     10,
          displayColors:    false,
          callbacks: {
            title: (items) => {
              const full = ['January','February','March','April','May','June','July','August','September','October','November','December'];
              return full[items[0].dataIndex % 12] + ' ' + new Date().getFullYear();
            },
            label: (ctx)   => peso(ctx.parsed.y),
          }
        }
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

  /* ── Chart range toggle (6M / 12M) ── */
  const totalChip = document.getElementById('chartTotal');
  document.querySelectorAll('.kpi-toggle--sm button').forEach((btn) => {
    btn.addEventListener('click', function () {
      const group = this.closest('.kpi-toggle');
      group.querySelectorAll('button').forEach((b) => b.classList.remove('active'));
      this.classList.add('active');

      const months = parseInt(this.dataset.range, 10) || 12;
      const slice = salesData.slice(12 - months);
      const sliceLabels = labels.slice(12 - months);

      chart.data.labels = sliceLabels;
      chart.data.datasets[0].data = slice;
      chart.update();

      if (totalChip) {
        totalChip.textContent = (months === 12 ? 'YTD ' : months + 'M ') + peso(slice.reduce((a, b) => a + b, 0));
      }
    });
  });
})();
