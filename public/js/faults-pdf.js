/*
 * Faults report PDF (client-side, jsPDF + autoTable).
 * Same approach and branding as survey-pdf.js (LTE / connectivity surveys):
 * Powertel logo centred at the top, POWERTEL watermark, "© POWERTEL  Page x of y" footer.
 *
 * Data comes from GET faults/export/data (FaultController@exportData) with the
 * page's current filters (search, status, age, region, period/from/to).
 */
(function () {
  'use strict';

  var BRAND = [15, 61, 145];       // Powertel blue
  var INK = [17, 24, 39];
  var MUTED = [107, 114, 128];
  var LINE = [229, 231, 235];

  function s(v) { return v == null ? '' : String(v); }

  function loadScriptOnce(url, key) {
    if (!url) return Promise.resolve();
    var marker = 'faultsPdfScript:' + (key || url);
    // Reuse scripts already loaded by survey-pdf.js if present.
    var surveyMarker = 'ltePdfScript:' + (key || url);
    if (window[surveyMarker]) return window[surveyMarker];
    if (window[marker]) return window[marker];
    window[marker] = new Promise(function (resolve, reject) {
      var el = document.createElement('script');
      el.src = url;
      el.async = true;
      el.onload = function () { resolve(); };
      el.onerror = function () { reject(new Error('Failed to load script: ' + url)); };
      document.head.appendChild(el);
    });
    return window[marker];
  }

  function getWebpackRequire() {
    if (window.__lteWebpackRequire) return window.__lteWebpackRequire;
    if (!window.webpackChunk || !Array.isArray(window.webpackChunk)) return null;
    var req;
    try {
      window.webpackChunk.push([[Math.random()], {}, function (r) { req = r; }]);
    } catch (e) {
      return null;
    }
    window.__lteWebpackRequire = req;
    return req;
  }

  function ensureLibs(assets) {
    return Promise.all([
      loadScriptOnce(assets.jspdfChunkUrl, 'jspdf'),
      loadScriptOnce(assets.autotableChunkUrl, 'autotable'),
    ]).then(function () {
      var req = getWebpackRequire();
      if (!req) throw new Error('PDF libraries failed to initialise (app.js not loaded).');
      var mod = req('./node_modules/jspdf/dist/jspdf.es.min.js');
      var jsPDF = mod && (mod.jsPDF || mod.default || mod);
      if (!jsPDF) throw new Error('jsPDF not available');
      req('./node_modules/jspdf-autotable/dist/jspdf.plugin.autotable.js');
      return jsPDF;
    });
  }

  function fetchAsDataUrl(url) {
    if (!url) return Promise.resolve(null);
    window.__surveyPdfImageCache = window.__surveyPdfImageCache || {};
    if (window.__surveyPdfImageCache[url]) return Promise.resolve(window.__surveyPdfImageCache[url]);
    return fetch(url, { credentials: 'same-origin' })
      .then(function (r) { if (!r.ok) throw new Error('logo ' + r.status); return r.blob(); })
      .then(function (blob) {
        return new Promise(function (resolve) {
          var reader = new FileReader();
          reader.onload = function () { resolve(reader.result); };
          reader.readAsDataURL(blob);
        });
      })
      .then(function (d) { window.__surveyPdfImageCache[url] = d; return d; })
      .catch(function () { return null; });
  }

  function dims(doc) {
    return { w: doc.internal.pageSize.getWidth(), h: doc.internal.pageSize.getHeight() };
  }

  function watermark(doc) {
    var d = dims(doc);
    doc.setTextColor(232, 234, 238);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(64);
    doc.text('POWERTEL', d.w / 2, d.h / 2 + 10, { align: 'center', angle: 30 });
    doc.setTextColor(INK[0], INK[1], INK[2]);
    doc.setFont('helvetica', 'normal');
  }

  // Full header on page 1 (same layout as the LTE survey report).
  function firstPageHeader(doc, logo, data) {
    var d = dims(doc);
    var y = 8;
    var logoW = 64, logoH = 18;
    if (logo) doc.addImage(logo, 'PNG', (d.w - logoW) / 2, y, logoW, logoH, undefined, 'FAST');

    var titleY = y + logoH + 8;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.setTextColor(INK[0], INK[1], INK[2]);
    doc.text('Faults Report', d.w / 2, titleY, { align: 'center' });

    var f = data.filters || {};
    var line1 = 'Period: ' + s(f.period || 'All dates') +
      '   |   Status: ' + s(f.status || 'All statuses') +
      '   |   Age: ' + s(f.age || 'All ages') +
      '   |   Region: ' + s(f.region || 'All regions');
    if (f.search) line1 += '   |   Search: "' + s(f.search) + '"';
    var line2 = 'Generated ' + s(data.generated_at) + (data.generated_by ? ' by ' + s(data.generated_by) : '') +
      '   |   ' + s((data.totals || {}).faults || 0) + ' fault(s)';

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9.5);
    doc.setTextColor(55, 65, 81);
    doc.text(doc.splitTextToSize(line1, d.w - 40), d.w / 2, titleY + 6, { align: 'center' });
    doc.setFontSize(8.5);
    doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
    doc.text(line2, d.w / 2, titleY + 11.5, { align: 'center' });

    doc.setDrawColor(LINE[0], LINE[1], LINE[2]);
    doc.line(12, titleY + 15, d.w - 12, titleY + 15);
    return titleY + 20;
  }

  // Compact header on continuation pages.
  function smallHeader(doc, logo) {
    var d = dims(doc);
    if (logo) doc.addImage(logo, 'PNG', 12, 6, 32, 9, undefined, 'FAST');
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.setTextColor(INK[0], INK[1], INK[2]);
    doc.text('Faults Report', d.w - 12, 12, { align: 'right' });
    doc.setDrawColor(LINE[0], LINE[1], LINE[2]);
    doc.line(12, 17, d.w - 12, 17);
  }

  function summaryCards(doc, y, totals) {
    var d = dims(doc);
    var cards = [
      { k: 'Total faults', v: totals.faults, c: [239, 246, 255], b: [191, 219, 254] },
      { k: 'Still open', v: totals.open, c: [255, 251, 235], b: [253, 230, 138] },
      { k: 'Restored', v: totals.restored, c: [236, 253, 245], b: [167, 243, 208] },
      { k: 'Assigned', v: totals.assigned, c: [245, 243, 255], b: [221, 214, 254] },
      { k: 'Unassigned', v: totals.unassigned, c: [254, 242, 242], b: [254, 202, 202] },
    ];
    var gap = 4, x = 12, w = (d.w - 24 - gap * (cards.length - 1)) / cards.length, h = 15;
    cards.forEach(function (card, i) {
      var cx = x + i * (w + gap);
      doc.setFillColor(card.c[0], card.c[1], card.c[2]);
      doc.setDrawColor(card.b[0], card.b[1], card.b[2]);
      doc.roundedRect(cx, y, w, h, 2, 2, 'FD');
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(7.5);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text(card.k.toUpperCase(), cx + 3, y + 5);
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(13);
      doc.setTextColor(INK[0], INK[1], INK[2]);
      doc.text(s(card.v == null ? 0 : card.v), cx + 3, y + 12);
    });
    return y + h + 5;
  }

  function statusColor(code) {
    switch (s(code).toUpperCase()) {
      case 'CLN': return [5, 150, 105];   // restored
      case 'RVK': return [107, 114, 128]; // revoked
      case 'WAS': return [217, 119, 6];   // waiting assessment
      case 'POI': return [124, 58, 237];  // POP outage
      case 'ESC':
      case 'MES': return [220, 38, 38];   // escalated
      default: return [37, 99, 235];      // in progress
    }
  }

  function footers(doc) {
    var total = doc.internal.getNumberOfPages();
    for (var i = 1; i <= total; i++) {
      doc.setPage(i);
      var d = dims(doc);
      var y = d.h - 7;
      doc.setDrawColor(230);
      doc.line(12, y - 4, d.w - 12, y - 4);
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8.5);
      doc.setTextColor(MUTED[0], MUTED[1], MUTED[2]);
      doc.text('© ' + new Date().getFullYear() + ' POWERTEL  ·  iMPAZAMON Faults Report', 12, y);
      doc.text('Page ' + i + ' of ' + total, d.w - 12, y, { align: 'right' });
    }
  }

  function filenameFor(data) {
    var period = s((data.filters || {}).period || 'all').replace(/[^A-Za-z0-9]+/g, '_').replace(/^_|_$/g, '');
    var now = new Date();
    var stamp = now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0') +
      '_' + String(now.getHours()).padStart(2, '0') + String(now.getMinutes()).padStart(2, '0');
    return 'Powertel_Faults_Report_' + (period || 'All') + '_' + stamp + '.pdf';
  }

  function build(jsPDF, logo, data) {
    var doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4', compress: true });
    watermark(doc);
    var y = firstPageHeader(doc, logo, data);
    y = summaryCards(doc, y, data.totals || {});

    if (data.truncated) {
      doc.setFont('helvetica', 'italic');
      doc.setFontSize(8.5);
      doc.setTextColor(185, 28, 28);
      doc.text('Only the first ' + data.max_rows + ' faults are included. Narrow the date range or filters for a complete report.', 12, y);
      y += 4;
    }

    var rows = (data.rows || []).map(function (r, i) {
      return [i + 1, r.ref, r.customer, r.link, r.location, r.switch, r.port, r.assigned_to, r.reported_at, r.status, r.age];
    });

    doc.autoTable({
      startY: y,
      margin: { left: 12, right: 12, top: 21, bottom: 16 },
      theme: 'grid',
      head: [['#', 'Ref No', 'Customer', 'Link', 'Location', 'Switch', 'Port', 'Assigned To', 'Reported', 'Status', 'Age']],
      body: rows.length ? rows : [[{ content: 'No faults matched the selected filters.', colSpan: 11, styles: { halign: 'center', textColor: MUTED } }]],
      styles: { font: 'helvetica', fontSize: 7.6, cellPadding: 1.8, valign: 'middle', lineColor: LINE, lineWidth: 0.1, overflow: 'linebreak' },
      headStyles: { fillColor: BRAND, textColor: [255, 255, 255], fontStyle: 'bold', fontSize: 8 },
      alternateRowStyles: { fillColor: [248, 251, 255] },
      columnStyles: {
        0: { cellWidth: 8, halign: 'right', textColor: MUTED },
        1: { cellWidth: 25, fontStyle: 'bold' },
        2: { cellWidth: 30 },
        3: { cellWidth: 46 },
        4: { cellWidth: 24 },
        5: { cellWidth: 18 },
        6: { cellWidth: 14 },
        7: { cellWidth: 26 },
        8: { cellWidth: 22 },
        9: { cellWidth: 35 },
        10: { cellWidth: 'auto' },
      },
      willDrawPage: function (hook) {
        if (hook.pageNumber > 1) {
          watermark(doc);
          smallHeader(doc, logo);
        }
      },
      didParseCell: function (hook) {
        if (hook.section !== 'body' || hook.column.index !== 9 || !rows.length) return;
        var r = data.rows[hook.row.index];
        if (!r) return;
        hook.cell.styles.textColor = statusColor(r.status_code);
        hook.cell.styles.fontStyle = 'bold';
      },
    });

    footers(doc);
    return doc;
  }

  function generate(btn) {
    var assets = window.__FAULTS_PDF_ASSETS__ || {};
    var url = btn.getAttribute('data-url');
    var label = btn.querySelector('.label');
    var original = label ? label.textContent : '';
    if (btn.dataset.busy === '1') return;
    btn.dataset.busy = '1';
    if (label) label.textContent = 'Preparing PDF…';

    Promise.all([
      ensureLibs(assets),
      fetchAsDataUrl(assets.logoUrl),
      fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { if (!r.ok) throw new Error('Could not load faults (' + r.status + ')'); return r.json(); }),
    ])
      .then(function (res) {
        var doc = build(res[0], res[1], res[2]);
        doc.save(filenameFor(res[2]));
      })
      .catch(function (err) {
        console.error('[faults-pdf]', err);
        alert('Could not generate the PDF: ' + (err && err.message ? err.message : err));
      })
      .finally(function () {
        btn.dataset.busy = '0';
        if (label) label.textContent = original;
      });
  }

  // Exposed for testing / reuse.
  window.__faultsPdfBuild = build;

  function bind() {
    var btn = document.getElementById('faultsExportPdfBtn');
    if (!btn) return;
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      generate(btn);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bind);
  } else {
    bind();
  }
})();
