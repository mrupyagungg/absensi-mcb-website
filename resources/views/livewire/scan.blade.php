<div class="w-full">
  @php
    use Illuminate\Support\Carbon;
  @endphp
  @pushOnce('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  @pushOnce('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    let currentMap = document.getElementById('currentMap');
    let map = document.getElementById('map');

    setTimeout(() => {
      toggleMap();
      toggleCurrentMap();
    }, 1000);

    function toggleCurrentMap() {
      const mapIsVisible = currentMap.style.display === "none";
      currentMap.style.display = mapIsVisible ? "block" : "none";
      document.querySelector('#toggleCurrentMap').innerHTML = mapIsVisible ?
        `<x-heroicon-s-chevron-up class="mr-2 h-5 w-5" />` :
        `<x-heroicon-s-chevron-down class="mr-2 h-5 w-5" />`;
    }

    function toggleMap() {
      const mapIsVisible = map.style.display === "none";
      map.style.display = mapIsVisible ? "block" : "none";
    }
  </script>
  @endpushOnce

  @if (!$isAbsence)
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
  @endif

  <div class="flex flex-col gap-4 md:flex-row">
    @if (!$isAbsence)
      <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="mb-5 flex items-center gap-3">
          <div
            class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
            <x-heroicon-o-user class="h-7 w-7" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
              Face Recognition
            </h3>

            <p class="text-sm text-gray-500 dark:text-gray-400">
              Pilih shift dan arahkan wajah ke kamera
            </p>
          </div>
        </div>

        <div class="mb-5">
          <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
            Pilih Shift Kerja
          </label>
          <x-select id="shift"
            class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700"
            wire:model="shift_id" disabled="{{ !is_null($attendance) }}">

            <option value="">
              Pilih Shift
            </option>

            @foreach ($shifts as $shift)
              <option value="{{ $shift->id }}" {{ $shift->id == $shift_id ? 'selected' : '' }}>
                {{ $shift->name }} | {{ $shift->start_time }} - {{ $shift->end_time }}
              </option>
            @endforeach

          </x-select>
          @error('shift_id')
            <x-input-error for="shift" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>

        <div
          class="flex justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-5 dark:border-slate-600 dark:bg-slate-900"
          wire:ignore>

          <div id="faceScanner"
            class="relative h-72 w-72 overflow-hidden rounded-xl bg-black shadow-inner sm:h-80 sm:w-80">
            <video id="faceVideo" autoplay muted playsinline class="h-full w-full object-cover">
            </video>
            <div class="absolute bottom-3 left-0 right-0 text-center">
              <span id="faceStatus" class="rounded-lg bg-black/50 px-3 py-1 text-xs text-white">
                Memuat kamera...
              </span>

            </div>
          </div>
        </div>


        <div class="mt-5 rounded-xl bg-blue-50 p-4 text-sm text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
          <div class="flex gap-2">
            <x-heroicon-o-information-circle class="h-5 w-5 flex-shrink-0" />
            <p>
              Pastikan GPS aktif dan berada di lokasi kerja sebelum melakukan absensi.
            </p>
          </div>
        </div>
      </div>
    @endif
    <div class="w-full space-y-6">

      <div id=" scanner-error"
        class="hidden rounded-xl bg-red-50 p-4 text-sm font-medium text-red-600 dark:bg-red-900/30 dark:text-red-400"
        wire:ignore></div>

      <div id="scanner-result"
        class="hidden rounded-xl bg-green-50 p-4 text-sm font-medium text-green-600 dark:bg-green-900/30 dark:text-green-400">
        {{ $successMsg }}
      </div>

      <div class="grid gap-6 lg:grid-cols-2">

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
          <div class="flex items-center gap-3">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
              <x-heroicon-o-clock class="h-7 w-7" />
            </div>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Waktu Absensi</p>
              <h2 id="datetime" class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ now()->format('d/m/Y H:i:s') }}
              </h2>
            </div>
          </div>
          <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Pastikan waktu dan lokasi sesuai sebelum melakukan absensi.
          </p>
        </div>


        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                <x-heroicon-o-map-pin class="h-7 w-7" />
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lokasi Saat Ini</p>
                <p class="font-semibold text-gray-800 dark:text-white">GPS Location</p>
              </div>
            </div>

            <button onclick="toggleCurrentMap()" id="toggleCurrentMap"
              class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
              Peta
            </button>
          </div>
          @if (!is_null($currentLiveCoords))
            <a href="{{ \App\Helpers::getGoogleMapsUrl($currentLiveCoords[0], $currentLiveCoords[1]) }}" target="_blank"
              class="mt-4 block text-sm font-medium text-blue-600 hover:underline">
              Koordinat :
              {{ $currentLiveCoords[0] }}, {{ $currentLiveCoords[1] }}
            </a>
            <div wire:ignore
              class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-700 dark:bg-slate-700 dark:text-gray-200">
              <p class="mb-2 font-semibold text-gray-800 dark:text-white">
                Detail Alamat
              </p>

              <div id="address" class="mt-3 rounded-xl bg-gray-100 p-3 text-sm text-gray-700 dark:bg-slate-700
                    dark:text-gray-200">
                Mengambil alamat...
              </div>
            </div>
          @else
            <p class="mt-5 text-sm text-red-500">
              Lokasi belum tersedia
            </p>
          @endif
        </div>
      </div>
      <div id="currentMap" class="hidden h-80 w-full rounded-2xl border shadow-sm" wire:ignore></div>
      <div class="grid gap-5 md:grid-cols-3">

        <div
          class="{{ $attendance?->status == 'late' ? 'bg-red-600' : 'bg-blue-600' }} rounded-2xl p-6 text-white shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm opacity-80">Absen Masuk</p>
              <h3 class="mt-3 text-3xl font-bold">
                {{ $attendance?->time_in ? Carbon::parse($attendance->time_in)->format('H:i:s') : '-' }}
              </h3>
              @if ($attendance?->status == 'late')
                <span class="mt-2 block text-sm">⚠ Terlambat</span>
              @endif
            </div>
            <x-heroicon-o-arrow-down-left class="h-10 w-10" />
          </div>
        </div>

        <div class="rounded-2xl bg-orange-500 p-6 text-white shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm opacity-80">Absen Keluar</p>
              <h3 class="mt-3 text-3xl font-bold">
                {{ $attendance?->time_out ? Carbon::parse($attendance->time_out)->format('H:i:s') : '-' }}
              </h3>
            </div> <x-heroicon-o-arrow-up-right class="h-10 w-10" />
          </div>
        </div>

        <button onclick="toggleMap()"
          class="rounded-2xl bg-purple-600 p-6 text-left text-white shadow-lg hover:bg-purple-700">

          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm opacity-80">Koordinat Absensi</p>
              <h3 class="mt-3 text-lg font-bold">
                {{ $attendance ? $attendance->latitude . ', ' . $attendance->longitude : 'Belum Absen' }}
              </h3>
            </div>
            <x-heroicon-o-map-pin class="h-10 w-10" />
          </div>
        </button>
      </div>
      <div id="map" class="hidden h-80 w-full rounded-2xl border shadow-sm" wire:ignore></div>
      <div class="grid gap-4 md:grid-cols-3">

        <a href="{{ route('apply-leave') }}"
          class="flex items-center justify-center gap-3 rounded-xl bg-yellow-500 px-5 py-4 font-semibold text-white shadow hover:bg-yellow-600">
          <x-heroicon-o-envelope-open class="h-6 w-6" />
          Ajukan Izin
        </a>

        <a href="{{ route('attendance-history') }}"
          class="flex items-center justify-center gap-3 rounded-xl bg-blue-600 px-5 py-4 font-semibold text-white shadow hover:bg-blue-700">
          <x-heroicon-o-clock class="h-6 w-6" />
          Riwayat Absen
        </a>

        <a href="{{ route('leave-history') }}"
          class="flex items-center justify-center gap-3 rounded-xl bg-indigo-600 px-5 py-4 font-semibold text-white shadow hover:bg-indigo-700">
          <x-heroicon-o-document-text class="h-6 w-6" />
          Riwayat Izin
        </a>

      </div>

    </div>

  </div>
  {{-- modal error jarak --}}
  <div id="locationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
      <div class="text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
          <x-heroicon-o-x-circle class="h-10 w-10 text-red-600" />
        </div>
        <h3 class="mt-4 text-xl font-bold text-gray-800">
          Absensi Ditolak
        </h3>
        <p id="locationErrorMessage" class="mt-3 text-sm text-gray-600"></p>
        <button onclick="location.reload()" class="mt-4 rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
          Coba Lagi
        </button>
      </div>
    </div>
  </div>


  <script>
    function showLocationModal(message) {
      document.getElementById('locationErrorMessage').innerHTML = message;
      document.getElementById('locationModal').classList.remove('hidden');
    }

    function updateDateTime() {
      let now = new Date();
      let tanggal = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
      });

      let jam = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });

      document.getElementById('datetime').innerHTML =
        `${tanggal}<br><span class="text-blue-600">${jam}</span>`;
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
  </script>
  @script
  <script>
    const errorMsg = document.querySelector('#scanner-error');

    setTimeout(() => { getLocation(); }, 500);

    async function getLocation() {
      if (!navigator.geolocation) {
        setAddress("GPS tidak tersedia");
        return;
      }

      navigator.geolocation.getCurrentPosition(async (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        console.log("Latitude:", lat);
        console.log("Longitude:", lng);

        await $wire.set('currentLiveCoords', [lat, lng]);

        try {
          const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: { 'Accept': 'application/json' }
          });

          const data = await response.json();
          console.log("DATA ALAMAT:", data);

          if (!data.address) {
            setAddress("Alamat tidak ditemukan");
            return;
          }

          const a = data.address;

          let alamat = [
            a.road,
            a.village || a.suburb || a.neighbourhood,
            a.city_district || a.county,
            a.city || a.town || a.regency,
            a.state === "West Java" ? "Jawa Barat" : a.state,
            a.postcode,
            a.country
          ].filter(Boolean).join(', ');

          setAddress(alamat);

        } catch (error) {
          console.error("ERROR ALAMAT:", error);
          setAddress("Gagal mengambil alamat");
        }

      }, () => {
        setAddress("Izin lokasi ditolak");
      }, {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0
      });
    }

    function setAddress(text) {
      const address = document.getElementById('address');

      if (address) {
        address.innerHTML = text;
        console.log("Alamat tampil:", text);
      } else {
        console.log("Element #address tidak ditemukan");
      }
    }

    @if(!$isAbsence)

      const scanner = new Html5Qrcode('scanner');

      const config = {
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
        fps: 15,
        aspectRatio: 1,
        qrbox: { width: 280, height: 280 },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
      };

      async function startScanning() {
        if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
          return scanner.resume();
        }

        await scanner.start({ facingMode: "environment" }, config, onScanSuccess);
      }

      async function onScanSuccess(decodedText) {
        console.log("QR Code:", decodedText);

        if (scanner.getState() === Html5QrcodeScannerState.SCANNING) {
          scanner.pause(true);
        }

        if (!(await checkTime())) {
          scanner.resume();
          return;
        }

        try {
          const result = await $wire.scan(decodedText);

          console.log("HASIL ABSEN:", result);

          if (result === true) {
            onAttendanceSuccess();
            return;
          }

          if (typeof result === 'object' && result.status === 'outside') {
            showLocationModal(result.message);
            return;
          }

          if (typeof result === 'string') {
            if (
              result.toLowerCase().includes('lokasi') ||
              result.toLowerCase().includes('jangkauan') ||
              result.toLowerCase().includes('jarak')
            ) {
              showLocationModal(result);
              return;
            }

            errorMsg.innerHTML = result;
            scanner.resume();
          }

        } catch (error) {
          console.error(error);
          showLocationModal(error.message);
        }
      }

      async function checkTime() {
        const attendance = await $wire.getAttendance();

        if (attendance) {
          const timeIn = new Date(attendance.time_in).valueOf();
          const diff = (Date.now() - timeIn) / (1000 * 3600);

          if (diff <= 1) {
            const time = new Date(attendance.time_in).toLocaleTimeString([], {
              hour: '2-digit',
              minute: '2-digit'
            });

            return confirm(`Anda baru saja absen pada ${time}, apakah ingin absen keluar?`);
          }
        }

        return true;
      }

      function onAttendanceSuccess() {
        scanner.stop();
        errorMsg.innerHTML = '';
        document.querySelector('#scanner-result').classList.remove('hidden');
      }

      const shift = document.querySelector('#shift');

      if (shift) {
        setTimeout(() => {
          if (!shift.value) {
            errorMsg.innerHTML = 'Pilih shift terlebih dahulu';
          } else {
            startScanning();
          }
        }, 1000);

        shift.addEventListener('change', () => {
          if (!shift.value) {
            scanner.pause(true);
            errorMsg.innerHTML = 'Pilih shift terlebih dahulu';
          } else if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
            scanner.resume();
            errorMsg.innerHTML = '';
          }
        });
      }

    @endif

      @if($attendance?->latitude && $attendance?->longitude)

        const attendanceMap = L.map('map').setView([{{ $attendance->latitude }}, {{ $attendance->longitude }}], 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 21
        }).addTo(attendanceMap);

        L.marker([{{ $attendance->latitude }}, {{ $attendance->longitude }}]).addTo(attendanceMap);

        setTimeout(() => {
          attendanceMap.invalidateSize();
        }, 500);

      @endif
  </script>

  @endscript