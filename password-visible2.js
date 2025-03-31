const passwordInput1 = document.querySelector('#password1');
const passwordVisibilityToggle1 = document.querySelector('#password-visibility-toggle1');

passwordVisibilityToggle1.addEventListener('click', function () {
  if (passwordInput1.value.trim() !== '') { // Check if password input is not empty
    const type = passwordInput1.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput1.setAttribute('type', type);
    passwordVisibilityToggle1.innerHTML = type === 'password' ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
  }
});