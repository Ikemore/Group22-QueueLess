function switchTab(tab) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const errorBox = document.getElementById('formError');

    if (errorBox) errorBox.style.display = 'none';

    if (tab === 'register') {
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        loginTab.classList.remove('active');
        registerTab.classList.add('active');
    } else {
        registerForm.classList.add('hidden');
        loginForm.classList.remove('hidden');
        registerTab.classList.remove('active');
        loginTab.classList.add('active');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash === '#register' && document.getElementById('registerForm')) {
        switchTab('register');
    }
});

function toggleShopFields() {
    const role = document.getElementById('userRole').value;
    const shopFields = document.getElementById('shopFields');
    if (role === 'stylist' || role === 'admin') {
        shopFields.classList.remove('hidden');
    } else {
        shopFields.classList.add('hidden');
    }
}

function showError(msg) {
    const errorBox = document.getElementById('formError');
    errorBox.textContent = msg;
    errorBox.style.display = 'block';
}

// Displays a built-in Role 3 confirmation screen so you don't get "File not found"
function showRole3SuccessScreen(fullName, email, role, shopName) {
    const container = document.querySelector('.auth-container');
    const roleLabels = {
        customer: 'Customer ( Customer Dashboard - Role 5 )',
        stylist: 'Barber / Stylist ( Stylist Dashboard - Role 5 )',
        admin: 'Administrator / Owner ( Admin Dashboard - Role 5 )'
    };

    container.innerHTML = `
        <div style="text-align:center; padding: 10px 0;">
            <div style="width:56px; height:56px; background:#22c55e; color:#fff; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:28px; margin-bottom:14px;">✓</div>
            <h2 style="color:#0b1d35; margin-bottom:6px;">Welcome, ${fullName}!</h2>
            <p style="color:#64748b; font-size:14px; margin-bottom:20px;">Role 3 Authentication Successful</p>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; text-align:left; font-size:14px; margin-bottom:20px;">
                <p><strong>Email:</strong> ${email}</p>
                <p><strong>Assigned Role:</strong> ${roleLabels[role] || role}</p>
                ${shopName ? `<p><strong>Shop Name:</strong> ${shopName}</p>` : ''}
                <p style="margin-top:8px; color:#15803d; font-weight:600;">Status: Session Active (Ready for Role 5 Dashboard handoff)</p>
            </div>

            <button type="button" class="btn-submit" onclick="window.location.href='index.html'" style="margin-bottom:10px;">Go to Initial Home Page</button>
            <button type="button" class="btn-submit" onclick="window.location.reload()" style="background:#e2e8f0; color:#0b1d35;">Sign Out</button>
        </div>
    `;
}

function validateLogin() {
    const email = document.getElementById('loginEmail').value.trim();
    const pass = document.getElementById('loginPassword').value;

    if (!email.includes('@') || pass.length < 6) {
        showError('Enter a valid email and a password of at least 6 characters.');
        return false;
    }

    // If running via file:/// in VS Code without a PHP server, show success screen directly
    if (window.location.protocol === 'file:') {
        const savedUser = JSON.parse(localStorage.getItem('queueless_user') || 'null');
        const name = (savedUser && savedUser.email === email) ? savedUser.fullName : email.split('@')[0];
        const role = (savedUser && savedUser.email === email) ? savedUser.role : 'customer';
        const shop = (savedUser && savedUser.email === email) ? savedUser.shopName : '';
        showRole3SuccessScreen(name, email, role, shop);
        return false; // Prevents browser from jumping to missing file
    }
    return true;
}

function validateRegister() {
    const name = document.getElementById('regName').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const role = document.getElementById('userRole').value;
    const shopName = document.getElementById('shopName').value.trim();
    const pass = document.getElementById('regPassword').value;
    const confirm = document.getElementById('regConfirmPassword').value;

    if (name.length < 3) {
        showError('Please enter your full name.');
        return false;
    }
    if ((role === 'stylist' || role === 'admin') && shopName === '') {
        showError('Please enter your Barbershop or Salon name.');
        return false;
    }
    if (pass.length < 6) {
        showError('Password must be at least 6 characters.');
        return false;
    }
    if (pass !== confirm) {
        showError('Passwords do not match.');
        return false;
    }

    // If running via file:/// in VS Code without a PHP server, save & show success screen directly
    if (window.location.protocol === 'file:') {
        localStorage.setItem('queueless_user', JSON.stringify({
            fullName: name,
            email: email,
            role: role,
            shopName: shopName
        }));
        showRole3SuccessScreen(name, email, role, shopName);
        return false; // Prevents browser from jumping to missing file
    }
    return true;
}

function checkTicket() {
    const code = document.getElementById('ticketInput').value.trim().toUpperCase();
    if (!code) return;

    document.getElementById('displayCode').textContent = code;
    document.getElementById('displayPos').textContent = 'Position 2';
    document.getElementById('displayShop').textContent = "Isaac's Barbershop";
    document.getElementById('displayWait').textContent = '⏱ 25 minutes';
}