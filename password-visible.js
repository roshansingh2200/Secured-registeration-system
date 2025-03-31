const passwordInput = document.querySelector('#password');
const passwordVisibilityToggle = document.querySelector('#password-visibility-toggle');

passwordVisibilityToggle.addEventListener('click', function () {
  if (passwordInput.value.trim() !== '') { // Check if password input is not empty
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    passwordVisibilityToggle.innerHTML = type === 'password' ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
  }
});



