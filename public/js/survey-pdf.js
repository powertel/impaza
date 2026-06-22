(function () {
  function $(sel, root) {
    return (root || document).querySelector(sel);
  }

  function safeString(v) {
    return v == null ? '' : String(v);
  }

  function titleCase(s) {
    var t = safeString(s).trim();
    if (!t) return '';
    t = t.replace(/_/g, ' ');
    return t.charAt(0).toUpperCase() + t.slice(1);
  }

  function sanitizeFilenamePart(s) {
    return safeString(s)
      .trim()
      .replace(/[\\/:*?"<>|]+/g, '-')
      .replace(/\s+/g, '_')
      .slice(0, 80);
  }

  function formatDateForFilename(isoOrDate) {
    var s = safeString(isoOrDate).trim();
    if (!s) return '';
    var d = new Date(s);
    if (isNaN(d.getTime())) return sanitizeFilenamePart(s);
    var yyyy = String(d.getFullYear());
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    var dd = String(d.getDate()).padStart(2, '0');
    return yyyy + '-' + mm + '-' + dd;
  }

  function buildFilename(data, opts) {
    var o = opts || {};
    var base = sanitizeFilenamePart((o.prefix || 'Survey').replace(/\s+/g, '_'));
    var site = sanitizeFilenamePart(data.site_name || (data.payload && data.payload.general && data.payload.general.siteName) || 'Site');
    var customer = sanitizeFilenamePart(data.customer_name || (data.payload && data.payload.general && data.payload.general.customerName) || '');
    var date = formatDateForFilename((data.payload && data.payload.meta && data.payload.meta.date) || data.created_at);
    var parts = [base];
    if (customer) parts.push(customer);
    if (site) parts.push(site);
    parts.push(date || 'date');
    return parts.join('_') + '.pdf';
  }

  function loadScriptOnce(url, key) {
    if (!url) return Promise.resolve();
    var marker = 'ltePdfScript:' + (key || url);
    if (window[marker]) return window[marker];

    window[marker] = new Promise(function (resolve, reject) {
      var s = document.createElement('script');
      s.src = url;
      s.async = true;
      s.onload = function () { resolve(); };
      s.onerror = function () { reject(new Error('Failed to load script: ' + url)); };
      document.head.appendChild(s);
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

  function ensureLibsLoaded(assets) {
    var a = assets || {};
    return Promise.all([
      loadScriptOnce(a.jspdfChunkUrl, 'jspdf'),
      loadScriptOnce(a.autotableChunkUrl, 'autotable'),
      loadScriptOnce(a.html2canvasChunkUrl, 'html2canvas'),
    ]).then(function () {
      var req = getWebpackRequire();
      if (!req) {
        throw new Error('PDF libraries failed to initialize');
      }

      var jspdfMod = req('./node_modules/jspdf/dist/jspdf.es.min.js');
      var jsPDF = jspdfMod && (jspdfMod.jsPDF || jspdfMod.default || jspdfMod);
      if (!jsPDF) throw new Error('jsPDF not available');

      req('./node_modules/jspdf-autotable/dist/jspdf.plugin.autotable.js');
      var html2canvasMod = req('./node_modules/html2canvas/dist/html2canvas.js');
      var html2canvas = html2canvasMod && (html2canvasMod.default || html2canvasMod);
      return { jsPDF: jsPDF, html2canvas: html2canvas };
    });
  }

  function fetchAsDataUrl(url) {
    if (!url) return Promise.resolve(null);
    if (!window.__surveyPdfImageCache) window.__surveyPdfImageCache = {};
    if (window.__surveyPdfImageCache[url]) return Promise.resolve(window.__surveyPdfImageCache[url]);

    return fetch(url, { credentials: 'same-origin' })
      .then(function (r) {
        if (!r.ok) throw new Error('Failed to fetch image: ' + r.status);
        return r.blob();
      })
      .then(function (blob) {
        return new Promise(function (resolve) {
          var reader = new FileReader();
          reader.onload = function () { resolve(reader.result); };
          reader.readAsDataURL(blob);
        });
      })
      .then(function (dataUrl) {
        window.__surveyPdfImageCache[url] = dataUrl;
        return dataUrl;
      })
      .catch(function () { return null; });
  }

  function getPageDims(doc) {
    var w = doc.internal.pageSize.getWidth();
    var h = doc.internal.pageSize.getHeight();
    return { w: w, h: h };
  }

  function applyWatermark(doc, text) {
    var dims = getPageDims(doc);
    doc.setTextColor(220, 223, 228);
    doc.setFontSize(54);
    doc.text(text, dims.w / 2, dims.h / 2, { align: 'center', angle: 35 });
    doc.setTextColor(33, 37, 41);
  }

  function addHeader(doc, data, assets, opts) {
    var o = opts || {};
    var dims = getPageDims(doc);
    var marginX = 12;
    var y = 10;
    var headerH = 40;

    applyWatermark(doc, 'POWERTEL');

    var logoW = 64;
    var logoH = 18;
    var logoX = (dims.w - logoW) / 2;

    return fetchAsDataUrl(assets.logoUrl).then(function (logoDataUrl) {
      if (logoDataUrl) {
        doc.addImage(logoDataUrl, 'PNG', logoX, y, logoW, logoH, undefined, 'FAST');
      }

      var titleY = y + logoH + 9;
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(16);
      doc.text(safeString(o.reportTitle || 'LTE Site Survey Report'), dims.w / 2, titleY, { align: 'center' });

      var line1 = safeString(o.subLine1 || (data.site_name || (data.payload && data.payload.general && data.payload.general.siteName) || 'Untitled Site'));
      var line2 = safeString(o.subLine2 || '');
      if (!line2) {
        var region = safeString(data.province_region || (data.payload && data.payload.general && data.payload.general.provinceRegion) || '-');
        var date = safeString((data.payload && data.payload.meta && data.payload.meta.date) || '');
        var status = titleCase(data.status || 'draft');
        line2 = 'Region: ' + region + '   |   Date: ' + (date || '-') + '   |   Status: ' + status;
      }

      doc.setFont('helvetica', 'normal');
      doc.setFontSize(10);
      doc.setTextColor(55, 65, 81);
      doc.text(line1, dims.w / 2, titleY + 6, { align: 'center' });
      doc.setFontSize(9);
      doc.setTextColor(107, 114, 128);
      doc.text(line2, dims.w / 2, titleY + 11, { align: 'center' });
      doc.setTextColor(33, 37, 41);

      doc.setDrawColor(229, 231, 235);
      doc.line(marginX, y + headerH, dims.w - marginX, y + headerH);

      return y + headerH + 8;
    });
  }

  function addFooter(doc, pageNumber, totalPages) {
    var dims = getPageDims(doc);
    var marginX = 12;
    var y = dims.h - 10;
    doc.setDrawColor(230);
    doc.line(marginX, y - 4, dims.w - marginX, y - 4);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.setTextColor(107, 114, 128);
    doc.text('© ' + new Date().getFullYear() + ' POWERTEL', marginX, y);
    doc.text('Page ' + pageNumber + ' of ' + totalPages, dims.w - marginX, y, { align: 'right' });
    doc.setTextColor(33, 37, 41);
  }

  function addSectionTitle(doc, title, y) {
    var dims = getPageDims(doc);
    var marginX = 12;
    var w = dims.w - marginX * 2;
    if (y + 10 > dims.h - 18) {
      doc.addPage();
      y = 18;
    }
    doc.setFillColor(243, 244, 246);
    doc.setDrawColor(229, 231, 235);
    doc.roundedRect(marginX, y, w, 8, 2, 2, 'FD');
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11);
    doc.text(title, marginX + 3, y + 5.6);
    return y + 12;
  }

  function kvRows(pairs) {
    var rows = [];
    for (var i = 0; i < pairs.length; i += 2) {
      var a = pairs[i] || { k: '', v: '' };
      var b = pairs[i + 1] || { k: '', v: '' };
      rows.push([a.k, a.v, b.k, b.v]);
    }
    return rows;
  }

  function addInfoTable(doc, startY, pairs) {
    var marginX = 12;
    var body = kvRows(pairs);
    doc.autoTable({
      startY: startY,
      margin: { left: marginX, right: marginX },
      tableWidth: 'auto',
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 9, cellPadding: 2.2, valign: 'middle' },
      columnStyles: {
        0: { cellWidth: 34, textColor: [107, 114, 128], fontStyle: 'bold' },
        1: { cellWidth: 56 },
        2: { cellWidth: 34, textColor: [107, 114, 128], fontStyle: 'bold' },
        3: { cellWidth: 56 },
      },
      body: body,
    });
    return doc.lastAutoTable.finalY + 6;
  }

  function yesNo(v) {
    return v ? 'Yes' : 'No';
  }

  function newPage(doc) {
    doc.addPage();
    return 18;
  }

  function addChecklistSection(doc, startY, title, rows) {
    startY = addSectionTitle(doc, title, startY);
    doc.autoTable({
      startY: startY,
      margin: { left: 12, right: 12 },
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 9, cellPadding: 2.3 },
      headStyles: { fillColor: [229, 231, 235], textColor: [17, 24, 39], fontStyle: 'bold' },
      head: [['Item', 'Status']],
      body: rows,
      columnStyles: { 0: { cellWidth: 120 }, 1: { cellWidth: 66 } },
      didParseCell: function (d) {
        if (d.section !== 'body') return;
        if (d.column.index !== 1) return;
        var v = safeString(d.cell.raw).trim().toLowerCase();
        if (v === 'yes') {
          d.cell.styles.textColor = [16, 185, 129];
          d.cell.styles.fontStyle = 'bold';
        } else if (v === 'no') {
          d.cell.styles.textColor = [239, 68, 68];
          d.cell.styles.fontStyle = 'bold';
        }
      },
    });
    return doc.lastAutoTable.finalY + 6;
  }

  function statusBadge(v) {
    var s = safeString(v).toLowerCase();
    if (s === 'good') return { label: 'GOOD', color: [16, 185, 129] };
    if (s === 'bad') return { label: 'BAD', color: [239, 68, 68] };
    if (s === 'not_available' || s === 'not available') return { label: 'NOT AVAILABLE', color: [107, 114, 128] };
    if (!s) return { label: '-', color: [107, 114, 128] };
    return { label: titleCase(s), color: [59, 130, 246] };
  }

  function addTransmissionTable(doc, startY, tx) {
    startY = addSectionTitle(doc, 'SECTION D — TRANSMISSION DETAILS', startY);
    return addInfoTable(doc, startY, [
      { k: 'Nearest Manhole', v: safeString(tx.nearestManholeCoordinates || '-') },
      { k: 'Existing Fibre', v: safeString(tx.distanceFromExistingFibre || '-') },
      { k: 'POP', v: safeString(tx.distanceFromNearestPop || '-') },
      { k: 'Distance from POP', v: safeString(tx.distanceFromNearestPop2 || '-') },
      { k: 'Allocated Port', v: safeString(tx.allocatedPort || '-') },
      { k: 'Backhaul Type', v: titleCase(tx.backhaulType || '-') },
      { k: 'Backhaul Capacity', v: safeString(tx.requiredBackhaulCapacity || '-') },
      { k: '', v: '' },
    ]);
  }

  function addPowerTable(doc, startY, power) {
    startY = addSectionTitle(doc, 'SECTION E — POWER DETAILS', startY);
    var db = statusBadge(power.conditionOfDb);
    doc.autoTable({
      startY: startY,
      margin: { left: 12, right: 12 },
      theme: 'grid',
      styles: { font: 'helvetica', fontSize: 9, cellPadding: 2.2 },
      headStyles: { fillColor: [229, 231, 235], textColor: [17, 24, 39], fontStyle: 'bold' },
      head: [['Parameter', 'Value']],
      body: [
        ['Power Source', titleCase(power.powerSourceType || '-')],
        ['Phase', titleCase(power.phase || '-')],
        ['Input Voltage', safeString(power.inputVoltage || '-')],
        ['Battery Capacity', safeString(power.batteryCapacity || '-')],
        ['Battery Autonomy (hrs)', safeString(power.batteryAutonomyHrs || '-')],
        ['Earthing System', titleCase(power.earthingSystemInstalled || '-')],
        ['Cable Utility → Site', titleCase(power.cableUtilitySourceToSite || '-')],
        ['Distribution Board', db.label],
      ],
      didParseCell: function (d) {
        if (d.section !== 'body') return;
        if (d.column.index !== 1) return;
        if (d.row.index !== 7) return;
        d.cell.styles.textColor = db.color;
        d.cell.styles.fontStyle = 'bold';
      },
    });
    return doc.lastAutoTable.finalY + 6;
  }

  function normalizeItems(arr) {
    if (!Array.isArray(arr)) return [];
    return arr
      .map(function (r) {
        var desc = safeString(r.description || r.item || '').trim();
        var unit = safeString(r.unit || '').trim();
        var qty = safeString(r.qty || r.quantity || '').trim();
        if (!desc && !unit && !qty) return null;
        return { description: desc, unit: unit, qty: qty };
      })
      .filter(Boolean);
  }

  function addMaterialsTable(doc, startY, title, items) {
    startY = addSectionTitle(doc, title, startY);
    var rows = items.map(function (it) { return [it.description || '-', it.unit || '-', it.qty || '-']; });
    if (!rows.length) rows = [['No items', '', '']];
    doc.autoTable({
      startY: startY,
      margin: { left: 12, right: 12 },
      theme: 'striped',
      styles: { font: 'helvetica', fontSize: 9, cellPadding: 2.2 },
      headStyles: { fillColor: [55, 65, 81], textColor: [255, 255, 255], fontStyle: 'bold' },
      head: [['Item', 'Unit', 'Quantity']],
      body: rows,
      columnStyles: { 0: { cellWidth: 120 }, 1: { cellWidth: 28 }, 2: { cellWidth: 38, halign: 'right' } },
    });
    return doc.lastAutoTable.finalY + 6;
  }

  function groupPhotos(photos) {
    var out = {};
    (photos || []).forEach(function (p) {
      var label = safeString(p.label || 'Photos');
      if (!out[label]) out[label] = [];
      out[label].push(p);
    });
    return out;
  }

  function imageFormatFromDataUrl(dataUrl) {
    if (!dataUrl) return 'JPEG';
    if (dataUrl.indexOf('data:image/png') === 0) return 'PNG';
    if (dataUrl.indexOf('data:image/webp') === 0) return 'WEBP';
    return 'JPEG';
  }

  function addPhotosSection(doc, startY, photos, title) {
    startY = addSectionTitle(doc, safeString(title || 'SECTION H — SITE PHOTOS'), startY);

    var dims = getPageDims(doc);
    var marginX = 12;
    var contentW = dims.w - marginX * 2;
    var gap = 6;
    var colW = (contentW - gap) / 2;
    var maxImgH = 62;
    var y = startY;

    var groups = groupPhotos(photos);
    var labels = Object.keys(groups);

    function ensureSpace(neededH) {
      if (y + neededH > dims.h - 18) {
        doc.addPage();
        y = 18;
      }
    }

    var chain = Promise.resolve();
    labels.forEach(function (label) {
      var items = groups[label].slice();
      var images = items.filter(function (p) { return safeString(p.mime_type || '').indexOf('image/') === 0; });
      var nonImages = items.filter(function (p) { return safeString(p.mime_type || '').indexOf('image/') !== 0; });

      chain = chain.then(function () {
        ensureSpace(10);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text(titleCase(label), marginX, y);
        y += 5;

        if (nonImages.length) {
          doc.setFont('helvetica', 'normal');
          doc.setFontSize(9);
          doc.setTextColor(107, 114, 128);
          doc.text('Attachments: ' + nonImages.map(function (p) { return safeString(p.original_name || 'file'); }).join(', '), marginX, y);
          doc.setTextColor(33, 37, 41);
          y += 5;
        }

        var idx = 0;
        function next() {
          if (idx >= images.length) return Promise.resolve();
          var left = images[idx];
          var right = images[idx + 1];
          idx += 2;

          return Promise.all([
            fetchAsDataUrl(left && left.url),
            fetchAsDataUrl(right && right.url),
          ]).then(function (dataUrls) {
            var leftUrl = dataUrls[0];
            var rightUrl = dataUrls[1];

            var rowH = maxImgH + 8;
            ensureSpace(rowH);

            if (leftUrl) {
              doc.addImage(leftUrl, imageFormatFromDataUrl(leftUrl), marginX, y, colW, maxImgH, undefined, 'FAST');
              doc.setFont('helvetica', 'normal');
              doc.setFontSize(8);
              doc.setTextColor(107, 114, 128);
              doc.text(titleCase(left.label || ''), marginX, y + maxImgH + 4);
              doc.setTextColor(33, 37, 41);
            }
            if (right && rightUrl) {
              doc.addImage(rightUrl, imageFormatFromDataUrl(rightUrl), marginX + colW + gap, y, colW, maxImgH, undefined, 'FAST');
              doc.setFont('helvetica', 'normal');
              doc.setFontSize(8);
              doc.setTextColor(107, 114, 128);
              doc.text(titleCase(right.label || ''), marginX + colW + gap, y + maxImgH + 4);
              doc.setTextColor(33, 37, 41);
            }

            y += rowH;
            return next();
          });
        }

        if (!images.length) {
          doc.setFont('helvetica', 'normal');
          doc.setFontSize(9);
          doc.setTextColor(107, 114, 128);
          doc.text('No images available for this category.', marginX, y);
          doc.setTextColor(33, 37, 41);
          y += 6;
          return;
        }

        return next().then(function () { y += 4; });
      });
    });

    return chain.then(function () { return y; });
  }

  function buildLteSections(doc, data, startY) {
    var payload = data.payload || {};
    var meta = payload.meta || {};
    var general = payload.general || {};
    var access = payload.accessSecurity || payload.accessSecurity || {};
    var tower = payload.tower || {};
    var tx = payload.transmission || {};
    var power = payload.power || {};
    var civil = payload.civilWorks || {};
    var notes = payload.notes || {};
    var materials = payload.materials || {};

    var y = startY || 18;
    y = addSectionTitle(doc, 'SECTION A — GENERAL SITE INFORMATION', y);
    y = addInfoTable(doc, y, [
      { k: 'Survey Date', v: safeString(meta.date || '-') },
      { k: 'Surveyed By', v: safeString(meta.surveyPerformedBy || data.survey_performed_by || '-') },
      { k: 'Site Name', v: safeString(general.siteName || data.site_name || '-') },
      { k: 'JC Number', v: safeString(general.jcNumber || data.jc_number || '-') },
      { k: 'Coordinates', v: safeString(general.coordinates || data.coordinates || '-') },
      { k: 'Province', v: safeString(general.provinceRegion || data.province_region || '-') },
      { k: 'Latitude', v: safeString(general.latitude || data.latitude || '-') },
      { k: 'Longitude', v: safeString(general.longitude || data.longitude || '-') },
      { k: 'Address', v: safeString(general.physicalAddress || '-') },
      { k: 'Contact', v: safeString(general.contactDetails || '-') },
    ]);

    y = addChecklistSection(doc, y, 'SECTION B — SITE ACCESS & SECURITY', [
      ['Security Fence', yesNo(!!access.securityFenceAvailable)],
      ['24 Hour Access', yesNo(!!access.siteAccess24h)],
      ['Guard Availability', yesNo(!!access.guardAvailable)],
      ['Line of Sight', yesNo(!!access.lineOfSightAvailability)],
      ['Fence Condition', statusBadge(access.conditionOfFence).label],
    ]);

    y = addSectionTitle(doc, 'SECTION C — TOWER / STRUCTURAL DETAILS', y);
    y = addInfoTable(doc, y, [
      { k: 'Terrain Type', v: titleCase(tower.terrainType || '-') },
      { k: 'Tower Owner', v: safeString(tower.towerOwner || '-') },
      { k: 'Allocated Height', v: safeString(tower.allocatedHeight || '-') },
      { k: '', v: '' },
    ]);

    y = addTransmissionTable(doc, y, tx);
    y = newPage(doc);
    y = addPowerTable(doc, y, power);

    y = addChecklistSection(doc, y, 'SECTION F — CIVIL WORKS', [
      ['Trenching', yesNo(!!civil.trenchingRequired)],
      ['Concrete/Tar Breaking', yesNo(!!civil.breakingConcreteTar)],
      ['Pole Planting', yesNo(!!civil.polePlantingRequired)],
      ['Plinth Construction', yesNo(!!civil.constructionOfPlinth)],
      ['Manhole Requirement', yesNo(!!civil.newManholeRequired)],
    ]);

    var civilsItems = normalizeItems(materials.civils);
    var nteItems = normalizeItems(materials.nte);

    y = addMaterialsTable(doc, y, 'SECTION G — MATERIALS & QUANTITIES (Civils)', civilsItems);
    y = addMaterialsTable(doc, y, 'SECTION G — MATERIALS & QUANTITIES (NTE)', nteItems);

    y = addSectionTitle(doc, 'RECOMMENDATIONS', y);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    var rec = safeString(notes.notes || '').trim();
    if (!rec) rec = 'No recommendations captured.';
    var dims = getPageDims(doc);
    var lines = doc.splitTextToSize(rec, dims.w - 24);
    doc.text(lines, 12, y);
    y += (lines.length * 5) + 4;

    return y;
  }

  function yesNoText(v) {
    var s = safeString(v).trim().toLowerCase();
    if (s === 'yes' || s === 'y' || s === 'true' || s === '1') return 'Yes';
    if (s === 'no' || s === 'n' || s === 'false' || s === '0') return 'No';
    if (!s) return '-';
    return titleCase(s);
  }

  function buildConnectivitySections(doc, data, startY) {
    var payload = data.payload || {};
    var meta = payload.meta || {};
    var general = payload.general || {};
    var service = payload.serviceRequirements || {};
    var permissions = payload.permissions || {};
    var outdoor = payload.outdoor || {};
    var indoor = payload.indoor || {};
    var boq = payload.boq || {};

    var y = startY || 18;
    y = addSectionTitle(doc, 'SECTION A — GENERAL INFORMATION', y);
    y = addInfoTable(doc, y, [
      { k: 'Survey Date', v: safeString(meta.date || '-') },
      { k: 'Surveyed By', v: safeString(meta.surveyPerformedBy || data.survey_performed_by || '-') },
      { k: 'Customer', v: safeString(general.customerName || data.customer_name || '-') },
      { k: 'Account/JC', v: safeString(general.accountOrJcNumber || data.account_or_jc_number || '-') },
      { k: 'Site', v: safeString(general.siteName || data.site_name || '-') },
      { k: 'Coordinates', v: safeString(general.coordinates || data.coordinates || '-') },
      { k: 'Latitude', v: safeString(general.latitude || data.latitude || '-') },
      { k: 'Longitude', v: safeString(general.longitude || data.longitude || '-') },
      { k: 'Address', v: safeString(general.physicalAddress || '-') },
      { k: 'Contact', v: safeString([general.customerContactName, general.customerContactPhone, general.customerContactEmail].filter(Boolean).join(' • ') || '-') },
    ]);

    y = addSectionTitle(doc, 'SECTION B — SERVICE REQUIREMENTS', y);
    y = addInfoTable(doc, y, [
      { k: 'Service Type', v: safeString(service.serviceType || '-') },
      { k: 'Handover', v: safeString(service.handoverInterface || '-') },
      { k: 'BW Down (Mbps)', v: safeString(service.bandwidthDown || '-') },
      { k: 'BW Up (Mbps)', v: safeString(service.bandwidthUp || '-') },
      { k: 'Purpose', v: safeString(service.servicePurpose || '-') },
      { k: 'Redundancy', v: yesNoText(service.redundancyRequired) },
      { k: 'Public IPs', v: yesNoText(service.publicIpsRequired) },
      { k: 'Public IP Qty', v: safeString(service.publicIpsQty || '-') },
    ]);
    y = addSectionTitle(doc, 'VLAN / ROUTING NOTES', y);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    var vlan = safeString(service.vlanNotes || '').trim();
    if (!vlan) vlan = 'No notes captured.';
    var dims = getPageDims(doc);
    var lines = doc.splitTextToSize(vlan, dims.w - 24);
    doc.text(lines, 12, y);
    y += (lines.length * 5) + 4;

    y = addSectionTitle(doc, 'SECTION C — SITE ACCESS & PERMISSIONS', y);
    y = addInfoTable(doc, y, [
      { k: 'Access Contact', v: safeString(permissions.accessContact || '-') },
      { k: 'Survey Done With', v: safeString(permissions.surveyDoneWith || '-') },
      { k: 'Working Hours', v: safeString(permissions.workingHours || '-') },
      { k: 'Permissions Required', v: safeString(permissions.permissionsRequired || '-') },
      { k: 'Notes', v: safeString(permissions.notes || '-') },
      { k: '', v: '' },
    ]);

    y = addSectionTitle(doc, 'SECTION D — OUTDOOR CONNECTIVITY', y);
    y = addInfoTable(doc, y, [
      { k: 'Nearest POP/Node', v: safeString(outdoor.nearestPopNode || '-') },
      { k: 'Switch/OLT', v: safeString(outdoor.feederSwitchOlt || '-') },
      { k: 'Free Port', v: yesNoText(outdoor.freePortAvailable) },
      { k: 'Port ID', v: safeString(outdoor.portId || '-') },
      { k: 'Distance', v: safeString(outdoor.estimatedDistance || '-') },
      { k: 'Route Type', v: safeString(outdoor.routeType || '-') },
      { k: 'Infrastructure', v: safeString(outdoor.existingInfrastructure || '-') },
      { k: 'Risks', v: safeString(outdoor.obstructionsRisks || '-') },
      { k: 'Nearest Ref', v: safeString(outdoor.nearestManholePoleReference || '-') },
      { k: 'Manhole/JB', v: safeString(outdoor.manholeJbDetails || '-') },
    ]);

    y = addSectionTitle(doc, 'SECTION E — INDOOR ASSESSMENT', y);
    y = addInfoTable(doc, y, [
      { k: 'Space', v: safeString(indoor.spaceForEquipment || '-') },
      { k: 'Cabinet Available', v: yesNoText(indoor.cabinetAvailable) },
      { k: 'Cabinet Size', v: safeString(indoor.cabinetSize || '-') },
      { k: 'New Cabinet', v: yesNoText(indoor.newCabinetRequired) },
      { k: 'Power Available', v: yesNoText(indoor.powerAvailable) },
      { k: 'Socket Type', v: safeString(indoor.socketType || '-') },
      { k: 'Socket Distance', v: safeString(indoor.distanceToSocket || '-') },
      { k: 'Back-up Power', v: safeString(indoor.backupPower || '-') },
      { k: 'Air-conditioning', v: yesNoText(indoor.airConditioning) },
      { k: 'Earthing', v: safeString(indoor.earthing || '-') },
    ]);

    var civilsItems = normalizeItems(boq.civils);
    var nteItems = normalizeItems(boq.nte);
    y = addMaterialsTable(doc, y, 'SECTION F — BOQ (Civils)', civilsItems);
    y = addMaterialsTable(doc, y, 'SECTION G — BOQ (NTE)', nteItems);

    return y;
  }

  function getContextByType(type) {
    if (type === 'cc') {
      return {
        type: 'cc',
        assets: window.__CC_SURVEY_PDF_ASSETS__ || {},
        data: window.__CC_SURVEY_PDF_DATA__ || {},
        reportTitle: 'Customer Connectivity Survey Report',
        filenamePrefix: 'Connectivity_Survey',
        previewFrameId: '#ccSurveyPdfPreviewFrame',
        previewModalId: '#ccSurveyPdfPreviewModal',
        photosSectionTitle: 'SECTION H — PHOTOS & ATTACHMENTS',
      };
    }
    return {
      type: 'lte',
      assets: window.__LTE_SURVEY_PDF_ASSETS__ || {},
      data: window.__LTE_SURVEY_PDF_DATA__ || {},
      reportTitle: 'LTE Site Survey Report',
      filenamePrefix: 'LTE_Survey',
      previewFrameId: '#lteSurveyPdfPreviewFrame',
      previewModalId: '#lteSurveyPdfPreviewModal',
      photosSectionTitle: 'SECTION H — SITE PHOTOS',
    };
  }

  function generateSurveyPDF(mode, type) {
    var ctx = getContextByType(type || 'lte');
    var assets = ctx.assets || {};
    var data = ctx.data || {};
    if (!data || !data.id) throw new Error('Survey data not available');

    if (mode === 'regenerate') {
      window.__surveyPdfImageCache = {};
    }

    return ensureLibsLoaded(assets).then(function (libs) {
      var doc = new libs.jsPDF({ orientation: 'p', unit: 'mm', format: 'a4' });
      doc.setFont('helvetica', 'normal');
      doc.setTextColor(33, 37, 41);

      var subLine1;
      var subLine2;
      if (ctx.type === 'cc') {
        var payload = data.payload || {};
        var g = (payload && payload.general) ? payload.general : {};
        var m = (payload && payload.meta) ? payload.meta : {};
        var customer = safeString(data.customer_name || g.customerName || 'Customer');
        var site = safeString(data.site_name || g.siteName || 'Site');
        var date = safeString(m.date || '') || formatDateForFilename(data.created_at) || '-';
        var status = titleCase(data.status || 'draft');
        subLine1 = customer + ' • ' + site;
        subLine2 = 'Date: ' + (date || '-') + '   |   Status: ' + status;
      }

      return addHeader(doc, data, assets, {
        reportTitle: ctx.reportTitle,
        subLine1: subLine1,
        subLine2: subLine2,
      }).then(function (startY) {
        var y = startY;
        if (ctx.type === 'cc') y = buildConnectivitySections(doc, data, y);
        else y = buildLteSections(doc, data, y);
        var photosStart = newPage(doc);
        return addPhotosSection(doc, photosStart, data.photos || [], ctx.photosSectionTitle).then(function () {
          var pageCount = doc.getNumberOfPages();
          for (var p = 1; p <= pageCount; p++) {
            doc.setPage(p);
            addFooter(doc, p, pageCount);
          }

          var filename = buildFilename(data, { prefix: ctx.filenamePrefix });
          if (mode === 'download' || mode === 'regenerate') {
            doc.save(filename);
            return;
          }

          if (mode === 'preview') {
            var blobUrl = doc.output('bloburl');
            var frame = $(ctx.previewFrameId);
            if (frame) frame.src = blobUrl;
            var modalEl = $(ctx.previewModalId);
            if (modalEl && window.bootstrap && window.bootstrap.Modal) {
              var instance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
              var cleanup = function () {
                if (frame) frame.src = '';
                if (typeof URL !== 'undefined' && URL.revokeObjectURL) {
                  try { URL.revokeObjectURL(blobUrl); } catch (e) {}
                }
                modalEl.removeEventListener('hidden.bs.modal', cleanup);
              };
              modalEl.addEventListener('hidden.bs.modal', cleanup);
              instance.show();
            } else {
              window.open(blobUrl, '_blank');
            }
            return;
          }
        });
      });
    });
  }

  function setBusyFor(selector, key, isBusy) {
    var btns = document.querySelectorAll(selector);
    btns.forEach(function (b) {
      if (!(b instanceof HTMLButtonElement)) return;
      if (isBusy) {
        b.dataset[key] = b.disabled ? '1' : '0';
        b.disabled = true;
        return;
      }
      var wasDisabled = b.dataset[key] === '1';
      delete b.dataset[key];
      b.disabled = wasDisabled;
    });
  }

  function bindOne(selector, attr, type) {
    var btns = document.querySelectorAll(selector);
    if (!btns.length) return;
    btns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var action = btn.getAttribute(attr);
        setBusyFor(selector, (type === 'cc' ? 'ccPdfWasDisabled' : 'ltePdfWasDisabled'), true);
        Promise.resolve()
          .then(function () { return generateSurveyPDF(action, type); })
          .catch(function (e) { alert(safeString(e && e.message ? e.message : e)); })
          .finally(function () { setBusyFor(selector, (type === 'cc' ? 'ccPdfWasDisabled' : 'ltePdfWasDisabled'), false); });
      });
    });
  }

  function bind() {
    bindOne('[data-lte-pdf-action]', 'data-lte-pdf-action', 'lte');
    bindOne('[data-cc-pdf-action]', 'data-cc-pdf-action', 'cc');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bind);
  } else {
    bind();
  }
})();
