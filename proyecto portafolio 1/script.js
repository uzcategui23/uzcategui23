document.addEventListener('DOMContentLoaded', () => {
    const navToggler = document.querySelector('[data-nav-toggler]');
    const navbar = document.querySelector('[data-navbar]');
    const navLinks = document.querySelectorAll('[data-nav-link]');
    const menuIcon = navToggler.querySelector('.menu-icon');
    const closeIcon = navToggler.querySelector('.close-icon');
  
    // Toggle navbar visibility
    function toggleNavbar() {
      const isActive = navbar.classList.toggle('active');
      navToggler.setAttribute('aria-expanded', isActive);
      menuIcon.style.display = isActive ? 'none' : 'block';
      closeIcon.style.display = isActive ? 'block' : 'none';
    }
  
    // Close navbar
    function closeNavbar() {
      navbar.classList.remove('active');
      navToggler.setAttribute('aria-expanded', 'false');
      menuIcon.style.display = 'block';
      closeIcon.style.display = 'none';
    }
  
    navToggler.addEventListener('click', toggleNavbar);
  
    navLinks.forEach(link => link.addEventListener('click', closeNavbar));
  
    // Close navbar when clicking outside (mobile)
    document.addEventListener('click', (e) => {
      if (
        navbar.classList.contains('active') &&
        !navbar.contains(e.target) &&
        !navToggler.contains(e.target)
      ) {
        closeNavbar();
      }
    });
  
    // Accessibility: Close navbar with Esc key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && navbar.classList.contains('active')) {
        closeNavbar();
      }
    });
  
    // Contact form (demo, no backend)
    const contactForm = document.querySelector('.contact-form');
    if(contactForm){
      contactForm.addEventListener('submit', function(e){
        e.preventDefault();
        alert('Thank you for contacting us! We will get back to you soon.');
        contactForm.reset();
      });
    }
  });
  