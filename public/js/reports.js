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

  function initCharts(data) {
    if (typeof Chart === 'undefined') return; // Chart.js not loaded

    // Ensure charts follow container height for uniform sizing
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.responsive = true;

    // Monthly faults
    if (has('chartMonthlyFaults')) new Chart(el('chartMonthlyFaults'), {
      type: 'bar',
      data: { labels: data.monthlyLabels, datasets: [{ label: 'Faults', data: data.monthlyCounts, backgroundColor: '#4e73df' }] },
      options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // SLA gauge
    if (has('chartSLA')) new Chart(el('chartSLA'), {
      type: 'doughnut',
      data: { labels: ['Compliance','Remaining'], datasets: [{ data: [data.slaCompliance, 100 - data.slaCompliance], backgroundColor: ['#1cc88a','#e9ecef'], borderWidth: 0, hoverOffset: 2 }] },
      options: { circumference: 180, rotation: -90, cutout: '60%', plugins: { legend: { display: false } } }
    });

    // Status
    if (has('chartStatus')) new Chart(el('chartStatus'), {
      type: 'bar',
      data: { labels: data.statusLabels, datasets: [{ label: 'Faults', data: data.statusValues, backgroundColor: '#36b9cc' }] },
      options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // RFO
    if (has('chartRFO')) new Chart(el('chartRFO'), {
      type: 'pie',
      data: { labels: data.rfoLabels, datasets: [{ data: data.rfoValues, backgroundColor: ['#f6c23e','#e74a3b','#858796','#4e73df','#1cc88a','#36b9cc','#5a5c69'] }] },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Suspected RFO
    if (has('chartSuspectedRFO')) new Chart(el('chartSuspectedRFO'), {
      type: 'pie',
      data: { labels: data.suspectedRfoLabels, datasets: [{ data: data.suspectedRfoValues, backgroundColor: ['#9ad0f5','#ffcc99','#99cc99','#cc99ff','#66cccc','#ff9999','#cccccc'] }] },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

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

  function start() {
    const data = getData();
    if (!data) return;
    initCharts(data);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();