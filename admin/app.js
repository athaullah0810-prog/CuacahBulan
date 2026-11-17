// Profile Dropdown
const profileBtn = document.getElementById('profileDropdownBtn');
const profileMenu = document.getElementById('profileDropdownMenu');
const dropdownArrow = document.getElementById('dropdownArrow');

if (profileBtn && profileMenu) {
    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('hidden');
        dropdownArrow.classList.toggle('rotate-180');
        if (projectMenu) projectMenu.classList.add('hidden');
    });
}

// Project Switcher Dropdown
const projectBtn = document.getElementById('projectDropdownBtn');
const projectMenu = document.getElementById('projectDropdownMenu');

if (projectBtn && projectMenu) {
    projectBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        projectMenu.classList.toggle('hidden');
        if (profileMenu) profileMenu.classList.add('hidden');
    });
}

document.addEventListener('click', () => {
    if (profileMenu) profileMenu.classList.add('hidden');
    if (dropdownArrow) dropdownArrow.classList.remove('rotate-180');
    if (projectMenu) projectMenu.classList.add('hidden');
});

function switchProject(name, scope) {
    document.getElementById('currentProjectName').textContent = name;
    document.getElementById('currentProjectScope').textContent = scope;
    const isProj1 = name.includes('Projek 1');
    document.getElementById('checkProj1').classList.toggle('hidden', !isProj1);
    document.getElementById('checkProj2').classList.toggle('hidden', isProj1);
    projectMenu.classList.add('hidden');
}

// Mobile Sidebar Toggle
const toggleSidebar = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
if (toggleSidebar && sidebar) {
    toggleSidebar.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('absolute');
        sidebar.classList.toggle('h-full');
    });
}

// Live Clock (RTC / Battery Time Module)
function updateClock() {
    const clockEl = document.getElementById('rtcClock');
    if (clockEl) {
        const now = new Date();
        clockEl.textContent = now.toTimeString().split(' ')[0];
    }
}
setInterval(updateClock, 1000);
updateClock();
