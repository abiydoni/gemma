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
</body>
</html>

