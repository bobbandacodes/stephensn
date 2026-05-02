<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Only admins can access
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_plan':
                $stmt = db()->prepare("INSERT INTO pricing_plans (name, slug, description, price, billing_cycle, features, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['slug'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['billing_cycle'],
                    json_encode(explode("\n", $_POST['features'])),
                    $_POST['sort_order'] ?? 0
                ]);
                $success = "Pricing plan added successfully!";
                break;
                
            case 'update_plan':
                $stmt = db()->prepare("UPDATE pricing_plans SET name = ?, description = ?, price = ?, billing_cycle = ?, features = ?, is_active = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['billing_cycle'],
                    json_encode(explode("\n", $_POST['features'])),
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['sort_order'] ?? 0,
                    $_POST['id']
                ]);
                $success = "Pricing plan updated successfully!";
                break;
                
            case 'delete_plan':
                $stmt = db()->prepare("DELETE FROM pricing_plans WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $success = "Pricing plan deleted successfully!";
                break;
        }
    }
}

// Get all pricing plans
$plans = db()->query("SELECT * FROM pricing_plans ORDER BY sort_order ASC, name ASC")->fetchAll();

// Get payments statistics
$totalRevenue = db()->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'")->fetch()['total'] ?? 0;
$monthlyRevenue = db()->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE)")->fetch()['total'] ?? 0;
$recentPayments = db()->query("SELECT * FROM payments ORDER BY created_at DESC LIMIT 10")->fetchAll();

include __DIR__ . '/_layout.php';
?>

<div class="admin-header">
    <h1>Pricing Management</h1>
    <p>Manage pricing plans, subscriptions, and payments</p>
</div>

<?php if (isset($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Revenue</h3>
        <div class="stat-value">$<?= number_format($totalRevenue, 2) ?></div>
    </div>
    <div class="stat-card">
        <h3>Monthly Revenue</h3>
        <div class="stat-value">$<?= number_format($monthlyRevenue, 2) ?></div>
    </div>
    <div class="stat-card">
        <h3>Active Plans</h3>
        <div class="stat-value"><?= count(array_filter($plans, fn($p) => $p['is_active'])) ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Payments</h3>
        <div class="stat-value"><?= db()->query("SELECT COUNT(*) as count FROM payments")->fetch()['count'] ?></div>
    </div>
</div>

<!-- Pricing Plans -->
<div class="admin-section">
    <div class="section-header">
        <h2>Pricing Plans</h2>
        <button class="btn btn-primary" onclick="showAddPlanModal()">Add New Plan</button>
    </div>
    
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Billing</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plans as $plan): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($plan['name']) ?></strong>
                        <br><small class="muted"><?= htmlspecialchars($plan['description']) ?></small>
                    </td>
                    <td>$<?= number_format($plan['price'], 2) ?></td>
                    <td><?= ucfirst($plan['billing_cycle']) ?></td>
                    <td>
                        <span class="status-badge <?= $plan['is_active'] ? 'active' : 'inactive' ?>">
                            <?= $plan['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td><?= $plan['sort_order'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="editPlan(<?= $plan['id'] ?>)">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deletePlan(<?= $plan['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Payments -->
<div class="admin-section">
    <div class="section-header">
        <h2>Recent Payments</h2>
        <a href="payments.php" class="btn btn-secondary">View All</a>
    </div>
    
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Donor</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentPayments as $payment): ?>
                <tr>
                    <td><?= date('M j, Y H:i', strtotime($payment['created_at'])) ?></td>
                    <td><?= htmlspecialchars($payment['donor_name'] ?: 'Anonymous') ?></td>
                    <td>$<?= number_format($payment['amount'], 2) ?></td>
                    <td><?= ucfirst($payment['payment_method']) ?></td>
                    <td>
                        <span class="status-badge <?= $payment['status'] ?>">
                            <?= ucfirst($payment['status']) ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="viewPayment(<?= $payment['id'] ?>)">View</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Plan Modal -->
<div id="planModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Pricing Plan</h3>
            <button class="modal-close" onclick="closePlanModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="planForm">
                <input type="hidden" id="planId" name="id">
                <input type="hidden" name="action" value="add_plan">
                
                <div class="form-group">
                    <label>Plan Name</label>
                    <input type="text" name="name" id="planName" required>
                </div>
                
                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" id="planSlug" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="planDescription" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" name="price" id="planPrice" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Billing Cycle</label>
                        <select name="billing_cycle" id="planBillingCycle">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="one-time">One-Time</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Features (one per line)</label>
                    <textarea name="features" id="planFeatures" rows="5" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" id="planSortOrder" value="0">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" id="planActive" checked>
                            Active
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="savePlan()">Save Plan</button>
            <button class="btn btn-secondary" onclick="closePlanModal()">Cancel</button>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stat-card h3 {
    margin: 0 0 0.5rem 0;
    color: var(--muted);
    font-size: 0.9rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.status-badge.completed {
    background: #d4edda;
    color: #155724;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge.failed {
    background: #f8d7da;
    color: #721c24;
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
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
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
    border-top: 1px solid #eee;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
</style>

<script>
function showAddPlanModal() {
    document.getElementById('modalTitle').textContent = 'Add Pricing Plan';
    document.getElementById('planForm').reset();
    document.getElementById('planId').value = '';
    document.querySelector('input[name="action"]').value = 'add_plan';
    document.getElementById('planModal').style.display = 'flex';
}

function editPlan(id) {
    // This would fetch plan data and populate the form
    // For now, just show the modal
    document.getElementById('modalTitle').textContent = 'Edit Pricing Plan';
    document.getElementById('planId').value = id;
    document.querySelector('input[name="action"]').value = 'update_plan';
    document.getElementById('planModal').style.display = 'flex';
}

function deletePlan(id) {
    if (confirm('Are you sure you want to delete this pricing plan?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_plan">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closePlanModal() {
    document.getElementById('planModal').style.display = 'none';
}

function savePlan() {
    const form = document.getElementById('planForm');
    const formData = new FormData(form);
    
    // Convert form data to URL-encoded string
    const params = new URLSearchParams(formData);
    
    fetch(window.location.href, {
        method: 'POST',
        body: params
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    });
}

function viewPayment(id) {
    // Implement payment viewing functionality
    alert('Payment details view - ID: ' + id);
}
</script>

<?php include __DIR__ . '/_layout_footer.php'; ?>
