<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Bimbel Gemma</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen text-gray-800">
  <header class="w-full bg-blue-700 shadow z-10 flex items-center justify-between px-6 py-3">
    <div class="flex items-center gap-3">
      <img src="../assets/img/logo4.png" alt="Logo" class="w-8 h-8 object-contain rounded-full bg-white p-1">
      <span class="text-white font-extrabold text-lg tracking-wide">Dashboard</span>
    </div>
    <div class="flex items-center gap-3">
      <i class="fa-solid fa-user-circle text-white text-2xl"></i>
      <span class="text-white font-semibold hidden sm:inline">Admin</span>
      <a href="logout.php" class="px-4 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white font-bold rounded-full shadow hover:scale-105 hover:shadow-xl transition flex items-center gap-2 ml-2">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </div>
  </header>
  <main class="md:ml-64 transition-all duration-300">