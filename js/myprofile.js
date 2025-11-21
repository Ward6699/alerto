// Profile Page JavaScript - Fixed and Optimized

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar functionality
    initializeSidebar();
    
    // Get DOM elements
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelProfileBtn = document.getElementById('cancelProfileBtn');
    const profileForm = document.getElementById('profileForm');
    const profilePicture = document.getElementById('profilePicture');
    const profileImageInput = document.getElementById('profileImageInput');
    const viewPreparednessBtn = document.getElementById('viewPreparednessBtn');
    
    // Form inputs
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const birthdateInput = document.getElementById('birthdate');
    const addressInput = document.getElementById('address');
    
    // State management
    let isEditing = false;
    let originalValues = {};
    
    // Store original values on load
    storeOriginalValues();
    
    // Event listeners
    editProfileBtn.addEventListener('click', toggleEditMode);
    if (cancelProfileBtn) {
        cancelProfileBtn.addEventListener('click', cancelEditingFunction);
    }
    
    profilePicture.addEventListener('click', () => {
        if (isEditing) {
            profileImageInput.click();
        }
    });
    
    profileImageInput.addEventListener('change', handleImageUpload);
    viewPreparednessBtn.addEventListener('click', () => {
        window.location.href = 'prep.php';
    });
    
    // Initialize sidebar functionality
    function initializeSidebar() {
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const closeSidebar = document.getElementById('closeSidebar');

        // Open sidebar
        if (hamburger) {
            hamburger.addEventListener('click', function() {
                sidebar.classList.add('open');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        // Close sidebar
        function closeSidebarFunction() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarFunction);
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebarFunction);
        }

        // Close sidebar when clicking on nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Don't prevent default - let the link work
                closeSidebarFunction();
            });
        });
    }

    // Store original values for cancel functionality
    function storeOriginalValues() {
        originalValues = {
            name: nameInput.value,
            email: emailInput.value,
            phone: phoneInput.value,
            birthdate: birthdateInput.value,
            address: addressInput.value
        };
    }
    
    // Toggle edit mode
    function toggleEditMode() {
        if (isEditing) {
            saveProfile();
        } else {
            enableEditing();
        }
    }
    
    // Enable editing mode
    function enableEditing() {
        isEditing = true;
        
        // Remove readonly attribute from all inputs
        const inputs = profileForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.removeAttribute('readonly');
        });
        
        // Change button text and style
        editProfileBtn.textContent = 'Save Profile';
        editProfileBtn.classList.add('save');
        
        // Show cancel button
        if (cancelProfileBtn) {
            cancelProfileBtn.style.display = 'block';
        }
        
        // Add visual feedback for profile picture
        profilePicture.style.cursor = 'pointer';
        profilePicture.style.borderColor = '#ce0000';
        
        // Show hint
        showPopup('info', 'Click on profile picture to change it');
    }
    
    // Save profile via AJAX
    function saveProfile() {
        // Validate form first
        if (!validateForm()) {
            return;
        }
        
        // Create FormData
        const formData = new FormData();
        formData.append('update_profile', '1');
        formData.append('name', nameInput.value);
        formData.append('email', emailInput.value);
        formData.append('phone', phoneInput.value);
        formData.append('birthdate', birthdateInput.value);
        formData.append('address', addressInput.value);
        
        // Show loading
        editProfileBtn.disabled = true;
        editProfileBtn.textContent = 'Saving...';
        
        // Send AJAX request
        fetch('myprofile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update was successful
                disableEditing();
                storeOriginalValues();
                showPopup('success', data.message);
                
                // Update sidebar name if it changed
                updateSidebarName(nameInput.value);
            } else {
                // Show error
                showPopup('error', data.message);
                editProfileBtn.disabled = false;
                editProfileBtn.textContent = 'Save Profile';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showPopup('error', 'An error occurred. Please try again.');
            editProfileBtn.disabled = false;
            editProfileBtn.textContent = 'Save Profile';
        });
    }
    
    // Disable editing mode
    function disableEditing() {
        isEditing = false;
        
        // Add readonly attribute back to all inputs
        const inputs = profileForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.setAttribute('readonly', 'readonly');
        });
        
        // Change button text and style back
        editProfileBtn.textContent = 'Edit Profile';
        editProfileBtn.classList.remove('save');
        editProfileBtn.disabled = false;
        
        // Hide cancel button
        if (cancelProfileBtn) {
            cancelProfileBtn.style.display = 'none';
        }
        
        // Remove visual feedback
        profilePicture.style.cursor = 'default';
        profilePicture.style.borderColor = 'black';
    }
    
    // Validate form
    function validateForm() {
        let isValid = true;
        
        // Reset all borders
        const inputs = profileForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.style.borderColor = 'black';
        });
        
        // Check required fields
        if (!nameInput.value.trim()) {
            nameInput.style.borderColor = '#dc3545';
            isValid = false;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailInput.value.trim()) {
            emailInput.style.borderColor = '#dc3545';
            isValid = false;
        } else if (!emailRegex.test(emailInput.value)) {
            emailInput.style.borderColor = '#dc3545';
            showPopup('error', 'Please enter a valid email address');
            return false;
        }
        
        // Phone validation
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        if (!phoneInput.value.trim()) {
            phoneInput.style.borderColor = '#dc3545';
            isValid = false;
        } else if (!phoneRegex.test(phoneInput.value)) {
            phoneInput.style.borderColor = '#dc3545';
            showPopup('error', 'Please enter a valid phone number (at least 10 digits)');
            return false;
        }
        
        // Birthdate validation
        if (!birthdateInput.value) {
            birthdateInput.style.borderColor = '#dc3545';
            isValid = false;
        }
        
        // Address validation
        if (!addressInput.value.trim()) {
            addressInput.style.borderColor = '#dc3545';
            isValid = false;
        }
        
        if (!isValid) {
            showPopup('error', 'Please fill in all required fields');
        }
        
        return isValid;
    }
    
    // Handle image upload via AJAX
    function handleImageUpload(event) {
        const file = event.target.files[0];
        
        if (!file) return;
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showPopup('error', 'Please select a valid image file');
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showPopup('error', 'Image size should be less than 5MB');
            return;
        }
        
        // Create FormData
        const formData = new FormData();
        formData.append('profile_picture', file);
        
        // Show loading state
        showPopup('info', 'Uploading image...');
        
        // Send AJAX request
        fetch('myprofile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update profile picture display
                updateProfilePicture(data.profile_picture);
                showPopup('success', data.message);
            } else {
                showPopup('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showPopup('error', 'Failed to upload image. Please try again.');
        });
    }
    
    // Update profile picture in UI
    function updateProfilePicture(imagePath) {
        // Remove existing content
        profilePicture.innerHTML = '';
        
        // Create new image element
        const img = document.createElement('img');
        img.src = imagePath + '?t=' + Date.now(); // Cache bust
        img.alt = 'Profile Picture';
        img.id = 'profileImage';
        profilePicture.appendChild(img);
        
        // Update sidebar avatar too
        const sidebarAvatar = document.querySelector('.user-avatar');
        if (sidebarAvatar) {
            sidebarAvatar.innerHTML = `<img src="${imagePath}?t=${Date.now()}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        }
    }
    
    // Update sidebar name
    function updateSidebarName(newName) {
        const sidebarUserName = document.querySelector('.user-name');
        if (sidebarUserName) {
            sidebarUserName.textContent = newName;
        }
    }
    
    // Show popup modal
    function showPopup(type, message) {
        // Remove existing popup
        const existingPopup = document.querySelector('.profile-popup-overlay');
        if (existingPopup) {
            existingPopup.remove();
        }
        
        // Create popup overlay
        const popupOverlay = document.createElement('div');
        popupOverlay.className = 'profile-popup-overlay';
        popupOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        // Create popup content
        const popupContent = document.createElement('div');
        popupContent.className = 'profile-popup';
        popupContent.style.cssText = `
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            transform: scale(0.8);
            transition: transform 0.3s ease;
        `;
        
        // Create icon
        const icon = document.createElement('div');
        icon.style.cssText = `
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
        `;
        
        if (type === 'success') {
            icon.style.backgroundColor = '#d4edda';
            icon.style.color = '#28a745';
            icon.innerHTML = '✓';
        } else if (type === 'error') {
            icon.style.backgroundColor = '#f8d7da';
            icon.style.color = '#dc3545';
            icon.innerHTML = '✕';
        } else if (type === 'info') {
            icon.style.backgroundColor = '#d1ecf1';
            icon.style.color = '#17a2b8';
            icon.innerHTML = 'ℹ';
        }
        
        // Create message text
        const messageText = document.createElement('div');
        messageText.textContent = message;
        messageText.style.cssText = `
            font-family: 'Nunito', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
        `;
        
        // Create OK button
        const okButton = document.createElement('button');
        okButton.textContent = 'OK';
        okButton.style.cssText = `
            background-color: #ce0000;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 0.8rem 2rem;
            font-family: 'Archivo Narrow', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        `;
        
        // Button hover effect
        okButton.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#a00000';
            this.style.transform = 'translateY(-2px)';
        });
        
        okButton.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '#ce0000';
            this.style.transform = 'translateY(0)';
        });
        
        // Close popup function
        function closePopup() {
            popupOverlay.style.opacity = '0';
            popupContent.style.transform = 'scale(0.8)';
            setTimeout(() => {
                if (popupOverlay.parentNode) {
                    popupOverlay.parentNode.removeChild(popupOverlay);
                }
            }, 300);
        }
        
        // Add event listeners
        okButton.addEventListener('click', closePopup);
        popupOverlay.addEventListener('click', function(e) {
            if (e.target === popupOverlay) {
                closePopup();
            }
        });
        
        // Add elements to popup
        popupContent.appendChild(icon);
        popupContent.appendChild(messageText);
        popupContent.appendChild(okButton);
        popupOverlay.appendChild(popupContent);
        
        // Add to page
        document.body.appendChild(popupOverlay);
        
        // Animate in
        setTimeout(() => {
            popupOverlay.style.opacity = '1';
            popupContent.style.transform = 'scale(1)';
        }, 100);
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(event) {
        // Escape to cancel editing
        if (event.key === 'Escape' && isEditing) {
            cancelEditingFunction();
        }
    });
    
    // Assign cancel editing to button click
    function cancelEditingFunction() {
        if (isEditing) {
            // Restore original values
            nameInput.value = originalValues.name;
            emailInput.value = originalValues.email;
            phoneInput.value = originalValues.phone;
            birthdateInput.value = originalValues.birthdate;
            addressInput.value = originalValues.address;
            
            // Reset form state
            disableEditing();
            
            showPopup('info', 'Changes cancelled');
        }
    }
});