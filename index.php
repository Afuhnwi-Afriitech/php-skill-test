<?php
// index.php
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coalition Backend skill test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body class="bg-light">
    <div class="container py-4">
      <div class="row">
        <div class="col-12 col-lg-10 mx-auto">
          <div class="d-flex align-items-center mb-3">
            <h1 class="h3 mb-0">Backend skill test</h1>
          </div>

          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <form id="productForm" class="row g-3" autocomplete="off">
                <div class="col-12 col-md-6">
                  <label for="product_name" class="form-label">Product name</label>
                  <input type="text" class="form-control" id="product_name" name="product_name" required maxlength="120" placeholder="e.g. Plantains">
                </div>
                <div class="col-6 col-md-3">
                  <label for="quantity" class="form-label">Quantity in stock</label>
                  <input type="number" min="0" step="1" class="form-control" id="quantity" name="quantity" required>
                </div>
                <div class="col-6 col-md-3">
                  <label for="price" class="form-label">Price per item</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" min="0" step="0.01" class="form-control" id="price" name="price" required>
                  </div>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary" type="submit">
                    <span class="me-1">Add item</span>
                  </button>
                  <a class="btn btn-outline-secondary ms-2" href="data/data.json" target="_blank">View JSON</a>
                  <a class="btn btn-outline-secondary ms-2" href="data/data.xml" target="_blank">View XML</a>
                </div>
              </form>
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Submitted Data</h2>
                <small id="lastUpdated" class="text-muted"></small>
              </div>
              <div class="table-responsive">
                <table class="table table-striped align-middle" id="itemsTable">
                  <thead class="table-light">
                    <tr>
                      <th scope="col">Product name</th>
                      <th scope="col" class="text-end">Quantity in stock</th>
                      <th scope="col" class="text-end">Price per item</th>
                      <th scope="col">Datetime submitted</th>
                      <th scope="col" class="text-end">Total value number</th>
                      <th scope="col" class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="itemsBody">
                    <tr><td colspan="6" class="text-center text-muted">No items yet.</td></tr>
                  </tbody>
                  <tfoot id="itemsFoot"></tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Editing -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editForm" class="row g-3">
              <input type="hidden" id="edit_id">
              <div class="col-12">
                <label for="edit_product_name" class="form-label">Product name</label>
                <input type="text" class="form-control" id="edit_product_name" required maxlength="120">
              </div>
              <div class="col-6">
                <label for="edit_quantity" class="form-label">Quantity in stock</label>
                <input type="number" min="0" step="1" class="form-control" id="edit_quantity" required>
              </div>
              <div class="col-6">
                <label for="edit_price" class="form-label">Price per item</label>
                <input type="number" min="0" step="0.01" class="form-control" id="edit_price" required>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveEditBtn">Save changes</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/script.js"></script>
  </body>
</html>
