/* ============================================================
   ADMIN DASHBOARD — interaksi (sidebar, tab, chart, aksi AJAX)
   Setiap bagian dibungkus try/catch sendiri-sendiri supaya kalau
   satu bagian gagal (misal Chart.js gagal load dari CDN), bagian
   lain (terutama tombol aksi) tetap berfungsi normal.
   ============================================================ */
(function () {
  // ---------- 1. SIDEBAR COLLAPSE ----------
  try {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    if (sidebar && toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
      });
    } else {
      console.warn('[admin.js] #sidebar atau #sidebarToggle tidak ditemukan.');
    }
  } catch (err) {
    console.error('[admin.js] Gagal inisialisasi sidebar:', err);
  }

  // ---------- 2. TAB SWITCHING (sidebar + action pill) ----------
  try {
    const navItems = document.querySelectorAll('.nav-item[data-tab]');
    const tabPanels = document.querySelectorAll('.tab-panel');

    function goToTab(tabName) {
      navItems.forEach((item) => item.classList.toggle('active', item.dataset.tab === tabName));
      tabPanels.forEach((panel) => panel.classList.toggle('active', panel.id === `tab-${tabName}`));
    }

    navItems.forEach((item) => {
      item.addEventListener('click', () => goToTab(item.dataset.tab));
    });

    document.querySelectorAll('[data-goto]').forEach((btn) => {
      btn.addEventListener('click', () => goToTab(btn.dataset.goto));
    });
  } catch (err) {
    console.error('[admin.js] Gagal inisialisasi tab switching:', err);
  }

  // ---------- 3. CHART.JS ----------
  try {
    if (typeof Chart === 'undefined') {
      console.warn('[admin.js] Chart.js tidak termuat (CDN gagal diakses?). Grafik dilewati.');
    } else {
      const data = window.__chartData || { trendLabels: [], trendValues: [], topLabels: [], topValues: [] };
      const accent = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim() || '#5b6cff';

      // Line chart — tren penjualan
      const trendCtx = document.getElementById('chartTrend');
      if (trendCtx) {
        new Chart(trendCtx, {
          type: 'line',
          data: {
            labels: data.trendLabels.length ? data.trendLabels : ['Tidak ada data'],
            datasets: [{
              data: data.trendValues.length ? data.trendValues : [0],
              borderColor: accent,
              backgroundColor: 'rgba(91,108,255,0.08)',
              borderWidth: 2,
              pointRadius: 0,
              pointHoverRadius: 4,
              tension: 0.35,
              fill: true,
            }],
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
              x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#9aa0b1' } },
              y: { grid: { color: 'rgba(20,22,30,0.06)' }, ticks: { font: { size: 11 }, color: '#9aa0b1' } },
            },
          },
        });
      }

      // Bar chart — produk terlaris
      const topCtx = document.getElementById('chartTop');
      if (topCtx) {
        new Chart(topCtx, {
          type: 'bar',
          data: {
            labels: data.topLabels.length ? data.topLabels : ['Tidak ada data'],
            datasets: [{
              data: data.topValues.length ? data.topValues : [0],
              backgroundColor: accent,
              borderRadius: 6,
              maxBarThickness: 28,
            }],
          },
          options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
              x: { grid: { color: 'rgba(20,22,30,0.06)' }, ticks: { font: { size: 11 }, color: '#9aa0b1' } },
              y: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#5a5f70' } },
            },
          },
        });
      }
    }
  } catch (err) {
    console.error('[admin.js] Gagal inisialisasi chart:', err);
  }

  // ---------- 4. AKSI: Verifikasi/Tolak transaksi, Toggle produk, Hapus ulasan ----------
  try {
    const confirmMessages = {
      verify_transaction: 'Tandai pembayaran ini sebagai sudah diverifikasi (sukses)?',
      reject_transaction: 'Tolak transaksi ini? Pembeli perlu dihubungi ulang.',
      toggle_active_product: 'Ubah status aktif produk ini?',
      delete_review: 'Hapus ulasan ini secara permanen?',
    };

    // Actions yang menghapus baris dari tampilan setelah berhasil
    const removeRowActions = new Set(['delete_review']);

    const actionButtons = document.querySelectorAll('[data-action]');
    console.log(`[admin.js] Ditemukan ${actionButtons.length} tombol aksi.`);

    actionButtons.forEach((btn) => {
      btn.addEventListener('click', async () => {
        const action = btn.dataset.action;
        const id = btn.dataset.id;
        const row = btn.closest('tr');

        if (!confirm(confirmMessages[action] || 'Lanjutkan aksi ini?')) return;

        const originalLabel = btn.textContent;
        btn.disabled = true;
        btn.textContent = '...';

        let body = `action=${encodeURIComponent(action)}&id=${encodeURIComponent(id)}`;
        if (action === 'toggle_active_product') {
          body += `&active=${encodeURIComponent(btn.dataset.active)}`;
        }

        try {
          const res = await fetch('admin_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body,
          });

          if (!res.ok) {
            alert(`Server merespons error (HTTP ${res.status}). Cek admin_actions.php.`);
            btn.disabled = false;
            btn.textContent = originalLabel;
            return;
          }

          const json = await res.json();

          if (!json.success) {
            alert(json.message || 'Aksi gagal diproses.');
            btn.disabled = false;
            btn.textContent = originalLabel;
            return;
          }

          if (removeRowActions.has(action) && row) {
            row.style.transition = 'opacity 0.2s ease';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 200);
            return;
          }

          // ---- Update tampilan in-place ----
          if (row) {
            const badge = row.querySelector('.js-status-badge');

            if (action === 'verify_transaction' || action === 'reject_transaction') {
              if (badge) {
                badge.textContent = json.new_label;
                badge.className = `badge js-status-badge ${json.new_class}`;
              }
              const actionCell = btn.closest('.col-action');
              if (actionCell) actionCell.innerHTML = '<span class="muted">—</span>';
            }

            if (action === 'toggle_active_product') {
              if (badge) {
                badge.textContent = json.new_label;
                badge.className = `badge js-status-badge ${json.new_class}`;
              }
              btn.textContent = json.btn_label;
              btn.className = `btn-mini js-toggle-btn ${json.btn_class}`;
              btn.dataset.active = json.new_active;
              btn.disabled = false;
            }
          }
        } catch (err) {
          console.error('[admin.js] Fetch admin_actions.php gagal:', err);
          alert('Terjadi kesalahan koneksi. Coba lagi.');
          btn.disabled = false;
          btn.textContent = originalLabel;
        }
      });
    });
  } catch (err) {
    console.error('[admin.js] Gagal inisialisasi tombol aksi:', err);
  }
})();