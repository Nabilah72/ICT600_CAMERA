<?php
$successMsgs = [
    'added' => 'Record added successfully.',
    'updated' => 'Record updated successfully.',
    'deleted' => 'Record deleted successfully.'
];

$errorMsgs = [
    'invalidName' => 'Name can only contain letters and spaces.',
    'invalidPhone' => 'Phone number can only contain digits.',
    'add' => 'Failed to add record.',
    'update' => 'Failed to update record.',
    'delete' => 'Failed to delete record.',
    'invalidID' => 'Invalid ID.',
    'duplicatePhone' => 'Phone number already exists.',
    'duplicateEmail' => 'Email address already exists.',
];

if (!empty($_GET['success']) && isset($successMsgs[$_GET['success']])): ?>
    <div class="popup-message success-message">
        <?= htmlspecialchars($successMsgs[$_GET['success']]) ?>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['error']) && isset($errorMsgs[$_GET['error']])): ?>
    <div class="popup-message error-message">
        <?= htmlspecialchars($errorMsgs[$_GET['error']]) ?>
    </div>
<?php endif; ?>

<style>
    .popup-message {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 250px;
        padding: 12px 20px;
        border-radius: 6px;
        z-index: 9999;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        animation: fadeOut 5s forwards;
        opacity: 1;
        transition: opacity 0.5s ease;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @keyframes fadeOut {
        0% {
            opacity: 1;
        }

        80% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            visibility: hidden;
        }
    }
</style>