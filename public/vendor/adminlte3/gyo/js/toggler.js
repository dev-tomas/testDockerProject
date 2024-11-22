let passwordToggler = document.querySelector('.toggle-password');
let passwordInput   = document.querySelector('#password');
let icon            = passwordToggler.querySelector('.fas');
  
passwordToggler.addEventListener('click', (e)=>{
  passwordInput.type = (passwordInput.type == 'password') ? 'text' : 'password';
  if (icon.classList.contains('fa-eye')) {
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    icon.classList.add('fa-eye');
    icon.classList.remove('fa-eye-slash');
  }
});