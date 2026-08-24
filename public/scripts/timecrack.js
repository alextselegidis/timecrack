/* ----------------------------------------------------------------------------
 * Timecrack - Time Tracking Application
 *
 * @package     Timecrack
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://github.com/alextselegidis/timecrack
 * ---------------------------------------------------------------------------- */

// Auto-hide alert

document.querySelectorAll('.toast').forEach(function (toastEl) {
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000,
    });
    toast.show();
});

// Auto-focus create modal input

document.getElementById('create-modal')?.addEventListener('shown.bs.modal', (event) => {
    return event.target.querySelector('input:not([type="hidden"])').focus();
});

// Close other table row dropdowns when one opens

document.addEventListener('show.bs.dropdown', function (event) {
    // Check if this dropdown is inside a table
    const table = event.target.closest('table');
    if (table) {
        // Find all other open dropdowns in this table and close them
        const openDropdowns = table.querySelectorAll('.dropdown-menu.show');
        openDropdowns.forEach(function (dropdown) {
            // Skip if it's the same dropdown being opened
            if (dropdown !== event.target.nextElementSibling) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.previousElementSibling);
                if (dropdownInstance) {
                    dropdownInstance.hide();
                }
            }
        });
    }
});

// Unsaved changes warning for forms
(function () {
    const trackedForms = document.querySelectorAll('form[id]:not([data-no-unsaved-warning])');

    if (!trackedForms.length) {
        return;
    }

    let formChanged = false;
    let isSubmitting = false;

    /**
     * Store initial form state for comparison
     */
    function getFormData(form) {
        const formData = new FormData(form);
        const data = {};
        for (const [key, value] of formData.entries()) {
            if (data[key]) {
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
        return JSON.stringify(data);
    }

    const initialFormStates = new Map();

    trackedForms.forEach(function (form) {
        // Skip forms inside modals (create modals, etc.)
        if (form.closest('.modal')) {
            return;
        }

        // Store initial state
        initialFormStates.set(form, getFormData(form));

        // Listen for changes on form inputs
        form.addEventListener('input', function () {
            const currentState = getFormData(form);
            const initialState = initialFormStates.get(form);
            formChanged = currentState !== initialState;
        });

        form.addEventListener('change', function () {
            const currentState = getFormData(form);
            const initialState = initialFormStates.get(form);
            formChanged = currentState !== initialState;
        });

        // Mark as submitting when form is submitted
        form.addEventListener('submit', function () {
            isSubmitting = true;
        });
    });

    // Also handle submit buttons that reference forms via form attribute
    document.querySelectorAll('button[type="submit"][form]').forEach(function (button) {
        button.addEventListener('click', function () {
            isSubmitting = true;
        });
    });

    // Warn before leaving page with unsaved changes
    window.addEventListener('beforeunload', function (event) {
        if (formChanged && !isSubmitting) {
            event.preventDefault();
            event.returnValue = '';
            return '';
        }
    });
})();

// Label the table cells so that tables can be shown as one card per row on small
// screens (see the mobile section of styles/timecrack.css)

document.querySelectorAll('table.table').forEach(function (table) {
    const headers = Array.from(table.querySelectorAll('thead th')).map(function (th) {
        return th.textContent.replace(/\s+/g, ' ').trim();
    });

    if (!headers.length) {
        return;
    }

    table.classList.add('table-stacked');

    table.querySelectorAll('tbody > tr, tfoot > tr').forEach(function (row) {
        let column = 0;

        Array.from(row.children).forEach(function (cell) {
            const span = cell.colSpan || 1;

            // Cells spanning several columns (totals, empty states) get no label.
            cell.setAttribute('data-label', span > 1 ? '' : headers[column] || '');

            column += span;
        });
    });
});

// Row dropdowns live inside a horizontally scrollable table, which would clip them. The
// fixed positioning strategy takes the menu out of the scroll container.

document.querySelectorAll('.table-responsive .dropdown-toggle').forEach(function (toggle) {
    bootstrap.Dropdown.getOrCreateInstance(toggle, {
        popperConfig: {
            strategy: 'fixed',
        },
    });
});
