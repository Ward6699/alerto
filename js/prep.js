// ==================== EMERGENCY CONTACTS CRUD ====================
        
let contacts = [];

// Load contacts on page load
document.addEventListener('DOMContentLoaded', function() {
    loadContacts();
    loadKitItems();
});

// READ - Load all contacts from database
function loadContacts() {
    fetch('prep.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_contacts'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            contacts = data.contacts;
            renderContacts();
        }
    })
    .catch(error => console.error('Error loading contacts:', error));
}

// Render all contacts
function renderContacts() {
    const grid = document.getElementById('contactsGrid');
    grid.innerHTML = '';
    
    contacts.forEach(contact => {
        const entry = createContactEntry(contact);
        grid.appendChild(entry);
    });
}

// Create contact entry HTML element
function createContactEntry(contact) {
    const entry = document.createElement('div');
    entry.className = 'contact-entry';
    entry.dataset.contactId = contact.contact_id;
    
    entry.innerHTML = `
        <div class="entry-header">
            <div class="entry-name-display">
                <span class="name-text">${escapeHtml(contact.name)}</span>
            </div>
            <div class="entry-actions">
                <button class="icon-btn edit-btn" onclick="editContact(${contact.contact_id})" title="Edit">
                    <img src="images/edit.jpg" alt="Edit" onerror="this.style.display='none'; this.parentElement.innerHTML='✏️';">
                </button>
                <button class="icon-btn delete-btn" onclick="deleteContact(${contact.contact_id})" title="Delete">
                    <img src="images/close.jpg" alt="Delete" onerror="this.style.display='none'; this.parentElement.innerHTML='✕';">
                </button>
            </div>
        </div>
        <div class="input-group">
            <input type="tel" value="${escapeHtml(contact.phone_number || '')}" placeholder="Contact Number" class="input-field" readonly>
            <input type="text" value="${escapeHtml(contact.relation || '')}" placeholder="Relationship" class="input-field" readonly>
            <input type="text" value="${escapeHtml(contact.address || '')}" placeholder="Address" class="input-field" readonly>
        </div>
    `;
    
    return entry;
}

// CREATE - Add new contact (UI only, not saved yet)
document.getElementById('addContactBtn').addEventListener('click', function() {
    const grid = document.getElementById('contactsGrid');
    
    const entry = document.createElement('div');
    entry.className = 'contact-entry';
    entry.dataset.new = 'true';
    
    entry.innerHTML = `
        <div class="entry-header">
            <div class="entry-name-display">
                <input type="text" value="NAME" class="name-input" onfocus="if(this.value==='NAME') this.value=''">
            </div>
            <div class="entry-actions">
                <button class="icon-btn delete-btn" onclick="removeNewContact(this)" title="Delete">
                    <img src="images/close.jpg" alt="Delete" onerror="this.style.display='none'; this.parentElement.innerHTML='✕';">
                </button>
            </div>
        </div>
        <div class="input-group">
            <input type="tel" placeholder="Contact Number" class="input-field">
            <input type="text" placeholder="Relationship" class="input-field">
            <input type="text" placeholder="Address" class="input-field">
        </div>
    `;
    
    grid.appendChild(entry);
});

// Remove new contact (before saving)
function removeNewContact(btn) {
    const entry = btn.closest('.contact-entry');
    entry.remove();
}

// UPDATE - Edit contact
function editContact(contactId) {
    const entry = document.querySelector(`[data-contact-id="${contactId}"]`);
    const contact = contacts.find(c => c.contact_id == contactId);
    
    if (!contact) return;
    
    entry.innerHTML = `
        <div class="entry-header">
            <div class="entry-name-display">
                <input type="text" value="${escapeHtml(contact.name)}" class="name-input">
            </div>
            <div class="entry-actions">
                <button class="icon-btn delete-btn" onclick="deleteContact(${contactId})" title="Delete">
                    <img src="images/close.jpg" alt="Delete" onerror="this.style.display='none'; this.parentElement.innerHTML='✕';">
                </button>
            </div>
        </div>
        <div class="input-group">
            <input type="tel" value="${escapeHtml(contact.phone_number || '')}" placeholder="Contact Number" class="input-field">
            <input type="text" value="${escapeHtml(contact.relation || '')}" placeholder="Relationship" class="input-field">
            <input type="text" value="" placeholder="Address" class="input-field">
        </div>
    `;
    
    entry.dataset.editing = 'true';
}

// DELETE - Delete contact
function deleteContact(contactId) {
    if (!confirm('Are you sure you want to delete this contact?')) {
        return;
    }
    
    fetch('prep.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete_contact&contact_id=${contactId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadContacts();
            alert('Contact deleted successfully!');
        } else {
            alert('Error deleting contact: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting contact');
    });
}

// SAVE - Save all contacts
document.getElementById('saveBtn').addEventListener('click', function() {
    const entries = document.querySelectorAll('.contact-entry');
    let savePromises = [];
    let hasError = false;
    
    entries.forEach(entry => {
        const inputs = entry.querySelectorAll('.input-field');
        const nameInput = entry.querySelector('.name-input');
        
        if (!nameInput) return;
        
        const contactName = nameInput.value.trim();
        const contactNumber = inputs[0].value.trim();
        const relationship = inputs[1].value.trim();
        const address = inputs[2].value.trim();
        
        if (!contactName || contactName === 'NAME') {
            alert('Please enter a name for all contacts');
            hasError = true;
            return;
        }
        
        if (entry.dataset.new === 'true') {
            const formData = new URLSearchParams();
            formData.append('action', 'add_contact');
            formData.append('contact_name', contactName);
            formData.append('contact_number', contactNumber);
            formData.append('relationship', relationship);
            formData.append('address', address);
            
            savePromises.push(
                fetch('prep.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                }).then(response => response.json())
            );
        } else if (entry.dataset.editing === 'true') {
            const contactId = entry.dataset.contactId;
            const formData = new URLSearchParams();
            formData.append('action', 'update_contact');
            formData.append('contact_id', contactId);
            formData.append('contact_name', contactName);
            formData.append('contact_number', contactNumber);
            formData.append('relationship', relationship);
            formData.append('address', address);
            
            savePromises.push(
                fetch('prep.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                }).then(response => response.json())
            );
        }
    });
    
    if (hasError) return;
    
    if (savePromises.length === 0) {
        alert('No changes to save');
        return;
    }
    
    Promise.all(savePromises)
        .then(results => {
            console.log('Save results:', results);
            const allSuccess = results.every(r => r.success);
            if (allSuccess) {
                alert('All contacts saved successfully!');
                loadContacts();
            } else {
                const failedErrors = results.filter(r => !r.success);
                console.error('Save errors:', failedErrors);
                alert('Error saving contacts: ' + (failedErrors[0]?.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Error saving contacts: ' + error.message);
        });
});

// ==================== EMERGENCY KIT BUILDER CRUD ====================

let kitItems = [];

// READ - Load all kit items
function loadKitItems() {
    fetch('prep.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_kit_items'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            kitItems = data.items;
            renderKitItems();
        }
    })
    .catch(error => console.error('Error loading kit items:', error));
}

// Render kit items grouped by category
function renderKitItems() {
    const grid = document.getElementById('kitGrid');
    grid.innerHTML = '';
    
    // Group items by category
    const categories = {};
    kitItems.forEach(item => {
        if (!categories[item.category]) {
            categories[item.category] = [];
        }
        categories[item.category].push(item);
    });
    
    // Create category cards
    Object.keys(categories).forEach(categoryName => {
        const categoryCard = createCategoryCard(categoryName, categories[categoryName]);
        grid.appendChild(categoryCard);
    });
}

// Create category card
function createCategoryCard(categoryName, items) {
    const card = document.createElement('div');
    card.className = 'kit-category';
    card.dataset.category = categoryName;
    
    let itemsHtml = '';
    items.forEach(item => {
        itemsHtml += `
            <div class="kit-item-row" data-item-id="${item.item_id}">
                <input type="text" value="${escapeHtml(item.item_name)}" placeholder="Item name" readonly class="item-input">
                <input type="number" value="${item.quantity}" min="1" placeholder="QTY" readonly class="qty-input">
                <button class="icon-btn delete-kit-item" onclick="deleteKitItem(${item.item_id})" title="Delete">
                    <img src="images/close.jpg" alt="Delete" onerror="this.style.display='none'; this.parentElement.innerHTML='✕';">
                </button>
            </div>
        `;
    });
    
    card.innerHTML = `
        <div class="category-header">
            <div class="category-name">
                <span class="category-text">${escapeHtml(categoryName)}</span>
            </div>
            <div class="category-actions">
                <button class="icon-btn edit-category" onclick="editCategory('${escapeHtml(categoryName)}')" title="Edit">
                    <img src="images/edit.jpg" alt="Edit" onerror="this.style.display='none'; this.parentElement.innerHTML='✏️';">
                </button>
                <button class="icon-btn delete-category" onclick="deleteCategory('${escapeHtml(categoryName)}')" title="Delete">
                    <img src="images/close.jpg" alt="Delete" onerror="this.style.display='none'; this.parentElement.innerHTML='✕';">
                </button>
            </div>
        </div>
        <div class="kit-items-list">
            ${itemsHtml}
        </div>
        <button class="add-item-btn" onclick="addItemToCategory('${escapeHtml(categoryName)}')">+ Add Item</button>
    `;
    
    return card;
}

// Edit kit item - switch to edit mode
function editKitItem(itemId) {
    const row = document.querySelector(`[data-item-id="${itemId}"]`);
    const item = kitItems.find(i => i.item_id == itemId);
    
    if (!item) return;
    
    const itemInput = row.querySelector('.item-input');
    const qtyInput = row.querySelector('.qty-input');
    
    itemInput.removeAttribute('readonly');
    qtyInput.removeAttribute('readonly');
    
    row.dataset.editing = 'true';
    
    itemInput.focus();
    itemInput.select();
}

// Add item to category (UI only)
function addItemToCategory(categoryName) {
    const card = document.querySelector(`[data-category="${categoryName}"]`);
    const itemsList = card.querySelector('.kit-items-list');
    
    const itemRow = document.createElement('div');
    itemRow.className = 'kit-item-row';
    itemRow.dataset.new = 'true';
    
    itemRow.innerHTML = `
        <input type="text" placeholder="Item name" class="item-input">
        <input type="number" value="1" min="1" placeholder="QTY" class="qty-input">
        <button class="icon-btn delete-item" onclick="removeNewItem(this)" title="Delete">
            <img src="images/close.jpg" alt="Delete" onerror="this.style.display='none'; this.parentElement.innerHTML='✕';">
        </button>
    `;
    
    itemsList.appendChild(itemRow);
}

// Remove new item (before saving)
function removeNewItem(btn) {
    const row = btn.closest('.kit-item-row');
    row.remove();
}

// Add new category (UI only)
document.getElementById('addCategoryBtn').addEventListener('click', function() {
    const grid = document.getElementById('kitGrid');
    
    const card = document.createElement('div');
    card.className = 'kit-category';
    card.dataset.new = 'true';
    
    card.innerHTML = `
        <div class="category-header">
            <div class="category-name">
                <input type="text" value="Category" placeholder="Category name" class="category-input" onfocus="if(this.value==='Category') this.value=''">
            </div>
            <div class="category-actions">
                <button class="icon-btn delete-category" onclick="removeNewCategory(this)" title="Delete">
                    <img src="images/close.jpg" alt="Delete" onerror="this.style.display='none'; this.parentElement.innerHTML='✕';">
                </button>
            </div>
        </div>
        <div class="kit-items-list"></div>
        <button class="add-item-btn" onclick="addItemToNewCategory(this)">+ Add Item</button>
    `;
    
    grid.appendChild(card);
});

// Remove new category
function removeNewCategory(btn) {
    const card = btn.closest('.kit-category');
    card.remove();
}

// Add item to new category
function addItemToNewCategory(btn) {
    const card = btn.closest('.kit-category');
    const itemsList = card.querySelector('.kit-items-list');
    
    const itemRow = document.createElement('div');
    itemRow.className = 'kit-item-row';
    itemRow.dataset.new = 'true';
    
    itemRow.innerHTML = `
        <input type="text" placeholder="Item name" class="item-input">
        <input type="number" value="1" min="1" placeholder="QTY" class="qty-input">
        <button class="icon-btn delete-item" onclick="removeNewItem(this)" title="Delete">
            <img src="images/close.jpg" alt="Delete" onerror="this.style.display='none'; this.parentElement.innerHTML='✕';">
        </button>
    `;
    
    itemsList.appendChild(itemRow);
}

// Delete kit item
function deleteKitItem(itemId) {
    if (!confirm('Are you sure you want to delete this item?')) {
        return;
    }
    
    fetch('prep.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete_kit_item&item_id=${itemId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadKitItems();
            alert('Item deleted successfully!');
        } else {
            alert('Error deleting item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting item');
    });
}

// Delete category (all items in category)
function deleteCategory(categoryName) {
    if (!confirm('Are you sure you want to delete this entire category?')) {
        return;
    }
    
    const itemsInCategory = kitItems.filter(item => item.category === categoryName);
    let deletePromises = [];
    
    itemsInCategory.forEach(item => {
        deletePromises.push(
            fetch('prep.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_kit_item&item_id=${item.item_id}`
            }).then(response => response.json())
        );
    });
    
    Promise.all(deletePromises)
        .then(() => {
            loadKitItems();
            alert('Category deleted successfully!');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting category');
        });
}

// Edit category - switch to edit mode for category name and all items
function editCategory(categoryName) {
    const card = document.querySelector(`[data-category="${escapeHtml(categoryName)}"]`);
    const categoryText = card.querySelector('.category-text');
    
    if (!categoryText) return;
    
    // Convert category name to input
    const categoryInput = document.createElement('input');
    categoryInput.type = 'text';
    categoryInput.className = 'category-input';
    categoryInput.value = categoryName;
    
    categoryText.parentElement.replaceChild(categoryInput, categoryText);
    card.dataset.editing = 'true';
    card.dataset.oldCategory = categoryName;
    
    // Make all items in this category editable
    const itemRows = card.querySelectorAll('.kit-item-row');
    itemRows.forEach(row => {
        const itemInput = row.querySelector('.item-input');
        const qtyInput = row.querySelector('.qty-input');
        
        if (itemInput && qtyInput) {
            itemInput.removeAttribute('readonly');
            qtyInput.removeAttribute('readonly');
            row.dataset.editing = 'true';
        }
    });
    
    categoryInput.focus();
    categoryInput.select();
}

// SAVE - Save all kit items
document.getElementById('saveKitBtn').addEventListener('click', function() {
    const cards = document.querySelectorAll('.kit-category');
    let savePromises = [];
    let hasError = false;
    
    cards.forEach(card => {
        const categoryInput = card.querySelector('.category-input');
        const categoryText = card.querySelector('.category-text');
        const categoryName = categoryInput ? categoryInput.value.trim() : (categoryText ? categoryText.textContent.trim() : '');
        
        if (!categoryName) {
            alert('Please enter a category name');
            hasError = true;
            return;
        }
        
        const itemRows = card.querySelectorAll('.kit-item-row');
        
        itemRows.forEach(row => {
            const itemInput = row.querySelector('.item-input');
            const qtyInput = row.querySelector('.qty-input');
            const itemName = itemInput.value.trim();
            const quantity = parseInt(qtyInput.value) || 1;
            
            if (!itemName) {
                return; // Skip empty items
            }
            
            if (row.dataset.new === 'true') {
                const formData = new URLSearchParams();
                formData.append('action', 'add_kit_item');
                formData.append('category', categoryName);
                formData.append('item_name', itemName);
                formData.append('quantity', quantity);
                
                savePromises.push(
                    fetch('prep.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: formData
                    }).then(response => response.json())
                );
            } else if (row.dataset.editing === 'true') {
                const itemId = row.dataset.itemId || row.getAttribute('data-item-id');
                if (itemId) {
                    const formData = new URLSearchParams();
                    formData.append('action', 'update_kit_item');
                    formData.append('item_id', itemId);
                    formData.append('category', categoryName);
                    formData.append('item_name', itemName);
                    formData.append('quantity', quantity);
                    
                    savePromises.push(
                        fetch('prep.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: formData
                        }).then(response => response.json())
                    );
                }
            }
        });
    });
    
    if (hasError) return;
    
    if (savePromises.length === 0) {
        alert('No items to save');
        return;
    }
    
    Promise.all(savePromises)
        .then(results => {
            console.log('Kit save results:', results);
            const allSuccess = results.every(r => r.success);
            if (allSuccess) {
                alert('All items saved successfully!');
                loadKitItems();
            } else {
                const failedErrors = results.filter(r => !r.success);
                console.error('Kit save errors:', failedErrors);
                alert('Error saving items: ' + (failedErrors[0]?.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Kit fetch error:', error);
            alert('Error saving items: ' + error.message);
        });
});

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==================== SIDEBAR FUNCTIONALITY ====================

const hamburger = document.getElementById('hamburger');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const closeSidebar = document.getElementById('closeSidebar');

hamburger.addEventListener('click', function() {
    sidebar.classList.add('open');
    sidebarOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
});

function closeSidebarFunction() {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

closeSidebar.addEventListener('click', closeSidebarFunction);
sidebarOverlay.addEventListener('click', closeSidebarFunction);

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

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && sidebar.classList.contains('open')) {
        closeSidebarFunction();
    }
});

// Navigation button functionality
const contactsNav = document.getElementById('contactsNav');
const kitNav = document.getElementById('kitNav');
const contactsSection = document.getElementById('contacts');
const kitSection = document.getElementById('kit');

function removeActiveClasses() {
    contactsNav.classList.remove('active');
    kitNav.classList.remove('active');
}

contactsNav.addEventListener('click', function() {
    removeActiveClasses();
    this.classList.add('active');
    contactsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

kitNav.addEventListener('click', function() {
    removeActiveClasses();
    this.classList.add('active');
    kitSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

window.addEventListener('scroll', function() {
    const contactsTop = contactsSection.offsetTop - 150;
    const kitTop = kitSection.offsetTop - 150;
    const scrollPos = window.pageYOffset;

    if (scrollPos >= kitTop) {
        removeActiveClasses();
        kitNav.classList.add('active');
    } else if (scrollPos >= contactsTop) {
        removeActiveClasses();
        contactsNav.classList.add('active');
    }
});

// Generate PDF functionality
document.getElementById('generatePdfNavBtn').addEventListener('click', function() {
    // Show loading message
    const originalText = this.innerHTML;
    this.innerHTML = '<span style="display: flex; align-items: center; gap: 10px;"><span>Generating PDF...</span></span>';
    this.disabled = true;
    
    // Redirect to generate PDF
    window.location.href = 'generate_pdf.php';
});