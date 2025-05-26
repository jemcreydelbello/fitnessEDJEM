function loadMembers() {
  fetch('handler.php?action=list')
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector("#memberTable tbody");
      tbody.innerHTML = '';
      data.forEach((m, i) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td><img src="${m.photo}" width="50"/></td>
          <td>${m.name}</td>
          <td>${m.number}</td>
          <td>${m.age}</td>
          <td>${m.gender}</td>
          <td>${m.address}</td>
          <td>${m.membership}</td>
          <td class="actions">
            <button class="action-btn btn-edit" onclick="location.href='edit.html?id=${i}'">✏️ Edit</button>
            <button class="action-btn btn-delete" onclick="deleteMember(${i})">🗑 Delete</button>
          </td>
        `;
        tbody.appendChild(row);
      });
    });
}

function deleteMember(id) {
  if (confirm('Are you sure you want to delete this member?')) {
    fetch('handler.php?action=delete&id=' + id)
      .then(res => res.text())
      .then(() => {
        // Redirect with status for toast
        window.location.href = 'table.html?status=deleted';
      });
  }
}

document.getElementById("search").addEventListener("input", function () {
  const query = this.value.toLowerCase();
  const rows = document.querySelectorAll("#memberTable tbody tr");
  rows.forEach(row => {
    const name = row.cells[1].innerText.toLowerCase();
    const number = row.cells[2].innerText.toLowerCase();
    row.style.display = name.includes(query) || number.includes(query) ? "" : "none";
  });
});

loadMembers();
