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
          window.location.href = 'logout.php';
        }
      });
    }
  </script>
  <script>
    // Dropdown user
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    document.addEventListener('click', function(e) {
      if (userMenuBtn && userDropdown) {
        if (userMenuBtn.contains(e.target)) {
          userDropdown.classList.toggle('hidden');
        } else {
          userDropdown.classList.add('hidden');
        }
      }
    });

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
