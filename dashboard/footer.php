      </main>
    </div>
  </div>

  <!-- Script SweetAlert -->
  <script>
    function logout() {
      Swal.fire({
        title: 'Logout?',
        text: 'Apakah Anda yakin ingin keluar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '../api/logout.php';
        }
      });
    }
  </script>
  <script>
    // Dropdown user (untuk id dropdownUserBtn & dropdownUserMenu)
    const dropdownUserBtn = document.getElementById('dropdownUserBtn');
    const dropdownUserMenu = document.getElementById('dropdownUserMenu');
    document.addEventListener('click', function(e) {
      if (dropdownUserBtn && dropdownUserMenu) {
        if (dropdownUserBtn.contains(e.target)) {
          dropdownUserMenu.classList.toggle('hidden');
        } else if (!dropdownUserMenu.contains(e.target)) {
          dropdownUserMenu.classList.add('hidden');
        }
      }
    });
    // Handler menu edit profil
    const menuEditProfil = document.getElementById('menu-edit-profil');
    if (menuEditProfil) {
      menuEditProfil.onclick = function(e) {
        e.preventDefault();
        $('#form-user')[0].reset();
        $('#user-id').val('<?= $_SESSION['user_id'] ?? '' ?>');
        $('#user-email').val('<?= $_SESSION['user_email'] ?? '' ?>').prop('readonly', true);
        $('#user-nama').val('<?= $_SESSION['user_nama'] ?? '' ?>');
        if (typeof setRoleDropdown === 'function') setRoleDropdown();
        $('#user-role').val('<?= $_SESSION['user_role'] ?? 'user' ?>');
        $('#modal-title').text('Edit Profil');
        $('#modal-user').removeClass('hidden');
        dropdownUserMenu.classList.add('hidden');
      };
    }
    // Handler menu ubah password
    const menuUbahPassword = document.getElementById('menu-ubah-password');
    if (menuUbahPassword) {
      menuUbahPassword.onclick = function(e) {
        e.preventDefault();
        $('#password-id').val('<?= $_SESSION['user_id'] ?? '' ?>');
        $('#password-baru').val('');
        $('#modal-password').removeClass('hidden');
        dropdownUserMenu.classList.add('hidden');
      };
    }
    // Fetch total siswa
    fetch('api/get_info.php')
      .then(res => res.json())
      .then(data => {
        var el = document.getElementById('totalSiswa');
        if(el && data.total_siswa !== undefined) {
          el.textContent = data.total_siswa;
        }
      });
  </script>
</body>
</html>
