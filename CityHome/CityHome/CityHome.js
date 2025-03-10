document.addEventListener('DOMContentLoaded', () => {
    fetch('http://localhost:3000/api/tenders')
        .then(response => response.json())
        .then(data => {
            const tenderContainer = document.querySelector('.tender-info');

            data.forEach(tender => {
                const tenderDiv = document.createElement('div');
                tenderDiv.innerHTML = `
                    <h2>${tender.title}</h2>
                    <div class="company-list" style="min-height: 12em;">
                        <strong>List of Companies:</strong>
                        <p>${tender.company1} <br> Bidding: ${tender.bidding1}</p>
                        <p>${tender.company2} <br> Bidding: ${tender.bidding2}</p>
                        <p>${tender.company3} <br> Bidding: ${tender.bidding3}</p>
                    </div>
                    <button class="vote-button" style="display: block; margin-right: auto;">Vote</button>
                    <br>
                    <button class="back-button" style="display: block; margin-right: auto;">Back</button>
                    <p><strong>Bidding price:</strong> ${tender.bidding_price}$</p>
                    <p><strong>Winning company:</strong> ${tender.winning_company}</p>
                    <p><strong>Information about Tender:</strong> ${tender.information}</p>
                `;
                tenderContainer.appendChild(tenderDiv);
            });
        })
        .catch(error => console.error('Error fetching tenders:', error));
});
