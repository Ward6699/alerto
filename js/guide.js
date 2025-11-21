// Sidebar functionality
const hamburger = document.getElementById('hamburger');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const closeSidebar = document.getElementById('closeSidebar');

// Open sidebar
hamburger.addEventListener('click', function() {
    sidebar.classList.add('open');
    sidebarOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
});

// Close sidebar
function closeSidebarFunction() {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

closeSidebar.addEventListener('click', closeSidebarFunction);
sidebarOverlay.addEventListener('click', closeSidebarFunction);

// Close sidebar when clicking on nav items
document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        if (href === '#') {
            e.preventDefault();
        } else {
            closeSidebarFunction();
        }
    });
});

// Set active navigation item based on current page
function setActiveNavItem() {
    const currentPage = window.location.pathname.split('/').pop();
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item');
    
    navItems.forEach(item => {
        item.classList.remove('active');
        const href = item.getAttribute('href');
        
        if (href === currentPage) {
            item.classList.add('active');
        }
    });
    
    if (currentPage === '' || currentPage === 'index.php' || currentPage === 'homepage.php') {
        const homeLink = document.querySelector('.sidebar-nav a[href="homepage.php"]');
        if (homeLink) {
            homeLink.classList.add('active');
        }
    }
}

// Disaster Navigation Functionality
function initializeDisasterNavigation() {
    const disasterNavItems = document.querySelectorAll('.disaster-nav-item');
    
    disasterNavItems.forEach(item => {
        item.addEventListener('click', function() {
            disasterNavItems.forEach(navItem => navItem.classList.remove('active'));
            this.classList.add('active');
            
            const disasterType = this.querySelector('.disaster-nav-text').textContent.toLowerCase().replace(' ', '-');
            console.log('Switched to:', disasterType);
        });
    });
}

// Close sidebar with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && sidebar.classList.contains('open')) {
        closeSidebarFunction();
    }
});

// Universal Disaster Carousel Configuration
const disasterCarousel = {
  track: document.getElementById('carouselTrack'),
  slides: document.querySelectorAll('.carousel-slide'),
  prevBtn: document.getElementById('prevBtn'),
  nextBtn: document.getElementById('nextBtn'),
  dots: document.querySelectorAll('.dot'),
  currentIndex: 0,
  totalSlides: document.querySelectorAll('.carousel-slide').length,
  autoSlideInterval: null,
  isTransitioning: false
};

// Initialize Disaster Carousel
function initDisasterCarousel() {
  const { track, slides, prevBtn, nextBtn, dots } = disasterCarousel;
  
  if (!track || slides.length === 0) return;

  // Update carousel position and active states
  function updateCarousel() {
    if (disasterCarousel.isTransitioning) return;
    
    disasterCarousel.isTransitioning = true;
    
    const currentIndex = disasterCarousel.currentIndex;
    
    track.style.transform = `translateX(-${currentIndex * 100}%)`;
    
    dots.forEach((dot, index) => {
      dot.classList.toggle('active', index === currentIndex);
    });
    
    prevBtn.disabled = currentIndex === 0;
    nextBtn.disabled = currentIndex === disasterCarousel.totalSlides - 1;
    
    setTimeout(() => {
      disasterCarousel.isTransitioning = false;
    }, 600);
  }

  // Navigate to specific slide
  function goToSlide(index) {
    if (index < 0 || index >= disasterCarousel.totalSlides) return;
    disasterCarousel.currentIndex = index;
    updateCarousel();
    resetAutoSlide();
  }

  // Previous slide
  function prevSlide() {
    if (disasterCarousel.currentIndex > 0) {
      disasterCarousel.currentIndex--;
      updateCarousel();
      resetAutoSlide();
    }
  }

  // Next slide
  function nextSlide() {
    if (disasterCarousel.currentIndex < disasterCarousel.totalSlides - 1) {
      disasterCarousel.currentIndex++;
      updateCarousel();
      resetAutoSlide();
    }
  }

  // Auto-slide functionality
  function startAutoSlide() {
    disasterCarousel.autoSlideInterval = setInterval(() => {
      if (disasterCarousel.currentIndex < disasterCarousel.totalSlides - 1) {
        disasterCarousel.currentIndex++;
      } else {
        disasterCarousel.currentIndex = 0;
      }
      updateCarousel();
    }, 5000);
  }

  function stopAutoSlide() {
    if (disasterCarousel.autoSlideInterval) {
      clearInterval(disasterCarousel.autoSlideInterval);
      disasterCarousel.autoSlideInterval = null;
    }
  }

  function resetAutoSlide() {
    stopAutoSlide();
    startAutoSlide();
  }

  // Event Listeners
  if (prevBtn) {
    prevBtn.addEventListener('click', prevSlide);
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', nextSlide);
  }

  // Dot navigation
  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => goToSlide(index));
  });

  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') prevSlide();
    if (e.key === 'ArrowRight') nextSlide();
  });

  // Touch/Swipe support
  let touchStartX = 0;
  let touchEndX = 0;

  track.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
    stopAutoSlide();
  }, { passive: true });

  track.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
    resetAutoSlide();
  }, { passive: true });

  function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;

    if (Math.abs(diff) > swipeThreshold) {
      if (diff > 0) {
        nextSlide();
      } else {
        prevSlide();
      }
    }
  }

  // Pause on hover
  const carouselContainer = track.closest('.carousel-container');
  if (carouselContainer) {
    carouselContainer.addEventListener('mouseenter', stopAutoSlide);
    carouselContainer.addEventListener('mouseleave', startAutoSlide);
  }

  // Initialize
  updateCarousel();
  startAutoSlide();
}

// Run when page loads
document.addEventListener('DOMContentLoaded', function() {
    setActiveNavItem();
    initializeDisasterNavigation();
    initDisasterCarousel();
    
    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add hover effects to emergency kit items
    const kitItems = document.querySelectorAll('.kit-item');
    kitItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Add click effects to advice cards
    const adviceCards = document.querySelectorAll('.advice-card');
    adviceCards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Add loading animation for images
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.3s ease';
        
        if (img.complete) {
            img.style.opacity = '1';
        } else {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            
            setTimeout(() => {
                if (img.style.opacity === '0') {
                    img.style.opacity = '1';
                }
            }, 2000);
        }
    });
    
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    const sections = document.querySelectorAll('.phase-section, .emergency-kit, .disaster-carousel-section');
    sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });
});

// Disaster Navigation Functionality with Redirection
function initializeDisasterNavigation() {
    const disasterNavItems = document.querySelectorAll('.disaster-nav-item');
    
    disasterNavItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetPage = this.getAttribute('data-page');
            
            // Redirect to the target page
            if (targetPage) {
                window.location.href = targetPage;
            }
        });
    });
}