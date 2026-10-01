function addSaleRow() {
    const tpl = document.getElementById('saleRowTemplate');
    const target = document.getElementById('saleItems');
    if (!tpl || !target) return;

    const row = tpl.content.cloneNode(true);
    const qtyInput = row.querySelector('.qty-input');
    if (qtyInput) {
        qtyInput.addEventListener('input', updateSaleTotal);
    }
    const select = row.querySelector('.product-select');
    if (select) {
        select.addEventListener('change', updateSaleTotal);
    }
    target.appendChild(row);
    updateSaleTotal();
}

function updateSaleTotal() {
    let total = 0;
    document.querySelectorAll('.sale-row').forEach(row => {
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.qty-input');
        const qty = Math.max(0, parseInt(qtyInput?.value || '0', 10));
        const price = parseFloat(select?.selectedOptions[0]?.dataset.price || '0');
        total += price * qty;
    });

    const el = document.getElementById('saleTotal');
    if (el) {
        el.textContent = total.toLocaleString('pt-PT', { style: 'currency', currency: 'EUR' });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    addSaleRow();

    const sidebar = document.getElementById('sidebar');
    const mobileMenu = document.querySelector('.mobile-menu');
    if (mobileMenu && sidebar) {
        mobileMenu.addEventListener('click', () => sidebar.classList.toggle('open'));
    }

    document.querySelectorAll('.app-alert').forEach((alertEl) => {
        setTimeout(() => {
            alertEl.style.opacity = '0';
            alertEl.style.transform = 'translateY(-6px)';
            setTimeout(() => alertEl.remove(), 260);
        }, 4500);
    });
});
