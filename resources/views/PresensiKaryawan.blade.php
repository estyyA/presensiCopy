{{-- resources/views/presensiKaryawan.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-md mx-auto bg-white shadow-lg rounded-2xl mt-6 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-400 p-5 flex items-center">
            <img src="https://i.pravatar.cc/80" alt="Profile" class="w-16 h-16 rounded-full border-2 border-white">
            <div class="ml-4 text-white">
                <h2 class="text-lg font-semibold">I Made Sugi Hantara</h2>
                <p class="text-sm">72220562 - Senior UX Designer</p>
            </div>
        </div>

        {{-- Live Attendance --}}
        <div class="text-center py-6">
            <p class="text-gray-500 text-sm">Live Attendance</p>
            <h1 class="text-3xl font-bold text-blue-600">08:34 AM</h1>
            <p class="text-gray-400">Fri, 14 April 2023</p>
        </div>

        {{-- Office Hours --}}
        <div class="text-center border-t border-b py-4">
            <p class="text-gray-600">Office Hours</p>
            <h2 class="text-lg font-semibold">08:00 AM – 05:00 PM</h2>
        </div>

        {{-- Tombol Presensi --}}
        <div class="flex justify-around py-4">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">Masuk</button>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">Keluar</button>
        </div>

        {{-- Attendance History --}}
        <div class="p-5">
            <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                <span class="mr-2">📅</span> Attendance History
            </h3>

            <ul class="space-y-3 text-sm">
                <li class="flex justify-between items-center border-b pb-2">
                    <span class="text-gray-600">Fri, 14 April 2023</span>
                    <div class="text-right">
                        <p><span class="text-blue-600 font-medium">08:00 AM</span> <span class="text-xs text-gray-500">(Yogyakarta)</span></p>
                        <p><span class="text-red-500 font-medium">05:00 PM</span> <span class="text-xs text-gray-500">(Bantul)</span></p>
                    </div>
                </li>
                <li class="flex justify-between items-center border-b pb-2">
                    <span class="text-gray-600">Thu, 13 April 2023</span>
                    <div class="text-right">
                        <p><span class="text-red-500 font-medium">08:45 AM</span> <span class="text-xs text-gray-500">(Sleman)</span></p>
                        <p><span class="text-red-500 font-medium">05:00 PM</span> <span class="text-xs text-gray-500">(Sleman)</span></p>
                    </div>
                </li>
                <li class="flex justify-between items-center border-b pb-2">
                    <span class="text-gray-600">Wed, 12 April 2023</span>
                    <div class="text-right">
                        <p><span class="text-blue-600 font-medium">07:53 AM</span> <span class="text-xs text-gray-500">(Yogyakarta)</span></p>
                        <p><span class="text-gray-600 font-medium">05:00 PM</span> <span class="text-xs text-gray-500">(Yogyakarta)</span></p>
                    </div>
                </li>
            </ul>
        </div>

    </div>

</body>
</html>
