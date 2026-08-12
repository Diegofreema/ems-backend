<option value="">Select Subject</option>
<?php foreach ($subjects as $id => $name): ?>
    <option value="<?= $id ?>"><?= h($name) ?></option>
<?php endforeach; ?>


