function addSaleRow() {
    const tpl = document.getElementById('saleRowTemplate');
    const target = document.getElementById('saleItems');
    if (!tpl || !target) return;
    target.appendChild(tpl.content.cloneNode(true));
    updateSaleTotal();
}
function updateSaleTotal() {
    let total = 0;
    document.querySelectorAll('.sale-row').forEach(row => {
        const select = row.querySelector('.product-select');
        const qty = parseInt(row.querySelector('.qty-input')?.value || '0', 10);
        const price = parseFloat(select?.selectedOptions[0]?.dataset.price || '0');
        total += price * qty;
    });
    const el = document.getElementById('saleTotal');
    if (el) el.textContent = total.toLocaleString('pt-PT', {style:'currency', currency:'EUR'});
}
setTimeout(() => document.querySelectorAll('.app-alert').forEach(el => { el.style.opacity='0'; setTimeout(()=>el.remove(),400); }), 4500);
