<?php

namespace App\Helpers\HS;

class AjaxHelper
{
  /**
   * Generate AJAX JavaScript helper
   * 
   */

  ////  USAGE EXAMPLE WITH LARAVEL BLADE FILE /////
  // {{-- Include the AJAX helper --}}
  // {!! App\Helpers\HS\AjaxHelper::include() !!}

  // {{-- Or with custom options --}}
  // {!! App\Helpers\HS\AjaxHelper::include([
  //     'showLoader' => true,
  //     'autoRedirect' => false,
  //     'timeout' => 60000
  // ]) !!}

  // <script>
  // // Now you can use ajaxRequest anywhere
  // function createUser() {
  //     ajaxRequest('CREATE', '{{ route("users.store") }}', {
  //         name: 'John Doe',
  //         email: 'john@example.com'
  //     });
  // }
  // </script>


  public static function generateScript($options = [])
  {
    $config = array_merge([
      'timeout' => 30000,
      'showLoader' => true,
      'showNotifications' => true,
      'autoRedirect' => true,
      'csrfToken' => csrf_token(),
      'baseUrl' => url('/'),
    ], $options);

    $jsConfig = json_encode($config);

    return "
        <script>
        // AJAX Configuration
        const AJAX_CONFIG = {$jsConfig};

        /**
         * Main AJAX function
         * @param {string} type - CREATE, EDIT, DELETE, READ
         * @param {string} url - Request URL
         * @param {object} data - Request data (optional)
         * @param {object} options - Additional options (optional)
         */
        function ajaxRequest(type, url, data = {}, options = {}) {
            // Determine HTTP method based on type
            const methodMap = {
                'CREATE': 'POST',
                'EDIT': 'PUT',
                'DELETE': 'DELETE',
                'READ': 'GET'
            };

            const method = methodMap[type.toUpperCase()] || 'POST';
            
            // Default options
            const defaultOptions = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': AJAX_CONFIG.csrfToken
                },
                showLoader: AJAX_CONFIG.showLoader,
                showNotifications: AJAX_CONFIG.showNotifications,
                autoRedirect: AJAX_CONFIG.autoRedirect,
                onSuccess: null,
                onError: null,
                onComplete: null
            };

            // Merge options
            const config = { ...defaultOptions, ...options };

            // Show loading
            if (config.showLoader) {
                showAjaxLoader();
            }

            // Prepare request body
            let requestOptions = {
                method: config.method,
                headers: config.headers
            };

            // Add body for non-GET requests
            if (method !== 'GET') {
                if (data instanceof FormData) {
                    // For file uploads, remove content-type and add CSRF token to FormData
                    delete requestOptions.headers['Content-Type'];
                    if (!data.has('_token')) {
                        data.append('_token', AJAX_CONFIG.csrfToken);
                    }
                    requestOptions.body = data;
                } else {
                    requestOptions.body = JSON.stringify(data);
                }
            } else {
                // For GET requests, append data as query parameters
                if (Object.keys(data).length > 0) {
                    const urlObj = new URL(url, AJAX_CONFIG.baseUrl);
                    Object.keys(data).forEach(key => {
                        urlObj.searchParams.append(key, data[key]);
                    });
                    url = urlObj.toString();
                }
            }

            // Make the request
            return fetch(url, requestOptions)
                .then(response => {
                    return response.json().then(data => {
                        if (!response.ok) {
                            throw new Error(data.message || `HTTP \${response.status}: \${response.statusText}`);
                        }
                        return data;
                    });
                })
                .then(responseData => {
                    // Handle success
                    handleAjaxSuccess(responseData, config);
                    
                    // Call custom success callback
                    if (config.onSuccess && typeof config.onSuccess === 'function') {
                        config.onSuccess(responseData);
                    }
                    
                    return responseData;
                })
                .catch(error => {
                    // Handle error
                    handleAjaxError(error, config);
                    
                    // Call custom error callback
                    if (config.onError && typeof config.onError === 'function') {
                        config.onError(error);
                    }
                    
                    throw error;
                })
                .finally(() => {
                    // Hide loading
                    if (config.showLoader) {
                        hideAjaxLoader();
                    }
                    
                    // Call complete callback
                    if (config.onComplete && typeof config.onComplete === 'function') {
                        config.onComplete();
                    }
                });
        }

        /**
         * Handle successful AJAX responses
         */
        function handleAjaxSuccess(data, config) {
            // Show success message
            if (config.showNotifications && data.message) {
                showNotification(data.message, 'success');
            }

            // Handle redirects
            if (config.autoRedirect && data.data && data.data.redirectUrl) {
                setTimeout(() => {
                    window.location.href = data.data.redirectUrl;
                }, 1500);
            }

            // Handle page reload
            if (data.data && data.data.reload) {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        }

        /**
         * Handle AJAX errors
         */
        function handleAjaxError(error, config) {
            console.error('AJAX Error:', error);

            if (config.showNotifications) {
                let message = 'An error occurred. Please try again.';
                
                if (error.message) {
                    message = error.message;
                }

                showNotification(message, 'error');
            }
        }

        /**
         * Show loading indicator
         */
        function showAjaxLoader() {
            // Remove existing loader
            hideAjaxLoader();

            // Create loader overlay
            const loader = document.createElement('div');
            loader.id = 'ajax-loader-overlay';
            loader.innerHTML = `
                <div class=\"ajax-loader-content\">
                    <div class=\"ajax-spinner\"></div>
                    <p>Loading...</p>
                </div>
            `;
            
            // Add styles
            loader.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            `;

            const content = loader.querySelector('.ajax-loader-content');
            content.style.cssText = `
                background: white;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            `;

            const spinner = loader.querySelector('.ajax-spinner');
            spinner.style.cssText = `
                border: 4px solid #f3f3f3;
                border-top: 4px solid #3498db;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
                margin: 0 auto 10px;
            `;

            // Add spinner animation
            if (!document.querySelector('#ajax-spinner-style')) {
                const style = document.createElement('style');
                style.id = 'ajax-spinner-style';
                style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(loader);
        }

        /**
         * Hide loading indicator
         */
        function hideAjaxLoader() {
            const loader = document.querySelector('#ajax-loader-overlay');
            if (loader) {
                loader.remove();
            }
        }

        /**
         * Show notification message
         */
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existing = document.querySelectorAll('.ajax-notification');
            existing.forEach(notification => notification.remove());

            // Create notification
            const notification = document.createElement('div');
            notification.className = 'ajax-notification';
            notification.textContent = message;

            // Styles based on type
            const colors = {
                success: { bg: '#d4edda', border: '#c3e6cb', text: '#155724' },
                error: { bg: '#f8d7da', border: '#f5c6cb', text: '#721c24' },
                info: { bg: '#d1ecf1', border: '#bee5eb', text: '#0c5460' }
            };

            const color = colors[type] || colors.info;

            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: \${color.bg};
                border: 1px solid \${color.border};
                color: \${color.text};
                padding: 15px 20px;
                border-radius: 4px;
                z-index: 10000;
                max-width: 400px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                animation: slideIn 0.3s ease;
            `;

            // Add slide animation
            if (!document.querySelector('#ajax-notification-style')) {
                const style = document.createElement('style');
                style.id = 'ajax-notification-style';
                style.textContent = `
                    @keyframes slideIn {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification && notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        /**
         * Convenience functions for common operations
         */
        function ajaxCreate(url, data, options = {}) {
            return ajaxRequest('CREATE', url, data, options);
        }

        function ajaxEdit(url, data, options = {}) {
            return ajaxRequest('EDIT', url, data, options);
        }

        function ajaxDelete(url, data = {}, options = {}) {
            return ajaxRequest('DELETE', url, data, options);
        }

        function ajaxRead(url, data = {}, options = {}) {
            return ajaxRequest('READ', url, data, options);
        }

        /**
         * Form helper - automatically handle form submissions
         */
        function ajaxForm(formElement, type = 'CREATE', options = {}) {
            if (typeof formElement === 'string') {
                formElement = document.querySelector(formElement);
            }

            if (!formElement) {
                console.error('Form element not found');
                return Promise.reject(new Error('Form element not found'));
            }

            const formData = new FormData(formElement);
            const url = formElement.action || window.location.href;

            return ajaxRequest(type, url, formData, options);
        }
        </script>
        ";
  }

  /**
   * Quick include method for Blade templates
   */
  public static function include($options = [])
  {
    return self::generateScript($options);
  }
}