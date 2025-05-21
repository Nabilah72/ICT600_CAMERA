// Select all toggle-password icons
const togglePasswords = document.querySelectorAll('.toggle-password');

togglePasswords.forEach(toggle => {
  toggle.addEventListener('click', () => {
    // Find the related input field in the same input-group container
    const input = toggle.parentElement.querySelector('input');
    if (input) {
      const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
      input.setAttribute('type', type);

      // Toggle icon classes for eye/eye-off
      toggle.querySelector('i').classList.toggle('bxs-show');
      toggle.querySelector('i').classList.toggle('bxs-hide');
    }
  });
});
