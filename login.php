<?php include 'includes/header.php'; ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-white to-blue-200">
  <div class="w-full max-w-md mx-auto">
    <!-- Step 1: Pilih Role Login -->
    <div id="login-role-step" class="flex flex-col gap-8 items-center justify-center py-12">
      <div class="text-2xl font-bold text-blue-700 mb-4 text-center">Login Sebagai</div>
      <div class="flex gap-8 w-full justify-center">
        <button type="button" id="btn-role-siswa" class="group flex flex-col items-center justify-center bg-white/70 rounded-2xl shadow-xl px-8 py-8 border-2 border-transparent hover:border-blue-400 hover:scale-105 transition-all duration-300 cursor-pointer">
          <i class="fa-solid fa-user-graduate text-blue-500 text-5xl mb-2 group-hover:text-blue-700 transition-all"></i>
          <span class="font-bold text-blue-700 text-lg">Siswa</span>
        </button>
        <button type="button" id="btn-role-pengajar" class="group flex flex-col items-center justify-center bg-white/70 rounded-2xl shadow-xl px-8 py-8 border-2 border-transparent hover:border-blue-400 hover:scale-105 transition-all duration-300 cursor-pointer">
          <i class="fa-solid fa-chalkboard-user text-blue-500 text-5xl mb-2 group-hover:text-blue-700 transition-all"></i>
          <span class="font-bold text-blue-700 text-lg">Pengajar</span>
        </button>
      </div>
    </div>
    <!-- Step 2: Form Login -->
    <div id="login-form-step" class="hidden animate__animated animate__fadeIn flex flex-col gap-6 items-center justify-center py-12">
      <div class="flex items-center gap-3 mb-6">
        <div id="icon-role-login"></div>
        <span id="label-role-login" class="text-xl font-bold text-blue-700"></span>
      </div>
      <form class="w-full bg-white/80 rounded-2xl shadow-2xl px-8 py-8 flex flex-col gap-5 backdrop-blur-md">
        <div class="flex items-center gap-3 bg-white/70 rounded-lg px-4 py-3 shadow transition-all">
          <i class="fa-solid fa-envelope text-blue-400 text-lg"></i>
          <input type="email" name="email" required placeholder="Email" class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400" />
        </div>
        <div class="flex items-center gap-3 bg-white/70 rounded-lg px-4 py-3 shadow transition-all relative">
          <i class="fa-solid fa-lock text-blue-400 text-lg"></i>
          <input type="password" name="password" id="login-password" required placeholder="Password" class="w-full bg-transparent outline-none focus:ring-0 text-base font-semibold text-blue-900 placeholder-gray-400" />
          <button type="button" id="toggle-password" tabindex="-1" class="absolute right-4 text-blue-400 hover:text-blue-600 bg-transparent"><i class="fa-solid fa-eye"></i></button>
        </div>
        <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-400 text-white font-bold rounded-full mt-2 shadow-lg hover:scale-105 hover:shadow-xl transition-all text-lg tracking-wide flex items-center justify-center gap-2">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
        </button>
      </form>
    </div>
  </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script>
// Step 1: Pilih role
const loginRoleStep = document.getElementById('login-role-step');
const loginFormStep = document.getElementById('login-form-step');
const iconRoleLogin = document.getElementById('icon-role-login');
const labelRoleLogin = document.getElementById('label-role-login');
let selectedRole = '';
document.getElementById('btn-role-siswa').onclick = function() {
  selectedRole = 'Siswa';
  iconRoleLogin.innerHTML = '<i class="fa-solid fa-user-graduate text-blue-500 text-3xl"></i>';
  labelRoleLogin.textContent = 'Login Siswa';
  loginRoleStep.classList.add('hidden');
  loginFormStep.classList.remove('hidden');
};
document.getElementById('btn-role-pengajar').onclick = function() {
  selectedRole = 'Pengajar';
  iconRoleLogin.innerHTML = '<i class="fa-solid fa-chalkboard-user text-blue-500 text-3xl"></i>';
  labelRoleLogin.textContent = 'Login Pengajar';
  loginRoleStep.classList.add('hidden');
  loginFormStep.classList.remove('hidden');
};
// Show/hide password
const togglePassword = document.getElementById('toggle-password');
const inputPassword = document.getElementById('login-password');
togglePassword.onclick = function(e) {
  e.preventDefault();
  if(inputPassword.type === 'password') {
    inputPassword.type = 'text';
    togglePassword.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
  } else {
    inputPassword.type = 'password';
    togglePassword.innerHTML = '<i class="fa-solid fa-eye"></i>';
  }
};
</script>
<?php include 'includes/footer.php'; ?> 