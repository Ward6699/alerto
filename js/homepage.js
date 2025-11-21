// Update current date
function updateDate() {
    const now = new Date();
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    const dateString = now.toLocaleDateString('en-US', options).toUpperCase();
    document.getElementById('current-date').textContent = dateString;
}

// Update date on page load
updateDate();

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
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Only prevent default for placeholder links (#)
        if (href === '#') {
            e.preventDefault();
        } else {
            // For real links, just close the sidebar and let navigation happen
            closeSidebarFunction();
        }
    });
});

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

// Run when page loads
document.addEventListener('DOMContentLoaded', setActiveNavItem);

// Close sidebar with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && sidebar.classList.contains('open')) {
        closeSidebarFunction();
    }
});
// Smooth scrolling for anchor links
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