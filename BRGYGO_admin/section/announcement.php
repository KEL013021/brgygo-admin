<?php 
include('sidebar.php'); 
include('../database/connection.php');
?>

<!-- External Dependencies -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="../css/announcement.css" />

<style>
  .image-box {
    border: 2px dashed #ccc;
    border-radius: 10px;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background-color: #f9f9f9;
    overflow: hidden;
    transition: border-color 0.3s ease;
  }

  .image-box:hover {
    border-color: #007bff;
    background-color: #f0f8ff;
  }

  .image-box img {
    max-height: 100%;
    max-width: 100%;
    object-fit: cover;
  }

  .announcement img.uploaded-image {
    width: 100%;
    object-fit: cover;
    max-height: 600px;
    margin-top: 10px;
    border-radius: 10px;
  }

  .profile {
    width: 40px;
    height: 40px;
    border-radius: 50%;
  }
</style>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="navbar-container">
    <div class="section-name">Barangay Announcement</div>
    <div class="notification-wrapper" id="notifBtn">
      <i class="bi bi-bell-fill" style="font-size: 35px;"></i>
      <span class="badge-number">4</span>
    </div>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container" style="margin-left:150px; margin-top: 40px;">
  <div class="row g-4">
    
    <!-- Add/Edit Announcement Form -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100" id="formCard">
        <div class="card-header bg-primary text-white text-center" id="formHeaderContainer" style="height: 60px;">
          <h5 class="mb-0" id="formHeader" style="margin-top: 10px">Add Announcement</h5>
        </div>
        <div class="card-body">
          <form id="announcementForm" method="POST" action="../database/announcement_add.php" enctype="multipart/form-data">
            <input type="hidden" name="announcement_id" id="announcement_id" value="">

            <!-- Image Upload Box -->
            <div class="image-box mb-3" onclick="document.getElementById('image').click();">
              <img id="preview" src="https://via.placeholder.com/400x200?text=Click+to+Upload" alt="Upload Preview" />
            </div>
            <input type="file" name="image" id="image" accept="image/*" style="display: none;">

            <!-- Title -->
            <div class="mb-3">
              <input type="text" class="form-control" id="title" name="title" placeholder="Announcement Title" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
              <textarea class="form-control" id="content" name="content" rows="4" placeholder="Announcement Description" required></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="btn btn-primary w-100">Post Announcement</button>

            <!-- Cancel Button -->
            <button type="button" id="cancelEdit" class="btn btn-secondary w-100 mt-2" style="display: none;">Cancel Edit</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Posted Announcements -->
    <div class="col-md-8">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-success text-white text-center" style="height: 60px;">
          <h5 class="mb-0" style="margin-top: 10px">Posted Announcements</h5>
        </div>
        <div class="card-body d-flex flex-column align-items-center" id="postedContainer" style="overflow-y: auto; max-height:500px">
          <?php
          $result = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
          while ($row = $result->fetch_assoc()):
          ?>
          <div class="announcement mb-3 p-3 border rounded bg-light" data-id="<?= $row['id'] ?>" style="width:100%">

            <!-- Header with Admin Info and Dropdown -->
            <div class="announcement-header d-flex justify-content-between align-items-start mb-2">
              <div class="d-flex align-items-center">
                <img src="../image/Logo.png" class="profile me-2" />
                <div>
                  <strong>Admin</strong><br />
                  <small class="text-muted">Posted on <?= date("F j, Y", strtotime($row['date_posted'])) ?></small>
                </div>
              </div>

              <!-- Dropdown Menu -->
              <div class="dropdown">
                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item editBtn" href="#" data-id="<?= $row['id'] ?>">Edit</a></li>
                  <li><a class="dropdown-item deleteBtn" href="#">Delete</a></li>
                </ul>
              </div>
            </div>

            <!-- Title and Content -->
            <p><strong><?= htmlspecialchars($row['title']) ?></strong></p>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>

            <!-- Image -->
            <?php if ($row['image']): ?>
              <img src="../image/announcement/<?= $row['image'] ?>" class="uploaded-image" />
            <?php endif; ?>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Confirmation Modal -->
<div class="modal fade" id="editConfirmModal" tabindex="-1" aria-labelledby="editConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="editConfirmModalLabel">Confirm Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to edit this announcement?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmEditBtn">Yes, Edit</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this announcement? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>


<!-- Scripts -->
<script>
  // Preview image
  $('#image').on('change', function (event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $('#preview').attr('src', e.target.result);
      };
      reader.readAsDataURL(file);
    }
  });

  // Conditional image validation
  $('#announcementForm').submit(function (e) {
    const imageFile = $('#image')[0].files[0];
    const isEdit = $('#announcement_id').val().trim() !== '';

    if (!isEdit && !imageFile) {
      alert("Please upload an image before submitting.");
      return false;
    }
  });

  let selectedEditId = null; // Store the ID temporarily

$(document).on('click', '.editBtn', function (e) {
  e.preventDefault();
  selectedEditId = $(this).data('id'); // Store ID
  $('#editConfirmModal').modal('show'); // Show modal
});

 $('#confirmEditBtn').on('click', function () {
  if (!selectedEditId) return;

  $.ajax({
    url: '../database/announcement_get.php',
    type: 'GET',
    data: { id: selectedEditId },
    success: function (data) {
      const ann = JSON.parse(data);

      // Fill form
      $('#announcement_id').val(ann.id);
      $('#title').val(ann.title);
      $('#content').val(ann.content);
      $('#preview').attr('src', ann.image || 'https://via.placeholder.com/400x200?text=Click+to+Upload');

      // Switch to Edit Mode
      $('#announcementForm').attr('action', '../database/announcement_edit.php');
      $('#submitBtn').text('Update Announcement');
      $('#formHeader').text('Edit Announcement');
      $('#formHeaderContainer').removeClass('bg-primary').addClass('bg-warning text-dark');
      $('#cancelEdit').show();

      // Hide the modal
      $('#editConfirmModal').modal('hide');
    },
    error: function () {
      alert('Failed to fetch announcement data.');
      $('#editConfirmModal').modal('hide');
    }
  });
});

  // Cancel Edit
  $('#cancelEdit').on('click', function () {
    $('#announcementForm')[0].reset();
    $('#announcement_id').val('');
    $('#preview').attr('src', 'https://via.placeholder.com/400x200?text=Click+to+Upload');
    $('#announcementForm').attr('action', '../database/add_announcement.php');
    $('#submitBtn').text('Post Announcement');
    $('#formHeader').text('Add Announcement');
    $('#formHeaderContainer').removeClass('bg-warning text-dark').addClass('bg-primary text-white');
    $(this).hide();
  });

  let selectedDeleteId = null;

// Show delete modal
$(document).on('click', '.deleteBtn', function (e) {
  e.preventDefault();
  selectedDeleteId = $(this).closest('.announcement').data('id');
  $('#deleteConfirmModal').modal('show');
});

// Confirm delete via AJAX
$('#confirmDeleteBtn').on('click', function () {
  if (!selectedDeleteId) return;

  $.ajax({
    url: '../database/announcement_delete.php',
    type: 'POST',
    data: { id: selectedDeleteId },
    success: function (response) {
      // Remove post from DOM
      $(`.announcement[data-id="${selectedDeleteId}"]`).remove();
      $('#deleteConfirmModal').modal('hide');
    },
    error: function () {
      alert('Failed to delete announcement.');
      $('#deleteConfirmModal').modal('hide');
    }
  });
});
</script>
