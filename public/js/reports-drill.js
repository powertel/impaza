/*
 * Operations Analytics drill-down.
 * Click any chart point / bar / slice, KPI card or customer row to see the faults behind it.
 * Inside the panel you can keep drilling (root cause, customer, region, status) and step back
 * with the breadcrumbs. Data: GET dashboard/reports/drilldown (DashboardController@drilldown).
 */
(function () {
  'use strict';

  var dataEl, url, ctx, monthKeys, modalEl, modal;
  var stack = [];        // [{ label, params }]
  var current = null;    // last response
  var reqSeq = 0;

  function $(id) { return document.getElementById(id); }
  function esc(t) {
    return String(t == null ? '' : t).replace(/[&<>"']/g, function (c) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
    });
  }
  function parse(attr, fb) { try { var v = dataEl.getAttribute(attr); return v ? JSON.parse(v) : fb; } catch (e) { return fb; } }

  /* ---------- params ---------- */

  function baseParams() {
    var p = {};
    if (ctx.from) p.from = ctx.from;
    if (ctx.to) p.to = ctx.to;
    if (ctx.region) p.region = ctx.region;
    if (ctx.impact && ctx.impact !== 'all') p.impact = ctx.impact;
    if (ctx.customer_id) p.customer_id = ctx.customer_id;
    return p;
  }

  function mergedParams() {
    var p = baseParams();
    stack.forEach(function (s) {
      Object.keys(s.params).forEach(function (k) {
        if (k === 'month') { delete p.from; delete p.to; }
        p[k] = s.params[k];
      });
    });
    return p;
  }

  /* ---------- open / navigate ---------- */

  function openDrill(label, params, reset) {
    if (reset) stack = [];
    stack.push({ label: label, params: params || {} });
    show();
    load();
  }

  function goTo(index) {
    stack = stack.slice(0, index + 1);
    load();
  }

  function show() {
    if (!modalEl) return;
    if (window.bootstrap && window.bootstrap.Modal) {
      modal = modal || window.bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    } else if (window.$ && window.$.fn && window.$.fn.modal) {
      window.$(modalEl).modal('show');
    } else {
      modalEl.style.display = 'block';
      modalEl.classList.add('show');
    }
  }

  function load() {
    var seq = ++reqSeq;
    renderCrumbs();
    $('drillLoading').classList.remove('d-none');
    $('drillContent').classList.add('d-none');
    $('drillError').classList.add('d-none');

    var qs = new URLSearchParams(mergedParams()).toString();
    fetch(url + (qs ? '?' + qs : ''), { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(function (d) {
        if (seq !== reqSeq) return;
        current = d;
        render(d);
        $('drillLoading').classList.add('d-none');
        $('drillContent').classList.remove('d-none');
      })
      .catch(function (e) {
        if (seq !== reqSeq) return;
        $('drillLoading').classList.add('d-none');
        $('drillError').textContent = 'Could not load the faults (' + e.message + '). Please try again.';
        $('drillError').classList.remove('d-none');
      });
  }

  /* ---------- rendering ---------- */

  function renderCrumbs() {
    var title = stack.length ? stack[stack.length - 1].label : 'Faults';
    $('drillTitle').textContent = title;
    var html = '<a href="#" data-crumb="-1" class="drill-crumb-home"><i class="fas fa-chart-line me-1"></i>Dashboard</a>';
    stack.forEach(function (s, i) {
      html += '<i class="fas fa-chevron-right drill-crumb-sep"></i>';
      html += (i === stack.length - 1)
        ? '<span class="drill-crumb-current">' + esc(s.label) + '</span>'
        : '<a href="#" data-crumb="' + i + '">' + esc(s.label) + '</a>';
    });
    $('drillCrumbs').innerHTML = html;
  }

  function stat(label, value, cls, drill) {
    return '<div class="drill-stat ' + cls + (drill ? ' is-clickable' : '') + '"' + (drill ? ' data-drill-status="' + drill + '"' : '') + '>' +
      '<div class="drill-stat-value">' + esc(value) + '</div><div class="drill-stat-label">' + esc(label) + '</div></div>';
  }

  function breakdown(title, icon, items, kind) {
    if (!items || !items.length) return '';
    var max = Math.max.apply(null, items.map(function (i) { return i.count; })) || 1;
    var rows = items.map(function (i) {
      var clickable = kind && !(kind === 'region' && i.label === 'Unknown');
      var attrs = clickable ? ' data-bd-kind="' + kind + '" data-bd-id="' + esc(i.id != null ? i.id : i.label) + '" data-bd-label="' + esc(i.label) + '"' : '';
      return '<div class="drill-bd-row' + (clickable ? ' is-clickable' : '') + '"' + attrs + ' title="' + (clickable ? 'Drill into ' + esc(i.label) : '') + '">' +
        '<div class="drill-bd-label">' + esc(i.label) + '</div>' +
        '<div class="drill-bd-bar"><span style="width:' + Math.max(4, (i.count / max) * 100) + '%"></span></div>' +
        '<div class="drill-bd-count">' + i.count.toLocaleString() + '</div></div>';
    }).join('');
    return '<div class="drill-bd"><div class="drill-bd-title"><i class="fas ' + icon + ' me-1"></i>' + esc(title) + '</div>' + rows + '</div>';
  }

  function statusClass(code) {
    switch (String(code || '').toUpperCase()) {
      case 'CLN': return 'st-restored';
      case 'RVK': return 'st-closed';
      case 'WAS': return 'st-waiting';
      case 'POI': return 'st-pop';
      case 'ESC': case 'MES': return 'st-escalated';
      default: return 'st-progress';
    }
  }

  function render(d) {
    var params = mergedParams();
    $('drillPeriod').innerHTML = '<i class="fas fa-calendar-check me-1"></i>' + esc(d.period.label);

    var stats = stat('Faults', d.total.toLocaleString(), 'ds-total') +
      stat('Still open', d.open.toLocaleString(), 'ds-open', params.status_group ? null : 'open') +
      stat('Restored', d.restored.toLocaleString(), 'ds-restored', params.status_group ? null : 'restored') +
      stat('Avg restore time', d.avg_restore || '—', 'ds-mttr');
    $('drillStats').innerHTML = stats;

    var bds = breakdown('Root causes', 'fa-search', d.by_rfo, params.rfo ? null : 'rfo') +
      breakdown('Customers', 'fa-building', d.by_customer, 'customer') +
      breakdown('Regions', 'fa-map-marker-alt', d.by_region, 'region') +
      breakdown('Status', 'fa-tasks', d.by_status, null);
    $('drillBreakdowns').innerHTML = bds;

    $('drillSearch').value = '';
    renderRows();
  }

  function renderRows() {
    var d = current;
    if (!d) return;
    var q = $('drillSearch').value.trim().toLowerCase();
    var rows = !q ? d.rows : d.rows.filter(function (r) {
      return [r.ref, r.customer, r.link, r.location, r.root_cause, r.suspected, r.status, r.assigned_to]
        .some(function (v) { return v && String(v).toLowerCase().indexOf(q) !== -1; });
    });

    var body = rows.map(function (r) {
      var cause = r.root_cause
        ? '<span class="drill-cause is-clickable" data-bd-kind="rfo" data-bd-id="' + r.rfo_id + '" data-bd-label="' + esc(r.root_cause) + '">' + esc(r.root_cause) + '</span>'
        : (r.suspected ? '<span class="text-muted" title="Suspected – not yet confirmed">' + esc(r.suspected) + ' <small>(suspected)</small></span>' : '<span class="text-muted">—</span>');
      var cust = r.customer_id
        ? '<span class="drill-cust is-clickable" data-bd-kind="customer" data-bd-id="' + r.customer_id + '" data-bd-label="' + esc(r.customer) + '">' + esc(r.customer) + '</span>'
        : esc(r.customer);
      return '<tr>' +
        '<td><a href="' + esc(r.url) + '" target="_blank" rel="noopener" class="drill-ref" title="Open in Faults">' + esc(r.ref) + ' <i class="fas fa-external-link-alt"></i></a>' +
          (r.pop ? '<div><span class="drill-tag">POP outage</span></div>' : '') + '</td>' +
        '<td>' + cust + '</td>' +
        '<td class="drill-link">' + esc(r.link) + '<div class="text-muted small">' + esc(r.location) + '</div></td>' +
        '<td>' + cause + '</td>' +
        '<td><span class="drill-status ' + statusClass(r.status_code) + '">' + esc(r.status) + '</span>' +
          (r.assigned_to ? '<div class="text-muted small">' + esc(r.assigned_to) + '</div>' : '') + '</td>' +
        '<td class="text-nowrap">' + esc(r.reported) + '</td>' +
        '<td class="text-nowrap"><span class="text-muted small">' + esc(r.duration_label) + '</span><div class="fw-semibold">' + esc(r.duration) + '</div></td>' +
        '</tr>';
    }).join('');

    $('drillRows').innerHTML = body || '<tr><td colspan="7" class="text-center text-muted py-4">No faults match.</td></tr>';
    var note = 'Showing ' + rows.length.toLocaleString() + ' of ' + d.total.toLocaleString() + ' fault(s)';
    if (d.truncated) note += ' — the newest ' + d.limit + ' are listed; narrow the drill-down to see more';
    $('drillCount').textContent = note;
  }

  /* ---------- drill from inside the panel ---------- */

  function drillBreakdown(kind, id, label) {
    if (kind === 'rfo') openDrill('Root cause: ' + label, { rfo: String(id) });
    else if (kind === 'customer') openDrill(label, { customer_id: String(id) });
    else if (kind === 'region') openDrill('Region: ' + label, { region: label });
  }

  function exportCsv() {
    if (!current) return;
    var cols = ['Ref', 'Customer', 'Link', 'Location', 'Root cause', 'Suspected cause', 'Status', 'Assigned to', 'Reported', 'Duration'];
    var lines = [cols.join(',')].concat(current.rows.map(function (r) {
      return [r.ref, r.customer, r.link, r.location, r.root_cause || '', r.suspected || '', r.status, r.assigned_to || '', r.reported, r.duration_label + ' ' + r.duration]
        .map(function (v) { return '"' + String(v == null ? '' : v).replace(/"/g, '""') + '"'; }).join(',');
    }));
    var blob = new Blob(['﻿' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8' });
    var a = document.createElement('a');
    var name = (stack.length ? stack[stack.length - 1].label : 'faults').replace(/[^A-Za-z0-9]+/g, '_').replace(/^_|_$/g, '');
    a.href = URL.createObjectURL(blob);
    a.download = 'Faults_' + name + '.csv';
    document.body.appendChild(a);
    a.click();
    setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 500);
  }

  /* ---------- wire up charts, KPI cards, tables ---------- */

  function chartFor(id) {
    var c = $(id);
    return (c && window.Chart && window.Chart.getChart) ? window.Chart.getChart(c) : null;
  }

  function makeClickable(chart, handler) {
    if (!chart) return;
    chart.options.onClick = function (evt, elements) {
      if (!elements || !elements.length) return;
      handler(elements[0]);
    };
    chart.options.onHover = function (evt, elements) {
      var t = evt.native ? evt.native.target : evt.target;
      if (t) t.style.cursor = elements && elements.length ? 'pointer' : 'default';
    };
    chart.update('none');
  }

  function wireCharts() {
    // Monthly trend: a point = that month
    makeClickable(chartFor('chartMonthlyFaults'), function (e) {
      var key = monthKeys[e.index];
      var label = chartFor('chartMonthlyFaults').data.labels[e.index];
      if (key) openDrill('Faults logged in ' + label, { month: key }, true);
    });

    // Direct vs POP split
    makeClickable(chartFor('chartFaultCategorySplit'), function (e) {
      var label = String(chartFor('chartFaultCategorySplit').data.labels[e.index] || '');
      var cat = /pop/i.test(label) ? 'pop' : 'direct';
      openDrill(label, { category: cat }, true);
    });

    // Category trend: month + category
    makeClickable(chartFor('chartFaultCategoryTrend'), function (e) {
      var ch = chartFor('chartFaultCategoryTrend');
      var key = monthKeys[e.index];
      var cat = e.datasetIndex === 1 ? 'pop' : 'direct';
      var label = ch.data.datasets[e.datasetIndex].label + ' – ' + ch.data.labels[e.index];
      if (key) openDrill(label, { month: key, category: cat }, true);
    });

    // Root causes
    makeClickable(chartFor('chartRFO'), function (e) {
      var ch = chartFor('chartRFO');
      var ds = ch.data.datasets[0];
      var ids = ds.drillIds && ds.drillIds[e.index];
      if (!ids || !ids.length) return;
      var label = ch.data.labels[e.index];
      openDrill(ds.drillOther && ds.drillOther[e.index] ? 'Root cause: other causes' : 'Root cause: ' + label, { rfo: ids.join(',') }, true);
    });

    // Regions
    makeClickable(chartFor('chartCityFaults'), function (e) {
      var label = String(chartFor('chartCityFaults').data.labels[e.index] || '');
      if (!label || /^unknown/i.test(label) || /^other \(/i.test(label)) return;
      openDrill('Region: ' + label, { region: label }, true);
    });
  }

  function wirePage() {
    document.querySelectorAll('[data-drill]').forEach(function (node) {
      node.classList.add('is-drillable');
      node.setAttribute('tabindex', '0');
      node.setAttribute('role', 'button');
      var go = function () {
        var p = {};
        try { p = JSON.parse(node.getAttribute('data-drill') || '{}'); } catch (e) {}
        openDrill(node.getAttribute('data-drill-label') || 'Faults', p, true);
      };
      node.addEventListener('click', go);
      node.addEventListener('keydown', function (ev) { if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); go(); } });
    });

    document.querySelectorAll('[data-drill-customer]').forEach(function (row) {
      row.addEventListener('click', function () {
        var id = row.getAttribute('data-drill-customer');
        if (id && id !== '0') openDrill(row.getAttribute('data-drill-customer-name') || 'Customer', { customer_id: id }, true);
      });
    });

    modalEl.addEventListener('click', function (ev) {
      var crumb = ev.target.closest('[data-crumb]');
      if (crumb) {
        ev.preventDefault();
        var i = parseInt(crumb.getAttribute('data-crumb'), 10);
        if (i < 0) {
          if (modal) modal.hide(); else if (window.$ && window.$.fn && window.$.fn.modal) window.$(modalEl).modal('hide');
        } else {
          goTo(i);
        }
        return;
      }
      var bd = ev.target.closest('[data-bd-kind]');
      if (bd) { drillBreakdown(bd.getAttribute('data-bd-kind'), bd.getAttribute('data-bd-id'), bd.getAttribute('data-bd-label')); return; }
      var st = ev.target.closest('[data-drill-status]');
      if (st) { var g = st.getAttribute('data-drill-status'); openDrill(g === 'open' ? 'Still open' : 'Restored', { status_group: g }); }
    });

    $('drillSearch').addEventListener('input', renderRows);
    $('drillCsv').addEventListener('click', exportCsv);
    $('drillBack').addEventListener('click', function () { if (stack.length > 1) goTo(stack.length - 2); });
    modalEl.addEventListener('shown.bs.modal', function () { $('drillBack').classList.toggle('d-none', stack.length < 2); });
    var obs = new MutationObserver(function () { $('drillBack').classList.toggle('d-none', stack.length < 2); });
    obs.observe($('drillCrumbs'), { childList: true });
  }

  function init() {
    dataEl = $('reportsData');
    modalEl = $('drillModal');
    if (!dataEl || !modalEl) return;
    url = dataEl.getAttribute('data-drill-url');
    ctx = parse('data-drill-context', {});
    monthKeys = parse('data-monthly-keys', []);
    wirePage();
    // Charts are created by reports.js on DOMContentLoaded; wire them right after.
    setTimeout(wireCharts, 0);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
