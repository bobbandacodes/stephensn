<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';
$page = 'pricing';
$pageTitle = 'Pricing & Donations';

include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow">Support the Ministry</span>
    <h1>Pricing & Donations</h1>
    <p class="lead">Partner with us in spreading the Gospel and advancing God's kingdom</p>
  </div>
</section>

<section class="block">
  <div class="wrap">
    <div class="section-head">
      <h2>Choose Your Support Level</h2>
      <p class="muted">Every contribution helps us reach more souls with the message of hope</p>
    </div>
    
    <div class="pricing-grid">
      <!-- Basic Support -->
      <div class="pricing-card">
        <div class="pricing-header">
          <h3>Partner</h3>
          <div class="price">$25<span>/month</span></div>
        </div>
        <div class="pricing-features">
          <ul>
            <li>✓ Monthly newsletter</li>
            <li>✓ Prayer requests</li>
            <li>✓ Access to sermons</li>
            <li>✓ Ministry updates</li>
          </ul>
        </div>
        <div class="pricing-actions">
          <button class="btn btn-primary" onclick="selectPlan('partner', 25)">Choose Partner</button>
        </div>
      </div>

      <!-- Standard Support -->
      <div class="pricing-card featured">
        <div class="pricing-header">
          <h3>Kingdom Builder</h3>
          <div class="price">$50<span>/month</span></div>
        </div>
        <div class="pricing-features">
          <ul>
            <li>✓ All Partner benefits</li>
            <li>✓ Exclusive teachings</li>
            <li>✓ Early event access</li>
            <li>✓ Personal prayer</li>
            <li>✓ Ministry resources</li>
          </ul>
        </div>
        <div class="pricing-actions">
          <button class="btn btn-primary" onclick="selectPlan('kingdom', 50)">Choose Kingdom Builder</button>
        </div>
      </div>

      <!-- Premium Support -->
      <div class="pricing-card">
        <div class="pricing-header">
          <h3>Legacy Giver</h3>
          <div class="price">$100<span>/month</span></div>
        </div>
        <div class="pricing-features">
          <ul>
            <li>✓ All Kingdom Builder benefits</li>
            <li>✓ One-on-one mentoring</li>
            <li>✓ VIP event invitations</li>
            <li>✓ Prophetic insights</li>
            <li>✓ Legacy recognition</li>
          </ul>
        </div>
        <div class="pricing-actions">
          <button class="btn btn-primary" onclick="selectPlan('legacy', 100)">Choose Legacy Giver</button>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="block alt">
  <div class="wrap">
    <div class="section-head">
      <h2>One-Time Donations</h2>
      <p class="muted">Make a single contribution to support specific ministry needs</p>
    </div>
    
    <div class="donation-options">
      <div class="preset-amounts">
        <button class="btn btn-ghost" onclick="setDonationAmount(25)">$25</button>
        <button class="btn btn-ghost" onclick="setDonationAmount(50)">$50</button>
        <button class="btn btn-ghost" onclick="setDonationAmount(100)">$100</button>
        <button class="btn btn-ghost" onclick="setDonationAmount(250)">$250</button>
        <button class="btn btn-ghost" onclick="setDonationAmount(500)">$500</button>
      </div>
      
      <div class="custom-amount">
        <input type="number" id="customAmount" placeholder="Enter custom amount" min="1">
        <button class="btn btn-primary" onclick="setDonationAmount(document.getElementById('customAmount').value)">Donate</button>
      </div>
    </div>
  </div>
</section>

<!-- Payment Modal -->
<div id="paymentModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Complete Your Support</h3>
      <button class="modal-close" onclick="closePaymentModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div class="payment-summary">
        <p><strong>Plan:</strong> <span id="selectedPlan"></span></p>
        <p><strong>Amount:</strong> $<span id="selectedAmount"></span></p>
        <p><strong>Type:</strong> <span id="paymentType"></span></p>
      </div>
      
      <div class="payment-methods">
        <h4>Choose Payment Method</h4>
        
        <!-- Payment Gateway Option -->
        <div class="payment-option">
          <label>
            <input type="radio" name="paymentMethod" value="gateway" checked>
            <span>Credit/Debit Card (Secure Payment)</span>
          </label>
          <div id="gatewayForm" class="payment-form">
            <div class="form-group">
              <label>Card Number</label>
              <input type="text" placeholder="1234 5678 9012 3456" maxlength="19">
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Expiry</label>
                <input type="text" placeholder="MM/YY" maxlength="5">
              </div>
              <div class="form-group">
                <label>CVV</label>
                <input type="text" placeholder="123" maxlength="3">
              </div>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" placeholder="your@email.com">
            </div>
          </div>
        </div>
        
        <!-- Manual Payment Option -->
        <div class="payment-option">
          <label>
            <input type="radio" name="paymentMethod" value="manual">
            <span>Bank Transfer / Mobile Money</span>
          </label>
          <div id="manualForm" class="payment-form" style="display:none;">
            <div class="manual-instructions">
              <h5>Bank Transfer Details:</h5>
              <p><strong>Bank:</strong> [Your Bank Name]</p>
              <p><strong>Account Name:</strong> Prophet Stephen SN Ministry</p>
              <p><strong>Account Number:</strong> [Account Number]</p>
              <p><strong>Branch:</strong> [Branch Name]</p>
              
              <h5>Mobile Money:</h5>
              <p><strong>MTN:</strong> [MTN Number]</p>
              <p><strong>Airtel:</strong> [Airtel Number]</p>
              
              <div class="form-group">
                <label>Your Name/Reference</label>
                <input type="text" id="payerName" placeholder="Enter your full name">
              </div>
              <div class="form-group">
                <label>Transaction ID (Optional)</label>
                <input type="text" id="transactionId" placeholder="Enter transaction ID">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="processPayment()">Complete Support</button>
      <button class="btn btn-ghost" onclick="closePaymentModal()">Cancel</button>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Thank You for Your Support!</h3>
    </div>
    <div class="modal-body">
      <p>Your contribution has been received. We are grateful for your partnership in spreading the Gospel.</p>
      <div id="paymentConfirmation"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="closeSuccessModal()">Close</button>
    </div>
  </div>
</div>

<style>
.pricing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin: 3rem 0;
}

.pricing-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  text-align: center;
  position: relative;
}

.pricing-card.featured {
  border: 2px solid var(--primary);
  transform: scale(1.05);
}

.pricing-header h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: var(--primary);
}

.price {
  font-size: 3rem;
  font-weight: bold;
  color: var(--dark);
  margin-bottom: 2rem;
}

.price span {
  font-size: 1rem;
  color: var(--muted);
}

.pricing-features ul {
  list-style: none;
  padding: 0;
  margin: 0 0 2rem 0;
}

.pricing-features li {
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border);
}

.pricing-features li:last-child {
  border-bottom: none;
}

.donation-options {
  text-align: center;
}

.preset-amounts {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.custom-amount {
  display: flex;
  justify-content: center;
  gap: 1rem;
  align-items: center;
}

.custom-amount input {
  width: 200px;
  padding: 0.75rem;
  border: 1px solid var(--border);
  border-radius: 8px;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid var(--border);
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.payment-summary {
  background: var(--light);
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.payment-option {
  margin-bottom: 1.5rem;
}

.payment-option label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  margin-bottom: 1rem;
}

.payment-form {
  margin-top: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--border);
  border-radius: 8px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.manual-instructions {
  background: var(--light);
  padding: 1rem;
  border-radius: 8px;
}

.manual-instructions h5 {
  margin-top: 1rem;
  margin-bottom: 0.5rem;
  color: var(--primary);
}

.manual-instructions p {
  margin: 0.25rem 0;
}

@media (max-width: 768px) {
  .pricing-grid {
    grid-template-columns: 1fr;
  }
  
  .pricing-card.featured {
    transform: none;
  }
  
  .preset-amounts {
    flex-direction: column;
  }
  
  .custom-amount {
    flex-direction: column;
  }
  
  .custom-amount input {
    width: 100%;
  }
}
</style>

<script>
let selectedPlan = '';
let selectedAmount = 0;
let paymentType = '';

function selectPlan(plan, amount) {
  selectedPlan = plan;
  selectedAmount = amount;
  paymentType = 'monthly';
  
  document.getElementById('selectedPlan').textContent = plan.charAt(0).toUpperCase() + plan.slice(1);
  document.getElementById('selectedAmount').textContent = amount;
  document.getElementById('paymentType').textContent = 'Monthly Subscription';
  
  document.getElementById('paymentModal').style.display = 'flex';
}

function setDonationAmount(amount) {
  if (!amount || amount < 1) {
    alert('Please enter a valid amount');
    return;
  }
  
  selectedPlan = 'donation';
  selectedAmount = amount;
  paymentType = 'one-time';
  
  document.getElementById('selectedPlan').textContent = 'One-Time Donation';
  document.getElementById('selectedAmount').textContent = amount;
  document.getElementById('paymentType').textContent = 'One-Time';
  
  document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
  document.getElementById('paymentModal').style.display = 'none';
}

function closeSuccessModal() {
  document.getElementById('successModal').style.display = 'none';
}

function processPayment() {
  const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
  
  if (paymentMethod === 'gateway') {
    // Simulate payment gateway processing
    setTimeout(() => {
      showSuccess('Payment processed successfully via credit card');
    }, 2000);
  } else {
    // Manual payment
    const payerName = document.getElementById('payerName').value;
    if (!payerName) {
      alert('Please enter your name for manual payment');
      return;
    }
    
    showSuccess(`Manual payment instructions sent. Please complete the transfer and reference: ${payerName}`);
  }
}

function showSuccess(message) {
  document.getElementById('paymentConfirmation').innerHTML = `<p><strong>${message}</strong></p>`;
  document.getElementById('paymentModal').style.display = 'none';
  document.getElementById('successModal').style.display = 'flex';
}

// Payment method toggle
document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
  radio.addEventListener('change', function() {
    document.getElementById('gatewayForm').style.display = 
      this.value === 'gateway' ? 'block' : 'none';
    document.getElementById('manualForm').style.display = 
      this.value === 'manual' ? 'block' : 'none';
  });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
