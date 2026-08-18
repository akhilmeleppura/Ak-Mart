/**
 * App eCommerce Add Product Script
 */
'use strict';

//Javascript to handle the e-commerce product add page

(function () {
  // Comment editor

  const commentEditor = document.querySelector('.comment-editor');

  if (commentEditor) {
    new Quill(commentEditor, {
      modules: {
        toolbar: '.comment-toolbar'
      },
      placeholder: 'Product Description',
      theme: 'snow'
    });
  }

  // previewTemplate: Updated Dropzone default previewTemplate

  // ! Don't change it unless you really know what you are doing

  const previewTemplate = `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">No preview</span>
    <div class="dz-success-mark"></div>
    <div class="dz-error-mark"></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
    <div class="progress">
      <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
    </div>
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
</div>`;

  // ? Start your code from here

  // Basic Dropzone

  const dropzoneBasic = document.querySelector('#dropzone-basic');
  if (dropzoneBasic) {
    const myDropzone = new Dropzone(dropzoneBasic, {
      url: baseUrl + 'app/ecommerce/media/upload',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      previewTemplate: previewTemplate,
      parallelUploads: 1,
      maxFilesize: 5,
      acceptedFiles: '.jpg,.jpeg,.png,.gif',
      addRemoveLinks: true,
      maxFiles: 1,
      init: function () {
        this.on('success', function (file, response) {
          if (response.success) {
            document.querySelector('#productImage').value = response.filename;
          }
        });
        this.on('removedfile', function (file) {
          const filename = document.querySelector('#productImage').value;
          if (filename) {
            $.ajax({
              url: baseUrl + 'app/ecommerce/media/delete',
              type: 'DELETE',
              data: { filename: filename },
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            });
            document.querySelector('#productImage').value = '';
          }
        });
      }
    });
  }

  // Basic Tags

  const tagifyBasicEl = document.querySelector('#ecommerce-product-tags');
  const TagifyBasic = new Tagify(tagifyBasicEl);

  // Flatpickr

  // Datepicker
  const date = new Date();

  const productDate = document.querySelector('.product-date');

  if (productDate) {
    productDate.flatpickr({
      monthSelectorType: 'static',
      defaultDate: date
    });
  }
})();

//Jquery to handle the e-commerce product add page

$(function () {
  // Select2
  var select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        dropdownParent: $this.parent(),
        placeholder: $this.data('placeholder') // for dynamic placeholder
      });
    });
  }

  var formRepeater = $('.form-repeater');

  // Form Repeater
  // ! Using jQuery each loop to add dynamic id and class for inputs. You may need to improve it based on form fields.
  // -----------------------------------------------------------------------------------------------------------------

  if (formRepeater.length) {
    var row = 2;
    var col = 1;
    formRepeater.on('submit', function (e) {
      e.preventDefault();
    });
    formRepeater.repeater({
      show: function () {
        var fromControl = $(this).find('.form-control, .form-select');
        var formLabel = $(this).find('.form-label');

        fromControl.each(function (i) {
          var id = 'form-repeater-' + row + '-' + col;
          $(fromControl[i]).attr('id', id);
          $(formLabel[i]).attr('for', id);
          col++;
        });

        row++;
        $(this).slideDown();
        $('.select2-container').remove();
        $('.select2.form-select').select2({
          placeholder: 'Placeholder text'
        });
        $('.select2-container').css('width', '100%');
        $('.form-repeater:first .form-select').select2({
          dropdownParent: $(this).parent(),
          placeholder: 'Placeholder text'
        });
        $('.position-relative .select2').each(function () {
          $(this).select2({
            dropdownParent: $(this).closest('.position-relative')
          });
        });
      }
    });
  }

  // Variants logic
  const variantsContainer = $('#variants-container');
  const addVariantBtn = $('#add-variant');
  let variantIndex = $('.variant-row').length;

  if (addVariantBtn.length) {
    addVariantBtn.on('click', function() {
      const html = `
        <div class="variant-row row mb-4 border-bottom pb-4">
          <div class="col-md-3">
            <label class="form-label">Attribute</label>
            <input type="text" name="variants[${variantIndex}][name]" class="form-control" placeholder="Size">
          </div>
          <div class="col-md-3">
            <label class="form-label">Value</label>
            <input type="text" name="variants[${variantIndex}][value]" class="form-control" placeholder="XL">
          </div>
          <div class="col-md-2">
            <label class="form-label">Price</label>
            <input type="number" name="variants[${variantIndex}][price]" class="form-control" placeholder="Price">
          </div>
          <div class="col-md-2">
            <label class="form-label">Qty</label>
            <input type="number" name="variants[${variantIndex}][qty]" class="form-control" placeholder="Qty">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-label-danger remove-variant">Remove</button>
          </div>
        </div>
      `;
      variantsContainer.append(html);
      variantIndex++;
    });
  }

  variantsContainer.on('click', '.remove-variant', function() {
    $(this).closest('.variant-row').remove();
  });

  // AI Generator Logic
  $('#btn-generate-ai').on('click', function() {
    const title = $('#ecommerce-product-name').val();
    const btn = $(this);
    
    if (!title) {
      alert(window.__('Please enter a product title first.') || 'Please enter a product title first.');
      return;
    }

    const originalText = btn.html();
    const generatingText = window.__('Generating...') || 'Generating...';
    btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ' + generatingText);
    btn.prop('disabled', true);

    const currentLocale = (window.AK_I18N && window.AK_I18N.locale) ? window.AK_I18N.locale : 'en';

    $.ajax({
      url: baseUrl + 'app/ecommerce/ai/generate',
      type: 'POST',
      data: {
        title: title,
        locale: currentLocale,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      success: function(response) {
        if (response.success) {
          // Auto-fill fields
          $('textarea[name="description"]').val(response.data.description);
          $('#meta-title').val(response.data.meta_title);
          $('textarea[name="meta_description"]').val(response.data.meta_description);
          
          // Show a simple toast or alert
          alert(window.__('Content generated successfully!') || 'Content generated successfully!');
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function() {
        alert(window.__('An error occurred while generating content. Please check your API configuration.') || 'An error occurred while generating content. Please check your API configuration.');
      },
      complete: function() {
        btn.html(originalText);
        btn.prop('disabled', false);
      }
    });
  });
});
