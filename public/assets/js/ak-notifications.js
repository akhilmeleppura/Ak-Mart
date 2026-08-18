/**
 * AK-Mart Premium Custom Notification System (AKNotify)
 * Unified, Accessible, Multilingual, and Backward-Compatible
 */

(function (window, document) {
  'use strict';

  const ICONS = {
    success: 'bx bx-check-circle',
    error: 'bx bx-x-circle',
    warning: 'bx bx-error-circle',
    info: 'bx bx-info-circle',
    loading: 'bx bx-loader-alt bx-spin'
  };

  const DEFAULT_DURATIONS = {
    success: 4000,
    info: 5000,
    warning: 6000,
    error: 8000,
    loading: 0
  };

  class AKNotificationEngine {
    constructor() {
      this.position = 'top-right';
      this.maxVisible = 5;
      this.container = null;
      this.queue = [];
      this.activeToasts = [];
      this.currentLoading = null;
      this.init();
    }

    init() {
      if (!this.container) {
        this.container = document.createElement('div');
        this.container.id = 'ak-notification-container';
        this.container.className = this.position;
        this.container.setAttribute('aria-live', 'polite');
        this.container.setAttribute('aria-atomic', 'true');
        document.body.appendChild(this.container);
      }
    }

    setPosition(pos) {
      const allowed = ['top-right', 'top-center', 'bottom-right', 'bottom-center'];
      if (allowed.includes(pos)) {
        this.position = pos;
        if (this.container) {
          this.container.className = pos;
        }
      }
    }

    translate(key, replace = {}) {
      if (typeof window.__ === 'function') {
        return window.__(key, replace);
      }
      return key;
    }

    notify(type, message, title = null, options = {}) {
      if (!this.container) this.init();

      if (this.activeToasts.length >= this.maxVisible) {
        this.queue.push({ type, message, title, options });
        return null;
      }

      const duration = typeof options.duration === 'number' ? options.duration : DEFAULT_DURATIONS[type];
      const autoDismiss = duration > 0;

      const toast = document.createElement('div');
      toast.className = `ak-toast ak-toast-${type}`;
      toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

      const iconClass = options.icon || ICONS[type] || ICONS.info;
      const displayTitle = title ? this.translate(title) : (type === 'loading' ? this.translate('Please wait...') : null);
      const displayMessage = this.translate(message);

      let html = `
        <div class="ak-toast-body">
          <div class="ak-toast-icon-wrap">
            <i class="${iconClass}"></i>
          </div>
          <div class="ak-toast-content">
            ${displayTitle ? `<div class="ak-toast-title">${displayTitle}</div>` : ''}
            <div class="ak-toast-message">${displayMessage}</div>
          </div>
          <button type="button" class="ak-toast-close" aria-label="${this.translate('Close')}">&times;</button>
        </div>
      `;

      if (autoDismiss) {
        html += `
          <div class="ak-toast-progress">
            <div class="ak-toast-progress-bar" style="transition: transform ${duration}ms linear; transform: scaleX(0);"></div>
          </div>
        `;
      } else if (type === 'loading') {
        html += `
          <div class="ak-toast-progress">
            <div class="ak-toast-progress-bar"></div>
          </div>
        `;
      }

      toast.innerHTML = html;

      // Close button handler
      const closeBtn = toast.querySelector('.ak-toast-close');
      closeBtn.addEventListener('click', () => {
        this.dismiss(toast);
      });

      this.container.appendChild(toast);
      this.activeToasts.push(toast);

      // Start progress countdown
      let timerId = null;
      if (autoDismiss) {
        // Trigger CSS transition on next frame
        requestAnimationFrame(() => {
          const bar = toast.querySelector('.ak-toast-progress-bar');
          if (bar) bar.style.transform = 'scaleX(0)';
        });

        timerId = setTimeout(() => {
          this.dismiss(toast);
        }, duration);

        toast.addEventListener('mouseenter', () => {
          clearTimeout(timerId);
          const bar = toast.querySelector('.ak-toast-progress-bar');
          if (bar) bar.style.transition = 'none';
        });

        toast.addEventListener('mouseleave', () => {
          timerId = setTimeout(() => {
            this.dismiss(toast);
          }, 1500);
        });
      }

      return toast;
    }

    dismiss(toast) {
      if (!toast || !toast.parentNode) return;

      toast.classList.add('ak-toast-hiding');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
        this.activeToasts = this.activeToasts.filter(t => t !== toast);

        if (this.queue.length > 0) {
          const next = this.queue.shift();
          this.notify(next.type, next.message, next.title, next.options);
        }
      }, 250);
    }

    clearAll() {
      this.queue = [];
      [...this.activeToasts].forEach(t => this.dismiss(t));
    }

    success(message, title = null, options = {}) {
      return this.notify('success', message, title || this.translate('Success'), options);
    }

    error(message, title = null, options = {}) {
      return this.notify('error', message, title || this.translate('Error'), options);
    }

    warning(message, title = null, options = {}) {
      return this.notify('warning', message, title || this.translate('Warning'), options);
    }

    info(message, title = null, options = {}) {
      return this.notify('info', message, title || this.translate('Information'), options);
    }

    loading(message = 'Processing...', title = null, options = {}) {
      if (this.currentLoading) {
        this.dismiss(this.currentLoading);
      }

      const toast = this.notify('loading', message, title, { ...options, duration: 0 });
      this.currentLoading = toast;

      return {
        update: (newMessage, newTitle = null) => {
          if (toast && toast.parentNode) {
            const msgEl = toast.querySelector('.ak-toast-message');
            if (msgEl) msgEl.innerText = this.translate(newMessage);
            if (newTitle) {
              const titleEl = toast.querySelector('.ak-toast-title');
              if (titleEl) titleEl.innerText = this.translate(newTitle);
            }
          }
        },
        success: (successMessage, successTitle = null) => {
          this.dismiss(toast);
          this.currentLoading = null;
          return this.success(successMessage, successTitle);
        },
        error: (errorMessage, errorTitle = null) => {
          this.dismiss(toast);
          this.currentLoading = null;
          return this.error(errorMessage, errorTitle);
        },
        close: () => {
          this.dismiss(toast);
          this.currentLoading = null;
        }
      };
    }

    confirm(options = {}) {
      return new Promise((resolve) => {
        const title = options.title ? this.translate(options.title) : this.translate('Are you sure?');
        const message = options.message || options.text ? this.translate(options.message || options.text) : this.translate('This action cannot be undone.');
        const confirmText = options.confirmText ? this.translate(options.confirmText) : this.translate('Confirm');
        const cancelText = options.cancelText ? this.translate(options.cancelText) : this.translate('Cancel');
        const type = options.type || 'warning';

        const backdrop = document.createElement('div');
        backdrop.className = 'ak-confirm-backdrop';
        backdrop.setAttribute('role', 'dialog');
        backdrop.setAttribute('aria-modal', 'true');

        let iconColorClass = 'ak-confirm-icon-warning';
        let iconName = 'bx bx-error-alt';
        let confirmBtnClass = 'btn-warning';

        if (type === 'danger' || type === 'error') {
          iconColorClass = 'ak-confirm-icon-danger';
          iconName = 'bx bx-trash';
          confirmBtnClass = 'btn-danger';
        } else if (type === 'info') {
          iconColorClass = 'ak-confirm-icon-info';
          iconName = 'bx bx-help-circle';
          confirmBtnClass = 'btn-primary';
        }

        backdrop.innerHTML = `
          <div class="ak-confirm-modal">
            <div class="ak-confirm-header">
              <div class="ak-confirm-icon-wrap ${iconColorClass}">
                <i class="${iconName}"></i>
              </div>
              <div class="ak-confirm-title">${title}</div>
              <div class="ak-confirm-message">${message}</div>
            </div>
            <div class="ak-confirm-actions">
              <button type="button" class="btn btn-label-secondary ak-btn-cancel">${cancelText}</button>
              <button type="button" class="btn ${confirmBtnClass} ak-btn-confirm">${confirmText}</button>
            </div>
          </div>
        `;

        document.body.appendChild(backdrop);
        requestAnimationFrame(() => {
          backdrop.classList.add('ak-show');
        });

        const cleanUp = (confirmed) => {
          backdrop.classList.remove('ak-show');
          setTimeout(() => {
            if (backdrop.parentNode) {
              backdrop.parentNode.removeChild(backdrop);
            }
            if (confirmed && typeof options.onConfirm === 'function') {
              options.onConfirm();
            } else if (!confirmed && typeof options.onCancel === 'function') {
              options.onCancel();
            }
            resolve({ isConfirmed: confirmed, isDismissed: !confirmed });
          }, 250);
        };

        backdrop.querySelector('.ak-btn-confirm').addEventListener('click', () => cleanUp(true));
        backdrop.querySelector('.ak-btn-cancel').addEventListener('click', () => cleanUp(false));

        backdrop.addEventListener('click', (e) => {
          if (e.target === backdrop) cleanUp(false);
        });

        document.addEventListener('keydown', function escHandler(e) {
          if (e.key === 'Escape') {
            document.removeEventListener('keydown', escHandler);
            cleanUp(false);
          }
        });
      });
    }
  }

  // Instantiate Global Singleton
  window.AKNotify = new AKNotificationEngine();

  // Backward-compatibility bridge for SweetAlert2 & window.alert / window.confirm
  if (typeof window.Swal === 'undefined') {
    window.Swal = {
      fire: function (titleOrOptions, text, icon) {
        if (typeof titleOrOptions === 'object') {
          const opts = titleOrOptions;
          if (opts.showCancelButton || opts.showConfirmButton === true && opts.showCancelButton === true) {
            return window.AKNotify.confirm({
              title: opts.title,
              message: opts.text || opts.html,
              type: opts.icon === 'warning' || opts.icon === 'error' ? 'danger' : 'info',
              confirmText: opts.confirmButtonText || 'Confirm',
              cancelText: opts.cancelButtonText || 'Cancel'
            });
          }

          const msg = opts.text || opts.html || opts.title || '';
          const type = opts.icon || 'info';
          if (type === 'success') window.AKNotify.success(msg, opts.title);
          else if (type === 'error') window.AKNotify.error(msg, opts.title);
          else if (type === 'warning') window.AKNotify.warning(msg, opts.title);
          else window.AKNotify.info(msg, opts.title);

          return Promise.resolve({ isConfirmed: true });
        } else {
          const msg = text || titleOrOptions;
          const type = icon || 'info';
          if (type === 'success') window.AKNotify.success(msg, titleOrOptions);
          else if (type === 'error') window.AKNotify.error(msg, titleOrOptions);
          else if (type === 'warning') window.AKNotify.warning(msg, titleOrOptions);
          else window.AKNotify.info(msg, titleOrOptions);

          return Promise.resolve({ isConfirmed: true });
        }
      }
    };
  }

  // Livewire Support
  document.addEventListener('DOMContentLoaded', () => {
    if (window.Livewire) {
      window.Livewire.on('notify', (data) => {
        const payload = Array.isArray(data) ? data[0] : data;
        if (typeof payload === 'string') {
          window.AKNotify.info(payload);
        } else if (payload && typeof payload === 'object') {
          const type = payload.type || 'info';
          if (typeof window.AKNotify[type] === 'function') {
            window.AKNotify[type](payload.message || payload.text, payload.title);
          } else {
            window.AKNotify.info(payload.message || payload.text, payload.title);
          }
        }
      });
    }

    // Generic window notify event listener
    window.addEventListener('notify', (e) => {
      const d = e.detail || {};
      const type = d.type || 'info';
      if (typeof window.AKNotify[type] === 'function') {
        window.AKNotify[type](d.message || d.text, d.title);
      } else {
        window.AKNotify.info(d.message || d.text, d.title);
      }
    });
  });

})(window, document);
