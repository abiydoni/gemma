<?php include 'includes/header.php'; ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-100 py-10 px-2">
  <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-blue-100 relative">
    <h2 class="text-3xl font-extrabold text-blue-700 mb-6 text-center flex items-center justify-center gap-2">
      <i class="fa-solid fa-right-to-bracket text-blue-500"></i> Login
    </h2>
    <form id="form-login" class="space-y-6">
      <div>
        <label class="block text-blue-700 font-semibold mb-1" for="email">Email</label>
        <input type="email" name="email" id="email" required class="w-full px-4 py-3 rounded-lg border border-blue-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-base transition" placeholder="Email">
      </div>
      <div>
        <label class="block text-blue-700 font-semibold mb-1" for="password">Password</label>
        <input type="password" name="password" id="password" required class="w-full px-4 py-3 rounded-lg border border-blue-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-base transition" placeholder="Password">
      </div>
      <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-400 text-white font-bold rounded-full shadow-lg hover:scale-105 hover:shadow-xl transition-all text-lg tracking-wide flex items-center justify-center gap-2">
        <i class="fa-solid fa-right-to-bracket"></i> Login
      </button>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('form-login').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = form.querySelector('button[type=submit]');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Login...';
  const res = await fetch('api/proses_login.php', { method: 'POST', body: formData });
  const data = await res.json();
  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> Login';
  if (data.status === 'ok') {
    Swal.fire({ icon: 'success', title: 'Login Berhasil', text: 'Selamat datang!' }).then(() => {
      window.location.href = 'dashboard/index.php';
    });
  } else {
    Swal.fire({ icon: 'error', title: 'Login Gagal', text: data.msg || 'Email atau password salah.' });
  }
});
</script>
<?php include 'includes/footer.php'; ?> 