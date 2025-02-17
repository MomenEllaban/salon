(function () {
    ("use strict");
    // Customizer Setting initialize
    let setting_options = document.querySelector('meta[name="setting_options"]');
    if (setting_options !== null && setting_options !== undefined) {
        setting_options = JSON.parse(setting_options.getAttribute("content"));
    } else {
        setting_options = JSON.parse("{}");
    }

    let saveLocal = document.querySelector('meta[name="setting_local"]')
    let app = document.querySelector('meta[name="app_name"]')?.getAttribute('content') || 'Default App'
    const storeKey = setting_options.storeKey || app.trim().replace(' ', '-').toLowerCase()
    const sessionStorageKey = sessionStorage.getItem(storeKey)
    const localStorageKey = localStorage.getItem(storeKey)
    if (saveLocal !== null) {
        const storageType = saveLocal.getAttribute("content")
        if (storageType == 'none') {
            sessionStorage.removeItem(storeKey)
            localStorage.removeItem(storeKey)
            setting_options.saveLocal = 'none'
        }
    } else {
        if (sessionStorageKey == 'none' && localStorageKey == 'none') {
            sessionStorage.removeItem(storeKey)
            localStorage.removeItem(storeKey)
        }
    }
    document.addEventListener('sidebar_show', function (value) {
        const sidebar = document.querySelector('[data-toggle="main-sidebar"]')
        if(sidebar !== null) {
            if (value.detail.value.length > 0) {
                sidebar.classList.remove('sidebar')
            } else {
                if (!sidebar.classList.contains('sidebar')) {
                    sidebar.classList.add('sidebar')
                }
            }
        }
    })
    const setting = (window.IQSetting = new IQSetting(setting_options));

    // Sidebar type event listener
    $(document).on("sidebar_type", function (e) {
        if (typeof setting !== typeof undefined) {
        const sidebarType = setting.options.setting.sidebar_type.value;
        if(e.detail.value.length !== 0) {
            if(e.detail.currentValue !== 'sidebar-mini' && e.detail.currentValue !== '') {
            if (sidebarType.includes("sidebar-hover") && !e.detail.value.includes("sidebar-mini")) {
                const newTypes = sidebarType;
                newTypes.push("sidebar-mini");
                setting.sidebar_type(newTypes);
            }
            }
        }
        }
    });

    // navbar style event listener
    $(document).on("header_navbar", function () {
        if (typeof setting !== typeof undefined) {
        const headerNavbar = setting.options.setting.header_navbar;
        if (headerNavbar.value == "nav-glass") {
            $(headerNavbar.target).addClass("navs-sticky");
        }
        }
    });

})();
