// All DOM-dependent code in ONE DOMContentLoaded listener
document.addEventListener("DOMContentLoaded", function () {
  // ========== SIDEBAR FUNCTIONALITY ==========
  const hamburger = document.getElementById('hamburger');
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const closeSidebar = document.getElementById('closeSidebar');

  // Only set up sidebar if elements exist
  if (hamburger && sidebar && sidebarOverlay && closeSidebar) {
    // Open sidebar
    hamburger.addEventListener('click', function() {
      sidebar.classList.add('open');
      sidebarOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    });

    // Close sidebar function
    function closeSidebarFunction() {
      sidebar.classList.remove('open');
      sidebarOverlay.classList.remove('active');
      document.body.style.overflow = 'auto';
    }

    closeSidebar.addEventListener('click', closeSidebarFunction);
    sidebarOverlay.addEventListener('click', closeSidebarFunction);

    // Close sidebar when clicking on nav items
    document.querySelectorAll('.nav-item').forEach(item => {
      item.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        if (href === '#') {
          e.preventDefault();
        } else {
          closeSidebarFunction();
        }
      });
    });

    // Close sidebar with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && sidebar.classList.contains('open')) {
        closeSidebarFunction();
      }
    });
  }

  // Set active navigation item based on current page
  setActiveNavItem();

  // ========== PASSWORD TOGGLE FUNCTIONALITY ==========
  const toggleButtons = document.querySelectorAll(".toggle-password");

  toggleButtons.forEach(button => {
    button.addEventListener("click", function () {
      const targetId = this.getAttribute("data-target");
      const input = document.getElementById(targetId);

      if (input) {
        if (input.type === "password") {
          input.type = "text";
          this.src = "images/eyewithline.png";
        } else {
          input.type = "password";
          this.src = "images/eyewithoutline.png";
        }
      }
    });
  });

  // ========== FORM VALIDATION ==========
  
  // Utility: validate password rules
  function validatePassword(password, confirmPassword) {
    if (password.length < 6) {
      alert("Password must be at least 6 characters long!");
      return false;
    }
    if (confirmPassword !== undefined && password !== confirmPassword) {
      alert("Passwords do not match!");
      return false;
    }
    return true;
  }

  // Registration form handler
  const registrationForm = document.getElementById("registrationForm");
  if (registrationForm) {
    registrationForm.addEventListener("submit", function (e) {
      const password = registrationForm.querySelector("input[name='password']").value;
      const confirmPassword = registrationForm.querySelector("input[name='confirm_password']").value;

      if (!validatePassword(password, confirmPassword)) {
        e.preventDefault();
      }
    });
  }

  // Sign in form handler
  const signInForm = document.getElementById("signInForm");
  if (signInForm) {
    signInForm.addEventListener("submit", function (e) {
      const email = signInForm.querySelector("input[name='email']").value.trim();
      const password = signInForm.querySelector("input[name='password']").value;

      if (!email || !password) {
        alert("Please fill in all fields!");
        e.preventDefault();
      }
    });
  }

  // Forgot password form handler
  const forgotForm = document.getElementById("forgotForm");
  if (forgotForm) {
    forgotForm.addEventListener("submit", function (e) {
      const email = forgotForm.querySelector("input[name='email']").value.trim();
      const password = forgotForm.querySelector("input[name='password']").value;
      const confirmPassword = forgotForm.querySelector("input[name='confirm_password']").value;

      if (!email || !password || !confirmPassword) {
        alert("Please fill in all fields!");
        e.preventDefault();
        return;
      }

      if (!validatePassword(password, confirmPassword)) {
        e.preventDefault();
      }
    });
  }
});

// ========== HELPER FUNCTIONS (outside DOMContentLoaded) ==========

// Set active navigation item based on current page
function setActiveNavItem() {
  const currentPage = window.location.pathname.split('/').pop();
  const navItems = document.querySelectorAll('.nav-item');
  
  navItems.forEach(item => {
    item.classList.remove('active');
    const href = item.getAttribute('href');
    
    if (href === currentPage) {
      item.classList.add('active');
    }
  });
  
  // Handle homepage as default
  if (currentPage === '' || currentPage === 'index.php' || currentPage === 'homepage.php') {
    const homeLink = document.querySelector('a[href="homepage.php"]');
    if (homeLink) {
      homeLink.classList.add('active');
    }
  }
}