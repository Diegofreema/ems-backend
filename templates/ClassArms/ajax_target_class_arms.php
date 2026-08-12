<select name="target_class_arm_id" class="form-control form-control-user2">
    <option value="">No Class Arm Assignment</option>
    <?php if (!empty($formattedClassArms)): ?>
        <?php foreach ($formattedClassArms as $id => $armName): ?>
            <option value="<?= h($id) ?>"><?= h($armName) ?></option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>
