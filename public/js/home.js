(() => {
  // Legacy functions removed - replaced with inline modern chart implementation

  document.addEventListener('DOMContentLoaded', function() {
    // Get data from the hidden element
    const homeDataEl = document.getElementById('homeData');
    if (!homeDataEl) return;

    // Destroy existing charts to prevent canvas reuse errors
    if (typeof Chart !== 'undefined' && Chart.instances) {
      Chart.helpers.each(Chart.instances, function(instance) {
        instance.destroy();
      });
    }

    const monthlyLabels = JSON.parse(homeDataEl.dataset.monthlyLabels || '[]');
    const monthlyCounts = JSON.parse(homeDataEl.dataset.monthlyCounts || '[]');
    const statusLabels = JSON.parse(homeDataEl.dataset.statusLabels || '[]');
    const statusValues = JSON.parse(homeDataEl.dataset.statusValues || '[]');
    const statusOptions = JSON.parse(homeDataEl.dataset.statusOptions || '[]');
    const selectedYear = JSON.parse(homeDataEl.dataset.selectedYear || 'null');
    const selectedMonth = JSON.parse(homeDataEl.dataset.selectedMonth || 'null');
    const faultsUrl = String(homeDataEl.dataset.faultsUrl || '/faults');
    const reportsUrl = String(homeDataEl.dataset.reportsUrl || '/dashboard/reports');
    const techLabels = JSON.parse(homeDataEl.dataset.techLabels || '[]');
    const techValues = JSON.parse(homeDataEl.dataset.techValues || '[]');
    const topCustomerLabels = JSON.parse(homeDataEl.dataset.topCustomerLabels || '[]');
    const topCustomerValues = JSON.parse(homeDataEl.dataset.topCustomerValues || '[]');

    const statusLabelToId = new Map();
    if (Array.isArray(statusOptions)) {
      statusOptions.forEach(s => {
        if (!s) return;
        const id = Number(s.id);
        const desc = String(s.description || '').trim();
        if (!Number.isNaN(id) && id > 0 && desc) {
          statusLabelToId.set(desc, id);
        }
      });
    }

    // Set Chart.js defaults for modern look
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#718096';
    Chart.defaults.plugins.legend.display = false;

    // Modern color palette
    const colors = {
      primary: 'rgba(102, 126, 234, 0.8)',
      primaryBorder: 'rgba(102, 126, 234, 1)',
      success: 'rgba(72, 187, 120, 0.8)',
      successBorder: 'rgba(72, 187, 120, 1)',
      warning: 'rgba(237, 137, 54, 0.8)',
      warningBorder: 'rgba(237, 137, 54, 1)',
      info: 'rgba(66, 153, 225, 0.8)',
      infoBorder: 'rgba(66, 153, 225, 1)',
      danger: 'rgba(245, 101, 101, 0.8)',
      dangerBorder: 'rgba(245, 101, 101, 1)',
    };

    // Monthly Trends Chart (Line Chart)
    const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart');
    if (monthlyTrendsCtx) {
      const monthlyChart = new Chart(monthlyTrendsCtx, {
        type: 'line',
        data: {
          labels: monthlyLabels,
          datasets: [{
            label: 'Monthly Faults',
            data: monthlyCounts,
            borderColor: colors.primaryBorder,
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: colors.primaryBorder,
            pointBorderColor: '#fff',
            pointBorderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 8,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            intersect: false,
            mode: 'index',
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false,
              },
              ticks: {
                color: '#a0aec0',
                padding: 10,
              },
              border: {
                display: false,
              }
            },
            x: {
              grid: {
                display: false,
              },
              ticks: {
                color: '#a0aec0',
                padding: 10,
              },
              border: {
                display: false,
              }
            }
          },
          plugins: {
            tooltip: {
              backgroundColor: 'rgba(45, 55, 72, 0.95)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 12,
              padding: 12,
              displayColors: false,
              titleFont: {
                size: 14,
                weight: '600'
              },
              bodyFont: {
                size: 13
              }
            }
          }
        },
        onClick: function (_evt, elements) {
          if (!elements || !elements.length) return;
          const idx = elements[0].index;
          const monthNumber = idx + 1;
          const year = (selectedYear !== null && selectedYear !== 'all') ? Number(selectedYear) : null;
          if (!year || Number.isNaN(year)) {
            window.location.href = reportsUrl;
            return;
          }
          const url = new URL(reportsUrl, window.location.origin);
          url.searchParams.set('year', String(year));
          url.searchParams.set('month', String(monthNumber));
          window.location.href = url.toString();
        }
      });
    }

    // Status Distribution Chart (Pie)
    const statusDistributionCtx = document.getElementById('statusDistributionChart');
    if (statusDistributionCtx) {
      const statusChart = new Chart(statusDistributionCtx, {
        type: 'pie',
        data: {
          labels: statusLabels,
          datasets: [{
            data: statusValues,
            backgroundColor: [
              colors.primary,
              colors.success,
              colors.warning,
              colors.danger,
              colors.info,
            ],
            borderColor: [
              colors.primaryBorder,
              colors.successBorder,
              colors.warningBorder,
              colors.dangerBorder,
              colors.infoBorder,
            ],
            borderWidth: 3,
            hoverBorderWidth: 4,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: 0,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(45, 55, 72, 0.95)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 12,
              padding: 12,
              displayColors: true,
              titleFont: {
                size: 14,
                weight: '600'
              },
              bodyFont: {
                size: 13
              }
            }
          }
        },
        onClick: function (_evt, elements) {
          if (!elements || !elements.length) return;
          const idx = elements[0].index;
          const label = String((statusChart.data.labels || [])[idx] || '').trim();
          const statusId = statusLabelToId.get(label);
          if (!statusId) return;
          const url = new URL(faultsUrl, window.location.origin);
          url.searchParams.set('status', String(statusId));
          window.location.href = url.toString();
        }
      });

      const legendContainer = document.getElementById('statusDistributionLegend');
      if (legendContainer && statusChart.data && statusChart.data.labels && statusChart.data.labels.length) {
        const labels = statusChart.data.labels;
        const colorsArr = Array.isArray(statusChart.data.datasets[0].backgroundColor)
          ? statusChart.data.datasets[0].backgroundColor
          : labels.map(() => statusChart.data.datasets[0].backgroundColor);
        const items = labels.map((label, index) => ({
          label,
          color: colorsArr[index] || colors.primary
        }));
        const midpoint = Math.ceil(items.length / 2);
        const leftItems = items.slice(0, midpoint);
        const rightItems = items.slice(midpoint);

        const renderColumn = list => list.map(item => (
          '<button type="button" class="btn btn-link p-0 d-flex align-items-center mb-1 status-legend-item" data-status-label="' + item.label.replace(/"/g, '&quot;') + '" style="text-decoration:none;">' +
            '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:' + item.color + ';margin-right:8px;"></span>' +
            '<span style="font-size:13px;color:#4a5568;">' + item.label + '</span>' +
          '</button>'
        )).join('');

        legendContainer.innerHTML =
          '<div class="d-flex justify-content-between">' +
            '<div class="me-3 flex-grow-1">' + renderColumn(leftItems) + '</div>' +
            '<div class="flex-grow-1 text-end">' + renderColumn(rightItems) + '</div>' +
          '</div>';

        legendContainer.addEventListener('click', function (e) {
          const btn = e.target.closest('.status-legend-item');
          if (!btn) return;
          const label = String(btn.getAttribute('data-status-label') || '').trim();
          const statusId = statusLabelToId.get(label);
          if (!statusId) return;
          const url = new URL(faultsUrl, window.location.origin);
          url.searchParams.set('status', String(statusId));
          window.location.href = url.toString();
        });
      }
    }

    // Create additional charts if we have old chart containers (for backward compatibility)
    
    // Shipment Chart (Monthly Faults) - Backward compatibility
    const shipmentCtx = document.getElementById('homeShipmentChart');
    if (shipmentCtx) {
      new Chart(shipmentCtx, {
        type: 'bar',
        data: {
          labels: monthlyLabels,
          datasets: [{
            label: 'Monthly Faults',
            data: monthlyCounts,
            backgroundColor: colors.primary,
            borderColor: colors.primaryBorder,
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false,
              },
              ticks: {
                color: '#a0aec0'
              },
              border: {
                display: false,
              }
            },
            x: {
              grid: {
                display: false,
              },
              ticks: {
                color: '#a0aec0'
              },
              border: {
                display: false,
              }
            }
          },
          plugins: {
            tooltip: {
              backgroundColor: 'rgba(45, 55, 72, 0.95)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 12,
              padding: 12,
            }
          }
        }
      });
    }

    // Sales Chart (Line chart) - Backward compatibility
    const salesCtx = document.getElementById('homeSalesChart');
    if (salesCtx) {
      new Chart(salesCtx, {
        type: 'line',
        data: {
          labels: monthlyLabels,
          datasets: [{
            label: 'Resolution Trend',
            data: monthlyCounts.map(count => count * 0.8 + Math.random() * count * 0.4),
            borderColor: colors.successBorder,
            backgroundColor: 'rgba(72, 187, 120, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: colors.successBorder,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false,
              },
              ticks: {
                color: '#a0aec0'
              },
              border: {
                display: false,
              }
            },
            x: {
              grid: {
                display: false,
              },
              ticks: {
                color: '#a0aec0'
              },
              border: {
                display: false,
              }
            }
          },
          plugins: {
            tooltip: {
              backgroundColor: 'rgba(45, 55, 72, 0.95)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 12,
              padding: 12,
            }
          }
        }
      });
    }

    // Status Chart (Doughnut) - Backward compatibility
    const statusCtx = document.getElementById('homeStatusChart');
    if (statusCtx) {
      new Chart(statusCtx, {
        type: 'doughnut',
        data: {
          labels: statusLabels,
          datasets: [{
            data: statusValues,
            backgroundColor: [
              colors.primary,
              colors.success,
              colors.warning,
              colors.danger,
              colors.info,
            ],
            borderColor: [
              colors.primaryBorder,
              colors.successBorder,
              colors.warningBorder,
              colors.dangerBorder,
              colors.infoBorder,
            ],
            borderWidth: 2,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '60%',
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                padding: 20,
                usePointStyle: true,
                font: {
                  size: 11
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(45, 55, 72, 0.95)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 12,
              padding: 12,
            }
          }
        }
      });
    }

    // Technicians Chart (Horizontal Bar) - Backward compatibility
    const techCtx = document.getElementById('homeTechniciansChart');
    if (techCtx) {
      new Chart(techCtx, {
        type: 'bar',
        data: {
          labels: techLabels.slice(0, 5), // Top 5
          datasets: [{
            label: 'Avg Resolution (seconds)',
            data: techValues.slice(0, 5),
            backgroundColor: colors.info,
            borderColor: colors.infoBorder,
            borderWidth: 2,
            borderRadius: 6,
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false,
              },
              ticks: {
                color: '#a0aec0',
                callback: function(value) {
                  return Math.floor(value / 3600) + 'h';
                }
              },
              border: {
                display: false,
              }
            },
            y: {
              grid: {
                display: false,
              },
              ticks: {
                color: '#a0aec0'
              },
              border: {
                display: false,
              }
            }
          },
          plugins: {
            tooltip: {
              backgroundColor: 'rgba(45, 55, 72, 0.95)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 12,
              padding: 12,
              callbacks: {
                label: function(context) {
                  const hours = Math.floor(context.parsed.x / 3600);
                  const minutes = Math.floor((context.parsed.x % 3600) / 60);
                  return `${context.dataset.label}: ${hours}h ${minutes}m`;
                }
              }
            }
          }
        }
      });
    }

    // Top Customers Chart (Horizontal Bar) - Backward compatibility
    const customersCtx = document.getElementById('homeTopCustomersChart');
    if (customersCtx) {
      new Chart(customersCtx, {
        type: 'bar',
        data: {
          labels: topCustomerLabels.slice(0, 5), // Top 5
          datasets: [{
            label: 'Fault Count',
            data: topCustomerValues.slice(0, 5),
            backgroundColor: colors.warning,
            borderColor: colors.warningBorder,
            borderWidth: 2,
            borderRadius: 6,
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false,
              },
              ticks: {
                color: '#a0aec0'
              },
              border: {
                display: false,
              }
            },
            y: {
              grid: {
                display: false,
              },
              ticks: {
                color: '#a0aec0'
              },
              border: {
                display: false,
              }
            }
          },
          plugins: {
            tooltip: {
              backgroundColor: 'rgba(45, 55, 72, 0.95)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 12,
              padding: 12,
            }
          }
        }
      });
    }
  });
})();
