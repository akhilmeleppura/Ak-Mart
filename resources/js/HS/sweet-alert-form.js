document.addEventListener('submit', function (e) {
  const form = e.target;

  if (form.hasAttribute('data-ajax')) {
    e.preventDefault();

    fetch(form.action, {
      method: form.method,
      body: new FormData(form),
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json'
      }
    })
      .then(res => res.json())
      .then(data => {
        // Show SweetAlert with dynamic icon + message
        Swal.fire({
          icon: data.type || 'info', // success | error | warning | info
          title: data.type === 'success' ? 'Success' : 'Error',
          text: data.message,
          timer: 2000,
          showConfirmButton: true
        }).then(() => {
          // Redirect if provided
          if (data.redirect) {
            window.location.href = data.redirect;
          }
          // Reload if no redirect but success
          else if (data.success) {
            window.location.reload();
          }
        });
      })
      .catch(() => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Something went wrong!'
        });
      });
  }
});
