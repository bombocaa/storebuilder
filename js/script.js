document.querySelector('.user-btn').addEventListener('click', function() {
    document.querySelector('.dropdown-content').classList.toggle('show');
});

window.onclick = function(event) {
    if (!event.target.matches('.user-btn')) {
        let dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            let openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function updateStock() {
    fetch('fetch_stock.php')
    .then(response => response.json())
    .then(data => {
        for (const [id, stock] of Object.entries(data)) {
            let stockElement = document.getElementById(`stock-${id}`);
            if (stockElement) {
                stockElement.textContent = stock;
            }
        }
    })
    .catch(error => console.error('Error fetching stock:', error));
}

setInterval(updateStock, 5000);
updateStock();
