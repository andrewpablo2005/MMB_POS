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
    gradient.addColorStop(0, 'rgba(22, 163, 74, .15)');
    gradient.addColorStop(1, 'rgba(22, 163, 74, .85)');
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
          if (!chartArea) return 'rgba(22,163,74,.7)';
          if (width !== chart.width || height !== chart.height) {
            gradient = getGradient(ctx, chartArea);
            width    = chart.width;
            height   = chart.height;
          }
          return gradient;
        },
        borderColor:          'rgba(22, 163, 74, 0)',
        borderWidth:           0,
        borderRadius:          10,
        borderSkipped:         false,
        hoverBackgroundColor: 'rgba(22, 163, 74, .95)',
      }]
    },
    options: {
      responsive:          true,
      maintainAspectRatio: false,
      animation:           { duration: 900, easing: 'easeInOutQuart' },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a2535',
          titleColor:      '#fff',
          bodyColor:       '#94a3b8',
          padding:          12,
          cornerRadius:     10,
          displayColors:    false,
          callbacks: {
            title: (items) => labels[items[0].dataIndex],
            label: (ctx)   => ' ₱ ' + Number(ctx.parsed.y).toLocaleString('en-PH', { minimumFractionDigits: 2 }),
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid:   { color: 'rgba(0,0,0,.04)', drawBorder: false },
          border: { display: false },
          ticks:  {
            color:  '#94a3b8',
            font:   { size: 11, family: 'Inter' },
            callback: (v) => v >= 1000 ? '₱' + (v / 1000).toFixed(0) + 'k' : '₱' + v,
          }
        },
        x: {
          grid:   { display: false },
          border: { display: false },
          ticks:  { color: '#94a3b8', font: { size: 11, family: 'Inter' } },
        }
      }
    }
  });
})();
