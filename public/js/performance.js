/*
 * Performance dashboard: charts + drill-down wiring.
 * Uses the shared drill panel (reports-drill.js -> window.ImpazaDrill).
 */
(function () {
  'use strict';

  var GREEN = '#10b981', AMBER = '#f59e0b', BLUE_SOFT = '#93c5fd';

  function $(id) { return document.getElementById(id); }
  function drill(label, params) {
    if (window.ImpazaDrill) window.ImpazaDrill.open(label, params);
  }

  function data() {
    try { return JSON.parse($('perfData').getAttribute('data-chart') || '{}'); } catch (e) { return {}; }
  }

  /* ---- period type: show only the relevant fields ---- */
  function wireFilters() {
    var sel = $('filterType');
    if (!sel) return;
    var form = sel.form;
    function apply() {
      var v = sel.value;
      document.querySelectorAll('.perf-f').forEach(function (el) {
        var on = (el.getAttribute('data-for') || '').split(' ').indexOf(v) !== -1;
        el.style.display = on ? '' : 'none';
        el.querySelectorAll('select, input').forEach(function (i) { i.disabled = !on; });
      });
      // "All years" only makes sense for the yearly view
      var all = form.querySelector('option[data-only="year"]');
      if (all) { all.hidden = v !== 'year'; if (v !== 'year' && all.selected) form.elements.year.selectedIndex = 0; }
    }
    sel.addEventListener('change', apply);
    apply();
  }

  /* ---- ranked stacked bars: resolved + pending ---- */
  function rankedChart(canvasId, wrapId, rows, kind, labelPrefix) {
    var canvas = $(canvasId);
    if (!canvas || !window.Chart) return;
    rows = (rows || []).slice(0, 15);
    var wrap = $(wrapId);
    if (wrap) wrap.style.height = Math.max(150, rows.length * 34 + 50) + 'px';

    if (!rows.length) {
      wrap.innerHTML = '<div class="text-muted small text-center pt-5">No faults in this period.</div>';
      return;
    }

    var endLabels = {
      id: 'perfEndLabels',
      afterDatasetsDraw: function (chart) {
        var ctx = chart.ctx;
        var meta = chart.getDatasetMeta(1);
        ctx.save();
        ctx.font = '600 11px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = '#374151';
        meta.data.forEach(function (bar, i) {
          var r = rows[i];
          ctx.fillText(r.rate + '% of ' + r.total, bar.x + 6, bar.y);
        });
        ctx.restore();
      }
    };

    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: rows.map(function (r) { return r.name; }),
        datasets: [
          { label: 'Resolved', data: rows.map(function (r) { return r.resolved; }), backgroundColor: GREEN, borderRadius: 4, maxBarThickness: 24 },
          { label: 'Pending', data: rows.map(function (r) { return r.pending; }), backgroundColor: AMBER, borderRadius: 4, maxBarThickness: 24 }
        ]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: { right: 78 } },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              afterBody: function (items) {
                var r = rows[items[0].dataIndex];
                return ['Resolution rate: ' + r.rate + '%', 'Avg resolution: ' + r.avg, '', 'Click to see these faults'];
              }
            }
          }
        },
        scales: {
          x: { stacked: true, beginAtZero: true, ticks: { precision: 0, color: '#9ca3af' }, grid: { color: '#f1f5f9' }, border: { display: false } },
          y: {
            stacked: true, grid: { display: false }, border: { display: false },
            ticks: {
              autoSkip: false, color: '#1f2937', font: { size: 12, weight: '600' },
              callback: function (v) { var l = this.getLabelForValue(v); return l.length > 24 ? l.slice(0, 23) + '…' : l; }
            }
          }
        },
        onHover: function (evt, el) { var t = evt.native && evt.native.target; if (t) t.style.cursor = el.length ? 'pointer' : 'default'; },
        onClick: function (evt, el) {
          if (!el.length) return;
          var r = rows[el[0].index];
          var p = {}; p[kind] = r.id;
          var resolved = el[0].datasetIndex === 0;
          p.status_group = resolved ? 'resolved' : 'pending';
          drill(labelPrefix + ': ' + r.name + (resolved ? ' (resolved)' : ' (pending)'), p);
        }
      },
      plugins: [endLabels]
    });
  }

  /* ---- logged vs resolved trend ---- */
  function trendChart(t) {
    var canvas = $('perfTrendChart');
    if (!canvas || !window.Chart || !t || !t.labels) return;
    new Chart(canvas, {
      data: {
        labels: t.labels,
        datasets: [
          { type: 'bar', label: 'Logged', data: t.logged, backgroundColor: BLUE_SOFT, borderRadius: 6, maxBarThickness: 42, order: 2 },
          { type: 'line', label: 'Resolved', data: t.resolved, borderColor: GREEN, backgroundColor: GREEN, borderWidth: 3, tension: 0.35, pointRadius: 4, pointHoverRadius: 6, order: 1 }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              afterBody: function (items) {
                var i = items[0].dataIndex;
                var l = t.logged[i] || 0, r = t.resolved[i] || 0;
                return [(l ? Math.round(r / l * 100) : 0) + '% resolved', '', 'Click to see these faults'];
              }
            }
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#6b7280', maxRotation: 0, autoSkip: true } },
          y: { beginAtZero: true, ticks: { precision: 0, color: '#9ca3af', maxTicksLimit: 6 }, grid: { color: '#f1f5f9' }, border: { display: false } }
        },
        onHover: function (evt, el) { var tg = evt.native && evt.native.target; if (tg) tg.style.cursor = el.length ? 'pointer' : 'default'; },
        onClick: function (evt, el) {
          if (!el.length) return;
          var i = el[0].index;
          var k = t.keys[i];
          var resolved = el.some(function (e) { return e.datasetIndex === 1; }) && el.length === 1;
          var p = { from: k.from, to: k.to };
          var label = (t.bucket === 'week' ? 'Week of ' : '') + t.labels[i];
          drill('Logged ' + label, p);
        }
      }
    });
  }

  /* ---- DataTables (sorting / search), if available ---- */
  function wireTables() {
    if (!(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable)) return;
    window.jQuery('.perf-dt').each(function () {
      if (window.jQuery(this).find('tbody tr td[colspan]').length) return;
      window.jQuery(this).DataTable({
        autoWidth: false,
        order: [],  // keep the server ranking (resolved, then rate, then speed); click a header to re-sort
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: { search: 'Search:' }
      });
    });
  }

  function init() {
    var d = data();
    wireFilters();
    wireTables();
    if (window.Chart) {
      trendChart(d.trend);
      rankedChart('perfUsersChart', 'perfUsersWrap', d.users, 'assigned_to', 'Technician');
      rankedChart('perfSectionsChart', 'perfSectionsWrap', d.sections, 'section_id', 'Section');
      rankedChart('perfDeptsChart', 'perfDeptsWrap', d.departments, 'department_id', 'Department');
    }
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
