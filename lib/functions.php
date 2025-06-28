<?php
function table_row($ress) {
    $columns = $ress->fetch_fields();
    ?>
    <thead>
      <tr>
        <?php foreach ($columns as $col): ?>
          <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $col->name))) ?></th>
        <?php endforeach; ?>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $ress->fetch_assoc()): ?>
      <tr>
        <?php foreach ($row as $key => $val): ?>
          <?php if ($key === 'img' || $key === 'image'): ?>
            <td>
              <img src="../uploads/<?= !empty($val) ? htmlspecialchars($val) : 'https://ui-avatars.com/api/?name=' . urlencode($row['username'] ?? 'User') . '&background=random' ?>" 
                   class="img-thumb" style="width:45px;height:45px;border-radius:50%;object-fit:cover;">
            </td>
          <?php elseif ($key === 'role'): ?>
            <?php 
              $role_badge = [
                'admin' => 'bg-label-danger',
                'doctor' => 'bg-label-success',
                'patient' => 'bg-label-primary'
              ];
            ?>
            <td>
              <span class="badge <?= $role_badge[$val] ?? 'bg-label-secondary' ?>">
                <?= ucfirst($val) ?>
              </span>
            </td>
          <?php elseif ($key === 'created_at' || $key === 'registerDate'): ?>
            <td><?= date('Y-m-d', strtotime($val)) ?></td>
          <?php else: ?>
            <td><?= htmlspecialchars($val) ?></td>
          <?php endif; ?>
        <?php endforeach; ?>

        <!-- Actions Column -->
        <td>
          <div class="dropdown">
            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
              <i class="icon-base bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item edit-btn" href="javascript:void(0);" 
                 data-bs-toggle="modal" data-bs-target="#editUserModal"
                 data-user='<?= json_encode($row) ?>'>
                <i class="icon-base bx bx-edit-alt me-1"></i> Edit
              </a>
              <a class="dropdown-item delete-btn" href="javascript:void(0);"
                 data-user-id="<?= $row['id'] ?>" 
                 data-user-name="<?= htmlspecialchars($row['username'] ?? '') ?>">
                <i class="icon-base bx bx-trash me-1"></i> Delete
              </a>
            </div>
          </div>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
    <?php
}
?>
