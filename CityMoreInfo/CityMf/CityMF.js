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
                                ${tender.companyList.map(company => `<p>${company.name}<br> Bidding: ${company.bidding}$</p>`).join('')}
                            </div>
                            <button class="back-button" style="display: block; margin-right: auto;">Vote</button>
                            <br>
                            <button class="back-button" style="display: block; margin-right: auto;">Back</button>
                            <p><strong>Bidding price:</strong> ${tender.biddingPrice}$</p>
                            <p><strong>Winning company:</strong> ${tender.winningCompany}</p>
                            <p><strong>Bidding price of ${tender.winningCompany}:</strong> ${tender.winningPrice}$</p>
                            <p><strong>Information about Tender:</strong> ${tender.information}</p>
                            <p><strong>Download documents:</strong>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                </svg>
                            </p>
                        `;
                        tenderContainer.appendChild(tenderDiv);
                    });
                })
                .catch(err => console.error('Error fetching data:', err));
        });
