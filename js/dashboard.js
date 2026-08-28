/**
 * dashboard.js
 * Chart.js initialization for the Dashboard page.
 * salesData is injected inline from PHP via window.dashboardData.
 */
(function () {
  const salesData = window.dashboardData?.monthlySalesTrend ?? Array(12).fill(0);
  const labels    = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

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

  new Chart(chartCtx, {
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
      onHover: (e, els, chart) => {
        chart.canvas.style.cursor = els.length ? 'pointer' : 'default';
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a2535',
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
              return full[items[0].dataIndex] + ' ' + new Date().getFullYear();
            },
            label: (ctx)   => '₱ ' + Number(ctx.parsed.y).toLocaleString('en-PH', { minimumFractionDigits: 2 }),
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
})();
