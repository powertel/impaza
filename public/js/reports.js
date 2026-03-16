(() => {
  function getData() {
    const el = document.getElementById('reportsData');
    if (!el) return null;
    const d = el.dataset;
    const parse = (key, fallback=[]) => {
      try { return d[key] ? JSON.parse(d[key]) : fallback; } catch { return fallback; }
    };
    const parseNum = (key, fallback=0) => { try { return d[key] ? JSON.parse(d[key]) : fallback; } catch { return fallback; } };
    return {
      monthlyLabels: parse('monthlyLabels'),
      monthlyCounts: parse('monthlyCounts'),
      statusLabels: parse('statusLabels'),
      statusValues: parse('statusValues'),
      rfoLabels: parse('rfoLabels'),
      rfoValues: parse('rfoValues'),
      suspectedRfoLabels: parse('suspectedRfoLabels'),
      suspectedRfoValues: parse('suspectedRfoValues'),
      rfoMonthlyLabels: parse('rfoMonthlyLabels'),
      rfoMonthlyCounts: parse('rfoMonthlyCounts'),
      workloadLabels: parse('workloadLabels'),
      workloadValues: parse('workloadValues'),
      linkLabels: parse('linkLabels'),
      linkValues: parse('linkValues'),
      slaCompliance: parseNum('slaCompliance'),
      faultTypeLabels: parse('faultTypeLabels'),
      priorityMatrix: parse('priorityMatrix'),
      customerImpactCountLabels: parse('customerImpactCountLabels'),
      customerImpactCountValues: parse('customerImpactCountValues'),
      customerImpactDurationLabels: parse('customerImpactDurationLabels'),
      customerImpactDurationValues: parse('customerImpactDurationValues'),
      serviceTypeLabels: parse('serviceTypeLabels'),
      serviceTypeValues: parse('serviceTypeValues'),
      cityFaultsLabels: parse('cityFaultsLabels'),
      cityFaultsValues: parse('cityFaultsValues'),
      amLabels: parse('amLabels'),
      amFaultsValues: parse('amFaultsValues'),
      amMttrValues: parse('amMttrValues'),
      slaPriorityLabels: parse('slaPriorityLabels'),
      slaPriorityValues: parse('slaPriorityValues'),
      stageBottlenecksLabels: parse('stageBottlenecksLabels'),
      stageBottlenecksValues: parse('stageBottlenecksValues'),
      sectionWorkloadLabels: parse('sectionWorkloadLabels'),
      sectionWorkloadValues: parse('sectionWorkloadValues'),
      techLoadLabels: parse('techLoadLabels'),
      techLoadOpen: parse('techLoadOpen'),
      techLoadResolved: parse('techLoadResolved'),
      standbyLabels: parse('standbyLabels'),
      standbyValues: parse('standbyValues'),
      regionalPerfLabels: parse('regionalPerfLabels'),
      regionalPerfValues: parse('regionalPerfValues'),
      linkStatusLabels: parse('linkStatusLabels'),
      linkStatusValues: parse('linkStatusValues'),
      linkServiceTypeLabels: parse('linkServiceTypeLabels'),
      linkServiceTypeValues: parse('linkServiceTypeValues'),
      linkCapacityLabels: parse('linkCapacityLabels'),
      linkCapacityValues: parse('linkCapacityValues'),
      linksMonthlyLabels: parse('linksMonthlyLabels'),
      linksMonthlyCreated: parse('linksMonthlyCreated'),
      linksMonthlyJcc: parse('linksMonthlyJcc'),
      linkHealthLabels: parse('linkHealthLabels'),
      linkHealthValues: parse('linkHealthValues'),
      linksPerCityLabels: parse('linksPerCityLabels'),
      linksPerCityValues: parse('linksPerCityValues'),
      coverageGapValues: parse('coverageGapValues'),
      mttaThisMonth: parseNum('mttaThisMonth'),
      mttaLastMonth: parseNum('mttaLastMonth'),
      reopenRate: parseNum('reopenRate')
    };
  }

  function el(id) { return document.getElementById(id); }
  function has(id) { return !!el(id); }

  function normalizePie(labels, values, maxSlices = null, groupOther = false) {
    const pairs = (labels || []).map((label, idx) => ({
      label: label ?? 'N/A',
      value: Number(values?.[idx] ?? 0) || 0
    })).filter(p => p.value > 0);

    pairs.sort((a, b) => b.value - a.value);

    const hasLimit = typeof maxSlices === 'number' && isFinite(maxSlices) && maxSlices > 0;
    if (!hasLimit || pairs.length <= maxSlices) {
      return {
        labels: pairs.map(p => p.label),
        values: pairs.map(p => p.value)
      };
    }

    const top = pairs.slice(0, maxSlices);
    if (groupOther) {
      const rest = pairs.slice(maxSlices);
      const otherValue = rest.reduce((sum, p) => sum + p.value, 0);
      if (otherValue > 0) {
        top.push({ label: 'Other', value: otherValue });
      }
    } else {
      pairs.slice(maxSlices).forEach(p => top.push(p));
    }

    return {
      labels: top.map(p => p.label),
      values: top.map(p => p.value)
    };
  }

  function getArcProps(arc) {
    if (!arc) return null;
    if (typeof arc.getProps === 'function') {
      return arc.getProps(['startAngle', 'endAngle', 'outerRadius', 'innerRadius', 'x', 'y'], true);
    }
    const v = arc._view || arc._model;
    if (v) {
      return {
        startAngle: v.startAngle,
        endAngle: v.endAngle,
        outerRadius: v.outerRadius,
        innerRadius: v.innerRadius || 0,
        x: v.x,
        y: v.y
      };
    }
    return null;
  }

  function buildDonutConfig({
    labels,
    values,
    palette,
    maxSlices = null,
    groupOther = false,
    showLegend = false,
    calloutTextLimit = 8,
    calloutTextMinPct = 3,
    calloutLabelMaxChars = 18
  }) {
    const normalized = normalizePie(labels, values, maxSlices, groupOther);
    const total = normalized.values.reduce((s, v) => s + (Number(v) || 0), 0) || 1;
    const colors = normalized.values.map((_, i) => palette[i % palette.length]);

    const calloutsPlugin = {
      id: 'callouts',
      afterDatasetsDraw(chart) {
        const ctx = chart?.ctx;
        const meta = chart?.getDatasetMeta?.(0);
        const arcs = meta?.data;
        if (!ctx || !arcs || !arcs.length) return;

        const data = chart.data?.datasets?.[0]?.data || [];
        const labelsArr = chart.data?.labels || [];

        ctx.save();
        const fontFamily = "'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif";
        const chartArea = chart.chartArea || { top: 0, bottom: chart.height || 0, left: 0, right: chart.width || 0 };
        const sliceCount = arcs.length;

        let labelSize = 12;
        let subSize = 11;
        let pctSize = 14;
        if (sliceCount > 14) { labelSize = 11; subSize = 10; pctSize = 13; }
        if (sliceCount > 20) { labelSize = 10; subSize = 9; pctSize = 12; }

        const fontBold = `600 ${labelSize}px ${fontFamily}`;
        const fontSub = `${subSize}px ${fontFamily}`;
        const fontPct = `700 ${pctSize}px ${fontFamily}`;
        const gap = Math.max(14, labelSize + subSize + 6);

        const clamp = (v, min, max) => Math.max(min, Math.min(max, v));
        const distribute = (items, top, bottom) => {
          if (!items.length) return items;
          items.sort((a, b) => a.desiredY - b.desiredY);
          let prev = -Infinity;
          items.forEach((it) => {
            let y = clamp(it.desiredY, top, bottom);
            if (y - prev < gap) y = prev + gap;
            it.y = y;
            prev = y;
          });
          const lastY = items[items.length - 1].y;
          if (lastY > bottom) {
            const shiftUp = lastY - bottom;
            items.forEach((it) => { it.y -= shiftUp; });
          }
          const firstY = items[0].y;
          if (firstY < top) {
            const shiftDown = top - firstY;
            items.forEach((it) => { it.y += shiftDown; });
          }
          items.forEach((it) => { it.y = clamp(it.y, top, bottom); });
          return items;
        };

        const leftItems = [];
        const rightItems = [];

        arcs.forEach((arc, i) => {
          const value = Number(data[i] || 0);
          if (!value) return;

          const p = getArcProps(arc);
          if (!p) return;

          const angle = (p.startAngle + p.endAngle) / 2;
          const innerR = p.innerRadius || 0;
          const outerR = p.outerRadius || 0;
          const cx = p.x;
          const cy = p.y;

          const pct = (value / total) * 100;
          if (pct >= 6) {
            const midR = innerR + (outerR - innerR) * 0.55;
            const pctX = cx + Math.cos(angle) * midR;
            const pctY = cy + Math.sin(angle) * midR;
            ctx.font = fontPct;
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(`${Math.round(pct)}%`, pctX, pctY);
          }

          const startX = cx + Math.cos(angle) * outerR;
          const startY = cy + Math.sin(angle) * outerR;
          const elbowX = cx + Math.cos(angle) * (outerR + 14);
          const desiredY = cy + Math.sin(angle) * (outerR + 14);
          const rightSide = Math.cos(angle) >= 0;

          const label = String(labelsArr[i] ?? '');
          const item = { i, value, startX, startY, elbowX, desiredY, rightSide, label, pct };
          if (rightSide) rightItems.push(item);
          else leftItems.push(item);
        });

        const allItems = leftItems.concat(rightItems);
        allItems.sort((a, b) => b.value - a.value);
        allItems.forEach((it) => {
          it.showText = it.pct >= calloutTextMinPct;
        });

        const top = chartArea.top + 12;
        const bottom = chartArea.bottom - 12;
        distribute(leftItems.filter((it) => it.showText), top, bottom);
        distribute(rightItems.filter((it) => it.showText), top, bottom);

        ctx.strokeStyle = '#111827';
        ctx.lineWidth = 1;
        ctx.textBaseline = 'middle';

        const ellipsize = (s, maxChars) => {
          const str = String(s || '');
          if (str.length <= maxChars) return str;
          return str.slice(0, Math.max(0, maxChars - 1)) + '…';
        };

        const clampTextX = (x, align) => {
          const pad = 10;
          if (align === 'left') return Math.min(Math.max(x, chartArea.left + pad), chartArea.right - pad);
          return Math.min(Math.max(x, chartArea.left + pad), chartArea.right - pad);
        };

        const outLen = 26;

        const drawItem = (item) => {
          if (!item.showText) return;
          const endY = item.y ?? item.desiredY;
          const outX = item.elbowX + (item.rightSide ? outLen : -outLen);
          const endX = outX;

          ctx.beginPath();
          ctx.moveTo(item.startX, item.startY);
          ctx.lineTo(item.elbowX, endY);
          ctx.lineTo(endX, endY);
          ctx.stroke();

          ctx.fillStyle = '#111827';
          ctx.beginPath();
          ctx.arc(item.startX, item.startY, 2.25, 0, Math.PI * 2);
          ctx.fill();

          const labelText = ellipsize(item.label, calloutLabelMaxChars);
          const align = item.rightSide ? 'left' : 'right';
          ctx.textAlign = align;
          const textX = clampTextX(endX + (item.rightSide ? 6 : -6), align);

          ctx.font = fontBold;
          ctx.fillStyle = '#111827';
          ctx.fillText(labelText, textX, endY - 6);

          ctx.font = fontSub;
          ctx.fillStyle = '#6b7280';
          ctx.fillText(`${item.pct.toFixed(0)}%`, textX, endY + 10);
        };

        leftItems.forEach(drawItem);
        rightItems.forEach(drawItem);

        ctx.restore();
      }
    };

    const tooltipLabel = (ctxOrItem, dataMaybe) => {
      if (ctxOrItem && typeof ctxOrItem.index === 'number' && dataMaybe) {
        const i = ctxOrItem.index;
        const label = String(dataMaybe.labels?.[i] ?? '');
        const raw = Number(dataMaybe.datasets?.[0]?.data?.[i] ?? 0);
        const pct = (raw / total) * 100;
        return `${label}: ${raw} (${pct.toFixed(1)}%)`;
      }
      const label = String(ctxOrItem?.label ?? '');
      const raw = Number(ctxOrItem?.raw ?? 0);
      const pct = (raw / total) * 100;
      return `${label}: ${raw} (${pct.toFixed(1)}%)`;
    };

    const legendCfg = {
      display: !!showLegend,
      position: 'bottom',
      labels: { boxWidth: 12, boxHeight: 12, fontSize: 10 }
    };

    const basePadding = showLegend ? { top: 16, bottom: 16, left: 56, right: 56 } : { top: 8, bottom: 8, left: 8, right: 8 };

    return {
      type: 'doughnut',
      data: {
        labels: normalized.labels,
        datasets: [{
          data: normalized.values,
          backgroundColor: colors,
          borderWidth: 2,
          borderColor: '#ffffff',
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '35%',
        cutoutPercentage: 35,
        layout: { padding: basePadding },
        legend: legendCfg,
        tooltips: { callbacks: { label: tooltipLabel } },
        plugins: {
          legend: legendCfg,
          tooltip: {
            callbacks: {
              label: tooltipLabel
            }
          }
        }
      },
      plugins: [calloutsPlugin]
    };
  }

  function initCharts(data) {
    if (typeof Chart === 'undefined') return; // Chart.js not loaded

    // Destroy existing charts to prevent canvas reuse errors
    try {
      const instances = Chart.instances;
      if (Array.isArray(instances)) {
        instances.forEach((instance) => instance?.destroy?.());
      } else if (instances && typeof instances.forEach === 'function') {
        instances.forEach((instance) => instance?.destroy?.());
      } else if (instances && typeof instances === 'object') {
        Object.keys(instances).forEach((k) => instances[k]?.destroy?.());
      }
    } catch (e) {}

    // Ensure charts follow container height for uniform sizing
    if (Chart.defaults) {
      Chart.defaults.maintainAspectRatio = false;
      Chart.defaults.responsive = true;
    }
    if (Chart.defaults?.font) {
      Chart.defaults.font.family = 'Nunito, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif';
      Chart.defaults.font.size = 11;
    } else if (Chart.defaults?.global) {
      Chart.defaults.global.defaultFontFamily = 'Nunito, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif';
      Chart.defaults.global.defaultFontSize = 11;
    }
    if (Chart.defaults?.borderColor) {
      Chart.defaults.borderColor = '#eef2f7';
    }
    if (Chart.defaults?.plugins?.tooltip) {
      Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17,24,39,0.9)';
      Chart.defaults.plugins.tooltip.titleColor = '#fff';
      Chart.defaults.plugins.tooltip.bodyColor = '#e5e7eb';
    } else if (Chart.defaults?.global?.tooltips) {
      Chart.defaults.global.tooltips.backgroundColor = 'rgba(17,24,39,0.9)';
      Chart.defaults.global.tooltips.titleFontColor = '#fff';
      Chart.defaults.global.tooltips.bodyFontColor = '#e5e7eb';
    }

    // Monthly faults (Performance Overview)
    if (has('chartMonthlyFaults')) {
      const ctx = el('chartMonthlyFaults').getContext('2d');
      const gradient = ctx.createLinearGradient(0, 0, 0, 400);
      gradient.addColorStop(0, 'rgba(78, 115, 223, 0.4)');
      gradient.addColorStop(1, 'rgba(78, 115, 223, 0.01)');
      
      new Chart(el('chartMonthlyFaults'), {
        type: 'line',
        data: { 
          labels: data.monthlyLabels, 
          datasets: [{ 
            label: 'Monthly Fault Trends',
            data: data.monthlyCounts, 
            borderColor: '#6366f1',
            backgroundColor: gradient,
            borderWidth: 4,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6366f1',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 9,
            pointHoverBackgroundColor: '#6366f1',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 4
          }] 
        },
        options: { 
          responsive: true, 
          maintainAspectRatio: false,
          plugins: { 
            legend: { display: false },
            tooltip: {
              backgroundColor: '#ffffff',
              titleColor: '#111827',
              bodyColor: '#4b5563',
              borderColor: '#e5e7eb',
              borderWidth: 1,
              padding: 12,
              displayColors: false,
              titleFont: { size: 13, weight: '600', family: 'Inter' },
              bodyFont: { size: 12, family: 'Inter' },
              callbacks: {
                label: function(context) {
                  return context.parsed.y + ' Faults';
                }
              }
            }
          }, 
          scales: { 
            x: {
              grid: { display: false },
              ticks: { 
                color: '#9ca3af',
                font: { size: 12, family: 'Inter' },
                padding: 10
              },
              border: { display: false }
            },
            y: { 
              beginAtZero: true,
              grid: { 
                color: '#f3f4f6',
                drawBorder: false,
                tickLength: 0
              },
              ticks: { 
                color: '#9ca3af',
                font: { size: 12, family: 'Inter' },
                padding: 10,
                stepSize: 2
              },
              border: { display: false }
            } 
          },
          interaction: {
            intersect: false,
            mode: 'index',
          },
          animation: {
            duration: 2000,
            easing: 'easeOutQuart'
          }
        }
      });
    }

    // SLA gauge
    if (has('chartSLA')) new Chart(el('chartSLA'), {
      type: 'doughnut',
      data: { 
        labels: ['Compliance','Remaining'], 
        datasets: [{ 
          data: [data.slaCompliance, 100 - data.slaCompliance], 
          backgroundColor: ['#1cc88a','#e9ecef'], 
          borderWidth: 0, 
          hoverOffset: 2 
        }] 
      },
      options: { 
        responsive: true,
        maintainAspectRatio: false,
        aspectRatio: 1,
        circumference: 180, 
        rotation: -90, 
        cutout: '60%', 
        plugins: { 
          legend: { display: false } 
        },
        layout: {
          padding: {
            top: 5,
            bottom: 5,
            left: 5,
            right: 5
          }
        },
        animation: {
          animateRotate: false,
          animateScale: false
        }
      }
    });

    // Status
    if (has('chartStatus')) new Chart(el('chartStatus'), {
      type: 'bar',
      data: { labels: data.statusLabels, datasets: [{ label: 'Faults', data: data.statusValues, backgroundColor: '#36b9cc' }] },
      options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // RFO
    if (has('chartRFO')) {
      const cfg = buildDonutConfig({
        labels: data.rfoLabels,
        values: data.rfoValues,
        palette: ['#3b82f6', '#ef4444', '#f59e0b', '#84cc16', '#14b8a6', '#60a5fa', '#a78bfa'],
        maxSlices: null,
        groupOther: false,
        showLegend: false,
        calloutTextMinPct: 3,
        calloutLabelMaxChars: 16
      });
      new Chart(el('chartRFO'), cfg);
    }

    // Suspected RFO
    if (has('chartSuspectedRFO')) {
      const cfg = buildDonutConfig({
        labels: data.suspectedRfoLabels,
        values: data.suspectedRfoValues,
        palette: ['#3b82f6', '#ef4444', '#f59e0b', '#84cc16', '#14b8a6', '#60a5fa', '#a78bfa'],
        maxSlices: 6,
        groupOther: true,
        showLegend: false
      });
      new Chart(el('chartSuspectedRFO'), cfg);
    }

    // RFO monthly trend
    if (has('chartRFOMonthly')) new Chart(el('chartRFOMonthly'), {
      type: 'line',
      data: { labels: data.rfoMonthlyLabels, datasets: [{ label: 'RFOs', data: data.rfoMonthlyCounts, borderColor: '#e74a3b', backgroundColor: 'rgba(231,74,59,0.1)' }] },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Workload
    if (has('chartWorkload')) new Chart(el('chartWorkload'), {
      type: 'bar',
      data: { labels: data.workloadLabels, datasets: [{ label: 'Open Assignments', data: data.workloadValues, backgroundColor: '#e74a3b' }] },
      options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Link inventory by type
    if (has('chartLinkInventory')) new Chart(el('chartLinkInventory'), {
      type: 'bar',
      data: { labels: data.linkLabels, datasets: [{ label: 'Links', data: data.linkValues, backgroundColor: '#5a5c69' }] },
      options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Priority × FaultType stacked
    if (has('chartPriorityHeat')) {
      const ds = (data.priorityMatrix || []).map((row, idx) => ({ label: row.label || `P${idx+1}`, data: row.data || [], backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b'][idx % 5] }));
      new Chart(el('chartPriorityHeat'), { type: 'bar', data: { labels: data.faultTypeLabels, datasets: ds }, options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } } });
    }

    // Customer impact
    if (has('chartCustomerCount')) new Chart(el('chartCustomerCount'), { type: 'bar', data: { labels: data.customerImpactCountLabels, datasets: [{ label: 'Faults', data: data.customerImpactCountValues, backgroundColor: '#4e73df' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
    if (has('chartCustomerDuration')) new Chart(el('chartCustomerDuration'), { type: 'bar', data: { labels: data.customerImpactDurationLabels, datasets: [{ label: 'Duration (s)', data: data.customerImpactDurationValues, backgroundColor: '#1cc88a' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

    // Service type & city faults
    if (has('chartServiceType')) new Chart(el('chartServiceType'), { type: 'bar', data: { labels: data.serviceTypeLabels, datasets: [{ label: 'Faults', data: data.serviceTypeValues, backgroundColor: '#36b9cc' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
    if (has('chartCityFaults')) new Chart(el('chartCityFaults'), { type: 'bar', data: { labels: data.cityFaultsLabels, datasets: [{ label: 'Faults', data: data.cityFaultsValues, backgroundColor: '#858796' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

    // Account managers
    if (has('chartAMFaults')) new Chart(el('chartAMFaults'), { type: 'bar', data: { labels: data.amLabels, datasets: [{ label: 'Faults', data: data.amFaultsValues, backgroundColor: '#5a5c69' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
    if (has('chartAMMttr')) new Chart(el('chartAMMttr'), { type: 'bar', data: { labels: data.amLabels, datasets: [{ label: 'Avg MTTR (s)', data: data.amMttrValues, backgroundColor: '#f6c23e' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

    // SLA & stages
    if (has('chartSLAPriority')) new Chart(el('chartSLAPriority'), { type: 'bar', data: { labels: data.slaPriorityLabels, datasets: [{ label: 'Compliance %', data: data.slaPriorityValues, backgroundColor: '#1cc88a' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } } });
    if (has('chartStageBottlenecks')) new Chart(el('chartStageBottlenecks'), { type: 'bar', data: { labels: data.stageBottlenecksLabels, datasets: [{ label: 'Avg Duration (s)', data: data.stageBottlenecksValues, backgroundColor: '#e74a3b' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

    // Sections & tech load
    if (has('chartSectionWorkload')) new Chart(el('chartSectionWorkload'), { type: 'bar', data: { labels: data.sectionWorkloadLabels, datasets: [{ label: 'Faults', data: data.sectionWorkloadValues, backgroundColor: '#4e73df' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
    if (has('chartTechLoad')) new Chart(el('chartTechLoad'), { type: 'bar', data: { labels: data.techLoadLabels, datasets: [ { label: 'Open', data: data.techLoadOpen, backgroundColor: '#e74a3b' }, { label: 'Resolved', data: data.techLoadResolved, backgroundColor: '#1cc88a' } ] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } } });

    // Standby & regional
    if (has('chartStandby')) new Chart(el('chartStandby'), { type: 'bar', data: { labels: data.standbyLabels, datasets: [{ label: 'Avg Duration (s)', data: data.standbyValues, backgroundColor: '#858796' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
    if (has('chartRegionalPerf')) new Chart(el('chartRegionalPerf'), { type: 'bar', data: { labels: data.regionalPerfLabels, datasets: [{ label: 'Avg Duration (s)', data: data.regionalPerfValues, backgroundColor: '#36b9cc' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

    // Links status/service/capacity
    if (has('chartLinkStatus')) new Chart(el('chartLinkStatus'), { type: 'bar', data: { labels: data.linkStatusLabels, datasets: [{ label: 'Links', data: data.linkStatusValues, backgroundColor: '#4e73df' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
    if (has('chartLinkServiceType')) new Chart(el('chartLinkServiceType'), { type: 'bar', data: { labels: data.linkServiceTypeLabels, datasets: [{ label: 'Links', data: data.linkServiceTypeValues, backgroundColor: '#1cc88a' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
    if (has('chartLinkCapacity')) new Chart(el('chartLinkCapacity'), { type: 'bar', data: { labels: data.linkCapacityLabels, datasets: [{ label: 'Links', data: data.linkCapacityValues, backgroundColor: '#5a5c69' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

    // Activation pipeline
    if (has('chartActivation')) new Chart(el('chartActivation'), { type: 'line', data: { labels: data.linksMonthlyLabels, datasets: [ { label: 'Links Created', data: data.linksMonthlyCreated, borderColor: '#4e73df', backgroundColor: 'rgba(78,115,223,0.1)' }, { label: 'JCC Issued', data: data.linksMonthlyJcc, borderColor: '#1cc88a', backgroundColor: 'rgba(28,200,138,0.1)' } ] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });

    // Link health
    if (has('chartLinkHealth')) new Chart(el('chartLinkHealth'), { type: 'bar', data: { labels: data.linkHealthLabels, datasets: [{ label: 'Faults', data: data.linkHealthValues, backgroundColor: '#e74a3b' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });

    // Links per city & coverage gap
    if (has('chartLinksPerCity')) new Chart(el('chartLinksPerCity'), { type: 'bar', data: { labels: data.linksPerCityLabels, datasets: [{ label: 'Links', data: data.linksPerCityValues, backgroundColor: '#36b9cc' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
    if (has('chartCoverageGap')) new Chart(el('chartCoverageGap'), { type: 'bar', data: { labels: data.linksPerCityLabels, datasets: [{ label: 'Faults per Link', data: data.coverageGapValues, backgroundColor: '#f6c23e' }] }, options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } } });
  }

  function initEvents() {
    const filterForm = document.getElementById('reportsFilterForm');
    if (filterForm) {
      const selects = filterForm.querySelectorAll('select');
      selects.forEach(select => {
        select.addEventListener('change', function() {
          filterForm.submit();
        });
      });
    }
  }

  function start() {
    const data = getData();
    if (!data) return;
    initCharts(data);
    initEvents();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
