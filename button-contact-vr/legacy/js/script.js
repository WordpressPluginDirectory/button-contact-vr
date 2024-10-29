// js admin color picker
jQuery(document).ready(function($){
    $('.my-color-field').wpColorPicker();
    
     function validatePhoneNumber(input) {
      var phoneNumberPattern = /^\d{8,12}$/; // Regular expression to match a phone number with 8 to 12 digits
      if (input.value.trim() !== '' && !phoneNumberPattern.test(input.value.trim())) {
        input.classList.add('invalid');
        return false;
      } else {
        input.classList.remove('invalid');
        return true;
      }
    }

    var phoneInputs = document.querySelectorAll('input[name="pzf_phone"], input[name="pzf_phone2"], input[name="pzf_phone3"]');
    phoneInputs.forEach(function(input) {
      input.addEventListener('change', function() {
        validatePhoneNumber(this);
      });
    });

    document.getElementById('form-button-contact').addEventListener('submit', function(event) {
      var allValid = true;
      phoneInputs.forEach(function(input) {
        if (!validatePhoneNumber(input)) {
          allValid = false;
        }
      });
      if (!allValid) {
        event.preventDefault(); // Ngăn không cho gửi form nếu có bất kỳ trường nào không hợp lệ
        alert('Please correct the invalid phone numbers.');
      }
    });
    
    
    
    function validateURL(input) {
      var urlPattern = /^(ftp|http|https):\/\/[^ "]+$/; // Regular expression to match a URL
      if (input.value.trim() !== '' && !urlPattern.test(input.value.trim())) {
        input.classList.add('invalid');
        return false;
      } else {
        input.classList.remove('invalid');
        return true;
      }
    }
    var urlInputs = document.querySelectorAll('input[name="pzf_telegram"], input[name="pzf_instagram"], input[name="pzf_youtube"], input[name="pzf_tiktok"], input[name="pzf_linkfanpage"]');
    urlInputs.forEach(function(input) {
      input.addEventListener('change', function() {
        validateURL(this);
      });
    });
    document.getElementById('form-button-contact').addEventListener('submit', function(event) {
      var allValid = true;
      urlInputs.forEach(function(input) {
        if (!validateURL(input)) {
          allValid = false;
        }
      });
      if (!allValid) {
        event.preventDefault(); // Ngăn không cho gửi form nếu có bất kỳ trường nào không hợp lệ
        alert('Please correct the invalid URLs.');
      }
    });
});