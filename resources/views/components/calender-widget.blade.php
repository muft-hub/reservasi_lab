@props(['reservasis' => [], 'ruangs' => [], 'role' => 'mahasiswa'])

<!-- Widget Kalender Reservasi Laboratorium -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden mb-6">
    <!-- Header Kalender & Filter -->
    <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <h2 class="text-base font-bold text-slate-800 tracking-tight">Kalender Penggunaan Laboratorium</h2>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Pilih tanggal untuk melihat rincian peminjam, laboratorium, dan jam peminjaman.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Filter Ruang Lab -->
            <select id="cal-filter-ruang" onchange="renderCalendar()" class="px-3 py-1.5 border border-slate-200 rounded-xl text-xs bg-slate-50 text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                <option value="all">Semua Laboratorium</option>
                @if(isset($ruangs) && count($ruangs) > 0)
                    @foreach($ruangs as $rg)
                        <option value="{{ $rg->id }}">{{ $rg->kode }} - {{ $rg->nama }}</option>
                    @endforeach
                @endif
            </select>

            <!-- Navigasi Bulan -->
            <div class="flex items-center bg-slate-100 p-0.5 rounded-xl border border-slate-200">
                <button type="button" onclick="changeMonth(-1)" class="p-1.5 hover:bg-white rounded-lg text-slate-600 hover:text-slate-900 transition" title="Bulan Sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <span id="cal-month-year" class="px-3 text-xs font-bold text-slate-800 min-w-[130px] text-center select-none">
                    -
                </span>
                <button type="button" onclick="changeMonth(1)" class="p-1.5 hover:bg-white rounded-lg text-slate-600 hover:text-slate-900 transition" title="Bulan Berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

            <!-- Tombol Kembali ke Hari Ini -->
            <button type="button" onclick="goToToday()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                Hari Ini
            </button>
        </div>
    </div>

    <!-- Legend Keterangan Status -->
    <div class="px-5 py-2.5 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center gap-4 text-[11px] text-slate-600">
        <span class="font-semibold text-slate-700">Keterangan:</span>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Disetujui</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span>Menunggu Konfirmasi</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            <span>Hari Ini</span>
        </div>
        <div class="ml-auto text-slate-400 hidden sm:inline">
            💡 Tips: Klik kotak tanggal untuk melihat rincian peminjam & waktu
        </div>
    </div>

    <!-- Grid Header Nama Hari -->
    <div class="grid grid-cols-7 border-b border-slate-100 text-center text-xs font-bold text-slate-600 bg-slate-50/50">
        <div class="py-2.5 border-r border-slate-100 text-rose-600">Min</div>
        <div class="py-2.5 border-r border-slate-100">Sen</div>
        <div class="py-2.5 border-r border-slate-100">Sel</div>
        <div class="py-2.5 border-r border-slate-100">Rab</div>
        <div class="py-2.5 border-r border-slate-100">Kam</div>
        <div class="py-2.5 border-r border-slate-100">Jum</div>
        <div class="py-2.5 text-blue-600">Sab</div>
    </div>

    <!-- Container Grid Tanggal Dinamis -->
    <div id="cal-grid" class="grid grid-cols-7 divide-x divide-y divide-slate-100 text-xs bg-slate-50/20">
    </div>
</div>

<!-- Modal Detail Tanggal Terpilih -->
<div id="modal-cal-detail" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between pb-3 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Rincian Jadwal</span>
                <h3 id="modal-detail-date" class="font-bold text-slate-800 text-base mt-1">-</h3>
                <p id="modal-detail-count" class="text-xs text-slate-500">-</p>
            </div>
            <button type="button" onclick="closeCalDetailModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
        </div>

        <!-- Daftar Peminjaman pada Tanggal Terpilih -->
        <div id="modal-detail-list" class="space-y-3"></div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
            @if($role === 'mahasiswa')
                <a id="btn-ajukan-tanggal" href="{{ url('/reservasi/create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajukan Reservasi di Tanggal Ini
                </a>
            @else
                <a href="{{ route('admin.reservasi.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-semibold transition shadow-xs flex items-center gap-1.5">
                    Kelola Reservasi
                </a>
            @endif

            <button type="button" onclick="closeCalDetailModal()" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-semibold">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
const allCalReservasis = @json($reservasis ?? []);
const userRole = '{{ $role }}';

let calCurrentDate = new Date();

const monthNamesIndo = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const dayNamesIndo = [
    'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
];

function changeMonth(step) {
    calCurrentDate.setMonth(calCurrentDate.getMonth() + step);
    renderCalendar();
}

function goToToday() {
    calCurrentDate = new Date();
    renderCalendar();
}

function closeCalDetailModal() {
    document.getElementById('modal-cal-detail').classList.add('hidden');
}

function renderCalendar() {
    const year = calCurrentDate.getFullYear();
    const month = calCurrentDate.getMonth();
    const filterRuangSelect = document.getElementById('cal-filter-ruang');
    const filterRuangId = filterRuangSelect ? filterRuangSelect.value : 'all';

    document.getElementById('cal-month-year').textContent = `${monthNamesIndo[month]} ${year}`;

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    const todayObj = new Date();
    const isThisCurrentMonth = todayObj.getFullYear() === year && todayObj.getMonth() === month;
    const todayDate = todayObj.getDate();

    const grid = document.getElementById('cal-grid');
    grid.innerHTML = '';

    const filteredReservasis = allCalReservasis.filter(item => {
        if (item.status === 'Ditolak') return false;
        if (filterRuangId !== 'all') {
            return String(item.ruang_id) === String(filterRuangId);
        }
        return true;
    });

    const reservasiByDate = {};
    filteredReservasis.forEach(item => {
        const dateStr = item.tanggal ? String(item.tanggal).substring(0, 10) : '';
        if (!reservasiByDate[dateStr]) {
            reservasiByDate[dateStr] = [];
        }
        reservasiByDate[dateStr].push(item);
    });

    // Hari dari bulan sebelumnya
    for (let i = firstDay - 1; i >= 0; i--) {
        const prevDayNum = daysInPrevMonth - i;
        const cell = document.createElement('div');
        cell.className = 'min-h-[85px] sm:min-h-[95px] p-2 bg-slate-50/50 text-slate-300 select-none';
        cell.innerHTML = `<span class="text-xs font-semibold">${prevDayNum}</span>`;
        grid.appendChild(cell);
    }

    // Hari bulan saat ini
    for (let day = 1; day <= daysInMonth; day++) {
        const dayString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayBookings = reservasiByDate[dayString] || [];
        const isToday = isThisCurrentMonth && day === todayDate;

        const cell = document.createElement('div');
        cell.className = `min-h-[85px] sm:min-h-[95px] p-2 transition cursor-pointer hover:bg-blue-50/40 relative flex flex-col justify-between ${
            isToday ? 'bg-blue-50/30 ring-1 ring-inset ring-blue-500 font-bold' : 'bg-white'
        }`;
        cell.onclick = () => openCalDetail(dayString, dayBookings);

        let bookingsHtml = '';
        if (dayBookings.length > 0) {
            bookingsHtml += `<div class="space-y-1 mt-1">`;
            dayBookings.slice(0, 2).forEach(b => {
                const isApproved = b.status === 'Disetujui';
                const pillBg = isApproved ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200';
                const dotColor = isApproved ? 'bg-emerald-500' : 'bg-amber-500';
                const labName = b.ruang ? b.ruang.kode : 'Lab';
                const jam = b.jam_mulai ? String(b.jam_mulai).substring(0, 5) : '';
                bookingsHtml += `
                    <div class="px-1.5 py-0.5 rounded border text-[10px] truncate leading-tight flex items-center gap-1 ${pillBg}">
                        <span class="w-1.5 h-1.5 rounded-full ${dotColor} flex-shrink-0"></span>
                        <span class="truncate font-medium">${jam} ${labName}</span>
                    </div>
                `;
            });
            if (dayBookings.length > 2) {
                bookingsHtml += `<div class="text-[9px] text-slate-500 font-semibold px-1">+${dayBookings.length - 2} lainnya</div>`;
            }
            bookingsHtml += `</div>`;
        }

        cell.innerHTML = `
            <div>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold ${
                        isToday ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-800'
                    }">${day}</span>
                    ${dayBookings.length > 0 ? `<span class="w-2 h-2 rounded-full ${dayBookings.some(b => b.status === 'Disetujui') ? 'bg-emerald-500' : 'bg-amber-500'}"></span>` : ''}
                </div>
                ${bookingsHtml}
            </div>
        `;

        grid.appendChild(cell);
    }

    // Hari bulan berikutnya
    const totalCells = (firstDay + daysInMonth);
    const remainder = 7 - (totalCells % 7);
    if (remainder < 7) {
        for (let nextDay = 1; nextDay <= remainder; nextDay++) {
            const cell = document.createElement('div');
            cell.className = 'min-h-[85px] sm:min-h-[95px] p-2 bg-slate-50/50 text-slate-300 select-none';
            cell.innerHTML = `<span class="text-xs font-semibold">${nextDay}</span>`;
            grid.appendChild(cell);
        }
    }
}

function openCalDetail(dateString, bookings) {
    const parts = dateString.split('-');
    const dateObj = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    
    const dayName = dayNamesIndo[dateObj.getDay()];
    const dayDate = dateObj.getDate();
    const monthName = monthNamesIndo[dateObj.getMonth()];
    const year = dateObj.getFullYear();

    document.getElementById('modal-detail-date').textContent = `${dayName}, ${dayDate} ${monthName} ${year}`;
    document.getElementById('modal-detail-count').textContent = bookings.length > 0 
        ? `${bookings.length} Ruang Laboratorium Terjadwal Dipinjam`
        : 'Belum ada ruangan yang dipinjam pada tanggal ini';

    const btnAjukan = document.getElementById('btn-ajukan-tanggal');
    if (btnAjukan) {
        btnAjukan.href = `{{ url('/reservasi/create') }}?tanggal=${dateString}`;
    }

    const listContainer = document.getElementById('modal-detail-list');
    listContainer.innerHTML = '';

    if (bookings.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center py-8 px-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-700 text-sm">Laboratorium Bebas / Kosong</h4>
                <p class="text-xs text-slate-500 mt-1">Tidak ada jadwal peminjaman pada tanggal ini. Ruang lab siap diajukan.</p>
            </div>
        `;
    } else {
        bookings.forEach((b) => {
            const isApproved = b.status === 'Disetujui';
            const statusBadge = isApproved
                ? '<span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md font-bold text-[10px]">Disetujui</span>'
                : '<span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-md font-bold text-[10px]">Menunggu Konfirmasi</span>';

            const jamMulai = b.jam_mulai ? String(b.jam_mulai).substring(0, 5) : '';
            const jamSelesai = b.jam_selesai ? String(b.jam_selesai).substring(0, 5) : '';

            const itemCard = document.createElement('div');
            itemCard.className = 'p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5';
            itemCard.innerHTML = `
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-bold text-slate-800 text-sm">${b.ruang ? b.ruang.nama : 'Ruangan'}</span>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 bg-slate-200 text-slate-700 rounded font-semibold">${b.ruang ? b.ruang.kode : ''}</span>
                        </div>
                        <p class="text-xs text-slate-500">📍 ${b.ruang ? b.ruang.lokasi : '-'}</p>
                    </div>
                    ${statusBadge}
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-200/70">
                    <div>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase">Peminjam / Mahasiswa</p>
                        <p class="font-bold text-slate-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            ${b.user ? b.user.name : 'Mahasiswa'}
                        </p>
                        <p class="text-[11px] text-slate-500">${b.user ? b.user.email : '-'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase">Waktu / Jadwal</p>
                        <p class="font-bold text-slate-800 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            ${jamMulai} - ${jamSelesai} WIB
                        </p>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200/70 text-xs">
                    <p class="text-[10px] text-slate-400 font-semibold uppercase">Keperluan Penggunaan</p>
                    <p class="text-slate-700 italic text-xs mt-0.5">"${b.keperluan}"</p>
                </div>
            `;
            listContainer.appendChild(itemCard);
        });
    }

    document.getElementById('modal-cal-detail').classList.remove('hidden');
}

// Render kalender otomatis saat halaman siap
document.addEventListener('DOMContentLoaded', () => {
    renderCalendar();
});
</script>