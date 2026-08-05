<div>
    <x-section-title>
        <x-slot name="title">
            Daftar Wajah
        </x-slot>

        <x-slot name="description">
            Daftarkan wajah untuk digunakan saat absensi.
        </x-slot>
    </x-section-title>

    <div class="mt-5">

        @if(auth()->user()->face_photo)

            <div class="flex items-center gap-4">
                <img src="{{ asset('storage/' . auth()->user()->face_photo) }}" class="w-32 h-32 rounded-full object-cover">

                <div>
                    <p class="text-green-600">
                        ✓ Wajah sudah terdaftar
                    </p>
                </div>
            </div>

        @else

            <div>
                <p class="text-red-600">
                    Wajah belum terdaftar
                </p>

                <a href="{{ route('profile.face') }}" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded">
                    Daftar Wajah
                </a>
            </div>

        @endif

    </div>
</div>