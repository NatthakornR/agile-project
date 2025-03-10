<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('http://localhost:5000/api/tenders')
            .then(response => response.json())
            .then(data => {
                const tenderContainer = document.querySelector('.tender-info');
                data.forEach(tender => {
                    const tenderDiv = document.createElement('div');
                    tenderDiv.innerHTML = `
                        <h2>${tender.title}</h2>
                        <div class="company-list" style="min-height: 12em;">
                            <strong>List of Companies:</strong>
                            ${tender.companyList.map(company => `<p>${company.name}<br> Bidding: ${company.bidding}</p>`).join('')}
                        </div>
                        <p><strong>Bidding price:</strong> ${tender.biddingPrice}$</p>
                        <p><strong>Winning company:</strong> ${tender.winningCompany}</p>
                        <p><strong>Bidding price of ${tender.winningCompany}:</strong> ${tender.winningPrice}$</p>
                        <p><strong>Information about Tender:</strong> ${tender.information}</p>
                        <button class="back-button" style="display: block; margin-right: auto;">Vote</button>
                        <br>
                        <button class="back-button" style="display: block; margin-right: auto;">Back</button>
                    `;
                    tenderContainer.appendChild(tenderDiv);
                });
            })
            .catch(err => console.error('Error fetching data:', err));
    });
</script>
