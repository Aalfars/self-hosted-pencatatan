import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * ============================================================================
 * Fintech Polish: Haptic Feedback Helper
 * ============================================================================
 */
export function triggerHaptic(duration = 12) {
    if (typeof navigator !== 'undefined' && navigator.vibrate) {
        try {
            navigator.vibrate(duration);
        } catch (e) {}
    }
}
window.triggerHaptic = triggerHaptic;

/**
 * ============================================================================
 * Fintech Polish: Indonesian Terbilang (Number to Words)
 * Converts numbers like 75000 -> "Tujuh puluh lima ribu rupiah"
 * ============================================================================
 */
export function terbilang(angka) {
    angka = Math.floor(Math.abs(Number(angka)));
    if (isNaN(angka) || angka === 0) return '';
    const satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

    if (angka < 12) return satuan[angka];
    if (angka < 20) return terbilang(angka - 10) + ' belas';
    if (angka < 100) return terbilang(Math.floor(angka / 10)) + ' puluh ' + terbilang(angka % 10);
    if (angka < 200) return 'seratus ' + terbilang(angka - 100);
    if (angka < 1000) return terbilang(Math.floor(angka / 100)) + ' ratus ' + terbilang(angka % 100);
    if (angka < 2000) return 'seribu ' + terbilang(angka - 1000);
    if (angka < 1000000) return terbilang(Math.floor(angka / 1000)) + ' ribu ' + terbilang(angka % 1000);
    if (angka < 1000000000) return terbilang(Math.floor(angka / 1000000)) + ' juta ' + terbilang(angka % 1000000);
    if (angka < 1000000000000) return terbilang(Math.floor(angka / 1000000000)) + ' milyar ' + terbilang(angka % 1000000000);
    return terbilang(Math.floor(angka / 1000000000000)) + ' triliun ' + terbilang(angka % 1000000000000);
}

export function formatTerbilang(angka) {
    const hasil = terbilang(angka).trim().replace(/\s+/g, ' ');
    if (!hasil) return '';
    return hasil.charAt(0).toUpperCase() + hasil.slice(1) + ' rupiah';
}
window.terbilang = terbilang;
window.formatTerbilang = formatTerbilang;

/**
 * ============================================================================
 * Toast Notification Dispatcher
 * ============================================================================
 */
window.toast = function(message, type = 'success', duration = 3500) {
    window.dispatchEvent(new CustomEvent('financee:toast', {
        detail: { message, type, duration }
    }));
    triggerHaptic(15);
};

/**
 * ============================================================================
 * Currency Helper & Quick Amount Chips Controller
 * ============================================================================
 */
function initFintechHelpers() {
    // Live Rupiah & Terbilang Preview
    document.querySelectorAll('[data-currency-input]').forEach(input => {
        const helperId = input.getAttribute('data-currency-input');
        const helperEl = document.getElementById(helperId);
        if (!helperEl) return;

        function updateHelper() {
            const val = parseFloat(input.value);
            if (val && val > 0) {
                const formatted = 'Rp ' + Math.floor(val).toLocaleString('id-ID');
                const kata = formatTerbilang(val);
                helperEl.innerHTML = `<span class="font-bold text-blue-500 dark:text-blue-400">${formatted}</span> &bull; <span class="capitalize text-slate-500 dark:text-slate-400">${kata}</span>`;
                helperEl.classList.remove('hidden');
            } else {
                helperEl.classList.add('hidden');
                helperEl.innerHTML = '';
            }
        }

        input.addEventListener('input', updateHelper);
        input.addEventListener('change', updateHelper);
        if (input.value) updateHelper();
    });

    // Quick Amount Chips
    document.querySelectorAll('[data-quick-amount]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const amountToAdd = parseInt(btn.getAttribute('data-quick-amount'));
            const targetInputSelector = btn.getAttribute('data-target-input') || '#addModal [name="amount"]';
            const targetInput = document.querySelector(targetInputSelector);
            if (!targetInput) return;

            triggerHaptic(12);

            if (amountToAdd === 0) {
                targetInput.value = '';
            } else {
                const current = parseFloat(targetInput.value) || 0;
                targetInput.value = current + amountToAdd;
            }

            targetInput.dispatchEvent(new Event('input', { bubbles: true }));
            targetInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    // Quick Date Shortcuts
    document.querySelectorAll('[data-quick-date]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const mode = btn.getAttribute('data-quick-date');
            const targetInputSelector = btn.getAttribute('data-target-input') || '#addModal [name="date"]';
            const targetInput = document.querySelector(targetInputSelector);
            if (!targetInput) return;

            triggerHaptic(10);
            const d = new Date();
            if (mode === 'yesterday') {
                d.setDate(d.getDate() - 1);
            }
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            targetInput.value = `${yyyy}-${mm}-${dd}`;
        });
    });
}

// Auto init on load & DOM changes
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFintechHelpers);
} else {
    initFintechHelpers();
}

/**
 * ============================================================================
 * SmoothModal Engine (Tailwind Animated Sheet & Modal Controller)
 * ============================================================================
 */
class SmoothModal {
    constructor(element) {
        this.element = typeof element === 'string' ? document.querySelector(element) : element;
        if (!this.element) return;
        this.isOpen = false;
        this._bindEvents();
    }

    _bindEvents() {
        if (this.element._smoothBound) return;
        this.element._smoothBound = true;

        // Backdrop click to close
        this.element.addEventListener('click', (e) => {
            if (e.target === this.element) {
                this.hide();
            }
        });

        // Close triggers inside modal
        this.element.querySelectorAll('[data-bs-dismiss="modal"], [data-modal-dismiss]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.hide();
            });
        });
    }

    show() {
        if (!this.element || this.isOpen) return;
        this.isOpen = true;

        this.element.style.display = 'flex';
        this.element.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        triggerHaptic(8);

        // Allow DOM reflow then trigger transition
        requestAnimationFrame(() => {
            this.element.classList.add('modal-visible');
        });
    }

    hide() {
        if (!this.element || !this.isOpen) return;
        this.isOpen = false;
        this.element.classList.remove('modal-visible');
        this.element.setAttribute('aria-hidden', 'true');
        triggerHaptic(8);

        setTimeout(() => {
            if (!this.isOpen) {
                this.element.style.display = 'none';
                // Only restore scroll if no other modals are active
                if (!document.querySelector('.modal.modal-visible')) {
                    document.body.classList.remove('overflow-hidden');
                }
            }
        }, 260);
    }

    toggle() {
        this.isOpen ? this.hide() : this.show();
    }

    static getOrCreateInstance(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        if (!element) return null;
        if (!element._smoothModalInstance) {
            element._smoothModalInstance = new SmoothModal(element);
        }
        return element._smoothModalInstance;
    }
}

// Global Bootstrap Compatibility Shim
window.bootstrap = window.bootstrap || {};
window.bootstrap.Modal = SmoothModal;
window.bootstrap.Modal.getOrCreateInstance = SmoothModal.getOrCreateInstance;
window.SmoothModal = SmoothModal;

// Auto-bind click on [data-bs-toggle="modal"] or [data-modal-target]
document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-bs-toggle="modal"], [data-modal-target]');
    if (trigger) {
        e.preventDefault();
        const targetSelector = trigger.getAttribute('data-bs-target') || trigger.getAttribute('data-modal-target');
        if (targetSelector) {
            const modalEl = document.querySelector(targetSelector);
            if (modalEl) {
                const instance = SmoothModal.getOrCreateInstance(modalEl);
                instance.show();
            }
        }
    }
});

// ESC key to close top active modal
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const visibleModals = document.querySelectorAll('.modal.modal-visible');
        if (visibleModals.length > 0) {
            const topModal = visibleModals[visibleModals.length - 1];
            const instance = SmoothModal.getOrCreateInstance(topModal);
            if (instance) instance.hide();
        }
    }
});
