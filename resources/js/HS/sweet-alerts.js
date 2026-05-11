document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('addActionForm');

  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
      .then(async res => {
        let data;
        try {
          data = await res.json();
        } catch (err) {
          throw new Error('Invalid JSON response from server');
        }

        if (!data || typeof data !== 'object') {
          throw new Error('Unexpected server response');
        }

        // Default type if missing
        const type = data.type || (data.success ? 'success' : 'error');
        const message = data.message || 'No message from server.';

        if (data.success) {
          // Hide modal
          const modalEl = document.getElementById('addAccountModal');
          const modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();

          // Show alert after modal fades out
          setTimeout(() => {
            Swal.fire({
              icon: type,
              title: type.charAt(0).toUpperCase() + type.slice(1),
              text: message
            }).then(() => location.reload());
          }, 300);
        } else {
          Swal.fire({
            icon: type,
            title: type.charAt(0).toUpperCase() + type.slice(1),
            text: message
          });
        }
      })
      .catch(err => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: err.message || 'Something went wrong.'
        });
      });
  });
});
