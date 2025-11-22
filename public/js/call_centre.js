(function(){
  function el(id){ return document.getElementById(id); }
  function has(id){ return !!el(id); }
  var data = window.callCentreData || {};
  var isWeekly = (data.filter === 'weekly');
  var colors = { blue: '#004578', teal: '#00A9A5', sky: '#1A73E8', orange: '#F97316', red: '#EA4335', green: '#34A853', purple: '#8B5CF6', amber: '#FBBF24', light: '#D1D5DB' };
  var weekPalette = [colors.blue, colors.teal, colors.sky, colors.orange, colors.purple, colors.green, colors.amber];
  function makeColors(len, palette){ var arr=[]; for (var i=0;i<len;i++){ arr.push(palette[i % palette.length]); } return arr; }
  function makeDayColors(labels){ return (labels||[]).map(function(l){ try { var d = new Date(String(l).trim() + 'T00:00:00'); var dow = d.getDay(); switch(dow){ case 1: return colors.blue; case 2: return colors.sky; case 3: return colors.teal; case 4: return colors.green; case 5: return colors.orange; case 6: return colors.red; case 0: default: return colors.purple; } } catch(_){ return colors.light; } }); }
  var ticksColor = '#6b7280';
  var gridColor = 'rgba(0,0,0,0.06)';
  function shade(hex, pct){
    var c = hex.replace('#','');
    var r = parseInt(c.substring(0,2),16), g = parseInt(c.substring(2,4),16), b = parseInt(c.substring(4,6),16);
    r = Math.min(255, Math.max(0, r + Math.round(255*pct/100)));
    g = Math.min(255, Math.max(0, g + Math.round(255*pct/100)));
    b = Math.min(255, Math.max(0, b + Math.round(255*pct/100)));
    return '#' + r.toString(16).padStart(2,'0') + g.toString(16).padStart(2,'0') + b.toString(16).padStart(2,'0');
  }
  function hexToRGBA(hex, alpha){
    var c = hex.replace('#','');
    var r = parseInt(c.substring(0,2),16), g = parseInt(c.substring(2,4),16), b = parseInt(c.substring(4,6),16);
    return 'rgba(' + r + ',' + g + ',' + b + ',' + (alpha || 1) + ')';
  }
  function gradientColor(ctx, base){
    var ca = ctx.chart.chartArea; if (!ca) return base;
    var g = ctx.chart.ctx.createLinearGradient(0, ca.top, 0, ca.bottom);
    g.addColorStop(0, hexToRGBA(base, 0.75));
    g.addColorStop(1, hexToRGBA(base, 0.45));
    return g;
  }
  function weekGradient(ctx){ var baseList = weekPalette; var base = baseList[ctx.dataIndex % baseList.length]; return gradientColor(ctx, base); }
  function weekSolid(ctx){ var baseList = weekPalette; var base = baseList[ctx.dataIndex % baseList.length]; return hexToRGBA(base, 1); }
  function dayGradient(ctx){
    var labels = (ctx.chart && ctx.chart.data && ctx.chart.data.labels) ? ctx.chart.data.labels : [];
    var l = labels[ctx.dataIndex];
    try {
      var d = new Date(String(l).trim() + 'T00:00:00');
      var dow = d.getDay();
      var base;
      switch(dow){ case 1: base = colors.blue; break; case 2: base = colors.sky; break; case 3: base = colors.teal; break; case 4: base = colors.green; break; case 5: base = colors.orange; break; case 6: base = colors.red; break; case 0: default: base = colors.purple; }
      return gradientColor(ctx, base);
    } catch(_){ return gradientColor(ctx, colors.light); }
  }
  function daySolid(ctx){
    var labels = (ctx.chart && ctx.chart.data && ctx.chart.data.labels) ? ctx.chart.data.labels : [];
    var l = labels[ctx.dataIndex];
    try {
      var d = new Date(String(l).trim() + 'T00:00:00');
      var dow = d.getDay();
      var base;
      switch(dow){ case 1: base = colors.blue; break; case 2: base = colors.sky; break; case 3: base = colors.teal; break; case 4: base = colors.green; break; case 5: base = colors.orange; break; case 6: base = colors.red; break; case 0: default: base = colors.purple; }
      return hexToRGBA(base, 1);
    } catch(_){ return hexToRGBA(colors.light, 0.85); }
  }
  var modernPlugin = {
    id: 'modernStyle',
    beforeDatasetsDraw: function(chart){ var ctx = chart.ctx; ctx.save(); ctx.shadowColor = 'rgba(16,24,40,.18)'; ctx.shadowBlur = 10; ctx.shadowOffsetY = 6; },
    afterDatasetsDraw: function(chart, args, opts){
      var ctx = chart.ctx; ctx.restore();
      if (!opts || opts.labels === false) return;
      ctx.save();
      ctx.fillStyle = (opts.labelColor || '#111827');
      ctx.font = (opts.font || '11px Inter, system-ui, sans-serif');
      ctx.textAlign = 'center';
      function drawBadge(x,y,text,percent){
        var ca = chart.chartArea;
        var padX = 8, r = 10, h = 22;
        var w = ctx.measureText(text).width + padX*2;
        var top = Math.max(ca.top + 10, y - h - 6);
        var bottom = top + h;
        ctx.beginPath();
        ctx.moveTo(x - w/2 + r, top);
        ctx.lineTo(x + w/2 - r, top);
        ctx.arc(x + w/2 - r, top + r, r, -Math.PI/2, 0);
        ctx.lineTo(x + w/2, bottom - r);
        ctx.arc(x + w/2 - r, bottom - r, r, 0, Math.PI/2);
        ctx.lineTo(x - w/2 + r, bottom);
        ctx.arc(x - w/2 + r, bottom - r, r, Math.PI/2, Math.PI);
        ctx.lineTo(x - w/2, top + r);
        ctx.arc(x - w/2 + r, top + r, r, Math.PI, 3*Math.PI/2);
        ctx.fillStyle = percent ? 'rgba(16,185,129,0.14)' : 'rgba(79,70,229,0.14)';
        ctx.fill();
        ctx.fillStyle = '#111827';
        ctx.fillText(text, x, top + h/2);
      }
      chart.data.datasets.forEach(function(ds, di){
        var meta = chart.getDatasetMeta(di);
        meta.data.forEach(function(elm, idx){
          var v = ds.data[idx]; if (v == null) return;
          var pos = elm.tooltipPosition();
          var text = (opts.percent ? (v + '%') : String(v));
          drawBadge(pos.x, pos.y, text, !!opts.percent);
        });
      });
      ctx.restore();
    }
  };
  if (typeof Chart !== 'undefined') { Chart.register(modernPlugin); }
  function formatNumber(n){ try { return new Intl.NumberFormat().format(n); } catch(e) { return String(n); } }
  function barOptions(){
    return {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { backgroundColor: 'rgba(31,41,55,0.95)', titleColor: '#fff', bodyColor: '#fff', cornerRadius: 10 },
        modernStyle: { labels: true }
      },
      scales: {
        x: { grid: { display: false }, ticks: { color: ticksColor } },
        y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { color: ticksColor, callback: function(v){ return formatNumber(v); } } }
      },
      animation: { duration: 900, easing: 'easeOutQuart' }
    };
  }
  function barPointColors(values){ return (values || []).map(function(_,i){ return weekColors[i % weekColors.length]; }); }

  if (has('chartWeeklyNewSingle')) {
    new Chart(el('chartWeeklyNewSingle'), { type: 'bar', data: { labels: (isWeekly ? (data.dailyLabels || []) : (data.weeklyLabels || [])), datasets: [{ label: 'New Faults', data: (isWeekly ? (data.dailyNewFaults || []) : (data.weeklyNewFaults || [])), backgroundColor: (isWeekly ? dayGradient : weekGradient), borderColor: (isWeekly ? daySolid : weekSolid), borderWidth: 2, borderRadius: 8 }] }, options: barOptions() });
  }
  if (has('chartWeeklyResolvedSingle')) {
    new Chart(el('chartWeeklyResolvedSingle'), { type: 'bar', data: { labels: (isWeekly ? (data.dailyLabels || []) : (data.weeklyLabels || [])), datasets: [{ label: 'Resolved Faults', data: (isWeekly ? (data.dailyResolved || []) : (data.weeklyResolved || [])), backgroundColor: (isWeekly ? dayGradient : weekGradient), borderColor: (isWeekly ? daySolid : weekSolid), borderWidth: 2, borderRadius: 8 }] }, options: barOptions() });
  }
  if (has('chartWeeklyResolved3Days')) {
    new Chart(el('chartWeeklyResolved3Days'), {
      type: 'bar',
      data: {
        labels: (isWeekly ? (data.dailyLabels || []) : (data.weeklyLabels || [])),
        datasets: [{
          label: 'Resolved <= 3 days (%)',
          data: (isWeekly ? (data.dailyResolved3DaysPerc || []) : (data.weeklyResolved3DaysPerc || [])),
          backgroundColor: (isWeekly ? dayGradient : weekGradient),
          borderColor: (isWeekly ? daySolid : weekSolid),
          borderWidth: 2,
          borderRadius: 8
        }]
      },
      options: Object.assign({}, barOptions(), { scales: { x: { grid: { display: false }, ticks: { color: ticksColor } }, y: { beginAtZero: true, max: 100, grid: { color: gridColor, drawBorder: false }, ticks: { color: ticksColor, callback: function(v){ return v + '%'; } } } }, plugins: { modernStyle: { labels: true, percent: true }, legend: { display: false }, tooltip: { callbacks: { label: function(ctx){ return ((ctx.parsed && ctx.parsed.y) ? ctx.parsed.y : 0) + '%'; } } } } })
    });
  }
  if (has('chartWeeklyOutstandingSingle')) {
    new Chart(el('chartWeeklyOutstandingSingle'), { type: 'bar', data: { labels: (isWeekly ? (data.dailyLabels || []) : (data.weeklyLabels || [])), datasets: [{ label: 'Outstanding Faults', data: (isWeekly ? (data.dailyOutstanding || []) : (data.weeklyOutstanding || [])), backgroundColor: (isWeekly ? dayGradient : weekGradient), borderColor: (isWeekly ? daySolid : weekSolid), borderWidth: 2, borderRadius: 8 }] }, options: barOptions() });
  }
  function binsToVals(bins){
    var order = ['0_3','4_7','8_14','15_30','31_60','61_90','90_plus'];
    return order.map(function(k){ return (bins && typeof bins[k] !== 'undefined') ? bins[k] : 0; });
  }
  var binLabels = ['0-3 DAYS', '4-7 DAYS', '8-14 DAYS', '15-30 DAYS', '31-60 DAYS', '61-90 DAYS', 'ABOVE 90 DAYS'];
  function lineOptions(){
    return {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(31,41,55,0.95)', titleColor: '#fff', bodyColor: '#fff', cornerRadius: 10 } },
      scales: { x: { grid: { color: gridColor }, ticks: { color: ticksColor } }, y: { beginAtZero: true, grid: { color: gridColor, drawBorder: false }, ticks: { color: ticksColor, callback: function(v){ return formatNumber(v); } } } },
      elements: { point: { radius: 5, borderWidth: 2, borderColor: '#fff' } },
      interaction: { mode: 'index', intersect: false }
    };
  }
  if (has('chartResolvedAge')) {
    new Chart(el('chartResolvedAge'), { type: 'line', data: { labels: binLabels, datasets: [{ label: 'Resolved', data: binsToVals(data.resolvedBins), borderColor: colors.sky, backgroundColor: 'rgba(26,115,232,0.12)', tension: 0.35, pointBackgroundColor: colors.sky }] }, options: lineOptions() });
  }
  if (has('chartOutstandingAge')) {
    new Chart(el('chartOutstandingAge'), { type: 'line', data: { labels: binLabels, datasets: [{ label: 'Outstanding', data: binsToVals(data.outstandingBins), borderColor: colors.blue, backgroundColor: 'rgba(0,69,120,0.12)', tension: 0.35, pointBackgroundColor: colors.blue }] }, options: lineOptions() });
  }

  if (has('chartShiftTraffic')) {
    var labelsShift = isWeekly ? (data.dailyLabels || []) : (data.weeklyLabels || []);
    function bgGrad(base){ return function(ctx){ return gradientColor(ctx, base); }; }
    function fmtDate(s){ try { var d = new Date(s + 'T00:00:00'); return d.toLocaleDateString(undefined, { day: '2-digit', month: 'short' }); } catch(_){ return s; } }
    var valueLabelPlugin = { id: 'valueLabels', afterDatasetsDraw: function(chart){ var ctx = chart.ctx; ctx.save(); chart.data.datasets.forEach(function(ds, di){ var meta = chart.getDatasetMeta(di); meta.data.forEach(function(el, i){ var v = ds.data[i]; if (!v) return; ctx.fillStyle = '#374151'; ctx.font = '12px system-ui, -apple-system, Segoe UI, Roboto'; ctx.textAlign = 'center'; ctx.textBaseline = 'bottom'; ctx.fillText(v, el.x, el.y - 4); }); }); ctx.restore(); } };
    new Chart(el('chartShiftTraffic'), {
      type: 'bar',
      data: {
        labels: labelsShift,
        datasets: [
          { label: 'Morning', data: isWeekly ? (data.dailyShiftMorning || []) : (data.weeklyShiftMorning || []), backgroundColor: bgGrad(colors.sky), borderColor: hexToRGBA(colors.sky, 1), borderWidth: 2, borderRadius: 10 },
          { label: 'Afternoon', data: isWeekly ? (data.dailyShiftAfternoon || []) : (data.weeklyShiftAfternoon || []), backgroundColor: bgGrad(colors.orange), borderColor: hexToRGBA(colors.orange, 1), borderWidth: 2, borderRadius: 10 },
          { label: 'Night', data: isWeekly ? (data.dailyShiftNight || []) : (data.weeklyShiftNight || []), backgroundColor: bgGrad(colors.light), borderColor: hexToRGBA(colors.light, 1), borderWidth: 2, borderRadius: 10 }
        ]
      },
      plugins: [valueLabelPlugin],
      options: Object.assign(barOptions(), {
        plugins: { legend: { display: true, position: 'top', labels: { usePointStyle: true, boxWidth: 10 } }, tooltip: { callbacks: { title: function(items){ var i = items && items[0] ? items[0].dataIndex : 0; if (isWeekly) { var ds = labelsShift[i]; return ds || 'Day'; } var s = (data.weeklyRangeStarts||[])[i]; var e = (data.weeklyRangeEnds||[])[i]; var left = 'Week ' + (i+1); if (s && e) left += ' (' + fmtDate(s) + ' – ' + fmtDate(e) + ')'; return left; }, label: function(ctx){ var v = ctx.parsed && ctx.parsed.y != null ? ctx.parsed.y : ctx.raw; return ctx.dataset.label + ': ' + v; } } } },
        responsive: true,
        maintainAspectRatio: false,
        scales: { x: { stacked: false, grid: { display: false }, ticks: { color: '#6b7280' } }, y: { stacked: false, grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { color: '#6b7280', precision: 0 } } },
        datasets: { bar: { categoryPercentage: 0.7, barPercentage: 0.25 } }
      })
    });
  }
  var form = document.querySelector('form[action$="call-centre/reports"]');
  if (form) {
    var resetBtn = form.querySelector('[data-cc-reset]');
    if (resetBtn) {
      resetBtn.addEventListener('click', function(){ window.location.href = form.getAttribute('action'); });
    }
  }

  // Modern interactive features
  document.addEventListener('DOMContentLoaded', function() {
    // Add loading animations to KPI cards
    const kpiCards = document.querySelectorAll('.cc-kpi');
    kpiCards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 20px 40px rgba(16, 24, 40, 0.2)';
      });
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 8px 24px rgba(16, 24, 40, 0.1)';
      });
    });

    // Add chart card interactions
    const chartCards = document.querySelectorAll('.cc-chart-card');
    chartCards.forEach(card => {
      const expandBtn = card.querySelector('button');
      if (expandBtn) {
        expandBtn.addEventListener('click', function() {
          card.classList.toggle('cc-chart-card--expanded');
          const icon = this.querySelector('i');
          if (card.classList.contains('cc-chart-card--expanded')) {
            icon.className = 'fas fa-compress';
            this.setAttribute('title', 'Collapse view');
          } else {
            icon.className = 'fas fa-expand';
            this.setAttribute('title', 'Expand view');
          }
        });
      }
    });

    // Add real-time data refresh simulation
    const liveBadge = document.querySelector('.badge.bg-primary-subtle');
    if (liveBadge) {
      setInterval(() => {
        liveBadge.classList.toggle('pulse');
      }, 2000);
    }

    // Add tooltip initialization
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add smooth scrolling for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
  });

  // Performance metrics animation
  function animateValue(element, start, end, duration, suffix, decimals) {
    let startTimestamp = null;
    const step = (timestamp) => {
      if (!startTimestamp) startTimestamp = timestamp;
      const progress = Math.min((timestamp - startTimestamp) / duration, 1);
      const val = progress * (end - start) + start;
      const text = decimals ? val.toFixed(decimals) : formatNumber(Math.round(val));
      element.textContent = suffix ? (text + suffix) : text;
      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    };
    window.requestAnimationFrame(step);
  }

  // Initialize animations when page loads
  window.addEventListener('load', function() {
    const kpiValues = document.querySelectorAll('.cc-kpi-value');
    kpiValues.forEach(function(valueEl){
      const raw = (valueEl.textContent || '').trim();
      const isNumeric = /^[\d,]+(\.[\d]+)?%?$/.test(raw);
      if (!isNumeric) return;
      const suffix = raw.endsWith('%') ? '%' : '';
      const decimals = (raw.indexOf('.') >= 0) ? (raw.split('.')[1].replace(/[^\d]/g,'').length) : 0;
      const endNum = parseFloat(raw.replace(/[,%]/g,''));
      valueEl.textContent = suffix ? ('0' + suffix) : '0';
      setTimeout(function(){
        animateValue(valueEl, 0, endNum, 2000, suffix, decimals);
      }, 500);
    });
  });
})();