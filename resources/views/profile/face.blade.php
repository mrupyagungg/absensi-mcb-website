<x-app-layout>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            {{ __('Daftar Wajah') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">

                <h3 class="text-lg font-semibold mb-4">
                    Registrasi Wajah
                </h3>

                <video id="video" width="400" height="300" autoplay muted class="rounded border">
                </video>

                <canvas id="canvas" hidden></canvas>

                <div class="mt-4">
                    <button id="capture" class="px-4 py-2 bg-blue-600 text-white rounded">
                        Ambil Wajah
                    </button>
                </div>

                <p id="status" class="mt-3 text-gray-600">
                    Memuat model...
                </p>

            </div>

        </div>
    </div>


    {{-- Face API --}}
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>


    <script>

        const video = document.getElementById('video');
        const status = document.getElementById('status');
        const canvas = document.getElementById('canvas');


        /*
        |--------------------------------------------------------------------------
        | LOAD MODEL FACE API
        |--------------------------------------------------------------------------
        */

        async function loadModels() {

            try {

                status.innerHTML = "Memuat AI wajah...";
                await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                console.log("SSD loaded");
                await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                console.log("Landmark loaded");
                await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                console.log("Recognition loaded");
                status.innerHTML = "Model siap";
                startCamera();

            } catch (error) {

                console.error(error);

                status.innerHTML =
                    "Model gagal dimuat";

            }

        }


        /*
        |--------------------------------------------------------------------------
        | START CAMERA
        |--------------------------------------------------------------------------
        */

        async function startCamera() {

            try {

                const stream =
                    await navigator.mediaDevices.getUserMedia({

                        video: {
                            facingMode: "user",
                            width: 320,
                            height: 240
                        }

                        audio: false

                    });


                video.srcObject = stream;


                status.innerHTML =
                    "Kamera aktif";


            } catch (error) {

                console.error(error);


                status.innerHTML =
                    "Kamera gagal: " + error.message;

            }

        }



        /*
        |--------------------------------------------------------------------------
        | CAPTURE FACE
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('capture')
            .addEventListener('click', async () => {


                try {


                    status.innerHTML =
                        "Mendeteksi wajah...";



                    const detection =
                        await faceapi
                            .detectSingleFace(
                                video,
                                new faceapi.TinyFaceDetectorOptions({
                                    inputSize: 224,
                                    scoreThreshold: 0.5
                                })
                            )
                            .withFaceLandmarks()
                            .withFaceDescriptor();



                    if (!detection) {


                        status.innerHTML =
                            "Wajah tidak ditemukan";


                        return;

                    }



                    /*
                    | Ambil descriptor 128 angka
                    */

                    const descriptor =
                        Array.from(
                            detection.descriptor
                        );



                    console.log(
                        "Descriptor:",
                        descriptor
                    );



                    /*
                    | Ambil foto dari kamera
                    */

                    canvas.hidden = false;


                    canvas.width =
                        video.videoWidth;


                    canvas.height =
                        video.videoHeight;



                    const ctx =
                        canvas.getContext('2d');



                    ctx.drawImage(
                        video,
                        0,
                        0,
                        canvas.width,
                        canvas.height
                    );

                    const photo =
                        canvas.toDataURL(
                            'image/jpeg',
                            0.7
                        );

                    status.innerHTML =
                        "Menyimpan wajah...";



                    /*
                    | Kirim ke Laravel
                    */

                    const response =
                        await fetch(
                            "{{ route('profile.face.store') }}",
                            {

                                method: "POST",


                                headers: {

                                    "Content-Type":
                                        "application/json",


                                    "X-CSRF-TOKEN":
                                        document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .content

                                },


                                body: JSON.stringify({

                                    descriptor:
                                        descriptor,


                                    photo:
                                        photo

                                })

                            }
                        );

                    const result =
                        await response.json();

                    console.log(result);



                    if (result.success) {


                        status.innerHTML =
                            "✅ Wajah berhasil disimpan";


                    } else {


                        status.innerHTML =
                            "❌ Gagal menyimpan wajah";


                    }

                } catch (error) {


                    console.error(error);


                    status.innerHTML =
                        "Terjadi error";
                }

            });

        loadModels();


    </script>
</x-app-layout>