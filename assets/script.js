const form = document.getElementById('productForm');
const itemsBody = document.getElementById('itemsBody');
const itemsFoot = document.getElementById('itemsFoot');
const lastUpdated = document.getElementById('lastUpdated');

const editModalEl = document.getElementById('editModal');
const editModal = new bootstrap.Modal(editModalEl);
const editFields = {
  id: document.getElementById('edit_id'),
  name: document.getElementById('edit_product_name'),
  qty: document.getElementById('edit_quantity'),
  price: document.getElementById('edit_price'),
};
const saveEditBtn = document.getElementById('saveEditBtn');

async function fetchList() {
  const res = await fetch('api.php?action=list', {cache: 'no-store'});
  const data = await res.json();
  renderTable(data);
}

function formatMoney(n) {
  return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(n);
}

function formatDate(iso) {
  const d = new Date(iso);
  return d.toLocaleString();
}

function renderTable({items, sum_total, server_time}) {
  lastUpdated.textContent = 'Updated ' + new Date(server_time).toLocaleTimeString();
  if (!items || items.length === 0) {
    itemsBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No items yet.</td></tr>`;
    itemsFoot.innerHTML = '';
    return;
  }
  itemsBody.innerHTML = items.map(it => `
    <tr data-id="${it.id}">
      <td>${escapeHtml(it.product_name)}</td>
      <td class="text-end">${it.quantity}</td>
      <td class="text-end">${formatMoney(it.price)}</td>
      <td>${formatDate(it.datetime)}</td>
      <td class="text-end fw-semibold">${formatMoney(it.total)}</td>
      <td class="text-center">
        <button class="btn btn-sm btn-outline-primary" data-action="edit" data-id="${it.id}">Edit</button>
      </td>
    </tr>
  `).join('');

  itemsFoot.innerHTML = `
    <tr class="table-secondary fw-bold">
      <td colspan="4" class="text-end">Sum total</td>
      <td class="text-end">${formatMoney(sum_total)}</td>
      <td></td>
    </tr>
  `;
}

function escapeHtml(str) {
  return str.replace(/[&<>"']/g, m => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
  }[m]));
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(form);
  fd.append('action', 'add');
  const res = await fetch('api.php', { method: 'POST', body: fd });
  const data = await res.json();
  if (data && data.ok) {
    form.reset();
    fetchList();
  } else {
    alert(data.error || 'Failed to add item.');
  }
});

// editing values
itemsBody.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-action="edit"]');
  if (!btn) return;
  const tr = btn.closest('tr');
  const id = tr.getAttribute('data-id');
  const tds = tr.querySelectorAll('td');
  editFields.id.value = id;
  editFields.name.value = tds[0].textContent.trim();
  editFields.qty.value = parseInt(tds[1].textContent.trim(), 10);
  editFields.price.value = Number(tds[2].textContent.replace(/[^0-9.\-]+/g,""));
  editModal.show();
});

saveEditBtn.addEventListener('click', async () => {
  const fd = new FormData();
  fd.append('action', 'edit');
  fd.append('id', editFields.id.value);
  fd.append('product_name', editFields.name.value);
  fd.append('quantity', editFields.qty.value);
  fd.append('price', editFields.price.value);
  const res = await fetch('api.php', { method: 'POST', body: fd });
  const data = await res.json();
  if (data && data.ok) {
    editModal.hide();
    fetchList();
  } else {
    alert(data.error || 'Failed to edit item.');
  }
});

fetchList();
