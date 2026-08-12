<option value="">Select Class</option>
<?php foreach ($departments as $id => $name): ?>
    <option value="<?= $id ?>"><?= h($name) ?></option>
<?php endforeach; ?>


