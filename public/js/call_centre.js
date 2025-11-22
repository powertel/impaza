(function(){
  function el(id){ return document.getElementById(id); }
  function has(id){ return !!el(id); }
  var data = window.callCentreData || {};
  var weekColors = ['#4e73df','#f6a13e','#9aa0a6','#36b9cc'];
  function barPointColors(values){ return (values || []).map(function(_,i){ return weekColors[i % weekColors.length]; }); }

  if (has('chartWeeklyNewSingle')) {
    new Chart(el('chartWeeklyNewSingle'), { type: 'bar', data: { labels: data.weeklyLabels || [], datasets: [{ label: 'New Faults', data: data.weeklyNewFaults || [], backgroundColor: barPointColors(data.weeklyNewFaults || []) }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
  }
  if (has('chartWeeklyResolvedSingle')) {
    new Chart(el('chartWeeklyResolvedSingle'), { type: 'bar', data: { labels: data.weeklyLabels || [], datasets: [{ label: 'Resolved Faults', data: data.weeklyResolved || [], backgroundColor: barPointColors(data.weeklyResolved || []) }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
  }
  if (has('chartWeeklyResolved3Days')) {
    new Chart(el('chartWeeklyResolved3Days'), {
      type: 'bar',
      data: {
        labels: data.weeklyLabels || [],
        datasets: [{
          label: 'Resolved <= 3 days (%)',
          data: data.weeklyResolved3DaysPerc || [],
          backgroundColor: barPointColors(data.weeklyResolved3DaysPerc || [])
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: function(ctx){ return ((ctx.parsed && ctx.parsed.y) ? ctx.parsed.y : 0) + '%'; } } }
        },
        scales: { y: { beginAtZero: true, max: 100, ticks: { callback: function(v){ return v + '%'; } } } }
      }
    });
  }
  if (has('chartWeeklyOutstandingSingle')) {
    new Chart(el('chartWeeklyOutstandingSingle'), { type: 'bar', data: { labels: data.weeklyLabels || [], datasets: [{ label: 'Outstanding Faults', data: data.weeklyOutstanding || [], backgroundColor: barPointColors(data.weeklyOutstanding || []) }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
  }
  function binsToVals(bins){
    var order = ['0_3','4_7','8_14','15_30','31_60','61_90','90_plus'];
    return order.map(function(k){ return (bins && typeof bins[k] !== 'undefined') ? bins[k] : 0; });
  }
  var binLabels = ['0-3 DAYS', '4-7 DAYS', '8-14 DAYS', '15-30 DAYS', '31-60 DAYS', '61-90 DAYS', 'ABOVE 90 DAYS'];
  if (has('chartResolvedAge')) {
    new Chart(el('chartResolvedAge'), { type: 'line', data: { labels: binLabels, datasets: [{ label: 'Resolved', data: binsToVals(data.resolvedBins), borderColor: '#4e73df', backgroundColor: 'rgba(78,115,223,0.15)', tension: 0.3, pointBackgroundColor: '#4e73df' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
  }
  if (has('chartOutstandingAge')) {
    new Chart(el('chartOutstandingAge'), { type: 'line', data: { labels: binLabels, datasets: [{ label: 'Outstanding', data: binsToVals(data.outstandingBins), borderColor: '#1f88e5', backgroundColor: 'rgba(31,136,229,0.15)', tension: 0.3, pointBackgroundColor: '#1f88e5' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
  }
  var form = document.querySelector('form[action$="call-centre/reports"]');
  if (form) {
    var resetBtn = document.createElement('button');
    resetBtn.type = 'button';
    resetBtn.className = 'btn btn-light btn-sm';
    resetBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Reset';
    resetBtn.addEventListener('click', function(){ window.location.href = form.getAttribute('action'); });
    form.parentElement.appendChild(resetBtn);
  }
})();