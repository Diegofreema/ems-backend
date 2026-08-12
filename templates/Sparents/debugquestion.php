<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Debug Question</h3>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Question Debug Information</h5>
        </div>
        <div class="card-body">
            <h6><strong>Question ID:</strong> <?= $question->id ?></h6>
            <h6><strong>Question Text:</strong></h6>
            <p><?= h($question->question_text) ?></p>
            <h6><strong>Question Type:</strong> <?= h($question->question_type) ?></h6>
            <h6><strong>Points:</strong> <?= h($question->points) ?></h6>
            
            <h6><strong>Options:</strong></h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Option ID</th>
                        <th>Order</th>
                        <th>Text</th>
                        <th>Is Correct</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($question->question_options as $option): ?>
                        <tr class="<?= $option->is_correct ? 'table-success' : '' ?>">
                            <td><?= h($option->id) ?></td>
                            <td><?= h($option->order_number) ?></td>
                            <td><?= h($option->option_text) ?></td>
                            <td>
                                <?php if ($option->is_correct): ?>
                                    <span class="badge badge-success">CORRECT</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Incorrect</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="mt-3">
                <a href="<?= $this->Url->build(['action' => 'mykidsassignments']) ?>" class="btn btn-secondary">Back to Assignments</a>
            </div>
        </div>
    </div>
</div>
